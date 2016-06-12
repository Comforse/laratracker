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
 * @created    6/12/2016 4:32 AM
 * @copyright  Copyright (c) 2016 Comforse (comforse.github@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Comforse
 */
?>

@extends('layouts.adminlte.master')

@section('title')
    {{ trans('messages.admin_categories_index_title') }}
@stop

@section('page_title')
    {{ trans('messages.admin_categories_index_title') }}
@stop

@section('content')
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('messages.admin_categories_index_header') }}</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>{{ trans('messages.admin_categories_index_column_name') }}</th>
                    <th>{{ trans('messages.admin_categories_index_column_icon') }}</th>
                    <th style="width: 40px">{{ trans('messages.table_column_tools') }}</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->css_class }}</td>
                        <td>x</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- /.box-body -->
        <div class="box-footer clearfix">
            <ul class="pagination pagination-sm no-margin pull-right">
                <li><a href="#">«</a></li>
                <li><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">»</a></li>
            </ul>
        </div>
    </div>
@stop
