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
 * @file       Torrent.php
 * @created    12/18/2015 7:34 PM
 * @copyright  Copyright (c) 2015 Comforse (comforse.github@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Comforse
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * @property mixed peers
 */
class Torrent extends Model
{
    /**
     * The associated table
     *
     * @var string
     */
    protected $table = 'torrent';

    /**
     * Uploader
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function uploader()
    {
        return $this->hasOne('App\Models\User');
    }

    /**
     * Related category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->hasOne('App\Models\Category');
    }

    /**
     * Get peers
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function peers()
    {
        return $this->hasMany('App\Models\PeerTorrent');
    }

    public static function getByInfoHash($info_hash)
    {
        return self::where('info_hash', '=', $info_hash)->first();
    }

    /**
     * Count seeders
     *
     * @return mixed
     */
    public function getSeedersCount()
    {
        return $this->peers()->where('left', '=', 0)->where('stopped', '=', false)->count();
    }

    /**
     * Leechers count
     *
     * @return mixed
     */
    public function getLeechersCount()
    {
        return $this->peers()->where('left', '>', 0)->where('stopped', false)->count();
    }

    public function getPeersArray()
    {
        $pt = $this->peers();
        $peers_array = array();

        foreach($pt->getResults() AS $peer) {
            $p = $peer->peerInstance()->getResults();
            $ip = $p->ip_address;
            $peers_array[] = array('ip' => $ip, 'port' => $p->port, 'peer_id' => hex2bin($p->hash));
        }
        return $peers_array;
    }
}