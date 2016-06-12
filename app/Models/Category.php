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
 * @file       Category.php
 * @created    12/18/2015 7:39 PM
 * @copyright  Copyright (c) 2015 Comforse (comforse.github@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Comforse
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    protected $table = 'category';

    protected $fillable = [
        'name',
        'description',
        'css_class'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    protected function torrents()
    {
        return $this->hasMany('App\Models\Torrent');
    }

    /**
     * Returns an array('id' => 'name')
     *
     * @return mixed
     */
    public static function getAllKeyValueAsArray()
    {
        return DB::table('category')->select('id', 'name')->orderBy('name', 'ASC')->lists('name','id');
    }
}