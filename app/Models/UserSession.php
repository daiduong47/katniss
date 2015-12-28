<?php

namespace Katniss\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    const STATUS_IDLE = 2;
    const STATUS_INVISIBLE = 3;
    const STATUS_BUSY = 4;
    const STATUS_AWAY = 5;

    /**
     * @var string
     */
    public $table = 'sessions';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['payload', 'last_activity', 'client_ip', 'user_id', 'status'];

    /**
     * Returns all the guest users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGuests($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Returns all the registered users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLoggedInUsers($query)
    {
        return $query->whereNotNull('user_id')->with('user');
    }

    /**
     * Returns the user that belongs to this entry.
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function isGuest()
    {
        return empty($this->user_id);
    }

    public function getIdleTimeAttribute() // seconds
    {
        return time() - $this->last_activity;
    }

    public function isIdle()
    {
        return $this->idleTime > config('katniss.idle_switching_time');
    }
}
