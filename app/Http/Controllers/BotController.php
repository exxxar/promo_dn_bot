<?php

namespace App\Http\Controllers;

use BotMan\BotMan\Facades\BotMan;
use Illuminate\Http\Request;

class BotController extends Controller
{

    public function test($bot){
        $bot->reply("test");
    }
}
