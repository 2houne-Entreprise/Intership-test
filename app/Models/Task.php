<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'status', 'deadline', 'project_id', 'attachment_path'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'En attente',
            'in_progress' => 'En cours',
            'done' => 'Terminé',
        };
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())->where('status', '!=', 'done');
    }
}
