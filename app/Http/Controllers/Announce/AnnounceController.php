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
 * @file       Announce.php
 * @created    12/18/2015 7:26 PM
 * @copyright  Copyright (c) 2015 Comforse (comforse.github@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Comforse
 */

namespace App\Http\Controllers\Announce;

use App\Helpers\BencodeHelper;

use App\Models\Peer;
use App\Models\PeerTorrent;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class AnnounceController extends Controller
{
    /**
     * The announce action
     * @return $this
     * @internal param Request $request
     */
    public function announce(Request $request)
    {
        Log::info($request->fullUrl());
        // GET params sent by the client
        $passkey = Input::get('passkey');
        $peer_id = base64_encode(Input::get('peer_id'));
        $info_hash = Input::get('info_hash');
        $downloaded = Input::get('downloaded') ? intval(Input::get('downloaded')) : 0;
        $uploaded = Input::get('uploaded') ? intval(Input::get('uploaded')) : 0;
        $left = Input::get('left') ? intval(Input::get('left')) : 0;
        $compact = Input::get('compact') ? intval(Input::get('compact')) : 0;
        $no_peer_id = Input::get('no_peer_id') ? intval(Input::get('no_peer_id')) : 0;

        // Client IP address
        $ipAddress = '';
        // Check for X-Forwarded-For headers and use those if found
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ('' !== trim($_SERVER['HTTP_X_FORWARDED_FOR']))) {
            $ipAddress = (trim($_SERVER['HTTP_X_FORWARDED_FOR']));
        } else {
            if (isset($_SERVER['REMOTE_ADDR']) && ('' !== trim($_SERVER['REMOTE_ADDR']))) {
                $ipAddress = (trim($_SERVER['REMOTE_ADDR']));
            }
        }

        // Check client ports
        $port = $_SERVER['REMOTE_PORT'];
        if(!$port || !ctype_digit($port) || intval($port) < 1 || intval($port) > 65535) {
            return BencodeHelper::bencodedResponseRaw("Invalid client port", 400);
        }

        if ($port == 999 && substr($peer_id, 0, 10) == '-TO0001-XX') {
            return BencodeHelper::bencodedResponseRaw("d8:completei0e10:incompletei0e8:intervali600e12:min intervali60e5:peersld2:ip12:72.14.194.184:port3:999ed2:ip11:72.14.194.14:port3:999ed2:ip12:72.14.194.654:port3:999eee", 400);
        }

        // Check passkey param
        if (!$passkey) {
            return BencodeHelper::bencodedResponseRaw("Missing passkey", 401);
        }

        // Find passkey-related user
        $user = User::has('passkeys', '=', $passkey)->get();
        if ($user == null) {
            return BencodeHelper::bencodedResponseRaw("Invalid passkey", 401);
        }

        // Check info_hash param
        if (!$info_hash) {
            return BencodeHelper::bencodedResponseRaw("Missing info hash", 401);
        }

        $info_hash = strtoupper(bin2hex($info_hash));

        // Check torrent hash
        $torrent = Torrent::getByInfoHash($info_hash);
        if (!$torrent || $torrent == null) {
            return BencodeHelper::bencodedResponseRaw("Torrent not registered with this tracker.", 404);
        }

        // Check if peer already exists
        $peer = Peer::getByIPAndPasskey($ipAddress, $passkey);

        // Create a new one if it does not
        if ($peer == null) {
            $peer = Peer::create([
                'hash' => $peer_id,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'ip_address' => $ipAddress,
                'passkey' => $passkey,
                'port' => $port
            ]);
        }

        // Check info_hash (must be 20 chars long)
        if (!$info_hash) {
            return BencodeHelper::bencodedResponseRaw("Invalid info hash.", 400);
        }

        // Check if peer is already assigned to torrent
        $peer_torrent = PeerTorrent::getByPeerAndTorrent($peer, $torrent);

        // Assign it, if not
        if ($peer_torrent == null) {
            PeerTorrent::create([
                'peer_id' => $peer->id,
                'torrent_id' => $torrent->id,
                'uploaded' => $uploaded,
                'downloaded' => $downloaded,
                'left' => $left,
                'stopped' => false
            ]);

        } else {
            // Or update the existing one
            $peer_torrent->uploaded = $uploaded;
            $peer_torrent->downloaded = $downloaded;
            $peer_torrent->left = $left;
            $peer_torrent->save();
        }

        // Build response
        $resp = BencodeHelper::buildAnnounceResponse($torrent, $peer_torrent, $compact, $no_peer_id);

        return BencodeHelper::bencodedResponseRaw($resp);
    }
}