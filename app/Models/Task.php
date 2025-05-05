<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'description', 'status', 'created_by'];

    // Relationships
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function interns()
    {
        return $this->belongsToMany(Intern::class)->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
