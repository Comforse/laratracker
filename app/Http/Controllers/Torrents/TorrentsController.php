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
 * @file       TorrentsController.php
 * @created    12/27/2015 8:56 PM
 * @copyright  Copyright (c) 2015 Comforse (comforse.github@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Comforse
 */

namespace App\Http\Controllers\Torrents;

use App\Helpers\BencodeHelper;
use App\Http\Requests\TorrentUploadRequest;
use App\Models\Category;
use App\Models\Torrent;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TorrentsController extends Controller
{
    /**
     * Renders the list of torrents
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $torrents = DB::table('torrent')->orderBy('created_at', 'DESC')->paginate(50);
        return view('torrents.index', ['torrents' => $torrents]);
    }

    /**
     * Renders and handles the torrent upload form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @internal param TorrentUploadRequest|Request $request
     */
    public function upload()
    {
        $categories = Category::getAllKeyValueAsArray();

        return view('torrents.upload', ['categories' => $categories]);
    }

    public function uploadPost(TorrentUploadRequest $request)
    {
        if ($request->isMethod('POST')) {
            // Global config file
            $config = config('settings');

            // Get files from request
            $torrent_file   = $request->file('torrent_file');
            $picture_file   = $request->file('torrent_picture');
            $nfo_file       = $request->file('torrent_nfo');

            // Torrent file name
            $filename = strip_tags($torrent_file->getClientOriginalName());

            // Torrent name
            $name = strip_tags($request->get('torrent_name'));

            // Torrent description
            $description = strip_tags($request->get('torrent_description'));

            // Torrent category
            $category = $request->get('torrent_category');

            // Set the torrent name to be as the file name, if the name is empty
            if ($name == "" || strlen($name) < 10) {
                // Remove the extension
                $name = str_replace(".torrent", "", $filename);

                // Replace dots with spaces
                $name = str_replace(".", " ", $name);
            }

            // Load dictionary from the torrent file
            $dictionary = BencodeHelper::decodeFile($torrent_file);

            // Retrieve the announce and the info values from the dictionary
            list($announce, $info) = BencodeHelper::checkDictionary($dictionary, "announce(string):info");

            // Torrent name from the file, number of files and the files list
            list($dictionary_torrent_name, $pieces_length, $pieces) = BencodeHelper::checkDictionary($info, "name(string):piece length(integer):pieces(string)");

            // Init files list array
            $file_list = array();

            // Get torrent size from dictionary
            $total_length = BencodeHelper::getDictionaryValue($info, "length", "integer");

            // Default torrent type (i.e. the torrent has one file)
            $type = "single";
            if (isset($total_length)) {
                $file_list[] = array($dictionary_torrent_name, $total_length);
            } else {
                // Torrent has multiple files
                $f_list = BencodeHelper::getDictionaryValue($info, "files", "list");
                $total_length = 0;
                foreach ($f_list as $fn) {
                    list($ll, $ff) = BencodeHelper::checkDictionary($fn, "length(integer):path(list)");
                    $total_length += $ll;
                    $ffa = array();
                    foreach ($ff as $ffe) {
                        $ffa[] = $ffe["value"];
                    }
                    $ffe = implode("/", $ffa);
                    $file_list[] = array($ffe, $ll);
                }
                $type = "multi";
            }

            // Join files list into a string
            $files_list = BencodeHelper::getFileListAsString($file_list);

            // Hash
            $info_hash = strtoupper(sha1($info["string"]));

            // Torrent file hash
            $hash = md5($torrent_file);

            // Save poster column
            $poster = "";

            // Save picture
            if($picture_file !== null) {
                $extension = $picture_file->getClientOriginalExtension();
                $poster = sprintf("%s.%s", $hash, $extension);
                $picture_file->move($config['torrents_image_upload_dir'], $poster);
            }

            // Create the torrent in DB
            $torrent = Torrent::create([
                'name' => $name,
                'description' => $description,
                'filename' => $filename,
                'category_id' => $category,
                'info_hash' => $info_hash,
                'hash' => $hash,
                'size' => $total_length,
                'picture' => $poster,
                'files_list' => $files_list,
                'user_id' => Auth::id()
            ]);

            // Get global announce urls
            $announce_url = $config['global_announce_urls'];

            // Set them to the dictionary
            $dictionary["value"]["announce"] = array("type" => "list", "value" => $announce_url);

            // Add custom comment
            $dictionary["value"]["comment"] = array("type" => "string", "value" => sprintf("Torrent downloaded from %s", $config['site_name']));

            // Set it as private
            $dictionary["value"]["info"]["value"]["private"] = array("type" => "integer", "value" => "1");

            // Save the torrent file
            $path_to_new_file = sprintf("%s%s.torrent", $config['torrents_upload_dir'], $torrent->hash);
            $contents = BencodeHelper::bencode($dictionary);
            $new_file = File::put($path_to_new_file, $contents);

            // Save nfo
            $nfo_file->move($config['torrents_nfos_upload_dir'], sprintf("%s.nfo", $torrent->hash));

            return Redirect::to('/torrents');
        }
    }

    public function view($id)
    {
        $config = config('settings');
        $torrent = Torrent::getByStringID($id);

        if(!$torrent) {
            throw new NotFoundHttpException("Torrent not found");
        }

        $picture_path = sprintf("%s%s", $config['torrents_image_upload_dir'], $torrent->picture);
        $has_picture = (file_exists($picture_path));
        $picture = "";
        if($has_picture) {
            $picture = sprintf($config['torrent_public_img_path'], $torrent->picture);
        }
        return view('torrents.view', ['torrent' => $torrent, 'hasPicture' => $has_picture, 'picture' => $picture]);
    }

    public function download($id)
    {
        $config = config('settings');

        $torrent = Torrent::getByStringID($id);
        $dictionary = $torrent->getDictionary();

        $current_user = Auth::user();

        // Get global announce urls
        $announce_url = $config['global_announce_urls'];

        $announce_url_with_passkey = array();
        foreach($announce_url AS $url) {
            $announce_url_with_passkey[] = sprintf("%s?passkey=%s", $url, $current_user->passkeys->passkey);
        }

        Log::info($dictionary);
        // Set them to the dictionary
        $dictionary["value"]["announce"] = array("type" => "string", "value" => $announce_url_with_passkey[0]);

        $encoded = BencodeHelper::bencode($dictionary);
        Log::info($dictionary);
        $temp_file_path = $config['tmp_dir'].$current_user->passkeys->passkey."_".$torrent->hash.".torrent";
        $user_file_name = $torrent->filename;
        $tmp_file = File::put($temp_file_path, $encoded);

        return Response::download($temp_file_path, $user_file_name, ['content-type' => 'application/x-bittorrent']);
    }
}