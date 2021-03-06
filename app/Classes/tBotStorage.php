<?php


namespace App\Classes;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait tBotStorage
{

    public function clearStorage(){
        Cache::forget($this->telegram_user->id);
    }

    public function addToStorage($key, $value)
    {
        $tmp = json_decode(Cache::get($this->telegram_user->id, "[]"), true);

        $items = array_filter($tmp, function ($item) use ($key) {
            return isset($item[$key]);
        });

        if (count($items)==0) {
            array_push($tmp, ["$key" => $value]);
        } else
            $items[0][$key] = $value;


        Cache::forget($this->telegram_user->id);
        Cache::add($this->telegram_user->id, json_encode($tmp));

    }

    public function hasInStorage($key)
    {
        $tmp = json_decode(Cache::get($this->telegram_user->id, "[]"), true);

        return array_key_exists("$key", $tmp);
    }

    public function getFromStorage($key, $default)
    {
        $tmp = json_decode(Cache::get($this->telegram_user->id, "[]"), true);

        $items = array_filter($tmp, function ($item) use ($key) {
            return isset($item[$key]);
        });

        return count($items)>0? $tmp[0][$key] : $default;
    }
}
