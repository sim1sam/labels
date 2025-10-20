@extends('layouts.merchant')

@section('title', 'Bulk Upload Parcels')

@section('content')
<div class="container">
    <div class="header">
        <h1><i class="fas fa-upload"></i> Bulk Upload Parcels</h1>
        <p>Upload multiple parcels at once using a CSV file</p>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-info-circle"></i> Instructions</h3>
        </div>
        <div class="card-body">
            <div class="instructions">
                <h4>📋 CSV Format Requirements:</h4>
                <ul>
                    <li><strong>File Format:</strong> CSV (.csv) or Text (.txt)</li>
                    <li><strong>Maximum Size:</strong> 2MB</li>
                    <li><strong>Columns Required:</strong> customer_name, mobile_number, delivery_address, cod_amount, courier_name</li>
                    <li><strong>Courier Name:</strong> Must be one of your assigned couriers (optional)</li>
                </ul>

                <h4>📥 Download Sample Format:</h4>
                <a href="{{ route('merchant.parcels.download-format') }}" class="btn btn-primary">
                    <i class="fas fa-download"></i> Download CSV Format
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-upload"></i> Upload CSV File</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('merchant.parcels.bulk-store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label for="csv_file" style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                        Select CSV File <span style="color: #e53e3e;">*</span>
                    </label>
                    <input type="file" 
                           id="csv_file"
                           name="csv_file" 
                           accept=".csv,.txt"
                           required
                           style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                    @error('csv_file')
                        <p style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload & Create Parcels
                    </button>
                    <a href="{{ route('merchant.parcels.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Parcels
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($couriers->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-motorcycle"></i> Your Assigned Couriers</h3>
        </div>
        <div class="card-body">
            <div class="couriers-list">
                <p style="color: #718096; margin-bottom: 15px;">Use these courier names in your CSV file:</p>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                    @foreach($couriers as $courier)
                    <div style="background: #f7fafc; padding: 10px; border-radius: 6px; border-left: 4px solid #4299e1;">
                        <div style="font-weight: 600; color: #2d3748;">{{ $courier->courier_name }}</div>
                        <div style="font-size: 12px; color: #718096;">ID: {{ $courier->merchant_custom_id ?? 'N/A' }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-lightbulb"></i> Tips</h3>
        </div>
        <div class="card-body">
            <div class="tips">
                <ul>
                    <li><strong>Customer Creation:</strong> Customers will be automatically created/updated in the database</li>
                    <li><strong>Duplicate Prevention:</strong> Customers are identified by mobile number to prevent duplicates</li>
                    <li><strong>Courier Assignment:</strong> Only couriers assigned to your account can be used</li>
                    <li><strong>Error Handling:</strong> Invalid rows will be skipped with detailed error messages</li>
                    <li><strong>Status:</strong> All parcels will be created with "pending" status</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
}

.header {
    text-align: center;
    margin-bottom: 30px;
}

.header h1 {
    color: #2d3748;
    margin-bottom: 10px;
    font-size: 2.5rem;
}

.header p {
    color: #718096;
    font-size: 1.1rem;
}

.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 25px;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
}

.card-header h3 {
    margin: 0;
    font-size: 1.3rem;
}

.card-body {
    padding: 25px;
}

.instructions h4 {
    color: #2d3748;
    margin-bottom: 15px;
    font-size: 1.1rem;
}

.instructions ul {
    margin-bottom: 20px;
    padding-left: 20px;
}

.instructions li {
    margin-bottom: 8px;
    color: #4a5568;
}

.form-group {
    margin-bottom: 25px;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 25px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #e2e8f0;
    color: #4a5568;
}

.btn-secondary:hover {
    background: #cbd5e0;
    transform: translateY(-2px);
}

.couriers-list {
    margin-top: 15px;
}

.tips ul {
    margin: 0;
    padding-left: 20px;
}

.tips li {
    margin-bottom: 10px;
    color: #4a5568;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .container {
        padding: 15px;
    }
    
    .header h1 {
        font-size: 2rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        justify-content: center;
    }
}
</style>
@endsection

