<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Intern;
use App\Models\Comment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        try {
            // Get counts in a single query using DB::select
            $counts = DB::select("
                SELECT
                    (SELECT COUNT(*) FROM interns WHERE deleted_at IS NULL) as interns_count,
                    (SELECT COUNT(*) FROM tasks WHERE status IN ('pending', 'in_progress') AND deleted_at IS NULL) as active_tasks_count,
                    (SELECT COUNT(*) FROM comments WHERE deleted_at IS NULL) as comments_count
            ")[0];

            // Get recent tasks with their relationships in a single query with eager loading
            $recentTasks = Task::with([
                'interns',  // Eager load interns relation
                'comments'  // Eager load comments relation
            ])
                ->latest()
                ->take(5)
                ->get();

            return view('admin.dashboard', [
                'internsCount' => $counts->interns_count,
                'activeTasks' => $counts->active_tasks_count,
                'commentsCount' => $counts->comments_count,
                'recentTasks' => $recentTasks
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading admin dashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading dashboard data. Please try again.');
        }
    }
}
