<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', 'logo']);

        // Atualizar textos e dados comerciais
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Lidar com o upload da Logo
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('settings', 'public');
            $url = Storage::disk('public')->url($path);
            Setting::updateOrCreate(['key' => 'company_logo'], ['value' => $url]);
        }

        return redirect()->route('settings.index')->with('success', 'Configurações atualizadas com sucesso!');
    }
}
