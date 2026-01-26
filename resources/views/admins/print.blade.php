<!DOCTYPE html>
<html>
<head>
    <title>Users PDF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h4 class="mb-3">User List</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Deactivation Reason</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Birth Date</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $u)
            <tr>
                <td>USER-{{ str_pad($u->id, 4, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $u->firstName }} {{ $u->middleName }} {{ $u->lastName }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->role }}</td>
                <td>{{ $u->userStatus }}</td>
                <td>{{ $u->customReason ?? '-' }}</td>
                <td>{{ $u->contactNo ?? '-' }}</td>
                <td>{{ $u->homeAddress ?? '-' }}</td>
                <td>{{ $u->birthDate ?? '-' }}</td>
                <td>{{ optional($u->created_at)->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        window.print();
    </script>
</body>
</html>
