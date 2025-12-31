<?php
require 'db_connect.php';
if(!isset($_SESSION['username'])) header("Location: login.php");
if($_SESSION['role']!=='admin') die('Only admin can manage users');

// Add user
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_user'])){
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $full = $conn->real_escape_string($_POST['full_name']);
    $role = $conn->real_escape_string($_POST['role']);
    $resident_id = empty($_POST['resident_id']) ? 'NULL' : (int)$_POST['resident_id'];
    $conn->query("INSERT INTO users (username,password_hash,full_name,role,resident_id) VALUES ('$username','$hash','$full','$role',".($resident_id==='NULL'?'NULL':$resident_id).")");
}

// Delete
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$id");
}

$users = $conn->query("SELECT u.*, r.name AS resident_name FROM users u LEFT JOIN residents r ON u.resident_id=r.id ORDER BY u.id DESC");
$residents = $conn->query("SELECT * FROM residents");
?>
<link rel="stylesheet" href="style.css">
<div class="header"><div class="brand">Manage Users</div><div><a class="logout" href="dashboard.php">Back</a></div></div>
<div class="container">
  <div class="card">
    <form method="post">
      <div class="form-row">
        <input name="username" placeholder="Username" required>
        <input name="password" placeholder="Password" required>
        <input name="full_name" placeholder="Full name">
        <select name="role" required>
          <option value="admin">Admin</option>
          <option value="supervisor">Supervisor</option>
          <option value="resident">Resident</option>
        </select>
        <select name="resident_id">
          <option value="">Link to resident (optional)</option>
          <?php while($r=$residents->fetch_assoc()){ ?>
            <option value="<?=$r['id']?>"><?=htmlspecialchars($r['name']).' - '.$r['id']?></option>
          <?php } ?>
        </select>
      </div>
      <button class="btn" name="add_user">➕ Create User</button>
    </form>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>ID</th><th>Username</th><th>Full name</th><th>Role</th><th>Resident link</th><th>Action</th></tr></thead>
      <tbody>
        <?php while($u=$users->fetch_assoc()){ ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= htmlspecialchars($u['username']) ?></td>
          <td><?= htmlspecialchars($u['full_name']) ?></td>
          <td><?= $u['role'] ?></td>
          <td><?= htmlspecialchars($u['resident_name']) ?></td>
          <td class="action"><a class="delete" href="?delete=<?= $u['id'] ?>" onclick="return confirm('Delete user?')">Delete</a></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
