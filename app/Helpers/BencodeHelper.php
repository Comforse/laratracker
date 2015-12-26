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
 * @file       BencodeHelper.php
 * @created    12/19/2015 11:57 PM
 * @copyright  Copyright (c) 2015 Comforse (comforse.github@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Comforse
 */

namespace App\Helpers;

use App\Models\PeerTorrent;
use App\Models\Torrent;
use Illuminate\Http\Response;

class BencodeHelper
{
    const __INTERVAL = 1000;
    const __TIMEOUT = 120;
    const __INTERVAL_MIN = 60;
    const __MAX_PPR = 20;

    /**
     * Shortcut for bencoded response
     *
     * @param $dictionary
     * @return BencodeHelper
     * @internal param $d
     */
    public static function bencodedResponse($dictionary)
    {
        return self::bencodedResponseRaw(self::bencode(array('type' => 'dictionary', 'value' => $dictionary)));
    }

    /**
     * Returns the bencoded response to the client
     *
     * @param $dictionary
     * @param int $status
     * @return $this
     */
    public static function bencodedResponseRaw($dictionary, $status = 200)
    {
        $response = (new Response($dictionary, $status))
            ->header('Content-Type', 'text/plain')
            ->header("Pragma", "no-cache");

        if ($_SERVER['HTTP_ACCEPT_ENCODING'] == 'gzip') {
            $response->header("Content-Encoding", "gzip");
            $response->setContent(gzencode($dictionary, 9, FORCE_GZIP));
        }

        return $response;
    }

    /**
     * Converts object to bencode format
     *
     * @param $obj
     * @return string|void
     */
    public static function bencode($obj)
    {
        if (!is_array($obj) || !isset($obj["type"]) || !isset($obj["value"]))
            return "";
        $c = $obj["value"];
        switch ($obj["type"]) {
            case "string":
                return self::bencodeString($c);
                break;
            case "integer":
                return self::bencodeInt($c);
                break;
            case "list":
                return self::bencodeList($c);
                break;
            case "dictionary":
                return self::bencodeDictionary($c);
                break;
            default:
                return "";
        }
    }

    /**
     * Converts a string into its' bencode dictionary value
     *
     * @param $s
     * @return string
     */
    public static function bencodeString($s)
    {
        return strlen($s) . ":$s";
    }

    /**
     * Converts integer into its' bencode dictionary value
     *
     * @param $i
     * @return string
     */
    public static function bencodeInt($i)
    {
        return "i" . $i . "e";
    }

    /**
     * Converts an array into its' bencode dictionary value
     *
     * @param $a
     * @return string
     */
    public static function bencodeList($a)
    {
        $s = "l";
        foreach ($a as $e) {
            $s .= self::bencode($e);
        }
        $s .= "e";
        return $s;
    }

    /**
     * Converts a dictionary into bencode
     *
     * @param $d
     * @return string
     */
    public static function bencodeDictionary($d)
    {
        $s = "d";
        $keys = array_keys($d);
        sort($keys);
        foreach ($keys as $k) {
            $v = $d[$k];
            $s .= self::bencodeString($k);
            $s .= self::bencode($v);
        }
        $s .= "e";
        return $s;
    }

    /**
     * Converts hexadecimal string to binary
     *
     * @param $hex
     * @return string
     */
    public static function hex2bin($hex)
    {
        $r = '';
        for ($i = 0; $i < strlen($hex); $i += 2) {
            $r .= chr(hexdec($hex{$i} . $hex{($i + 1)}));
        }
        return $r;
    }

    /**
     * Builds tracker announce response in bencoded format
     *
     * @param Torrent $torrent
     * @param PeerTorrent $peerTorrent
     * @param $compact
     * @param int $no_peer_id
     * @return string
     */
    public static function buildAnnounceResponse(Torrent $torrent, PeerTorrent $peerTorrent, $compact, $no_peer_id = 0)
    {
        // Compact mode
        if ($compact != 1) {
            $resp = "d" . self::bencodeString("interval") . "i" . self::__INTERVAL . "e" . self::bencodeString("peers") . "l";
        } else {
            $resp = "d" . self::bencodeString("interval") . "i" . self::__INTERVAL . "e" . self::bencodeString("min interval") . "i" . 300 . "e5:" . "peers";
        }

        $peer = array();

        $peer_num = 0;
        foreach ($torrent->getPeersArray() as $row) {
            if ($compact != 1) {
                if ($row["peer_id"] === $peerTorrent->peerInstance()->hash) {
                    continue;
                }

                $resp .= "d" . self::bencodeString("ip") . self::bencodeString($row['ip']);

                if ($no_peer_id == 0) {
                    $resp .= self::bencodeString("peer id") . self::bencodeString($row["peer_id"]);
                }

                $resp .= self::bencodeString("port") . "i" . $row["port"] . "e" . "e";

            } else {
                $peer_ip = explode('.', $row["ip"]);
                $peer_ip = pack("C*", $peer_ip[0], $peer_ip[1], $peer_ip[2], $peer_ip[3]);
                $peer_port = pack("n*", (int)$row["port"]);
                $time = intval((time() % 7680) / 60);

                if ($peerTorrent->left == 0) {
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

        return $resp;
    }
}