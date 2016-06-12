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
 * @file       index.blade.php
 * @created    6/12/2016 4:29 AM
 * @copyright  Copyright (c) 2016 Comforse (comforse.github@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Comforse
 */
?>
@extends('layouts.adminlte.master')

@section('title')
    {{ trans('messages.admin_index_title') }}
@stop

@section('page_title')
    {{ trans('messages.admin_index_title') }}
@stop

@section('content')

    <!-- Categories -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"> {{ trans('messages.admin_index_categories_title') }}</h3>

            <div class="box-tools pull-right">
                <button title="Collapse" data-toggle="tooltip" data-widget="collapse" class="btn btn-box-tool" type="button">
                    <i class="fa fa-minus"></i></button>
                <button title="Remove" data-toggle="tooltip" data-widget="remove" class="btn btn-box-tool" type="button">
                    <i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body">
            <a class="btn btn-app">
                <i class="fa fa-plus"></i> {{ trans('messages.admin_index_add_category') }}
            </a>
            <a class="btn btn-app">
                <i class="fa fa-search"></i> {{ trans('messages.admin_index_view_categories') }}
            </a>
        </div>
        <!-- /.box-body -->
    </div>
@stop
