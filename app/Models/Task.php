<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'status',
        'deadline',
        'attachment_path',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeOverdue($query)
    {
        return $query
            ->where('deadline', '<', Carbon::today())
            ->where('status', '!=', 'done');
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'En attente',
            'in_progress' => 'En cours',
            'done' => 'Terminée',
            default => $this->status,
        };
    }
}
