<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parcel Label - {{ $parcel->parcel_id }}</title>
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
            height: 95mm;
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
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 0.5mm;
            text-align: center;
        }
        
        .merchant-id-value {
            font-size: 9px;
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
            width: 4mm;
            height: 4mm;
            background: #000;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1mm;
            font-size: 6px;
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
                overflow: hidden !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
                page-break-inside: avoid !important;
            }
            
            .label-container {
                width: 70mm !important;
                height: 90mm !important;
                padding: 3mm !important;
                margin: 0 !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
                page-break-inside: avoid !important;
                overflow: hidden !important;
            }
        }
    </style>
</head>
<body>
    <div class="label-container">
        <!-- Header with Logo and Merchant Name -->
        <div class="header">
            @if($parcel->merchant->logo)
                <img src="/{{ $parcel->merchant->logo }}" alt="Merchant Logo" class="logo merchant-logo">
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
            <div class="warning-icon">!</div>
            <div>Please make a 360-degree parcel opening video, otherwise no complain will be accepted.</div>
        </div>
    </div>
    
    <script>
        // Auto print when page loads
        window.onload = function() {
            // Force portrait orientation
            document.body.style.transform = 'rotate(0deg)';
            document.querySelector('.label-container').style.transform = 'rotate(0deg)';
            
            // Small delay to ensure CSS is applied
            setTimeout(function() {
                window.print();
            }, 100);
        };
        
        // Close window after printing
        window.onafterprint = function() {
            window.close();
        };
        
        // Additional orientation enforcement
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.transform = 'rotate(0deg)';
            document.querySelector('.label-container').style.transform = 'rotate(0deg)';
        });
    </script>
</body>
</html>

