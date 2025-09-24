<?php

namespace App\Http\Controllers\OnlineStore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContentManagementController extends Controller
{
    public function index()
    {
        return view('online_store.content_management.index');
    }

    public function media()
    {
        return view('online_store.content_management.media');
    }

    public function media_edit()
    {
        return view('online_store.content_management.media_edit');
    }

    public function page_management()
    {
        return view('online_store.content_management.page_management');
    }

    public function page_management_create()
    {
        return view('online_store.content_management.page_management_create');
    }
    
}
