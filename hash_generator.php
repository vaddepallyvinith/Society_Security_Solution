<?php
require 'db_connect.php';
$hash='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $pwd = $_POST['pwd'] ?? '';
  $hash = password_hash($pwd, PASSWORD_DEFAULT);
}
?>
<link rel="stylesheet" href="style.css">
<div class="container card" style="max-width:600px">
  <h2>Password Hash Generator</h2>
  <form method="post">
    <div class="form-row">
      <input name="pwd" placeholder="Enter password" required>
      <button class="btn" type="submit">Generate</button>
    </div>
  </form>
  <?php if($hash): ?>
    <div class="card"><strong>Hash:</strong><div style="word-break:break-all; margin-top:8px;"><?=htmlspecialchars($hash)?></div></div>
  <?php endif; ?>
  <a class="small" href="login.php">← Back to Login</a>
</div>
