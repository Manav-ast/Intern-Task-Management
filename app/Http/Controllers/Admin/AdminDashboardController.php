<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Intern;
use App\Models\Comment;

class AdminDashboardController extends Controller
{
    public function index()
    {
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
    }
}
