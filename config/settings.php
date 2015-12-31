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
 * @project    LaraTracker
 * @file       settings.php
 * @created    12/30/2015 9:18 PM
 * @copyright  Copyright (c) 2015 Comforse (comforse.github@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Comforse
 */

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

define("DR", $_SERVER['DOCUMENT_ROOT']);
define("DS", DIRECTORY_SEPARATOR);

return [
    'global_announce_urls'      => [
        'http://beta.LaraTracker/announce'
    ],
    'site_name'                 => 'LaraTracker',
    'torrents_upload_dir'       => DR.DS."files".DS."torrents".DS,
    'torrents_image_upload_dir' => DR.DS."files".DS."torrents_pictures".DS,
    'torrents_nfos_upload_dir'  => DR.DS."files".DS."torrents_nfos".DS,
    'torrent_public_img_path'   => "/files/torrents_pictures/%s",
    'tmp_dir'                   => DR.DS."tmp".DS,
];