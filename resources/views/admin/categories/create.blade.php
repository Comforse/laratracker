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
 * @file       create.blade.php
 * @created    6/12/2016 5:04 AM
 * @copyright  Copyright (c) 2016 Comforse (comforse.github@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Comforse
 */
?>

@extends('layouts.adminlte.master')

@section('title')
    {{ trans('messages.admin_categories_create_title') }}
@stop

@section('page_title')
    {{ trans('messages.admin_categories_create_title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <!-- general form elements -->
            <div class="box box-primary">
                <!-- /.box-header -->
                <!-- form start -->
                {!! Form::open(['action' => 'Admin\CategoriesController@create', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                <div class="box-body">
                    <div class="form-group">
                        {!! Form::label('category_name', Lang::get('messages.admin_categories_create_name')) !!}
                        {!! Form::text('category_name', '', ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('category_description', Lang::get('messages.admin_categories_create_description')) !!}
                        {!! Form::textarea('category_description', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('category_css', Lang::get('messages.admin_categories_create_css')) !!}
                        {!! Form::text('category_css', '', ['class' => 'form-control']) !!}
                    </div>
                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">{{ trans('Submit') }}</button>
                </div>
                {!! Form::close() !!}
            </div>
            <!-- /.box -->
        </div>
    </div>
@stop
