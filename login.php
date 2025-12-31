<?php
// login.php
require 'db_connect.php';
$error = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password_hash, role, resident_id FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param("s",$username);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res && $res->num_rows===1){
        $u = $res->fetch_assoc();
        if(password_verify($password, $u['password_hash'])){
            $_SESSION['id'] = $u['id'];
            $_SESSION['username'] = $u['username'];
            $_SESSION['role'] = $u['role'];
            $_SESSION['resident_id'] = $u['resident_id']; // may be null
            header("Location: dashboard.php");
            exit;
        } else $error = "Invalid credentials";
    } else $error = "User not found";
}
?>
<link rel="stylesheet" href="style.css">
<div class="header"><div class="brand">Society Security System</div></div>
<div class="container card" style="max-width:520px;margin-top:18px;">
  <h2>Login</h2>
  <?php if($error): ?><div class="notice"><?=$error?></div><?php endif;?>
  <form method="post">
    <div class="form-row">
      <input name="username" placeholder="Username" required>
      <input name="password" type="password" placeholder="Password" required>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
      <button class="btn" type="submit">Sign in</button>
      <a href="hash_generator.php" class="small">Generate password hash</a>
    </div>
  </form>
</div>
