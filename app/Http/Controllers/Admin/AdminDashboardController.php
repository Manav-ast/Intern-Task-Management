<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Intern;
use App\Models\Comment;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function index()
    {
        try {
            $internsCount = Intern::count();
            $activeTasks = Task::whereIn('status', ['pending', 'in_progress'])->count();
            $commentsCount = Comment::count();
            $recentTasks = Task::with(['interns', 'comments'])
                ->latest()
                ->take(5)
                ->get();

            return view('admin.dashboard', compact(
                'internsCount',
                'activeTasks',
                'commentsCount',
                'recentTasks'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading admin dashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading dashboard data. Please try again.');
        }
    }
}
