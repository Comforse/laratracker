<?php

namespace App\Models;

use App\Helpers\StringHelper;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Rooles\Traits\UserRole;

/**
 * @property mixed password_hash
 */
class User extends Model implements AuthenticatableContract,
    //AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, UserRole;

    const ACCOUNT_BANNED = -1;
    const ACCOUNT_PENDING = 0;
    const ACCOUNT_ACTIVE = 1;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'email', 'password_hash', 'secret', 'created_at', 'updated_at', 'last_login', 'last_seen', 'account_status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password_hash', 'secret'];

    /**
     * @param array $attributes
     * @return void|static
     */
    public static function create(array $attributes = [])
    {
        // Add salt
        if(!array_key_exists('secret', $attributes) || $attributes['secret'] == '') {
            $secret = StringHelper::generateRandomString(20);
            $attributes['secret'] = $secret;
        }

        // Account status
        $attributes['account_status'] = self::ACCOUNT_ACTIVE;

        $model = parent::create($attributes);

        // Generate passkey
        UserPasskeys::create([
            'user_id' => $model->id,
            'passkey' => md5(StringHelper::generateRandomString().time().$model->secret)
        ]);

        return $model;
    }

    public static function getByID($id)
    {
        return self::where('id', $id)->first();
    }

    /**
     * Get model by e-mail
     *
     * @param $email
     * @return mixed
     */
    public static function getByEmail($email)
    {
        return self::where('email', $email)->first();
    }

    /**
     * Get model by username
     *
     * @param $username
     * @return mixed
     */
    public static function getByUsername($username)
    {
        return self::where('username', $username)->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function torrents()
    {
        return $this->hasMany('App\Models\Torrent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function passkeys()
    {
        return $this->hasOne('App\Models\UserPasskeys');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function peers()
    {
        return $this->hasManyThrough('App\Model\Peer', 'App\Models\PeerTorrent');
    }

    /**
     * @return mixed
     */
    public function getAuthPassword() {
        return $this->password_hash;
    }
}
