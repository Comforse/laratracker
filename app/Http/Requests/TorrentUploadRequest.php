<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TorrentUploadRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check() && User::find(Auth::id())->can('upload.torrents');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'torrent_file'          => 'required|max:5120|mimes:torrent',
            'torrent_name'          => 'unique:torrent,name|max:80|regex:([a-zA-Z0-9\.\-]+)',
            'torrent_nfo'           => 'required|mimes:txt,nfo,application/octet-stream|max:5120',
            'torrent_description'   => 'required|min:20',
            'torrent_category'      => 'required|exists:categories,id',
            'torrent_picture'       => 'mimes:jpg,jpeg,gif,png,bmp|max:5120',
        ];
    }
}
