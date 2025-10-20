<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SteadfastApiService;

class TestSteadfastApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steadfast:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Steadfast API connection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Steadfast API connection...');
        
        $service = new SteadfastApiService(
            'w21i6x8sjwygmg6rz2on4omniflrd5rb',
            'g84orvx9hiywjtm7wy3w4h5e'
        );
        
        $result = $service->testConnection();
        
        if ($result['success']) {
            $this->info('✅ API Connection Successful!');
            $this->line('Current Balance: ' . ($result['data']['current_balance'] ?? 'N/A'));
            $this->line('Response Time: ' . ($result['data']['response_time'] ?? 'N/A') . 's');
        } else {
            $this->error('❌ API Connection Failed!');
            $this->line('Error: ' . $result['message']);
        }
        
        return $result['success'] ? 0 : 1;
    }
}