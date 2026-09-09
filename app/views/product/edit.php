<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
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
            max-width: 600px;
            background: var(--bg-panel);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 30px 80px -30px rgba(0,0,0,0.7), 0 0 0 1px rgba(255,255,255,0.02) inset;
        }

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

        .tab-strip {
            display: flex;
            background: var(--bg-tab-inactive);
            border-bottom: 1px solid var(--border);
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
        }
        .tab-icon { font-size: 10px; color: var(--orange); }

        .content-pane {
            padding: 26px;
            background:
                radial-gradient(circle at 50% 0%, rgba(95,179,255,0.06), transparent 60%),
                var(--bg);
        }

        .terminal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: var(--text-dim);
            margin-bottom: 20px;
        }
        .terminal-header .prompt { color: var(--green); }
        .terminal-header .path { color: var(--blue); margin: 0 6px; }
        .terminal-header .cmd { color: var(--text); }

        .back-link {
            font-size: 12px;
            color: var(--blue);
            border: 1px solid rgba(95, 179, 255, 0.3);
            background: rgba(95, 179, 255, 0.1);
            padding: 4px 10px;
            border-radius: 6px;
            transition: opacity 0.15s ease;
        }
        .back-link:hover { opacity: 0.85; }

        .error-banner {
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.3);
            color: var(--red);
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 12px;
            margin-bottom: 20px;
        }

        .error-banner ul {
            margin: 0;
            padding-left: 18px;
        }

        .error-banner li {
            margin-bottom: 4px;
        }
        .error-banner li:last-child {
            margin-bottom: 0;
        }

        .edit-form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 12px;
            color: var(--blue);
        }

        .form-control {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 10px 12px;
            color: var(--green);
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            outline: none;
            transition: border-color 0.15s ease, background 0.15s ease;
        }

        textarea.form-control {
            min-height: 90px;
            resize: vertical;
        }

        .form-control:focus {
            border-color: var(--blue);
            background: rgba(255, 255, 255, 0.05);
        }

        .submit-btn {
            margin-top: 8px;
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--blue), var(--green));
            border: none;
            border-radius: 6px;
            color: #0a0e14;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: opacity 0.15s ease, transform 0.1s ease;
        }

        .submit-btn:hover {
            opacity: 0.9;
        }

        .submit-btn:active {
            transform: scale(0.99);
        }

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

        @media (max-width: 520px) {
            .form-row { grid-template-columns: 1fr; }
            .status-bar { gap: 12px; font-size: 10px; }
            .content-pane { padding: 22px 16px 26px; }
            .title-bar-label { display: none; }
        }
    </style>
</head>
<body>

    <main class="editor-window">

        <div class="title-bar">
            <div class="traffic-lights">
                <span class="dot red"></span><span class="dot yellow"></span><span class="dot green"></span>
            </div>
            <div class="title-bar-label">products — edit.php</div>
        </div>

        <nav class="tab-strip">
            <div class="tab">
                <span class="tab-icon">●</span> edit.php
            </div>
        </nav>

        <div class="content-pane">

            <div class="terminal-header">
                <div>
                    <span class="prompt">admin@system</span><span class="path">~/products</span>$ <span class="cmd">php edit.php --id=<?= htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <a href="<?= site_url('/product/display'); ?>" class="back-link">← Back to products</a>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="error-banner">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('/product/edit/' . $product['id']); ?>" method="post" class="edit-form">
                
                <div class="form-group">
                    <label for="product_name">product_name:</label>
                    <input type="text" id="product_name" name="product_name" class="form-control" value="<?= htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">description:</label>
                    <textarea id="description" name="description" class="form-control" required><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">price ($):</label>
                        <input type="number" id="price" name="price" step="0.01" class="form-control" value="<?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="quantity">quantity:</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" value="<?= htmlspecialchars($product['quantity'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Update Product</button>
            </form>

        </div>

        <div class="status-bar">
            <span>● ready</span>
            <span>UTF-8</span>
            <span>PHP</span>
            <span>CodeIgniter</span>
            <span class="status-right">ID: <?= htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>

    </main>

</body>
</html>