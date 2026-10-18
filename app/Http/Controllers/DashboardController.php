<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'range' => ['nullable', 'in:month,last_month,all'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);
        $range = $validated['range'] ?? 'month';
        $search = $validated['search'] ?? '';
        $today = Carbon::now('Africa/Nouakchott');
        $todayLabel = $today->locale('fr')->translatedFormat('j F Y');
        [$periodStart, $periodEnd, $previousStart, $previousEnd] = $this->period($range, $today);
        $projects = Project::accessibleTo($request->user())
            ->withCount(['tasks as open_tasks_count' => fn ($query) => $query->where('status', '!=', 'done')])
            ->get();
        $projectIds = $projects->modelKeys();
        $tasks = Task::query()->whereIn('project_id', $projectIds);

        if ($periodStart !== null && $periodEnd !== null) {
            $tasks->whereBetween('created_at', [$periodStart, $periodEnd]);
        }

        $totalTasks = (clone $tasks)->count();
        $pendingTasks = (clone $tasks)->where('status', 'pending')->count();
        $inProgressTasks = (clone $tasks)->where('status', 'in_progress')->count();
        $completedTasks = (clone $tasks)->where('status', 'done')->count();
        $overdueTasks = (clone $tasks)->whereNotNull('deadline')
            ->whereDate('deadline', '<', $today->toDateString())
            ->where('status', '!=', 'done')
            ->count();
        $activeProjects = $projects->where('open_tasks_count', '>', 0)->count();
        $previousTasks = $previousStart !== null && $previousEnd !== null
            ? Task::query()->whereIn('project_id', $projectIds)
                ->whereBetween('created_at', [$previousStart, $previousEnd])
                ->count()
            : null;
        $comparison = $this->comparisonLabel($totalTasks, $previousTasks);
        $recentTasks = (clone $tasks)
            ->with('project:id,name')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', '%'.$search.'%')
                        ->orWhereHas('project', fn ($project) => $project->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->latest()
            ->limit(6)
            ->get();

        return view('dashboard', compact(
            'activeProjects',
            'comparison',
            'completedTasks',
            'inProgressTasks',
            'overdueTasks',
            'pendingTasks',
            'range',
            'recentTasks',
            'search',
            'totalTasks',
            'todayLabel',
        ));
    }

    private function period(string $range, Carbon $today): array
    {
        return match ($range) {
            'last_month' => [
                $today->copy()->subMonthNoOverflow()->startOfMonth(),
                $today->copy()->subMonthNoOverflow()->endOfMonth(),
                $today->copy()->subMonthsNoOverflow(2)->startOfMonth(),
                $today->copy()->subMonthsNoOverflow(2)->endOfMonth(),
            ],
            'all' => [null, null, null, null],
            default => [
                $today->copy()->startOfMonth(),
                $today->copy()->endOfMonth(),
                $today->copy()->subMonthNoOverflow()->startOfMonth(),
                $today->copy()->subMonthNoOverflow()->endOfMonth(),
            ],
        };
    }

    private function comparisonLabel(int $current, ?int $previous): string
    {
        if ($previous === null) {
            return 'Toutes périodes confondues';
        }

        if ($previous === 0) {
            return $current === 0 ? 'Aucune tâche sur la période précédente' : 'Nouvelles tâches cette période';
        }

        $change = (int) round((($current - $previous) / $previous) * 100);

        return sprintf('%+d%% par rapport à la période précédente', $change);
    }
}
