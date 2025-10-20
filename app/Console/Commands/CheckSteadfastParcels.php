<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Parcel;
use App\Models\Courier;

class CheckSteadfastParcels extends Command
{
    protected $signature = 'steadfast:check-parcels';
    protected $description = 'Check parcels with Steadfast tracking';

    public function handle()
    {
        $this->info('Checking recent parcels with Steadfast tracking...');
        
        $parcels = Parcel::whereNotNull('courier_tracking_number')
            ->with('courier')
            ->latest()
            ->take(10)
            ->get();
        
        if ($parcels->count() > 0) {
            $this->table(
                ['Parcel ID', 'Courier', 'Tracking Code', 'Status', 'Created'],
                $parcels->map(function($p) {
                    return [
                        $p->parcel_id,
                        $p->courier ? $p->courier->courier_name : 'None',
                        $p->courier_tracking_number,
                        $p->status,
                        $p->created_at->format('Y-m-d H:i')
                    ];
                })
            );
            
            $this->info('✅ Found ' . $parcels->count() . ' parcels with Steadfast tracking!');
        } else {
            $this->warn('⚠️  No parcels found with Steadfast tracking numbers.');
        }
        
        // Check total parcels
        $totalParcels = Parcel::count();
        $this->line('Total parcels in system: ' . $totalParcels);
        
        return 0;
    }
}