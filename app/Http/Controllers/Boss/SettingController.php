<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use App\Models\Setting;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = Setting::instance();
        return view('boss.settings.edit', compact('settings'));
    }

    public function update(SettingRequest $request)
    {
        $settings = Setting::instance();
        $settings->update($request->validated());

        return redirect()->route('boss.settings.edit')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}
