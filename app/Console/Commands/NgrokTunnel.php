<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NgrokTunnel extends Command
{
    protected $signature   = 'ngrok:tunnel {--port=80 : Local port to tunnel}';
    protected $description = 'Start ngrok tunnel and auto-update MPESA_CALLBACK_URL in .env';

    public function handle(): int
    {
        $port    = $this->option('port');
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            $this->error('.env file not found at ' . $envPath);
            return 1;
        }

        $this->info("Starting ngrok tunnel on port {$port}...");
        $this->line('  Make sure ngrok is installed: https://ngrok.com/download');
        $this->line('  Or: choco install ngrok / brew install ngrok');
        $this->newLine();

        // Kill any existing ngrok process
        if (PHP_OS_FAMILY === 'Windows') {
            exec('taskkill /F /IM ngrok.exe 2>NUL', $out, $code);
        } else {
            exec('pkill -f ngrok 2>/dev/null', $out, $code);
        }
        sleep(1);

        // Start ngrok in the background
        if (PHP_OS_FAMILY === 'Windows') {
            popen("start /B ngrok http {$port} > NUL 2>&1", 'r');
        } else {
            exec("nohup ngrok http {$port} > /dev/null 2>&1 &");
        }

        $this->line('Waiting for ngrok to start...');
        sleep(3);

        // Poll ngrok local API for the public URL
        $ngrokUrl = null;
        for ($i = 0; $i < 10; $i++) {
            $json = @file_get_contents('http://127.0.0.1:4040/api/tunnels');
            if ($json) {
                $data = json_decode($json, true);
                foreach ($data['tunnels'] ?? [] as $tunnel) {
                    if (str_starts_with($tunnel['public_url'] ?? '', 'https://')) {
                        $ngrokUrl = $tunnel['public_url'];
                        break 2;
                    }
                }
            }
            sleep(1);
        }

        if (!$ngrokUrl) {
            $this->error('Could not get ngrok public URL. Is ngrok installed and authenticated?');
            $this->line('  Run: ngrok config add-authtoken YOUR_TOKEN');
            return 1;
        }

        $callbackUrl = rtrim($ngrokUrl, '/') . '/api/callback';

        $this->info("Ngrok URL:    {$ngrokUrl}");
        $this->info("Callback URL: {$callbackUrl}");
        $this->newLine();

        // Update .env
        $env = file_get_contents($envPath);
        if (str_contains($env, 'MPESA_CALLBACK_URL=')) {
            $env = preg_replace('/^MPESA_CALLBACK_URL=.*/m', 'MPESA_CALLBACK_URL=' . $callbackUrl, $env);
        } else {
            $env .= "\nMPESA_CALLBACK_URL={$callbackUrl}\n";
        }
        file_put_contents($envPath, $env);

        // Clear config cache so the new URL is picked up immediately
        $this->call('config:clear');

        $this->info('MPESA_CALLBACK_URL updated in .env');
        $this->newLine();
        $this->line('<options=bold>Ngrok dashboard:</> http://127.0.0.1:4040');
        $this->line('<options=bold>Callback URL   :</> ' . $callbackUrl);
        $this->newLine();
        $this->comment('Keep this terminal open. M-Pesa callbacks will now reach your Laravel app.');

        return 0;
    }
}
