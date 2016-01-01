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


use App\Helpers\BencodeHelper;
use App\Helpers\StringHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * @property mixed peers
 * @property mixed files_list
 * @property mixed hash
 * @property mixed seeders
 * @property int leechers
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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'string_id', 'description', 'filename', 'category_id', 'info_hash', "picture", 'hash', 'size', 'files_list', 'user_id'];


    /**
     * Uploader
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Related category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category');
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

    public static function getByStringID($string_id)
    {
        return self::where('string_id', '=', $string_id)->first();
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

    /**
     * Returns an array of peers:
     * array(
     *  array(ip, port, peer_id)
     * ...
     * )
     *
     * @return array
     */
    public function getPeersArray()
    {
        $pt = $this->peers();
        $peers_array = array();

        foreach($pt->getResults() AS $peer) {
            $p = $peer->peerInstance()->getResults();
            $ip = $p->ip_address;
            $peers_array[] = array('ip' => $ip, 'port' => $p->port, 'peer_id' => (base64_decode($p->hash)));
        }
        return $peers_array;
    }

    /**
     * Handles model creation in DB
     *
     * @param array $attributes
     * @return static
     */
    public static function create(array $attributes = [])
    {
        // Add a random string as a key for extra security
        if(!array_key_exists('string_id', $attributes) || $attributes['string_id'] == '') {
            $attributes['string_id'] = StringHelper::generateRandomString(5);
        }

        return parent::create($attributes);
    }

    public function getFileListArray()
    {

    }

    /**
     * Returns the path to the server torrent file
     *
     * @return string
     */
    public function getFilePath()
    {
        $config = config('settings');
        return $config['torrents_upload_dir'].$this->hash.".torrent";
    }

    /**
     * Returns the decoded dictionary of the torrent file
     *
     * @return array|void
     */
    public function getDictionary()
    {
        return BencodeHelper::decodeFile($this->getFilePath());
    }

    /**
     * Updates seeders field and returns the object
     *
     * @return $this
     */
    public function updateSeeders()
    {
        $this->seeders = count(PeerTorrent::getSeeders($this));
        $this->save();
        return $this;
    }

    /**
     * Updates leechers field and returns the object
     *
     * @return $this
     */
    public function updateLeechers()
    {
        $this->leechers = count(PeerTorrent::getLeechers($this));
        $this->save();
        return $this;
    }

    /**
     * Actions to be performed when an announce occurs
     *
     * @return $this
     */
    public function announce()
    {
        // Yodate Seeders field
        $this->updateSeeders();

        // Update leechers field
        $this->updateLeechers();

        // return current object
        return $this;
    }
}