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
 * @file       PeerTorrent.php
 * @created    12/18/2015 7:46 PM
 * @copyright  Copyright (c) 2015 Comforse (comforse.github@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Comforse
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class PeerTorrent extends Model
{

    /**
     * Related table
     *
     * @var string
     */
    protected $table = 'peer_torrent';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['peer_id', 'torrent_id'];


    /**
     * Get peer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function peerInstance()
    {
        return $this->belongsTo('App\Models\Peer', 'peer_id', 'id');
    }

    /**
     * Associated torrents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function torrents()
    {
        return $this->hasMany('App\Models\Torrent');
    }

    public static function getByPeerAndTorrent(Peer $peer, Torrent $torrent)
    {
        return self::where('peer_id', '=', $peer->id)->where('torrent_id', '=', $torrent->id)->first();
    }
}