<?php

namespace App\Models;

use App\Enums\EqubDrawType;
use App\Enums\EqubGroupStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EqubGroup extends Model
{
    protected $fillable = [
        'equb_package_id',
        'name',
        'fixed_contribution_amount',
        'contribution_frequency_days',
        'duration_type',
        'duration_value',
        'duration_unit',
        'terms_content',
        'registration_open_at',
        'registration_close_at',
        'equb_start_date',
        'equb_end_date',
        'max_members',
        'status',
        'is_locked',
        'current_members_count',
        'draw_type',
        'total_amount_per_draw',
    ];

    protected $attributes = [
        'duration_type' => 'fixed',
    ];
    protected function casts(): array
    {
        return [
            'registration_open_at' => 'datetime',
            'registration_close_at' => 'datetime',
            'equb_start_date' => 'datetime',
            'equb_end_date' => 'datetime',
            'duration_value' => 'integer',
            'status' => EqubGroupStatus::class,
            'is_locked' => 'boolean',
            'draw_type' => EqubDrawType::class,
            'duration_type' => \App\Enums\EqubDurationType::class,
            'duration_unit' => \App\Enums\EqubDurationUnit::class,
            'fixed_contribution_amount' => 'decimal:2',
            'total_amount_per_draw' => 'decimal:2',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(EqubPackage::class, 'equb_package_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(EqubMembership::class, 'equb_group_id');
    }

    public function draws(): HasMany
    {
        return $this->hasMany(EqubDraw::class, 'equb_group_id');
    }

    public function cohorts(): HasMany
    {
        return $this->hasMany(Cohort::class, 'equb_group_id');
    }

    public function isRegistrationOpen(): bool
    {
        if ($this->status !== EqubGroupStatus::Registration) {
            return false;
        }
        if ($this->registration_open_at && $this->registration_open_at->isFuture()) {
            return false;
        }
        if ($this->registration_close_at && $this->registration_close_at->isPast()) {
            return false;
        }
        if ($this->max_members && $this->current_members_count >= $this->max_members) {
            return false;
        }

        return true;
    }
}
