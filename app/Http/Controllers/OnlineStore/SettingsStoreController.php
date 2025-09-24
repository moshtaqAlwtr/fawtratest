<?php

namespace App\Http\Controllers\OnlineStore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsStoreController extends Controller
{
    public function index()
    {
        return view('online-store.settings.index');
    }

    public function template()
    {
        return view('online-store.settings.template');
    }

    public function preview()
    {
        return view('online-store.settings.preview');
    }

}
