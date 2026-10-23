<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandProposal extends Model
{
    protected $fillable = [
        'workforce_demand_id', 'training_capacity_id', 'quantity', 'plan', 'status',
        'created_by', 'source', 'external_id', 'is_sample',
    ];

    protected function casts(): array
    {
        return ['quantity' => 'integer', 'is_sample' => 'boolean'];
    }

    public function demand(): BelongsTo
    {
        return $this->belongsTo(WorkforceDemand::class, 'workforce_demand_id');
    }

    public function capacity(): BelongsTo
    {
        return $this->belongsTo(TrainingCapacity::class, 'training_capacity_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
