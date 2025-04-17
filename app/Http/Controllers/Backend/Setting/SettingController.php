<?php

namespace App\Http\Controllers\Backend\Setting;

use App\Http\Controllers\Controller;
use App\Models\Backend\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();

        return view('backend.setting.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::first();

        $setting->update($request->all());

        return redirect()->back()->with('success', 'Setting updated successfully');
    }

    public function logoChange(Request $request)
    {
        $setting = Setting::first();

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $file_name = time() . '.' . $file->getClientOriginalExtension();
            $file_path = $file->storeAs('uploads/logo', $file_name, 'public');

            $setting->update([
            'logo' => $file_name
            ]);
        }

        return redirect()->back()->with('success', 'Logo updated successfully');
    }

    public function nameChange(Request $request)
    {
        $setting = Setting::first();

        $setting->update([
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'Name updated successfully');
    }

}
