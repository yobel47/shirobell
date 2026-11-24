<?php

namespace Tests\Feature\Invitation;

use App\Models\Invitation;
use App\Models\User;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Platform pages load only Inter + Cormorant; the editor's theme and design
 * tabs must load every family (font pair previews, canvas text).
 */
class EditorFontsTest extends TestCase
{
    use RefreshDatabase;

    public function test_theme_and_design_tabs_load_every_font_family(): void
    {
        $this->seed(ThemeSeeder::class);
        $owner = User::factory()->create();
        $invitation = Invitation::factory()->for($owner)->create();

        // Pinyon Script is only used by font pairs and engine themes.
        foreach (['tema', 'desain'] as $tab) {
            $this->actingAs($owner)->get(route('app.invitations.edit', [$invitation, $tab]))
                ->assertOk()
                ->assertSee('pinyon-script-400-normal', false);
        }

        $this->actingAs($owner)->get(route('app.invitations.edit', [$invitation, 'mempelai']))
            ->assertOk()
            ->assertDontSee('pinyon-script-400-normal', false)
            ->assertSee('cormorant-garamond-400-normal', false);
    }
}
