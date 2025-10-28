<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Parcel Creation - Steadfast API Debug</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }
        .status-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin: 15px 0;
        }
        .success { border-left: 4px solid #28a745; }
        .error { border-left: 4px solid #dc3545; }
        .warning { border-left: 4px solid #ffc107; }
        .info { border-left: 4px solid #17a2b8; }
        
        .btn {
            background: #007bff;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            font-size: 16px;
        }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }
        .btn-warning { background: #ffc107; color: #000; }
        
        .json-output {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .test-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .results-section {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Test Parcel Creation with Steadfast API</h1>
            <p>Create a test parcel and check Steadfast API integration</p>
        </div>

        <div class="status-card info">
            <h3>📋 Test Information</h3>
            <p>This tool will create a test parcel and attempt to create a Steadfast order. It will show you exactly what data is being sent and what response is received.</p>
        </div>

        <div class="test-form">
            <h3>🔧 Test Configuration</h3>
            <div class="form-group">
                <label>Customer Name:</label>
                <input type="text" id="customer_name" value="Test Customer" />
            </div>
            <div class="form-group">
                <label>Mobile Number:</label>
                <input type="text" id="mobile_number" value="01700000000" />
            </div>
            <div class="form-group">
                <label>Delivery Address:</label>
                <input type="text" id="delivery_address" value="Test Address, Dhaka 1205" />
            </div>
            <div class="form-group">
                <label>COD Amount:</label>
                <input type="number" id="cod_amount" value="100" />
            </div>
        </div>

        <div style="text-align: center; margin: 20px 0;">
            <button class="btn btn-success" onclick="runTest()">🚀 Run Test Parcel Creation</button>
            <button class="btn btn-warning" onclick="checkConfiguration()">🔍 Check Configuration</button>
            <button class="btn btn-danger" onclick="clearResults()">🗑️ Clear Results</button>
        </div>

        <div id="results" class="results-section">
            @if(isset($action) && isset($results))
                @if($action === 'config')
                    @if(isset($results['error']))
                        <div class="status-card error">
                            <h3>❌ Configuration Error</h3>
                            <div class="json-output">{{ $results['error'] }}</div>
                        </div>
                    @else
                        <div class="status-card success">
                            <h3>✅ Steadfast Configuration</h3>
                            <div class="json-output">{{ json_encode($results, JSON_PRETTY_PRINT) }}</div>
                        </div>
                    @endif
                @elseif($action === 'run')
                    @if(isset($results['error']))
                        <div class="status-card error">
                            <h3>❌ Test Failed</h3>
                            <div class="json-output">{{ $results['error'] }}</div>
                        </div>
                    @else
                        <div class="status-card success">
                            <h3>✅ Test Parcel Created</h3>
                            <div class="json-output">{{ json_encode($results['test_parcel'], JSON_PRETTY_PRINT) }}</div>
                        </div>
                        <div class="status-card {{ $results['api_result']['success'] ? 'success' : 'error' }}">
                            <h3>🔗 Steadfast API Response</h3>
                            <div class="json-output">{{ json_encode($results['api_result'], JSON_PRETTY_PRINT) }}</div>
                        </div>
                    @endif
                @endif
            @endif
        </div>

        <div class="status-card info">
            <h3>📚 What This Test Does</h3>
            <ul>
                <li><strong>Creates a test parcel</strong> with the specified data</li>
                <li><strong>Attempts Steadfast API call</strong> to create order</li>
                <li><strong>Shows detailed response</strong> from Steadfast API</li>
                <li><strong>Identifies missing data</strong> or configuration issues</li>
                <li><strong>Displays error messages</strong> if API call fails</li>
            </ul>
        </div>

        <div class="status-card info">
            <h3>🔗 Useful Links</h3>
            <p>
                <a href="/admin/couriers" class="btn">Manage Couriers</a>
                <a href="/admin/parcels" class="btn">Manage Parcels</a>
                <a href="/debug/steadfast-page" class="btn">Steadfast Debug</a>
            </p>
        </div>
    </div>

    <script>
        function showLoading(message) {
            document.getElementById('results').innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                    <p>${message}</p>
                </div>
            `;
        }

        function showResult(title, content, type = 'info') {
            const resultsDiv = document.getElementById('results');
            const cardClass = type === 'success' ? 'success' : type === 'error' ? 'error' : type === 'warning' ? 'warning' : 'info';
            
            resultsDiv.innerHTML += `
                <div class="status-card ${cardClass}">
                    <h3>${title}</h3>
                    <div class="json-output">${content}</div>
                </div>
            `;
        }

        function clearResults() {
            document.getElementById('results').innerHTML = '';
        }

        function runTest() {
            showLoading('Creating test parcel and calling Steadfast API...');
            
            // Redirect to test execution page
            window.location.href = '/test-parcel?action=run';
        }

        function checkConfiguration() {
            showLoading('Checking Steadfast configuration...');
            
            // Redirect to test execution page
            window.location.href = '/test-parcel?action=config';
        }

        // No auto-loading needed - results are handled server-side
    </script>
</body>
</html>
