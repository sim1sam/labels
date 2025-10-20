<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Merchant;

class ListMerchants extends Command
{
    protected $signature = 'merchants:list';
    protected $description = 'List all merchants with their IDs';

    public function handle()
    {
        $merchants = Merchant::all(['id', 'shop_name', 'email', 'status']);
        
        if ($merchants->count() > 0) {
            $this->table(
                ['ID', 'Shop Name', 'Email', 'Status'],
                $merchants->map(function($m) {
                    return [
                        $m->id,
                        $m->shop_name,
                        $m->email,
                        $m->status
                    ];
                })
            );
        } else {
            $this->warn('No merchants found.');
        }
        
        return 0;
    }
}