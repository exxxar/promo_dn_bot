<?php


namespace App\Classes;


use Illuminate\Support\Facades\Cache;

trait tBotStorage
{

    public function addToStorage($key, $value)
    {
        $tmp = json_decode(Cache::get($this->telegram_user->id, []), true);
        if (!array_key_exists("$key",$tmp)) {
            array_push($tmp, ["$key" => $value]);
        } else {
            $tmp[$key] = $value;
        }
        if (!Cache::has($this->telegram_user->id))
            Cache::add($this->telegram_user->id, json_decode($tmp));
        else {
            Cache::forget($this->telegram_user->id);
            Cache::add($this->telegram_user->id, json_decode($tmp));
        }
    }

    public function hasInStorage($key)
    {
        $tmp = json_decode(Cache::get($this->telegram_user->id, "[]"), true);

        return array_key_exists("$key",$tmp);
    }

    public function getFromStorage($key, $default)
    {
        $tmp = json_decode(Cache::get($this->telegram_user->id, "[]"), true);

        return array_key_exists("$key",$tmp) ? $tmp[$key] : $default;
    }
}
