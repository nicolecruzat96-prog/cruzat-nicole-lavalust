<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard</title>
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

  *{box-sizing:border-box;}

  html,body{
    margin:0;
    min-height:100vh;
    background:
      radial-gradient(circle at 20% 0%, rgba(95,179,255,0.08), transparent 45%),
      radial-gradient(circle at 85% 100%, rgba(255,134,176,0.06), transparent 50%),
      var(--bg);
    color:var(--text);
    font-family:'JetBrains Mono', ui-monospace, Menlo, monospace;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:32px 16px;
  }

  a{ color:inherit; text-decoration:none; }

  .editor-window{
    width:100%;
    max-width:760px;
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

  /* code pane */
  .code-pane{
    display:flex;
    padding:20px 0;
    background:var(--bg-panel);
    border-bottom:1px dashed var(--border);
    font-size:13px;
    line-height:1.7;
    overflow-x:auto;
  }
  .line-numbers{
    flex:none;
    padding:0 14px;
    text-align:right;
    color:#2c3644;
    user-select:none;
    white-space:pre;
  }
  .code-content{ padding-right:20px; }
  .code-content pre{ margin:0; white-space:pre; }
  .c-tag{ color:var(--text-dim); }
  .c-comment{ color:var(--comment); font-style:italic; }
  .c-kw{ color:var(--pink); }
  .c-class{ color:var(--orange); }
  .c-fn{ color:var(--blue); }
  .c-str{ color:var(--green); }
  .c-punc{ color:var(--text-dim); }

  /* terminal / hero */
  .terminal-pane{
    padding:28px 26px 30px;
    background:
      radial-gradient(circle at 50% 0%, rgba(95,179,255,0.06), transparent 60%),
      var(--bg);
  }
  .terminal-header{
    font-size:12px;
    color:var(--text-dim);
    margin-bottom:18px;
  }
  .terminal-header .prompt{ color:var(--green); }
  .terminal-header .path{ color:var(--blue); margin:0 6px; }
  .terminal-header .cmd{ color:var(--text); }

  #typed-heading{
    margin:0 0 10px;
    font-size:clamp(26px, 5vw, 38px);
    font-weight:700;
    letter-spacing:-0.01em;
    color:var(--text);
    min-height:1.2em;
  }
  .caret{
    display:inline-block;
    color:var(--green);
    animation:blink 1s step-end infinite;
  }
  @keyframes blink{ 50%{ opacity:0; } }

  .subhead{
    margin:0;
    font-family:'Inter', sans-serif;
    font-size:14px;
    color:var(--text-dim);
  }
  .subhead strong{ color:var(--blue); font-weight:600; }

  /* status bar */
  .status-bar{
    display:flex;
    gap:18px;
    align-items:center;
    padding:8px 16px;
    background:var(--blue);
    background:#153252;
    color:#a9d2ff;
    font-size:11px;
    letter-spacing:0.02em;
  }
  .status-bar span:first-child{ color:var(--green); }
  .status-right{ margin-left:auto; }

  @media (max-width:520px){
    .code-pane{ font-size:12px; }
    .status-bar{ gap:12px; font-size:10px; }
    .terminal-pane{ padding:22px 18px 26px; }
    .title-bar-label{ display:none; }
  }

  @media (prefers-reduced-motion: reduce){
    .caret{ animation:none; }
  }
</style>
</head>
<body>

  <main class="editor-window">

    <div class="title-bar">
      <div class="traffic-lights">
        <span class="dot red"></span><span class="dot yellow"></span><span class="dot green"></span>
      </div>
      <div class="title-bar-label">student — welcome.php</div>
    </div>

    <nav class="tab-strip">
      <a href="<?=site_url('student');?>" class="tab active">
        <span class="tab-icon">●</span> home.php
      </a>
      <a href="<?=site_url('student/profile');?>" class="tab">
        <span class="tab-icon">○</span> profile.php
      </a>
    </nav>

    <div class="code-pane">
      <div class="line-numbers">1
2
3
4
5
6
7</div>
      <div class="code-content"><pre><code><span class="c-tag">&lt;?php</span>
<span class="c-comment">// Information Technology program — student dashboard</span>
<span class="c-kw">class</span> <span class="c-class">Student</span> <span class="c-punc">{</span>
    <span class="c-kw">public function</span> <span class="c-fn">status</span><span class="c-punc">()</span> <span class="c-punc">{</span>
        <span class="c-kw">return</span> <span class="c-str">"compiling ideas, one commit at a time"</span><span class="c-punc">;</span>
    <span class="c-punc">}</span>
<span class="c-punc">}</span></code></pre></div>
    </div>

    <div class="terminal-pane">
      <div class="terminal-header">
        <span class="prompt">student@campus</span><span class="path">~/dashboard</span>$ <span class="cmd">php welcome.php</span>
      </div>
      <h1 id="typed-heading"><span class="caret">_</span></h1>
      <p class="subhead">Signed in as <strong>IT Student</strong> · Track: Software Engineering</p>
    </div>

    <div class="status-bar">
      <span>● online</span>
      <span>UTF-8</span>
      <span>PHP</span>
      <span>CodeIgniter</span>
      <span class="status-right">Ln 1, Col 1</span>
    </div>

  </main>

<script>
(function(){
  var el = document.getElementById('typed-heading');
  var text = "Welcome to my page";
  var caret = '<span class="caret">_</span>';
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (reduce) {
    el.innerHTML = text + caret;
    return;
  }

  var i = 0;
  function type(){
    if (i <= text.length) {
      el.innerHTML = text.slice(0, i) + caret;
      i++;
      setTimeout(type, 55);
    }
  }
  type();
})();
</script>

</body>
</html>