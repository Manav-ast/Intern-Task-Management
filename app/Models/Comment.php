<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = ['task_id', 'message'];

    // Relationships
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }
}
