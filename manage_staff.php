<?php
require 'db_connect.php';
if(!isset($_SESSION['username'])) header("Location: login.php");
$role = $_SESSION['role'];
if($role!=='admin' && $role!=='supervisor') die('Access denied');

// Add
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_staff'])){
    $name = $conn->real_escape_string($_POST['name']);
    $type = $conn->real_escape_string($_POST['staff_type']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $notes = $conn->real_escape_string($_POST['notes']);
    $conn->query("INSERT INTO staff (name,staff_type,phone,notes) VALUES ('$name','$type','$phone','$notes')");
}

// Delete
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM staff WHERE id=$id");
}

$res = $conn->query("SELECT * FROM staff ORDER BY id DESC");
?>
<link rel="stylesheet" href="style.css">
<div class="header"><div class="brand">Manage Staff</div><div><a class="logout" href="dashboard.php">Back</a></div></div>
<div class="container">
  <div class="card">
    <form method="post">
      <div class="form-row">
        <input name="name" placeholder="Staff name" required>
        <input name="staff_type" placeholder="Type (security/cleaning/etc)">
        <input name="phone" placeholder="Phone">
        <input name="notes" placeholder="Notes">
      </div>
      <button class="btn" name="add_staff" type="submit">➕ Add Staff</button>
    </form>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>ID</th><th>Name</th><th>Type</th><th>Phone</th><th>Notes</th><th>Action</th></tr></thead>
      <tbody>
        <?php while($s=$res->fetch_assoc()){ ?>
        <tr>
          <td><?= $s['id'] ?></td>
          <td><?= htmlspecialchars($s['name']) ?></td>
          <td><?= htmlspecialchars($s['staff_type']) ?></td>
          <td><?= htmlspecialchars($s['phone']) ?></td>
          <td><?= htmlspecialchars($s['notes']) ?></td>
          <td class="action">
            <?php if($role==='admin' || $role==='supervisor'){ ?>
              <a class="delete" href="?delete=<?= $s['id'] ?>" onclick="return confirm('Delete staff?')">Delete</a>
            <?php } ?>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
