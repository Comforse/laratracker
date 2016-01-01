<?php
/**
 * DjMike
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @project    scenefz.net
 * @file       BitTorrent.php
 * @created    1/1/2016 11:26 PM
 * @copyright  Copyright (c) 2016 DjMike (accounts.DjMike@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     DjMike
 */

namespace App\Helpers;


class BitTorrent
{
    // Possible announce events
    const EVENT_STARTED     = 'started';
    const EVENT_COMPLETED   = 'completed';
    const EVENT_STOPPED     = 'stopped';
}