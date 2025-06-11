<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PropertyType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Boot method for auto-generating slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    // Relationships
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function propertySubtypes(): HasMany
    {
        return $this->hasMany(PropertySubtype::class);
    }

    public function subtypes(): HasMany
    {
        return $this->propertySubtypes();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Helper methods
    public function getPropertiesCountAttribute(): int
    {
        return $this->properties()->count();
    }

    public function getActivePropertiesCountAttribute(): int
    {
        return $this->properties()->where('status', 'available')->count();
    }

    public static function getDefaultTypes(): array
    {
        return [
            ['name' => 'Apartment', 'icon' => 'heroicon-o-building-office'],
            ['name' => 'House', 'icon' => 'heroicon-o-home'],
            ['name' => 'Land', 'icon' => 'heroicon-o-map'],
            ['name' => 'Commercial', 'icon' => 'heroicon-o-building-storefront'],
            ['name' => 'Office Space', 'icon' => 'heroicon-o-building-office-2'],
            ['name' => 'Warehouse', 'icon' => 'heroicon-o-cube'],
        ];
    }
}
