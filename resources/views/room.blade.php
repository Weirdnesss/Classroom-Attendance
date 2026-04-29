<h1>Room {{ $room_id }}</h1>

<button onclick="markAttendance()">Mark Attendance</button>

<script>
async function markAttendance() {
    await fetch('/attendance/scan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            user_id: 1, // TEMP
            room_id: {{ $room_id }},
            method: 'qr'
        })
    });

    alert("Attendance recorded");
}
</script>