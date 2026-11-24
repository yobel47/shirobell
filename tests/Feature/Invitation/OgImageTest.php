<?php

namespace Tests\Feature\Invitation;

use App\Models\Invitation;
use App\Services\OgImageService;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OgImageTest extends TestCase
{
    use RefreshDatabase;

    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);
        Storage::fake('public');
        config(['shirobell.og_images' => true]);

        $this->invitation = Invitation::factory()->active()->create(['slug' => 'budi-ani']);
        $this->invitation->events()->create(['name' => 'Akad', 'date' => '2027-02-14', 'start_time' => '08:00', 'timezone' => 'WIB', 'venue_name' => 'Masjid Raya']);
    }

    private function cards(): array
    {
        return Storage::disk('public')->files('og/'.$this->invitation->uuid);
    }

    public function test_card_is_drawn_after_the_response_and_used_from_then_on(): void
    {
        $url = 'http://budi-ani.'.config('app.domain');
        Storage::disk('public')->deleteDirectory('og');

        // First view: nothing rendered inline; the card is drawn once the response is sent.
        $this->get($url)->assertOk()->assertDontSee('/storage/og/', false);
        $this->assertCount(1, $this->cards());

        $html = $this->get($url)->assertOk()->getContent();
        $this->assertMatchesRegularExpression('#<meta property="og:image" content="[^"]+/storage/og/'.$this->invitation->uuid.'/[0-9a-f]{12}\.png">#', $html);
        $this->assertStringContainsString('<meta property="og:image:width" content="1200">', $html);

        [$width, $height, $type] = getimagesizefromstring(Storage::disk('public')->get($this->cards()[0]));
        $this->assertSame([1200, 630, IMAGETYPE_PNG], [$width, $height, $type]);
    }

    public function test_card_is_cached_and_replaced_when_names_change(): void
    {
        $service = app(OgImageService::class);
        $first = $service->url($this->invitation);
        $this->assertSame($first, $service->url($this->invitation->fresh()));

        $this->invitation->update(['bride_nickname' => 'Sekar']);
        $second = $service->url($this->invitation->fresh());

        $this->assertNotSame($first, $second);
        $this->assertCount(1, $this->cards(), 'the outdated card is removed');
    }

    public function test_card_is_removed_with_the_invitation(): void
    {
        app(OgImageService::class)->url($this->invitation);
        $this->assertCount(1, $this->cards());

        $this->invitation->delete();

        $this->assertSame([], $this->cards());
    }

    public function test_drafts_get_no_card(): void
    {
        $draft = Invitation::factory()->create(['slug' => 'draf-saja']);

        app(OgImageService::class)->warm($draft);
        app()->terminate();

        $this->assertSame([], Storage::disk('public')->files('og/'.$draft->uuid));
    }

    public function test_long_names_and_missing_date_still_render(): void
    {
        $this->invitation->events()->delete();
        $this->invitation->update(['groom_nickname' => 'Muhammad Raihan Pratama', 'bride_nickname' => 'Anindya Kusumawardhani']);

        $png = app(OgImageService::class)->render($this->invitation->fresh(), null);

        $this->assertSame(1200, getimagesizefromstring($png)[0]);
    }
}
