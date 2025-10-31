<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlotSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'size_value',
        'unit',
        'size_in_sqm',
        'display_text',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'size_value' => 'decimal:2',
        'size_in_sqm' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get available size units
     */
    public static function getUnits(): array
    {
        return [
            'sqm' => 'Square Meters',
            'acre' => 'Acres',
            'hectare' => 'Hectares',
            'plot' => 'Plots (Nigerian Standard)',
        ];
    }

    /**
     * Convert size to square meters based on unit
     */
    public static function convertToSquareMeters(float $size, string $unit): float
    {
        return match ($unit) {
            'acre' => $size * 4047,
            'hectare' => $size * 10000,
            'plot' => $size * 1800, // Standard Nigerian plot is 1800 sqm
            'sqm' => $size,
            default => $size,
        };
    }

    /**
     * Scope for active plot sizes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered plot sizes
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('size_in_sqm');
    }

    /**
     * Get formatted display text
     */
    public function getFormattedDisplayAttribute(): string
    {
        if ($this->display_text) {
            return $this->display_text;
        }

        $formattedValue = number_format($this->size_value, $this->size_value == floor($this->size_value) ? 0 : 2);
        $unit = match ($this->unit) {
            'sqm' => 'sqm',
            'acre' => $this->size_value == 1 ? 'Acre' : 'Acres',
            'hectare' => $this->size_value == 1 ? 'Hectare' : 'Hectares',
            'plot' => $this->size_value == 1 ? 'Plot' : 'Plots',
            default => $this->unit,
        };

        return "{$formattedValue} {$unit}";
    }

    /**
     * Get options for forms
     */
    public static function getFormOptions(): array
    {
        return static::active()
            ->ordered()
            ->get()
            ->mapWithKeys(function ($plotSize) {
                return [$plotSize->id => $plotSize->name . ' (' . $plotSize->formatted_display . ')'];
            })
            ->toArray();
    }

    /**
     * Get range options for search filters
     */
    public static function getRangeOptions(): array
    {
        $sizes = static::active()->ordered()->get();
        $ranges = [];

        // Create ranges based on existing plot sizes
        $sizeThresholds = [1000, 5000, 20000];
        $labels = ['Small', 'Medium', 'Large', 'Very Large'];

        $lastMax = 0;
        foreach ($sizeThresholds as $index => $threshold) {
            $ranges[] = [
                'label' => $labels[$index] . " ({$lastMax} - " . number_format($threshold) . " sqm)",
                'min' => $lastMax,
                'max' => $threshold,
            ];
            $lastMax = $threshold + 1;
        }

        // Add the final range
        $ranges[] = [
            'label' => $labels[count($labels) - 1] . " (" . number_format($lastMax) . "+ sqm)",
            'min' => $lastMax,
            'max' => null,
        ];

        return $ranges;
    }

    /**
     * Boot method to auto-calculate size_in_sqm
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($plotSize) {
            $plotSize->size_in_sqm = static::convertToSquareMeters($plotSize->size_value, $plotSize->unit);
        });
    }
}
