<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use Carbon\Carbon;

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