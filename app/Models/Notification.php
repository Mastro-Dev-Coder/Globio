<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'id',
        'user_id',
        'title',
        'message',
        'type',
        'action_url',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function markAsRead()
    {
        $this->update(['read_at' => Carbon::now(config('app.timezone'))]);
    }

    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
    }
}
