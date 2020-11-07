<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GoogleDriveController extends Controller
{
    public function handleProviderGoogleCallback(Request $request)
    {
        dump($request->all());
    }

    public function evernoteLogin(Request $request){
        dump($request->all());
    }
}
