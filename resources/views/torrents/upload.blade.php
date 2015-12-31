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
 * @file       upload.blade.php
 * @created    12/27/2015 10:50 PM
 * @copyright  Copyright (c) 2015 Comforse (comforse.github@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Comforse
 */
?>
@extends('layouts.adminlte.master')

@section('title')
    {{ Lang::get("messages.torrent_upload_form_title") }}
@stop

@section('page_title')
    {{ Lang::get("messages.torrent_upload_form_title") }}
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <!-- general form elements -->
            <div class="box box-primary">
                <!-- /.box-header -->
                <!-- form start -->
                {!! Form::open(['action' => 'Torrents\TorrentsController@uploadPost', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                    <div class="box-body">
                        <div class="form-group">
                            {!! Form::label('torrent_file', Lang::get('messages.torrent_upload_field_filename')) !!}
                            {!! Form::file('torrent_file', ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('torrent_nfo', Lang::get('messages.torrent_nfo_file')) !!}
                            {!! Form::file('torrent_nfo', ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('torrent_picture', Lang::get('messages.torrent_picture')) !!}
                            {!! Form::file('torrent_picture', ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('torrent_name', Lang::get('messages.torrent_upload_field_name')) !!}
                            {!! Form::text('torrent_name', '', ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('torrent_category', Lang::get('messages.torrent_category')) !!}
                            {!! Form::select('torrent_category', $categories, null, ['class' => 'form-control select2']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('torrent_description', Lang::get('messages.torrent_description')) !!}
                            {!! Form::textarea('torrent_description', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                {!! Form::close() !!}
            </div>
            <!-- /.box -->
        </div>
    </div>
@stop