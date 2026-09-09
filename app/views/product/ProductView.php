<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProductView</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #090d13;
            --bg-panel: #0f141c;
            --bg-tab-inactive: #0b0f16;
            --bg-tab-active: #141b26;
            --border: #1c2430;
            --text: #d7dee7;
            --text-dim: #5b6472;
            --blue: #5fb3ff;
            --green: #8ddb94;
            --orange: #f2b774;
            --pink: #ff86b0;
            --red: #ff6b6b;
            --comment: #6b7688;
            --radius: 10px;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at 20% 0%, rgba(95,179,255,0.08), transparent 45%),
                radial-gradient(circle at 85% 100%, rgba(255,134,176,0.06), transparent 50%),
                var(--bg);
            color: var(--text);
            font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        a { color: inherit; text-decoration: none; }

        .editor-window {
            width: 100%;
            max-width: 900px;
            background: var(--bg-panel);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 30px 80px -30px rgba(0,0,0,0.7), 0 0 0 1px rgba(255,255,255,0.02) inset;
        }

        /* title bar */
        .title-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: var(--bg-tab-inactive);
            border-bottom: 1px solid var(--border);
        }
        .traffic-lights { display: flex; gap: 7px; }
        .dot { width: 11px; height: 11px; border-radius: 50%; display: inline-block; }
        .dot.red { background: #ff5f57; }
        .dot.yellow { background: #febc2e; }
        .dot.green { background: #28c840; }
        .title-bar-label {
            margin: 0 auto;
            font-size: 12px;
            color: var(--text-dim);
            letter-spacing: 0.02em;
        }

        /* tab strip */
        .tab-strip {
            display: flex;
            background: var(--bg-tab-inactive);
            border-bottom: 1px solid var(--border);
            overflow-x: auto;
        }
        .tab {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 11px 18px;
            font-size: 13px;
            color: var(--text);
            background: var(--bg-tab-active);
            border-right: 1px solid var(--border);
            box-shadow: inset 0 -2px 0 var(--blue);
            white-space: nowrap;
        }
        .tab-icon { font-size: 10px; color: var(--green); }

        /* content pane */
        .content-pane {
            padding: 26px;
            background:
                radial-gradient(circle at 50% 0%, rgba(95,179,255,0.06), transparent 60%),
                var(--bg);
        }

        /* header section */
        .terminal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
            font-size: 12px;
        }
        .user-greeting {
            color: var(--text-dim);
        }
        .user-greeting .prompt { color: var(--green); }
        .user-greeting .path { color: var(--blue); margin: 0 6px; }
        .user-greeting .cmd { color: var(--text); font-weight: 600; }

        .action-bar {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 500;
            transition: opacity 0.15s ease, transform 0.1s ease;
        }
        .btn:hover { opacity: 0.85; }
        .btn:active { transform: scale(0.98); }

        .btn-add {
            background: rgba(141, 219, 148, 0.15);
            color: var(--green);
            border: 1px solid rgba(141, 219, 148, 0.3);
        }

        .btn-logout {
            background: rgba(255, 107, 107, 0.15);
            color: var(--red);
            border: 1px solid rgba(255, 107, 107, 0.3);
        }

        /* notification toast */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            min-width: 280px;
            padding: 12px 16px;
            color: var(--green);
            background: #0f141c;
            border: 1px solid var(--green);
            border-radius: 6px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .notification button {
            color: var(--text-dim);
            background: transparent;
            border: 0;
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
            padding: 0 0 0 12px;
        }
        .notification button:hover { color: var(--red); }

        /* table styling */
        .table-container {
            width: 100%;
            overflow-x: auto;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.015);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }

        th {
            background: var(--bg-panel);
            color: var(--blue);
            padding: 12px 16px;
            font-weight: 500;
            border-bottom: 1px solid var(--border);
            letter-spacing: 0.02em;
        }

        td {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(28, 36, 48, 0.6);
            color: var(--text);
            font-size: 12px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .td-id { color: var(--pink); }
        .td-name { color: var(--orange); font-weight: 500; }
        .td-price { color: var(--green); }
        .td-date { color: var(--text-dim); }

        /* action links */
        .action-link {
            font-size: 12px;
            margin-right: 8px;
            padding: 2px 6px;
            border-radius: 4px;
            transition: background 0.15s ease;
        }
        .action-link.edit { color: var(--blue); }
        .action-link.edit:hover { background: rgba(95, 179, 255, 0.15); }
        .action-link.delete { color: var(--red); }
        .action-link.delete:hover { background: rgba(255, 107, 107, 0.15); }

        /* status bar */
        .status-bar {
            display: flex;
            gap: 18px;
            align-items: center;
            padding: 8px 16px;
            background: #153252;
            color: #a9d2ff;
            font-size: 11px;
            letter-spacing: 0.02em;
        }
        .status-bar span:first-child { color: var(--green); }
        .status-right { margin-left: auto; }

        @media (max-width: 600px) {
            .content-pane { padding: 18px 14px; }
            .terminal-header { flex-direction: column; align-items: flex-start; }
            .title-bar-label { display: none; }
            .status-bar { gap: 10px; font-size: 10px; }
        }
    </style>
</head>
<body>

    <main class="editor-window">

        <!-- Title Bar -->
        <div class="title-bar">
            <div class="traffic-lights">
                <span class="dot red"></span><span class="dot yellow"></span><span class="dot green"></span>
            </div>
            <div class="title-bar-label">products — ProductView.php</div>
        </div>

        <!-- Tab Strip -->
        <nav class="tab-strip">
            <div class="tab">
                <span class="tab-icon">●</span> ProductView.php
            </div>
        </nav>

        <!-- Main Content -->
        <div class="content-pane">

            <!-- Dynamic Notification Box -->
            <?php if (!empty($notification)): ?>
                <div class="notification" role="status" id="notification">
                    <span>[OK] <?= htmlspecialchars($notification, ENT_QUOTES, 'UTF-8'); ?></span>
                    <button type="button" onclick="document.getElementById('notification').remove();" aria-label="Close notification">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Terminal / User Greeting & Actions -->
            <div class="terminal-header">
                <div class="user-greeting">
                    <span class="prompt">user@system</span><span class="path">~/products</span>$ <span class="cmd">welcome, <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                
                <div class="action-bar">
                    <?php if ($user_role === 'admin'): ?>
                        <a href="<?= site_url('/product/create'); ?>" class="btn btn-add">+ Add Product</a>
                    <?php endif; ?>
                    <a href="<?= site_url('/logout'); ?>" class="btn btn-logout">Logout</a>
                </div>
            </div>

            <!-- Products Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Created At</th>
                            <?php if ($user_role === 'admin'): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="td-id">#<?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="td-name"><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="td-price">$<?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="td-date"><?php echo htmlspecialchars($product['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <?php if ($user_role === 'admin'): ?>
                                    <td>
                                        <a href="<?= site_url('/product/edit/' . $product['id']); ?>" class="action-link edit">Edit</a>
                                        <a href="<?= site_url('/product/delete/' . $product['id']); ?>" class="action-link delete" onclick="return confirm('Delete this product?');">Delete</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- Status Bar -->
        <div class="status-bar">
            <span>● database connected</span>
            <span>UTF-8</span>
            <span>PHP</span>
            <span>CodeIgniter</span>
            <span class="status-right">Role: <?= htmlspecialchars($user_role ?? 'guest', ENT_QUOTES, 'UTF-8'); ?></span>
        </div>

    </main>

    <?php if (!empty($notification)): ?>
        <script>
            window.setTimeout(function () {
                var notification = document.getElementById('notification');
                if (notification) {
                    notification.remove();
                }
            }, 4000);
        </script>
    <?php endif; ?>

</body>
</html>