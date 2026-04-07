<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'status',
        'deadline',
        'attachment_path'
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    // Relation avec Project
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // ✅ Local Scope pour les tâches en retard (US6)
    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                     ->where('status', '!=', 'done');
    }

    // ✅ Accessor pour formater le statut en français (US6)
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'in_progress' => 'En cours',
            'done' => 'Terminé',
            default => $this->status,
        };
    }
}