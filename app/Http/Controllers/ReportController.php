<?php

namespace App\Http\Controllers;

use App\Models\Parcel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    // Printed Parcels Report
    public function printedParcels(Request $request)
    {
        $query = Parcel::with(['merchant', 'courier'])
                      ->whereNotNull('printed_at');

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('printed_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('printed_at', '<=', $request->end_date);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Merchant filter
        if ($request->filled('merchant_id')) {
            $query->where('merchant_id', $request->merchant_id);
        }

        $parcels = $query->orderBy('printed_at', 'desc')->paginate(20);

        // Get merchants for filter dropdown
        $merchants = \App\Models\Merchant::orderBy('shop_name')->get();

        // Summary statistics
        $totalPrinted = Parcel::whereNotNull('printed_at')->count();
        $todayPrinted = Parcel::whereDate('printed_at', today())->count();
        $thisWeekPrinted = Parcel::whereBetween('printed_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $thisMonthPrinted = Parcel::whereMonth('printed_at', now()->month)
                                 ->whereYear('printed_at', now()->year)
                                 ->count();

        return view('admin.reports.printed-parcels', compact(
            'parcels', 
            'merchants', 
            'totalPrinted', 
            'todayPrinted', 
            'thisWeekPrinted', 
            'thisMonthPrinted'
        ));
    }

    // Download Printed Parcels CSV
    public function downloadPrintedParcels(Request $request)
    {
        $query = Parcel::with(['merchant', 'courier'])
                      ->whereNotNull('printed_at');

        // Apply same filters as the report
        if ($request->filled('start_date')) {
            $query->whereDate('printed_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('printed_at', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('merchant_id')) {
            $query->where('merchant_id', $request->merchant_id);
        }

        $parcels = $query->orderBy('printed_at', 'desc')->get();

        $filename = 'printed_parcels_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($parcels) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'Parcel ID',
                'Customer Name',
                'Mobile Number',
                'Delivery Address',
                'Merchant Name',
                'Courier Name',
                'COD Amount (' . \App\Models\Setting::getCurrency() . ')',
                'Status',
                'Created Date',
                'Printed Date',
                'Printed Time'
            ]);

            // CSV Data
            foreach ($parcels as $parcel) {
                fputcsv($file, [
                    $parcel->parcel_id,
                    $parcel->customer_name,
                    $parcel->mobile_number,
                    $parcel->delivery_address,
                    $parcel->merchant->shop_name ?? 'N/A',
                    $parcel->courier->courier_name ?? 'N/A',
                    number_format($parcel->cod_amount, 0),
                    ucfirst(str_replace('_', ' ', $parcel->status)),
                    $parcel->created_at->format('Y-m-d'),
                    $parcel->printed_at->format('Y-m-d'),
                    $parcel->printed_at->format('H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Merchant Printed Parcels Report
    public function merchantPrintedParcels(Request $request)
    {
        $merchant = auth()->user()->merchant;
        
        if (!$merchant) {
            return redirect()->route('login')->with('error', 'Merchant account not found.');
        }

        $query = Parcel::with(['courier'])
                      ->where('merchant_id', $merchant->id)
                      ->whereNotNull('printed_at');

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('printed_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('printed_at', '<=', $request->end_date);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $parcels = $query->orderBy('printed_at', 'desc')->paginate(20);

        // Summary statistics
        $totalPrinted = Parcel::where('merchant_id', $merchant->id)->whereNotNull('printed_at')->count();
        $todayPrinted = Parcel::where('merchant_id', $merchant->id)->whereDate('printed_at', today())->count();
        $thisWeekPrinted = Parcel::where('merchant_id', $merchant->id)->whereBetween('printed_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $thisMonthPrinted = Parcel::where('merchant_id', $merchant->id)->whereMonth('printed_at', now()->month)
                                 ->whereYear('printed_at', now()->year)
                                 ->count();

        return view('merchant.reports.printed-parcels', compact(
            'parcels', 
            'totalPrinted', 
            'todayPrinted', 
            'thisWeekPrinted', 
            'thisMonthPrinted'
        ));
    }

    // Download Merchant Printed Parcels CSV
    public function downloadMerchantPrintedParcels(Request $request)
    {
        $merchant = auth()->user()->merchant;
        
        if (!$merchant) {
            return redirect()->route('login')->with('error', 'Merchant account not found.');
        }

        $query = Parcel::with(['courier'])
                      ->where('merchant_id', $merchant->id)
                      ->whereNotNull('printed_at');

        // Apply same filters as the report
        if ($request->filled('start_date')) {
            $query->whereDate('printed_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('printed_at', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $parcels = $query->orderBy('printed_at', 'desc')->get();

        $filename = 'my_printed_parcels_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($parcels) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'Parcel ID',
                'Customer Name',
                'Mobile Number',
                'Delivery Address',
                'Courier Name',
                'COD Amount (' . \App\Models\Setting::getCurrency() . ')',
                'Status',
                'Created Date',
                'Printed Date',
                'Printed Time'
            ]);

            // CSV Data
            foreach ($parcels as $parcel) {
                fputcsv($file, [
                    $parcel->parcel_id,
                    $parcel->customer_name,
                    $parcel->mobile_number,
                    $parcel->delivery_address,
                    $parcel->courier->courier_name ?? 'N/A',
                    number_format($parcel->cod_amount, 0),
                    ucfirst(str_replace('_', ' ', $parcel->status)),
                    $parcel->created_at->format('Y-m-d'),
                    $parcel->printed_at->format('Y-m-d'),
                    $parcel->printed_at->format('H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}