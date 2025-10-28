<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Steadfast API Debug - Labels Management</title>
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
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }
        
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Steadfast API Debug Tool</h1>
            <p>Diagnose Steadfast API connection and configuration issues</p>
        </div>

        <div class="status-card info">
            <h3>📋 Instructions</h3>
            <p>This tool will help you diagnose Steadfast API issues on your live server. Click the buttons below to check different aspects of your configuration.</p>
        </div>

        <div style="text-align: center; margin: 20px 0;">
            <button class="btn" onclick="checkSteadfastConfig()">🔧 Check Steadfast Configuration</button>
            <button class="btn btn-success" onclick="testApiConnection()">🔗 Test API Connection</button>
            <button class="btn btn-danger" onclick="checkEnvironment()">⚙️ Check Environment</button>
            <button class="btn" onclick="clearResults()">🗑️ Clear Results</button>
        </div>

        <div id="results"></div>

        <div class="status-card info">
            <h3>📚 Quick Fixes</h3>
            <ul>
                <li><strong>If Steadfast Courier not found:</strong> Go to Admin Panel → Couriers → Create Steadfast Courier</li>
                <li><strong>If API credentials missing:</strong> Edit the Steadfast courier and add API Key & Secret</li>
                <li><strong>If environment variables wrong:</strong> Check your .env file has correct STEADFAST_* settings</li>
                <li><strong>If API connection fails:</strong> Check if your server can reach portal.packzy.com</li>
            </ul>
        </div>

        <div class="status-card info">
            <h3>🔗 Useful Links</h3>
            <p>
                <a href="/admin/couriers" class="btn">Manage Couriers</a>
                <a href="/admin/parcels" class="btn">Manage Parcels</a>
                <a href="/admin/settings" class="btn">Settings</a>
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

        async function checkSteadfastConfig() {
            showLoading('Checking Steadfast configuration...');
            
            try {
                const response = await fetch('/debug/steadfast');
                const data = await response.json();
                
                if (data.error) {
                    showResult('❌ Steadfast Configuration Error', JSON.stringify(data, null, 2), 'error');
                } else {
                    showResult('✅ Steadfast Configuration', JSON.stringify(data, null, 2), 'success');
                }
            } catch (error) {
                showResult('❌ Network Error', `Failed to fetch data: ${error.message}`, 'error');
            }
        }

        async function testApiConnection() {
            showLoading('Testing API connection...');
            
            try {
                const response = await fetch('/api/test-connection/7'); // Assuming Steadfast courier ID is 7
                const data = await response.json();
                
                if (data.success) {
                    showResult('✅ API Connection Test', JSON.stringify(data, null, 2), 'success');
                } else {
                    showResult('❌ API Connection Failed', JSON.stringify(data, null, 2), 'error');
                }
            } catch (error) {
                showResult('❌ API Test Error', `Failed to test API: ${error.message}`, 'error');
            }
        }

        async function checkEnvironment() {
            showLoading('Checking environment variables...');
            
            try {
                const response = await fetch('/debug/steadfast');
                const data = await response.json();
                
                if (data.environment) {
                    showResult('⚙️ Environment Configuration', JSON.stringify(data.environment, null, 2), 'info');
                } else {
                    showResult('❌ Environment Check Failed', 'Could not retrieve environment data', 'error');
                }
            } catch (error) {
                showResult('❌ Environment Error', `Failed to check environment: ${error.message}`, 'error');
            }
        }

        // Auto-run configuration check on page load
        window.onload = function() {
            checkSteadfastConfig();
        };
    </script>
</body>
</html>
