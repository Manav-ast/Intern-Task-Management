<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'message',
        'sender_id',
        'sender_type',
        'receiver_id',
        'receiver_type',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime'
    ];

    // Polymorphic sender/receiver
    public function sender()
    {
        return $this->morphTo();
    }

    public function receiver()
    {
        return $this->morphTo();
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead()
    {
        return !is_null($this->read_at);
    }
}
