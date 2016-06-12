<?php
/**
 * Comforse
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @project    Lara Tracker
 * @file       Peer.php
 * @created    12/18/2015 7:42 PM
 * @copyright  Copyright (c) 2015 Comforse (comforse.github@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Comforse
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Peer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['hash', 'user_agent', 'ip_address', 'passkey', 'created_at', 'updated_at', 'port'];

    
    public static function getByHashAndPasskey($hash, $passkey)
    {
        return self::where('hash', '=', $hash)->where('passkey', '=', $passkey)->first();
    }

    /**
     * Retrieves a peer by IP address and Passkey
     *
     * @param $ip
     * @param $passkey
     * @return mixed
     */
    public static function getByIPAndPasskey($ip, $passkey)
    {
        return self::where('ip_address', '=', $ip)->where('passkey', '=', $passkey)->first();
    }
}