<?php

namespace App\Http\Controllers;

use App\Models\Setting;

class SettingController extends Controller
{
  public function index()
  {
    $settings = Setting::find(1);

    return view('pages.settings.index', compact('settings'));
  }
  public function update()
  {
    $settings = Setting::find(1);
    $settings->update(request()->all());

    return redirect()->back()->with('success', 'Настройки успешно обновлены');
  }
}
