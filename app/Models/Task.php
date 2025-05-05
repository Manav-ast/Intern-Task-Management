<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'description', 'status', 'created_by', 'due_date'];

    protected $casts = [
        'due_date' => 'date'
    ];

    // Relationships
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function interns()
    {
        return $this->belongsToMany(Intern::class, 'task_intern');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
