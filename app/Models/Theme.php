<?php

namespace App\Models;

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

    public function viewName(): string
    {
        return "themes.{$this->slug}.index";
    }

    public function hasView(): bool
    {
        return view()->exists($this->viewName());
    }
}
