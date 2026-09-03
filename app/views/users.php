<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <style>
        :root {
            --bg: #0f1e17;
            --panel: #14261d;
            --panel-2: #17301f;
            --accent: #22c55e;
            --accent-dark: #16a34a;
            --accent-light: #4ade80;
            --text: #e7f3ec;
            --text-muted: #93a89c;
            --border: #234531;
            --row-hover: #1c3a26;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
            background: radial-gradient(circle at top left, #17301f 0%, var(--bg) 55%);
            color: var(--text);
        }

        .container {
            max-width: 950px;
            margin: auto;
            background: var(--panel);
            padding: 28px 30px 34px;
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.35);
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 4px;
        }

        h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h2::before {
            content: "";
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--accent-light);
            box-shadow: 0 0 10px var(--accent-light);
        }

        .subtitle {
            margin: 4px 0 18px;
            color: var(--text-muted);
            font-size: 13.5px;
        }

        .badge {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #08150d;
            font-weight: 700;
            font-size: 12.5px;
            padding: 6px 14px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
            background: var(--panel-2);
        }

        thead tr {
            background: linear-gradient(135deg, var(--accent-dark), #0d7a3c);
        }

        th {
            padding: 14px 16px;
            text-align: left;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #eafff1;
            font-weight: 600;
        }

        td {
            padding: 13px 16px;
            font-size: 14.5px;
            color: var(--text);
            border-bottom: 1px solid var(--border);
        }

        tbody tr {
            transition: background 0.15s ease;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background: var(--row-hover);
        }

        tbody tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.02);
        }

        tbody tr:nth-child(even):hover {
            background: var(--row-hover);
        }

        td:first-child {
            color: var(--accent-light);
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }

        .empty {
            text-align: center;
            color: var(--text-muted);
            padding: 40px 20px;
            font-size: 14.5px;
        }

        .empty::before {
            content: "🌿";
            display: block;
            font-size: 26px;
            margin-bottom: 8px;
            filter: grayscale(1) brightness(1.6);
        }

        @media (max-width: 600px) {
            .container { padding: 20px; }
            th, td { padding: 10px 12px; font-size: 13px; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Registered Users</h2>
        <span class="badge"><?= isset($users) ? count($users) : 0 ?> Total</span>
    </div>
    <p class="subtitle">List ng lahat ng users na naka-register sa system.</p>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Username</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= html_escape($user['id'] ?? ''); ?></td>
                            <td><?= html_escape($user['firstname'] ?? ''); ?></td>
                            <td><?= html_escape($user['lastname'] ?? ''); ?></td>
                            <td><?= html_escape($user['email'] ?? ''); ?></td>
                            <td><?= html_escape($user['username'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty">No users found in the database.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>