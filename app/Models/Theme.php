<?php

namespace App\Models;

use App\Services\ThemeRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'name', 'description', 'thumbnail_path', 'badge', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    /** Active themes whose Blade view actually exists. */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }

    /** Engine themes (config/themes.php) share one view; handcrafted themes have their own folder. */
    public function viewName(): string
    {
        return app(ThemeRegistry::class)->isEngine($this->slug)
            ? 'themes._engine.index'
            : "themes.{$this->slug}.index";
    }

    /** Vite CSS entry used by the theme's layout. */
    public function cssEntry(): string
    {
        return app(ThemeRegistry::class)->isEngine($this->slug)
            ? 'resources/css/themes/engine.css'
            : "resources/css/themes/{$this->slug}.css";
    }

    public function category(): ?string
    {
        return app(ThemeRegistry::class)->category($this->slug);
    }

    public function categoryLabel(): ?string
    {
        return app(ThemeRegistry::class)->categoryLabel($this->slug);
    }

    /** @return array<string, array{name: string, colors: array<string, string>}> */
    public function variants(): array
    {
        return app(ThemeRegistry::class)->variants($this->slug);
    }

    public function hasView(): bool
    {
        return view()->exists($this->viewName());
    }
}
