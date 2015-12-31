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
 * @file       index.blade.php
 * @created    12/27/2015 9:05 PM
 * @copyright  Copyright (c) 2015 Comforse (comforse.github@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Comforse
 */
?>
@extends('layouts.adminlte.master')

@section('title')
    Torrents
@stop

@section('page_title')
    Torrents
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">

                </div>
                <div class="box-body">
                    <table class="table table-bordered table-responsive table-condensed table-hover table-torrents">
                        <thead>
                            <tr>
                                <th>{{ Lang::get('messages.torrent_category')  }}</th>
                                <th>{{ Lang::get('messages.torrent_name')  }}</th>
                                <th class="text-center"><img id="icon_download" title="{{ Lang::get('messages.torrent_download')  }}"   alt="{{ Lang::get('messages.torrent_download')  }}" src="/images/categories/blank.gif" /></th>
                                <th class="text-center"><img id="icon_comments" title="{{ Lang::get('messages.torrent_comments')  }}"   alt="{{ Lang::get('messages.torrent_comments')  }}" src="/images/categories/blank.gif" /></th>
                                <th class="text-center"><img id="icon_size"     title="{{ Lang::get('messages.torrent_size')  }}"       alt="{{ Lang::get('messages.torrent_size')  }}"     src="/images/categories/blank.gif" /></th>
                                <th class="text-center"><img id="icon_seeders"  title="{{ Lang::get('messages.torrent_seeders')  }}"    alt="{{ Lang::get('messages.torrent_seeders')  }}"  src="/images/categories/blank.gif" /></th>
                                <th class="text-center"><img id="icon_leechers" title="{{ Lang::get('messages.torrent_leechers')  }}"   alt="{{ Lang::get('messages.torrent_leechers')  }}" src="/images/categories/blank.gif" /></th>
                            </tr>
                        </thead>
                        @foreach ($torrents as $torrent)
                            <tr>
                                <td class="category_col">
                                    <div class="category">
                                        <a href="#">
                                            <img src="/images/categories/blank.gif" class="t_category" />
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="torrent_name">
                                        <a href="{{ URL::to('/torrents', ['id' => $torrent->string_id]) }}">{{ $torrent->name }}</a>
                                    </div>
                                    <span>{{ $torrent->created_at }}</span>
                                </td>
                                <td class="small text-center"><a href="{{ URL::to('/torrents/download', ['id' => $torrent->string_id]) }}" title="{{ trans("messages.torrent_download_t") }}"><i class="fa fa-download"></i></a>
                                </td>
                                <td class="smallx2 text-center">{{ $torrent->comments }}</td>
                                <td class="medium text-center">{{ \App\Helpers\StringHelper::formatBytes($torrent->size, 2) }}</td>
                                <td class="smallx2 text-center">{{ $torrent->seeders }}</td>
                                <td class="smallx2 text-center">{{ $torrent->leechers }}</td>
                            </tr>
                        @endforeach
                    </table>

                    <div class="box-footer clearfix">
                        {!! $torrents->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop