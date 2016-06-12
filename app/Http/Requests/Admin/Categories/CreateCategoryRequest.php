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
 * @project    PhpStorm
 * @file       CreateCategoryRequest.php
 * @created    6/12/2016 5:18 AM
 * @copyright  Copyright (c) 2016 Comforse (comforse.github@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Comforse
 */

namespace App\Http\Requests\Admin\Categories;


use App\Http\Requests\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CreateCategoryRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check() && User::find(Auth::id())->can('admin.categories.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category_name'             => 'required|unique:category,name|max:80|regex:([a-zA-Z0-9\.\-]+)',
            'category_description'      => 'required|min:10|regex:([a-zA-Z0-9\.\-]+)',
            'category_css'              => 'required|min:1|regex:([a-zA-Z0-9\-]+)',
        ];
    }
}