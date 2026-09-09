<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
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
      max-width: 520px;
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
    .tab-icon { font-size: 10px; color: var(--green); }

    /* main content pane */
    .content-pane {
      padding: 26px 26px 30px;
      background:
        radial-gradient(circle at 50% 0%, rgba(95,179,255,0.06), transparent 60%),
        var(--bg);
    }

    .terminal-header {
      font-size: 12px;
      color: var(--text-dim);
      margin-bottom: 20px;
    }
    .terminal-header .prompt { color: var(--green); }
    .terminal-header .path { color: var(--blue); margin: 0 6px; }
    .terminal-header .cmd { color: var(--text); }

    /* error message banner */
    .error-banner {
      background: rgba(255, 107, 107, 0.1);
      border: 1px solid rgba(255, 107, 107, 0.3);
      color: var(--red);
      padding: 10px 14px;
      border-radius: 6px;
      font-size: 12px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* form styling */
    .login-form {
      display: flex;
      flex-direction: column;
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

    .form-control:focus {
      border-color: var(--blue);
      background: rgba(255, 255, 255, 0.05);
    }

    select.form-control {
      appearance: none;
      background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235fb3ff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 10px center;
      background-size: 14px;
      padding-right: 32px;
      cursor: pointer;
    }

    select.form-control option {
      background: var(--bg-panel);
      color: var(--text);
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

    @media (max-width: 520px) {
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
      <div class="title-bar-label">auth — login.php</div>
    </div>

    <nav class="tab-strip">
      <div class="tab">
        <span class="tab-icon">●</span> login.php
      </div>
    </nav>

    <div class="content-pane">
      <div class="terminal-header">
        <span class="prompt">guest@campus</span><span class="path">~/auth</span>$ <span class="cmd">php login.php --init</span>
      </div>

      <?php if (!empty($error)): ?>
        <div class="error-banner">
          <span>✖</span> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <form action="<?= site_url('/login'); ?>" method="post" class="login-form">
        <div class="form-group">
          <label for="email">email_address:</label>
          <input type="email" id="email" name="email" class="form-control" required placeholder="user@domain.com">
        </div>

        <div class="form-group">
          <label for="password">password:</label>
          <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
        </div>

        <div class="form-group">
          <label for="role">access_level:</label>
          <select id="role" name="role" class="form-control" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>

        <button type="submit" class="submit-btn">Execute Login</button>
      </form>
    </div>

    <div class="status-bar">
      <span>● ready</span>
      <span>UTF-8</span>
      <span>PHP</span>
      <span>CodeIgniter</span>
      <span class="status-right">Auth Module</span>
    </div>

  </main>

</body>
</html>