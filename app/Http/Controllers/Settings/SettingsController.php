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
        
        $env = [
            'MAIL_HOST' => env('MAIL_HOST', ''),
            'MAIL_PORT' => env('MAIL_PORT', ''),
            'MAIL_USERNAME' => env('MAIL_USERNAME', ''),
            'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION', 'tls'),
            'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS', ''),
            'MAIL_FROM_NAME' => env('MAIL_FROM_NAME', ''),
        ];

        return view('settings.index', compact('settings', 'env'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', 'logo', 'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name']);

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

        // Lidar com configurações de SMTP
        $envUpdates = [];
        if ($request->filled('mail_host')) $envUpdates['MAIL_HOST'] = $request->mail_host;
        if ($request->filled('mail_port')) $envUpdates['MAIL_PORT'] = $request->mail_port;
        if ($request->filled('mail_username')) $envUpdates['MAIL_USERNAME'] = $request->mail_username;
        if ($request->filled('mail_password')) $envUpdates['MAIL_PASSWORD'] = $request->mail_password;
        if ($request->filled('mail_encryption')) $envUpdates['MAIL_ENCRYPTION'] = $request->mail_encryption;
        if ($request->filled('mail_from_address')) $envUpdates['MAIL_FROM_ADDRESS'] = $request->mail_from_address;
        if ($request->filled('mail_from_name')) $envUpdates['MAIL_FROM_NAME'] = '"' . $request->mail_from_name . '"';

        if (!empty($envUpdates)) {
            $this->updateEnv($envUpdates);
            \Illuminate\Support\Facades\Artisan::call('config:cache');
        }

        return redirect()->route('settings.index')->with('success', 'Configurações atualizadas com sucesso!');
    }

    private function updateEnv($data)
    {
        $envPath = base_path('.env');
        
        if (!file_exists($envPath)) {
            copy(base_path('.env.example'), $envPath);
        }

        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            if (preg_match("/^{$key}=/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
