<?php

namespace App\Providers;

use App\Helpers\BencodeHelper;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('torrentFile', function($attribute, $value, $parameters, $validator) {
            $dictionary = BencodeHelper::decodeFile($value);
            list($announce, $info) = BencodeHelper::checkDictionary($dictionary, "announce(string):info", "global dictionary");
            list($dname, $plen, $pieces) = BencodeHelper::checkDictionary($info, "name(string):piece length(integer):pieces(string)", "Info dictionary");
            $total_length = BencodeHelper::getDictionaryValue($info, "length", "integer");
            $dicttionary_is_valid = BencodeHelper::checkDictionary($dictionary, "announce(string):info", "global dictionary") == BencodeHelper::_OK;
            return $dicttionary_is_valid && (int)$total_length > 0 && (int)$plen > 0 && $pieces !== "" && $announce !== "";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() == 'local') {
            $this->app->register('Laracasts\Generators\GeneratorsServiceProvider');
        }
    }
}
