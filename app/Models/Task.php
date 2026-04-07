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

    /**
     * Local Scope : Filtrer les tâches en retard
     * Une tâche est en retard si :
     * - La date limite est passée (deadline < aujourd'hui)
     * - ET le statut n'est pas "done" (terminé)
     */
    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                     ->where('status', '!=', 'done');
    }
    
    /**
     * Local Scope : Filtrer les tâches par statut
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
    
    /**
     * Local Scope : Filtrer les tâches à faire (pending + in_progress)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'in_progress']);
    }

    /**
     * Accessor : Formater le statut en français
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => '⏳ En attente',
            'in_progress' => '🔄 En cours',
            'done' => '✅ Terminé',
            default => $this->status,
        };
    }
    
    /**
     * Accessor : Vérifier si la tâche est en retard
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline && $this->deadline < now() && $this->status !== 'done';
    }
    
    /**
     * Mutator : Formater la date limite avant sauvegarde
     */
    public function setDeadlineAttribute($value)
    {
        $this->attributes['deadline'] = $value ?: null;
    }
}