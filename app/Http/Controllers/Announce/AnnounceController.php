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
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class AnnounceController extends Controller
{
    const __INTERVAL = 1000;
    const __TIMEOUT = 120;
    const __INTERVAL_MIN = 60;
    const __MAX_PPR = 20;

    public function announce(Request $request)
    {
        Log::info($request->fullUrl());
        $status = 200;
        $content = "";
        $passkey = Input::get('passkey');
        $peer_id = Input::get('peer_id');
        $port = Input::get('port');
        $info_hash = Input::get('info_hash');
        $downloaded = Input::get('uploaded') ? intval(Input::get('uploaded')) : 0;
        $uploaded = Input::get('uploaded') ? intval(Input::get('uploaded')) : 0;
        $left = Input::get('left') ? intval(Input::get('left')) : 0;
        $compact = Input::get('compact') ? intval(Input::get('compact')) : 0;
        $no_peer_id = Input::get('no_peer_id') ? intval(Input::get('no_peer_id')) : 0;

        $ipAddress = '';
        // Check for X-Forwarded-For headers and use those if found
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ('' !== trim($_SERVER['HTTP_X_FORWARDED_FOR']))) {
            $ipAddress = (trim($_SERVER['HTTP_X_FORWARDED_FOR']));
        } else {
            if (isset($_SERVER['REMOTE_ADDR']) && ('' !== trim($_SERVER['REMOTE_ADDR']))) {
                $ipAddress = (trim($_SERVER['REMOTE_ADDR']));
            }
        }

        $port = $_SERVER['REMOTE_PORT'];
        /*if(!$port || !ctype_digit($port) || intval($port) < 1 || intval($port) > 65535)
        {
            $content = BencodeHelper::track("Invalid client port.");
            $status = 401;
            return (new Response(AnnounceController::track($content), $status))
                ->header('Content-Type', $value);
        }

        if ($port == 999 && substr($peer_id, 0, 10) == '-TO0001-XX') {
            die("d8:completei0e10:incompletei0e8:intervali600e12:min intervali60e5:peersld2:ip12:72.14.194.184:port3:999ed2:ip11:72.14.194.14:port3:999ed2:ip12:72.14.194.654:port3:999eee");
        }*/


        if (!$passkey) {
            $content = BencodeHelper::track("Missing passkey.");
            $status = 401;
            return (new Response(AnnounceController::track($content), $status))
                ->header('Content-Type', $value);
        }


        $torrent = Torrent::getByInfoHash(sha1($info_hash));
        if (!$torrent || $torrent == null) {
            $content = "Torrent not registered with this tracker.";
            $status = 404;
            return (new Response(AnnounceController::track($content), $status))
                ->header('Content-Type', $value);
        }

        $user = User::has('passkeys', '=', $passkey)->get();

        if ($user == null) {
            $content = BencodeHelper::track("Invalid passkey.");
            $status = 401;
            return (new Response(AnnounceController::track($content), $status))
                ->header('Content-Type', $value);
        }

        $peer = Peer::getByHashAndPasskey(bin2hex($peer_id), $passkey);

        if ($peer == null) {
            $peer = Peer::create([
                'hash' => bin2hex($peer_id),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'ip_address' => $ipAddress,
                'passkey' => $passkey,
                'port' => $port
            ]);
        }

        if (!$info_hash || strlen($info_hash) != 20) {
            $content = BencodeHelper::track("Invalid info_hash.");
            $status = 401;
            return (new Response(AnnounceController::track($content), $status))
                ->header('Content-Type', $value);
        }


        $peer_torrent = PeerTorrent::getByPeerAndTorrent($peer, $torrent);

        if ($peer_torrent == null) {
            $peer_torrent = PeerTorrent::create([
                'peer_id' => $peer->id,
                'torrent_id' => $torrent->id,
                'uploaded' => $uploaded,
                'downloaded' => $downloaded,
                'left' => $left,
                'stopped' => false
            ]);

        } else {
            $peer_torrent->uploaded = $uploaded;
            $peer_torrent->downloaded = $downloaded;
            $peer_torrent->left = $left;
            $peer_torrent->save();
        }

        $seeders = $torrent->getSeedersCount();
        $leechers = $torrent->getLeechersCount();
        $resp = "";
        if ($compact != 1) {
            $resp = "d" . $this->benc_str("interval") . "i" . AnnounceController::__INTERVAL . "e" . $this->benc_str("peers") . "l";
        } else {
            $resp = "d" . $this->benc_str("interval") . "i" . AnnounceController::__INTERVAL . "e" . $this->benc_str("min interval") . "i" . 300 . "e5:" . "peers";
        }

        $peer = array();

        $peer_num = 0;
        foreach ($torrent->getPeersArray() as $row) {
            if ($compact != 1) {
                if ($row["peer_id"] === $peer->hash) {
                    continue;
                }

                $resp .= "d" . $this->benc_str("ip") . $this->benc_str($row['ip']);

                if ($no_peer_id == 0) {
                    $resp .= $this->benc_str("peer id") . $this->benc_str($row["peer_id"]);
                }

                $resp .= $this->benc_str("port") . "i" . $row["port"] . "e" . "e";

            } else {
                $peer_ip = explode('.', $row["ip"]);
                $peer_ip = pack("C*", $peer_ip[0], $peer_ip[1], $peer_ip[2], $peer_ip[3]);
                $peer_port = pack("n*", (int)$row["port"]);
                $time = intval((time() % 7680) / 60);

                if ($left == 0) {
                    $time += 128;
                }

                $time = pack("C", $time);
                $peer[] = $time . $peer_ip . $peer_port;
                $peer_num++;
            }
        }

        if ($compact != 1) {
            $resp .= "ee";
        } else {
            $o = "";
            for ($i = 0; $i < $peer_num; $i++) {
                $o .= substr($peer[$i], 1, 6);
            }
            $resp .= strlen($o) . ':' . $o . 'e';
        }

        $this->benc_resp_raw($resp);
    }

    public function benc_resp($d)
    {
        return $this->benc_resp_raw($this->benc(array('type' => 'dictionary', 'value' => $d)));
    }

    public function benc_resp_raw($x)
    {
        header("Content-Type: text/plain");
        header("Pragma: no-cache");

        if ($_SERVER['HTTP_ACCEPT_ENCODING'] == 'gzip') {
            header("Content-Encoding: gzip");
            echo gzencode($x, 9, FORCE_GZIP);
        } else {
            echo $x;
        }
    }

    function benc($obj)
    {
        if (!is_array($obj) || !isset($obj["type"]) || !isset($obj["value"]))
            return;
        $c = $obj["value"];
        switch ($obj["type"]) {
            case "string":
                return $this->benc_str($c);
            case "integer":
                return $this->benc_int($c);
            case "list":
                return $this->benc_list($c);
            case "dictionary":
                return $this->benc_dict($c);
            default:
                return;
        }
    }

    public function benc_str($s)
    {
        return strlen($s) . ":$s";
    }

    public function benc_int($i)
    {
        return "i" . $i . "e";
    }

    public function benc_list($a)
    {
        $s = "l";
        foreach ($a as $e) {
            $s .= $this->benc($e);
        }
        $s .= "e";
        return $s;
    }

    public function benc_dict($d)
    {
        $s = "d";
        $keys = array_keys($d);
        sort($keys);
        foreach ($keys as $k) {
            $v = $d[$k];
            $s .= $this->benc_str($k);
            $s .= $this->benc($v);
        }
        $s .= "e";
        return $s;
    }


    public function hex2bin($hex)
    {
        $r = '';
        for ($i = 0; $i < strlen($hex); $i += 2) {
            $r .= chr(hexdec($hex{$i} . $hex{($i + 1)}));
        }
        return $r;
    }
}