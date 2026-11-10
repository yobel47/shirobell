<?php

namespace App\Services\Admin;

use App\Models\GiftAccount;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\InvitationEvent;
use App\Models\InvitationPhoto;
use App\Models\InvitationStory;
use App\Models\Wish;
use App\Policies\InvitationPolicy;
use Illuminate\Database\Eloquent\Model;

/**
 * Request-scoped audit for admins editing a customer's invitation in the
 * customer editor. EditsInvitation::authorizeUpdate() calls begin(); every
 * content model saved or deleted afterwards in the same request is logged.
 * Filament actions never call begin(), so they are not double-logged
 * (AdminActionService logs those).
 */
class AdminEditAudit
{
    public const MODELS = [
        Invitation::class, InvitationEvent::class, InvitationPhoto::class, InvitationStory::class,
        GiftAccount::class, Guest::class, Wish::class,
    ];

    private const IGNORED_FIELDS = ['updated_at', 'created_at'];

    private ?Invitation $invitation = null;

    private ?string $source = null;

    public function __construct(private readonly AuditLogger $audit) {}

    public function begin(Invitation $invitation, string $source): void
    {
        $this->invitation = $invitation;
        $this->source = class_basename($source);
    }

    public function active(): bool
    {
        return $this->invitation !== null;
    }

    public function record(Model $model, string $event): void
    {
        if (! $this->invitation || ! in_array($model::class, self::MODELS, true)
            || ! InvitationPolicy::actingAsAdmin(auth()->user(), $this->invitation)) {
            return;
        }

        $fields = $event === 'deleted'
            ? []
            : array_values(array_diff(array_keys($model->getChanges()), self::IGNORED_FIELDS));

        if ($event !== 'deleted' && $fields === [] && ! $model->wasRecentlyCreated) {
            return;
        }

        $this->audit->log('invitation.admin_edit', $this->invitation, [
            'owner_id' => $this->invitation->user_id,
            'source' => $this->source,
            'model' => class_basename($model),
            'model_id' => $model->getKey(),
            'event' => $model->wasRecentlyCreated && $event === 'saved' ? 'created' : $event,
            'fields' => $fields,
        ]);
    }
}
