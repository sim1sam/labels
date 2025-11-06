<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Labels - {{ count($parcels) }} Parcels</title>
    <style>
        @page {
            size: 70mm 90mm;
            margin: 0;
            orientation: portrait;
        }
        
        @media print {
            @page {
                size: 70mm 90mm;
                margin: 0;
                orientation: portrait;
            }
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: "Trebuchet MS", Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            width: 70mm;
            height: 90mm;
            background: white;
            overflow: hidden;
        }
        
        .label-container {
            width: 70mm;
            height: 90mm;
            padding: 3mm;
            box-sizing: border-box;
            position: relative;
            transform: rotate(0deg);
        }
        
        .header {
            text-align: center;
            margin-bottom: 2mm;
        }
        
        .logo {
            width: 3mm;
            height: 3mm;
            margin: 0 auto 1mm;
            display: block;
        }
        
        .merchant-logo {
            width: 30%;
            height: 30%;
            object-fit: contain;
        }
        
        .default-logo {
            width: 30%;
            height: 30%;
            background: #f0f0f0;
            border: 1px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #666;
        }
        
        .merchant-name {
            font-weight: bold;
            font-size: 8px;
            margin-bottom: 1mm;
        }
        
        .divider {
            border-top: 1px solid #000;
            margin: 2mm 0;
        }
        
        .content-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2mm;
            align-items: flex-start;
        }
        
        .shipped-by {
            flex: 1;
            margin-right: 2mm;
        }
        
        .merchant-id-box {
            width: 25mm;
            height: 14mm;
            border: 2px solid #000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            padding: 1mm;
            box-sizing: border-box;
        }
        
        .merchant-id-label {
            font-size: 6px;
            font-weight: bold;
            margin-bottom: 0.5mm;
            text-align: center;
        }
        
        .merchant-id-value {
            font-size: 6px;
            font-weight: bold;
            text-align: center;
            word-break: break-all;
            line-height: 1;
            max-width: 100%;
            overflow: hidden;
        }
        
        .section-title {
            font-weight: bold;
            margin-bottom: 1mm;
        }
        
        .customer-info {
            margin-bottom: 2mm;
        }
        
        .customer-info .section-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 1mm;
        }
        
        .customer-info div:not(.section-title) {
            font-weight: normal;
            font-size: 11px;
        }
        
        .customer-name {
            font-weight: bold !important;
        }
        
        .customer-mobile {
            font-weight: bold !important;
        }
        
        .bill-amount-box {
            border: 2px solid #000;
            padding: 2mm;
            text-align: center;
            margin: 2mm 0;
        }
        
        .bill-amount {
            font-weight: bold;
            font-size: 12px;
        }
        
        .disclaimer {
            display: flex;
            align-items: center;
            font-size: 11px;
            margin-top: 2mm;
        }
        
        .warning-icon {
            width: 5mm;
            height: 4.5mm;
            margin-right: 2mm;
            flex-shrink: 0;
            display: inline-block;
        }
        
        .warning-icon svg {
            width: 100%;
            height: 100%;
            display: block;
        }
        
        @media print {
            @page {
                size: 70mm 90mm;
                margin: 0;
                orientation: portrait;
            }
            
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                width: 70mm !important;
                height: 90mm !important;
                overflow: visible !important;
            }
            
            .label-container {
                width: 70mm !important;
                height: 90mm !important;
                padding: 3mm !important;
                margin: 0 !important;
                page-break-after: always !important;
                page-break-before: auto !important;
                page-break-inside: avoid !important;
                overflow: hidden !important;
                display: block !important;
                position: relative !important;
            }
            
            .label-container:last-child {
                page-break-after: avoid !important;
            }
            
            .print-controls {
                display: none !important;
            }
        }

        /* Print controls - hidden when printing */
        .print-controls {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        @media print {
            .print-controls {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="print-controls">
        <h3 style="margin: 0 0 10px 0; color: #2d3748;">Bulk Labels Ready</h3>
        <p style="margin: 0 0 15px 0; color: #4a5568;">{{ count($parcels) }} labels found for {{ $startDate }} to {{ $endDate }}</p>
        <button onclick="printAllLabels()" style="background: #ed8936; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; margin-right: 10px;">
            <i class="fas fa-print"></i> Print All Labels
        </button>
        <button onclick="window.close()" style="background: #e2e8f0; color: #4a5568; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
            <i class="fas fa-times"></i> Close
        </button>
    </div>

    @foreach($parcels as $parcel)
        @php
            // Get the courier's merchant custom ID if assigned
            $courierMerchantId = null;
            if ($parcel->courier) {
                $pivot = $parcel->merchant->couriers()->where('courier_id', $parcel->courier->id)->first();
                if ($pivot) {
                    $courierMerchantId = $pivot->pivot->merchant_custom_id;
                }
            }
        @endphp

        <!-- Each label on separate page -->
        <div class="label-container" style="page-break-after: always;">
            <!-- Header with Logo and Merchant Name -->
            <div class="header">
                @if($parcel->merchant->logo_url)
                    <img src="{{ $parcel->merchant->logo_url }}" alt="Merchant Logo" class="logo merchant-logo">
                @else
                    <div class="logo default-logo">
                        LOGO
                    </div>
                @endif
                <div class="merchant-name"></div>
            </div>
            
            <div class="divider"></div>
            
            <!-- Shipped By and Merchant ID -->
            <div class="content-row">
                <div class="shipped-by">
                    <div class="section-title">Shipped By:</div>
                    <div>{{ $parcel->merchant->shop_name }}</div>
                    <div>Mobile# {{ $parcel->merchant->phone ?? 'N/A' }}</div>
                </div>
                <div class="merchant-id-box">
                    <div class="merchant-id-label">Merchant ID#</div>
                    <div class="merchant-id-value">{{ $courierMerchantId ?? $parcel->merchant->merchant_id }}</div>
                </div>
            </div>
            
            <div class="divider"></div>
            
            <!-- Shipped To -->
            <div class="customer-info">
                <div class="section-title">Shipped To:</div>
                <div class="customer-name">{{ $parcel->customer_name }}</div>
                <div>{{ $parcel->delivery_address }}</div>
                <div class="customer-mobile">Mobile# {{ $parcel->mobile_number }}</div>
            </div>
            
            <div class="divider"></div>
            
            <!-- Bill Amount -->
            <div class="bill-amount-box">
                <div class="bill-amount">Bill Amount: {{ number_format($parcel->cod_amount, 0) }} {{ \App\Models\Setting::getCurrency() }}</div>
            </div>
            
            <!-- Disclaimer -->
            <div class="disclaimer">
                <div class="warning-icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 22h20L12 2z" fill="none" stroke="#000" stroke-width="2" stroke-linejoin="round"/>
                        <text x="12" y="15" font-size="16" font-weight="bold" fill="#000" text-anchor="middle" dominant-baseline="middle" font-family="Arial, sans-serif">!</text>
                    </svg>
                </div>
                <div>Please make a 360-degree parcel opening video, otherwise no complain will be accepted.</div>
            </div>
        </div>
    @endforeach
    
    <script>
        function printAllLabels() {
            // Force print all pages
            window.print();
        }
        
        // Ensure all labels are visible before printing
        window.onbeforeprint = function() {
            // Make sure all labels are visible
            const labels = document.querySelectorAll('.label-container');
            labels.forEach(label => {
                label.style.display = 'block';
                label.style.pageBreakAfter = 'always';
            });
        };
        
        // Close window after printing
        window.onafterprint = function() {
            // Don't auto-close for bulk printing, let user decide
        };
        
        // Ensure proper page breaks
        document.addEventListener('DOMContentLoaded', function() {
            const labels = document.querySelectorAll('.label-container');
            labels.forEach((label, index) => {
                if (index < labels.length - 1) {
                    label.style.pageBreakAfter = 'always';
                } else {
                    label.style.pageBreakAfter = 'avoid';
                }
            });
        });
    </script>
</body>
</html>
