<?php

namespace App\Livewire\Invitation\Editor\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Move an item one position up or down within the invitation's own records.
 * Items are always looked up through the relation, so foreign IDs 404.
 */
trait ReordersItems
{
    abstract protected function reorderRelation(): HasMany;

    public function move(int $id, string $direction): void
    {
        $this->authorizeUpdate();

        $items = $this->reorderRelation()->get()->values();
        $index = $items->search(fn ($item) => $item->id === $id);
        abort_if($index === false, 404);

        $target = $direction === 'up' ? $index - 1 : $index + 1;
        if ($target < 0 || $target >= $items->count()) {
            return;
        }

        $ordered = $items->all();
        [$ordered[$index], $ordered[$target]] = [$ordered[$target], $ordered[$index]];

        foreach ($ordered as $position => $item) {
            if ($item->sort_order !== $position) {
                $item->forceFill(['sort_order' => $position])->save();
            }
        }
    }
}
