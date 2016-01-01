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
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

class BencodeHelper
{
    const __INTERVAL = 1000;
    const __TIMEOUT = 120;
    const __INTERVAL_MIN = 60;
    const __MAX_PPR = 20;
    const _OK = 1;

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
    public static function  bencodeDictionary($d)
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

    /**
     * Array of wrror codes
     *
     * @param $code
     * @param string $help
     * @return string
     */
    public static function errorCode($code, $help = "")
    {
        $codes = array(
            'wrong_dict_type'   => Lang::get('messages.bencode_wrong_type', ['help' => $help]),
            'missing_info'      => Lang::get('messages.bencode_missing_dictionary_info', ['help' => $help]),
            'invalid_info'      => Lang::get('messages.bencode_invalid_info', ['help' => $help]),
            'invalid_data_type' => Lang::get('messages.bencode_invalid_data_type', ['help' => $help]),
        );

        if(array_key_exists($code, $codes)) {
            return $codes[$code];
        }

        return "";
    }

    /**
     * Decodes bencoded string
     *
     * @param $string
     * @return array|void
     */
    public static function decode($string)
    {
        if (preg_match('/^(\d+):/', $string, $m)) {
            $l = $m[1];
            $pl = strlen($l) + 1;
            $v = substr($string, $pl, $l);
            $ss = substr($string, 0, $pl + $l);
            if (strlen($v) != $l)
                return "";
            return array('type' => "string", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss);
        }
        if (preg_match('/^i(\d+)e/', $string, $m)) {
            $v = $m[1];
            $ss = "i" . $v . "e";
            if ($v === "-0")
                return "";
            if ($v[0] == "0" && strlen($v) != 1)
                return "";
            return array('type' => "integer", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss);
        }
        switch ($string[0]) {
            case "l":
                return self::decodeList($string);
                break;
            case "d":
                return self::decodeDictionary($string);
                break;
            default:
                return "";
        }
    }

    /**
     * Decodes a torrent file
     *
     * @param $file
     * @return array|void
     */
    public static function decodeFile($file)
    {
        return self::decode(file_get_contents($file));
    }

    /**
     * Decodes a list
     *
     * @param $string
     * @return array|string|void
     * @internal param $s
     */
    public static function decodeList($string) {
        if ($string[0] != "l")
            return "";
        $sl = strlen($string);
        $i = 1;
        $v = array();
        $ss = "l";
        for (;;) {
            if ($i >= $sl)
                return "";
            if ($string[$i] == "e")
                break;
            $ret = self::decode(substr($string, $i));
            if (!isset($ret) || !is_array($ret))
                return "";
            $v[] = $ret;
            $i += $ret["strlen"];
            $ss .= $ret["string"];
        }
        $ss .= "e";
        return array('type' => "list", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss);
    }

    /**
     * Decodes bencode dictionary
     *
     * @param $s
     * @return array|string
     */
    public static function decodeDictionary($s) {
        if ($s[0] != "d")
            return "";
        $sl = strlen($s);
        $i = 1;
        $v = array();
        $ss = "d";
        for (;;) {
            if ($i >= $sl)
                return "";
            if ($s[$i] == "e")
                break;
            $ret = self::decode(substr($s, $i));
            if (!isset($ret) || !is_array($ret) || $ret["type"] != "string")
                return "";
            $k = $ret["value"];
            $i += $ret["strlen"];
            $ss .= $ret["string"];
            if ($i >= $sl)
                return "";
            $ret = self::decode(substr($s, $i));
            if (!isset($ret) || !is_array($ret))
                return "";
            $v[$k] = $ret;
            $i += $ret["strlen"];
            $ss .= $ret["string"];
        }
        $ss .= "e";
        return array('type' => "dictionary", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss);
    }

    /**
     * Validates dictionary
     *
     * @param $dictionary
     * @param $string
     * @param string $type
     * @return array|int|string
     */
    public static function checkDictionary($dictionary, $string, $type = "")
    {
        if ($dictionary["type"] != "dictionary") {
            return self::errorCode('wrong_dict_type');
        }
        $a = explode(":", $string);
        $dd = $dictionary["value"];
        $ret = array();
        foreach ($a as $k) {
            unset($t);
            if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
                $k = $m[1];
                $t = $m[2];
            }
            if (!isset($dd[$k])) {
                return self::errorCode('missing_info');
            }
            if (isset($t)) {
                if ($dd[$k]["type"] != $t) {
                    return self::errorCode('invalid_info');
                }
                $ret[] = $dd[$k]["value"];
            } else
                $ret[] = $dd[$k];
        }

        return $ret;
    }

    /**
     * Retrieves a value from the dictionary
     *
     * @param $dictionary
     * @param $key
     * @param $type
     * @return string|void
     */
    public static function getDictionaryValue($dictionary, $key, $type)
    {
        if ($dictionary["type"] != "dictionary") {
            throw new MissingMandatoryParametersException(self::errorCode('missing_data', $type));
        }
        $dd = $dictionary["value"];
        if (!isset($dd[$key]))
            return "";
        $v = $dd[$key];
        if ($v["type"] != $type) {
            throw new InvalidArgumentException(self::errorCode('invalid_data_type', sprintf("%s | %s", $v["type"], $type)));
        }

        return $v["value"];
    }

    public static function getFileListAsString($arr)
    {
        $new = array();
        foreach($arr as $v) {
            $new[] = $v[0] . "{:::}" . $v[1];
        }
        return join(":::",$new);
    }
}