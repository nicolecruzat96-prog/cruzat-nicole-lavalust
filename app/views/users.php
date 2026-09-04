<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg:#090d13;
            --bg-panel:#0f141c;
            --bg-tab-inactive:#0b0f16;
            --bg-tab-active:#141b26;
            --border:#1c2430;
            --text:#d7dee7;
            --text-dim:#5b6472;
            --blue:#5fb3ff;
            --green:#8ddb94;
            --orange:#f2b774;
            --pink:#ff86b0;
            --comment:#6b7688;
            --radius:10px;
        }

        *{ box-sizing:border-box; }

        html,body{
            margin:0;
            min-height:100vh;
            background:
                radial-gradient(circle at 15% 0%, rgba(95,179,255,0.08), transparent 45%),
                radial-gradient(circle at 90% 100%, rgba(255,134,176,0.06), transparent 50%),
                var(--bg);
            color:var(--text);
            font-family:'JetBrains Mono', ui-monospace, Menlo, monospace;
            padding:32px 16px;
        }

        a{ color:inherit; text-decoration:none; }

        .editor-window{
            width:100%;
            max-width:960px;
            margin:0 auto;
            background:var(--bg-panel);
            border:1px solid var(--border);
            border-radius:var(--radius);
            overflow:hidden;
            box-shadow:0 30px 80px -30px rgba(0,0,0,0.7), 0 0 0 1px rgba(255,255,255,0.02) inset;
        }

        /* title bar */
        .title-bar{
            display:flex;
            align-items:center;
            gap:10px;
            padding:10px 14px;
            background:var(--bg-tab-inactive);
            border-bottom:1px solid var(--border);
        }
        .traffic-lights{ display:flex; gap:7px; }
        .dot{ width:11px; height:11px; border-radius:50%; display:inline-block; }
        .dot.red{ background:#ff5f57; }
        .dot.yellow{ background:#febc2e; }
        .dot.green{ background:#28c840; }
        .title-bar-label{
            margin:0 auto;
            font-size:12px;
            color:var(--text-dim);
            letter-spacing:0.02em;
        }

        /* tabs = navigation */
        .tab-strip{
            display:flex;
            background:var(--bg-tab-inactive);
            border-bottom:1px solid var(--border);
        }
        .tab{
            display:flex;
            align-items:center;
            gap:8px;
            padding:11px 18px;
            font-size:13px;
            color:var(--text-dim);
            background:var(--bg-tab-inactive);
            border-right:1px solid var(--border);
            transition:background .15s ease, color .15s ease;
        }
        .tab:hover{ color:var(--text); background:#101620; }
        .tab:focus-visible{ outline:2px solid var(--blue); outline-offset:-2px; }
        .tab.active{
            background:var(--bg-tab-active);
            color:var(--text);
            box-shadow:inset 0 -2px 0 var(--blue);
        }
        .tab-icon{ font-size:10px; color:var(--blue); }
        .tab.active .tab-icon{ color:var(--green); }

        /* query header */
        .query-pane{
            padding:22px 26px 6px;
        }
        .terminal-header{
            font-size:12px;
            color:var(--text-dim);
            margin-bottom:16px;
        }
        .terminal-header .prompt{ color:var(--green); }
        .terminal-header .path{ color:var(--blue); margin:0 6px; }
        .terminal-header .cmd{ color:var(--text); }

        h2{
            font-family:'Inter', sans-serif;
            font-size:19px;
            font-weight:600;
            color:var(--text);
            margin:0 0 4px;
        }
        .row-count{
            font-size:12px;
            color:var(--comment);
            font-style:italic;
            margin-bottom:18px;
        }
        .row-count .n{ color:var(--green); font-style:normal; }

        /* table styled as query result */
        .table-wrap{
            padding:0 26px 26px;
            overflow-x:auto;
        }
        table{
            width:100%;
            border-collapse:collapse;
            font-size:13px;
            min-width:640px;
        }
        thead th{
            text-align:left;
            padding:10px 14px;
            background:rgba(255,255,255,0.02);
            color:var(--blue);
            font-weight:500;
            border-bottom:1px solid var(--border);
            white-space:nowrap;
        }
        thead th::before{
            content:"# ";
            color:var(--comment);
        }
        tbody td{
            padding:11px 14px;
            border-bottom:1px solid var(--border);
            color:var(--text);
        }
        tbody td:first-child{ color:var(--orange); }
        tbody tr:hover td{ background:rgba(95,179,255,0.05); }
        tbody tr:last-child td{ border-bottom:none; }

        .empty{
            text-align:center;
            padding:34px 20px !important;
            color:var(--comment) !important;
            font-style:italic;
        }
        .empty::before{
            content:"// ";
        }

        /* status bar */
        .status-bar{
            display:flex;
            gap:18px;
            align-items:center;
            padding:8px 16px;
            background:#153252;
            color:#a9d2ff;
            font-size:11px;
            letter-spacing:0.02em;
        }
        .status-bar span:first-child{ color:var(--green); }
        .status-right{ margin-left:auto; }

        @media (max-width:560px){
            .query-pane{ padding:18px 16px 4px; }
            .table-wrap{ padding:0 16px 20px; }
            .status-bar{ gap:12px; font-size:10px; }
            .title-bar-label{ display:none; }
        }
    </style>
</head>
<body>

<div class="editor-window">

    <div class="title-bar">
        <div class="traffic-lights">
            <span class="dot red"></span><span class="dot yellow"></span><span class="dot green"></span>
        </div>
        <div class="title-bar-label">Lavalust</div>
    </div>

    <nav class="tab-strip">
        <a href="<?=site_url('student');?>" class="tab">
            <span class="tab-icon">○</span> home.php
        </a>
        <a href="<?=site_url('student/profile');?>" class="tab">
            <span class="tab-icon">○</span> profile.php
        </a>
        <a href="<?=site_url('users');?>" class="tab active">
            <span class="tab-icon">●</span> users
        </a>
    </nav>

    <div class="query-pane">
        <div class="terminal-header">
            <span class="prompt">Database Records</span>
        </div>
        <h2>Registered Users</h2>
        <p class="row-count"> Overview of everyone currently registered in the system — <span class="n"><?= isset($users) ? count($users) : 0; ?></span> user<?= (isset($users) && count($users) === 1) ? '' : 's'; ?> on file.</p>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>id</th>
                    <th>firstname</th>
                    <th>lastname</th>
                    <th>email</th>
                    <th>username</th>
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

    <div class="status-bar">
        <span>● online</span>
        <span>UTF-8</span>
        <span>PHP</span>
        <span>MySQL</span>
        <span class="status-right">Ln 1, Col 1</span>
    </div>

</div>

</body>
</html>