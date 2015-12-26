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


class BencodeHelper
{
    public static function track($list, $c=0, $i=0) {
        if (is_string($list)) { //Did we get a string? Return an error to the client
            return 'd14:failure reason'.strlen($list).':'.$list.'e';
        }
        $p = ''; //Peer directory
        foreach($list as $d) { //Runs for each client
            $pid = '';
            if (!isset($_GET['no_peer_id'])) { //Send out peer_ids in the reply
                $real_id = hex2bin($d[2]);
                $pid = '7:peer id'.strlen($real_id).':'.$real_id;
            }
            $p .= 'd2:ip'.strlen($d[0]).':'.$d[0].$pid.'4:porti'.$d[1].'ee';
        }
        //Add some other paramters in the dictionary and merge with peer list
        $r = 'd8:intervali'.__INTERVAL.'e12:min intervali'.__INTERVAL_MIN.'e8:completei'.$c.'e10:incompletei'.$i.'e5:peersl'.$p.'ee';
        return $r;
    }
}