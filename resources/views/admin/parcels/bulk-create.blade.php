@extends('layouts.admin')

@section('title', 'Bulk Parcel Creation')

@section('content')
<div class="admin-content">
    <div class="admin-header">
        <h1>Bulk Parcel Creation</h1>
        <p>Upload a CSV file to create multiple parcels at once</p>
    </div>

    <div class="admin-body">
        <!-- Instructions Card -->
        <div style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="color: #2d3748; margin-bottom: 15px;">
                <i class="fas fa-info-circle" style="color: #4299e1; margin-right: 8px;"></i>
                How to Use Bulk Upload
            </h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <h4 style="color: #4a5568; margin-bottom: 10px;">Step 1: Download Format</h4>
                    <p style="color: #718096; margin-bottom: 15px;">Download the CSV template with the correct format and sample data.</p>
                    <a href="{{ route('admin.parcels.bulk.format') }}" 
                       style="background: #38a169; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block; font-weight: 600;">
                        <i class="fas fa-download" style="margin-right: 5px;"></i>
                        Download CSV Format
                    </a>
                </div>
                <div>
                    <h4 style="color: #4a5568; margin-bottom: 10px;">Step 2: Fill Data</h4>
                    <p style="color: #718096; margin-bottom: 10px;">Fill in your parcel data following the format:</p>
                    <ul style="color: #718096; margin: 0; padding-left: 20px;">
                        <li>merchant_name (required)</li>
                        <li>customer_name (required)</li>
                        <li>mobile_number (required)</li>
                        <li>delivery_address (required)</li>
                        <li>cod_amount (required)</li>
                        <li>courier_name (optional)</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Upload Form -->
        <div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="color: #2d3748; margin-bottom: 20px;">
                <i class="fas fa-upload" style="color: #ed8936; margin-right: 8px;"></i>
                Upload CSV File
            </h3>
            
            <form action="{{ route('admin.parcels.bulk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                        Select CSV File
                    </label>
                    <input type="file" 
                           name="csv_file" 
                           accept=".csv,.txt"
                           required
                           style="width: 100%; padding: 12px; border: 2px dashed #cbd5e0; border-radius: 6px; background: #f7fafc; cursor: pointer;"
                           onchange="showFileName(this)">
                    <p style="color: #718096; font-size: 14px; margin-top: 5px;">
                        Supported formats: CSV, TXT (Max size: 2MB)
                    </p>
                    @error('csv_file')
                        <p style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="display: flex; gap: 15px; align-items: center;">
                    <button type="submit" 
                            style="background: #4299e1; color: white; padding: 12px 24px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center;">
                        <i class="fas fa-upload" style="margin-right: 8px;"></i>
                        Upload & Create Parcels
                    </button>
                    
                    <a href="{{ route('admin.parcels.index') }}" 
                       style="background: #e2e8f0; color: #4a5568; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-arrow-left" style="margin-right: 8px;"></i>
                        Back to Parcels
                    </a>
                </div>
            </form>
        </div>

        <!-- Reference Data -->
        <div style="background: white; border-radius: 8px; padding: 20px; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="color: #2d3748; margin-bottom: 15px;">
                <i class="fas fa-database" style="color: #9f7aea; margin-right: 8px;"></i>
                Reference Data
            </h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <!-- Merchants -->
                <div>
                    <h4 style="color: #4a5568; margin-bottom: 10px;">Available Merchants</h4>
                    <div style="max-height: 200px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 4px; padding: 10px;">
                        @foreach($merchants as $merchant)
                            <div style="padding: 5px 0; border-bottom: 1px solid #f1f5f9;">
                                <strong>{{ $merchant->shop_name }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Couriers -->
                <div>
                    <h4 style="color: #4a5568; margin-bottom: 10px;">Available Couriers</h4>
                    <div style="max-height: 200px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 4px; padding: 10px;">
                        @foreach($couriers as $courier)
                            <div style="padding: 5px 0; border-bottom: 1px solid #f1f5f9;">
                                <strong>{{ $courier->courier_name }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showFileName(input) {
    const fileName = input.files[0]?.name;
    if (fileName) {
        const label = input.nextElementSibling;
        label.textContent = `Selected: ${fileName}`;
        label.style.color = '#38a169';
    }
}
</script>
@endsection
