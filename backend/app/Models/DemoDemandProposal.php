<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemoDemandProposal extends Model
{
    protected $fillable = [
        'demo_workforce_demand_id',
        'demo_training_capacity_id',
        'school_id',
        'quantity',
        'plan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function demand(): BelongsTo
    {
        return $this->belongsTo(DemoWorkforceDemand::class, 'demo_workforce_demand_id');
    }

    public function capacity(): BelongsTo
    {
        return $this->belongsTo(DemoTrainingCapacity::class, 'demo_training_capacity_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
