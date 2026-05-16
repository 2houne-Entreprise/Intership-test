<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'status',
        'deadline',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

     public function scopeOverdue($query)
    {
        return $query->whereNotNull('deadline')
                     ->where('deadline', '<', Carbon::today());
    }

        public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'En attente',
            'in_progress' => 'En cours',
            'done' => 'Terminé',
            default => $this->status,
        };
    }
   
}
