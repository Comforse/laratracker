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
 * @file       view.blade.php
 * @created    12/30/2015 10:59 PM
 * @copyright  Copyright (c) 2015 Comforse (comforse.github@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Comforse
 */
?>
@extends('layouts.adminlte.master')

@section('title')
    {{ $torrent->name }}
@stop

@section('page_title')
    {{ $torrent->name }}
@stop

@section('content')
    <div class="row">
        <div class="col-md-3">
            <a href="{{ URL::to('/torrents/download', ['id' => $torrent->string_id]) }}" class="btn btn-primary btn-block margin-bottom">{{ trans("messages.torrent_download_t") }}</a>
            @if ($hasPicture)
            <div class="box box-solid">
                <div class="box-header with-border" style="text-align: center;">
                    <img src="{{ $picture }}" width="150" height="300" />
                </div>
            </div>
            @endif
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans("messages.torrent_info_section") }}</h3>

                    <div class="box-tools">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="javascript:void(0);"><i class="fa fa-hdd-o "></i>
                                {{ trans("messages.torrent_size") }}
                                <span class="pull-right">
                                    {{ \App\Helpers\StringHelper::formatBytes($torrent->size, 2) }}
                                </span>
                            </a>
                        </li>
                        <li><a href="javascript:void(0);"><i class="fa fa-upload"></i>
                                {{ trans("messages.torrent_seeders") }}
                                <span class="pull-right">
                                    {{ $torrent->seeders }}
                                </span>
                            </a>
                        </li>
                        <li><a href="javascript:void(0);"><i class="fa fa-download"></i>
                                {{ trans("messages.torrent_leechers") }}
                                <span class="pull-right">
                                    {{ $torrent->leechers }}
                                </span>
                            </a>
                        </li>
                        <li><a href="javascript:void(0);"><i class="fa fa-list"></i>
                            {{ trans("messages.torrent_category") }}
                                <span class="pull-right">
                                    {{ $torrent->category->name  }}
                                </span>
                            </a>
                        </li>
                        <li><a href="javascript:void(0);"><i class="fa fa-clock-o"></i>
                            {{ trans("messages.torrent_uploaded") }}
                                <span class="pull-right">
                                    {{ date('F d, Y', strtotime($torrent->created_at)) }}
                                </span>
                            </a>
                        </li>
                        <li><a href="javascript:void(0);"><i class="fa fa-pencil-square-o"></i>
                            {{ trans("messages.torrent_last_modified") }}
                                <span class="pull-right">
                                    {{ date('F d, Y', strtotime($torrent->updated_at)) }}
                                </span>
                            </a>
                        </li>
                        <li><a href="javascript:void(0);"><i class="fa fa-user"></i>
                            {{ trans("messages.torrent_uploaded_by") }}
                                <span class="pull-right">
                                    {{ $torrent->user->username }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans("messages.torrent_description") }}</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    {{ $torrent->description }}
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /. box -->
        </div>
        <!-- /.col -->
    </div>
@stop