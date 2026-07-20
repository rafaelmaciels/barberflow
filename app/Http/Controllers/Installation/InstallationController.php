<?php

namespace App\Http\Controllers\Installation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use App\Models\User;
use PDO;
use Exception;

class InstallationController extends Controller
{
    public function index()
    {
        return view('installation.welcome');
    }

    public function database()
    {
        return view('installation.database');
    }

    public function setupDatabase(Request $request)
    {
        $request->validate([
            'db_host' => 'required',
            'db_port' => 'required|numeric',
            'db_database' => 'required',
            'db_username' => 'required',
        ]);

        try {
            // Test PDO Connection
            $dsn = "mysql:host={$request->db_host};port={$request->db_port};dbname={$request->db_database}";
            new PDO($dsn, $request->db_username, $request->db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

            // Update .env
            $this->updateEnv([
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $request->db_host,
                'DB_PORT' => $request->db_port,
                'DB_DATABASE' => $request->db_database,
                'DB_USERNAME' => $request->db_username,
                'DB_PASSWORD' => $request->db_password,
            ]);

            return redirect()->route('installation.migrations');

        } catch (Exception $e) {
            return back()->with('error', 'Erro ao conectar no banco de dados: ' . $e->getMessage())->withInput();
        }
    }

    public function migrations()
    {
        try {
            // Force DB purge and re-connect to ensure we use the new env vars
            DB::purge();
            
            Artisan::call('migrate:fresh', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
            
            return redirect()->route('installation.smtp');
        } catch (Exception $e) {
            return back()->with('error', 'Erro ao executar migrations: ' . $e->getMessage());
        }
    }

    public function smtp()
    {
        return view('installation.smtp');
    }

    public function setupSmtp(Request $request)
    {
        $request->validate([
            'mail_host' => 'required',
            'mail_port' => 'required',
            'mail_username' => 'required',
            'mail_password' => 'required',
            'mail_from_address' => 'required|email',
        ]);

        $this->updateEnv([
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => $request->mail_host,
            'MAIL_PORT' => $request->mail_port,
            'MAIL_USERNAME' => $request->mail_username,
            'MAIL_PASSWORD' => $request->mail_password,
            'MAIL_ENCRYPTION' => $request->mail_encryption ?? 'tls',
            'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            'MAIL_FROM_NAME' => $request->mail_from_name ?? 'BarberFlow',
        ]);

        return redirect()->route('installation.company');
    }

    public function company()
    {
        return view('installation.company');
    }

    public function setupCompany(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
        ]);

        // Force connection reload in case it's stale
        DB::purge();

        Setting::updateOrCreate(
            ['key' => 'company_name'],
            ['value' => $request->company_name]
        );
        Setting::updateOrCreate(
            ['key' => 'whatsapp'],
            ['value' => $request->whatsapp]
        );

        $this->updateEnv(['APP_NAME' => '"' . $request->company_name . '"']);

        return redirect()->route('installation.admin');
    }

    public function admin()
    {
        return view('installation.admin');
    }

    public function setupAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        DB::purge();

        // Clear users just in case seeders created some dummy ones
        User::truncate();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Mark as installed
        $this->updateEnv(['APP_INSTALLED' => 'true']);

        // Log the user in
        Auth::login($user);

        return redirect()->route('installation.complete');
    }

    public function complete()
    {
        return view('installation.complete');
    }

    private function updateEnv($data)
    {
        $envPath = base_path('.env');
        
        if (!file_exists($envPath)) {
            // Copy example if .env doesn't exist
            copy(base_path('.env.example'), $envPath);
        }

        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            // Check if key exists
            if (preg_match("/^{$key}=/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
