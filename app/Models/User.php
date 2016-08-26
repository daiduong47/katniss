<?php

namespace Katniss\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Katniss\Models\Helpers\DateTimeHelper;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use EntrustUserTrait;

    const AVATAR_THUMB_WIDTH = 100; // pixels
    const AVATAR_THUMB_HEIGHT = 100; // pixels

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'display_name',
        'name',
        'email',
        'password',
        'url_avatar',
        'url_avatar_thumb',
        'activation_code',
        'active',
        'setting_id',
        'channel',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'activation_code',
        'active',
        'setting_id',
        'settings',
        'channel',
        'created_at',
        'updated_at',
        'roles',
        'perms',
    ];

    public function getOwnDirectoryAttribute()
    {
        $dir = 'user_' . $this->id;
        makeUserPublicPath($dir);
        return $dir;
    }

    public function getProfilePictureDirectoryAttribute()
    {
        $dir = concatDirectories('user_' . $this->id, 'profile_pictures');
        makeUserPublicPath($dir);
        return $dir;
    }

    public function getMemberSinceAttribute()
    {
        return DateTimeHelper::getInstance()->shortDate($this->attributes['created_at']);
    }

    public function socialProviders()
    {
        return $this->hasMany(UserSocial::class, 'user_id', 'id');
    }

    public function scopeFromSocial($query, $provider, $provider_id, $email = null)
    {
        $query->whereExists(function ($query) use ($provider, $provider_id) {
            $query->select(DB::raw(1))
                ->from('user_socials')
                ->where('provider', $provider)->where('provider_id', $provider_id);
        });
        if (!empty($email)) {
            $query->orWhere('email', $email);
        }
        return $query;
    }

    public function settings()
    {
        return $this->hasOne(UserSetting::class, 'id', 'setting_id');
    }

    public static function create(array $attributes = [])
    {
        $settings = UserSetting::create();
        $attributes['setting_id'] = $settings->id;
        return parent::create($attributes);
    }
}