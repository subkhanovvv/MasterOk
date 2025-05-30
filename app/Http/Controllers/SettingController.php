<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
  public function index(){
        $settings = Setting::all();

     return view('pages.settings.index', compact('settings'));
  }
}
