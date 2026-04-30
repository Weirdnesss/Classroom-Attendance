<!DOCTYPE html>
<html>
<head>
    <title>RFID Scan Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="bg-white rounded border border-gray-200 p-8 w-full max-w-md">
    <h1 class="text-lg font-semibold mb-6">RFID Scan Simulator</h1>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">RFID Tag</label>
        <input type="text" id="rfid_tag" placeholder="e.g. 2025241511213"
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium mb-1">Device UID</label>
        <input type="text" id="device_uid" placeholder="e.g. RFID-001"
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
    </div>

    <button onclick="sendScan()"
            class="w-full px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
        Simulate Scan
    </button>

    <div id="result" class="mt-6 hidden">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Response</p>
        <pre id="result-text" class="bg-gray-50 border border-gray-200 rounded p-3 text-xs overflow-auto"></pre>
    </div>
</div>

<script>
async function sendScan() {
    const rfid_tag   = document.getElementById('rfid_tag').value;
    const device_uid = document.getElementById('device_uid').value;

    const response = await fetch('/api/scan', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ rfid_tag, device_uid })
    });

    const contentType = response.headers.get('content-type');
    let data;

    if (contentType && contentType.includes('application/json')) {
        data = await response.json();
    } else {
        const text = await response.text();
        data = { error: 'Server returned non-JSON response', raw: text.slice(0, 300) };
    }

    document.getElementById('result').classList.remove('hidden');
    document.getElementById('result-text').textContent = JSON.stringify(data, null, 2);
}
</script>
</body>
</html>