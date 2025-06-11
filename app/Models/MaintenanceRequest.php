<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MaintenanceRequest extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'property_id',
        'tenant_id',
        'lease_id',
        'landlord_id',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'estimated_cost',
        'actual_cost',
        'contractor_name',
        'contractor_phone',
        'contractor_email',
        'scheduled_date',
        'completed_date',
        'tenant_notes',
        'landlord_notes',
        'contractor_notes',
        'is_emergency',
        'reported_by',
        'assigned_to',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'completed_date' => 'datetime',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'is_emergency' => 'boolean',
    ];

    // Categories
    const CATEGORY_PLUMBING = 'plumbing';
    const CATEGORY_ELECTRICAL = 'electrical';
    const CATEGORY_HVAC = 'hvac';
    const CATEGORY_APPLIANCES = 'appliances';
    const CATEGORY_STRUCTURAL = 'structural';
    const CATEGORY_PAINTING = 'painting';
    const CATEGORY_FLOORING = 'flooring';
    const CATEGORY_SECURITY = 'security';
    const CATEGORY_LANDSCAPING = 'landscaping';
    const CATEGORY_OTHER = 'other';

    // Priorities
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Statuses
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_ACKNOWLEDGED = 'acknowledged';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ON_HOLD = 'on_hold';

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_PLUMBING => 'Plumbing',
            self::CATEGORY_ELECTRICAL => 'Electrical',
            self::CATEGORY_HVAC => 'HVAC/Air Conditioning',
            self::CATEGORY_APPLIANCES => 'Appliances',
            self::CATEGORY_STRUCTURAL => 'Structural',
            self::CATEGORY_PAINTING => 'Painting',
            self::CATEGORY_FLOORING => 'Flooring',
            self::CATEGORY_SECURITY => 'Security',
            self::CATEGORY_LANDSCAPING => 'Landscaping',
            self::CATEGORY_OTHER => 'Other',
        ];
    }

    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_ACKNOWLEDGED => 'Acknowledged',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_ON_HOLD => 'On Hold',
        ];
    }

    /**
     * Property the request is for
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Tenant who made the request
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Lease associated with the request
     */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    /**
     * Landlord receiving the request
     */
    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * User who reported the issue
     */
    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * User assigned to handle the request
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('before_photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif']);

        $this->addMediaCollection('after_photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif']);

        $this->addMediaCollection('documents')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }

    /**
     * Check if request is overdue
     */
    public function isOverdue(): bool
    {
        return $this->scheduled_date && 
               $this->scheduled_date < now() && 
               !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_SUBMITTED => 'warning',
            self::STATUS_ACKNOWLEDGED => 'info', 
            self::STATUS_IN_PROGRESS => 'primary',
            self::STATUS_SCHEDULED => 'info',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_ON_HOLD => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Get priority color for UI
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'success',
            self::PRIORITY_MEDIUM => 'warning',
            self::PRIORITY_HIGH => 'danger',
            self::PRIORITY_URGENT => 'danger',
            default => 'secondary'
        };
    }
}
