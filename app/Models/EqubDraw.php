<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EqubDraw extends Model
{
    protected $fillable = [
        'equb_group_id',
        'draw_date',
        'executed_by_admin_id',
        'winner_membership_id',
    ];

    protected function casts(): array
    {
        return [
            'draw_date' => 'datetime',
        ];
    }

    public function equbGroup(): BelongsTo
    {
        return $this->belongsTo(EqubGroup::class, 'equb_group_id');
    }

    public function executedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by_admin_id');
    }

    public function winnerMembership(): BelongsTo
    {
        return $this->belongsTo(EqubMembership::class, 'winner_membership_id');
    }
}
