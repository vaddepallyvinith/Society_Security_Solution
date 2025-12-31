<?php
require 'db_connect.php';
if(!isset($_SESSION['username'])) header("Location: login.php");
$role = $_SESSION['role'];
if($role!=='admin' && $role!=='supervisor' && $role!=='resident') die('Access denied');

// For guard-style check-in/check-out you could make separate UI; here we show logs and allow manual check-out if open
if(isset($_GET['checkout']) && ($role==='admin' || $role==='supervisor')){
    $id = (int)$_GET['checkout'];
    $conn->query("UPDATE visit_logs SET check_out=NOW() WHERE id=$id AND check_out IS NULL");
}

// Delete (admin/supervisor)
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM visit_logs WHERE id=$id");
}

$logs = $conn->query("SELECT vl.*, vr.name AS reg_name, s.name AS staff_name, f.flat_number FROM visit_logs vl 
LEFT JOIN visitors_regular vr ON vl.visitor_regular_id=vr.id
LEFT JOIN staff s ON vl.staff_id=s.id
LEFT JOIN flats f ON vl.flat_id=f.id
ORDER BY vl.id DESC");
?>
<link rel="stylesheet" href="style.css">
<div class="header"><div class="brand">Visit Logs</div><div><a class="logout" href="dashboard.php">Back</a></div></div>
<div class="container">
  <div class="table-wrap">
    <table>
      <thead><tr><th>ID</th><th>Regular Visitor</th><th>Staff</th><th>Flat</th><th>Check-in</th><th>Check-out</th><th>Actions</th></tr></thead>
      <tbody>
        <?php while($l=$logs->fetch_assoc()){ ?>
        <tr>
          <td><?= $l['id'] ?></td>
          <td><?= htmlspecialchars($l['reg_name']) ?></td>
          <td><?= htmlspecialchars($l['staff_name']) ?></td>
          <td><?= htmlspecialchars($l['flat_number']) ?></td>
          <td><?= $l['check_in'] ?></td>
          <td><?= $l['check_out'] ?></td>
          <td class="action">
            <?php if(($role==='admin' || $role==='supervisor') && !$l['check_out']){ ?>
              <a class="edit" href="?checkout=<?= $l['id'] ?>">Check-out</a>
            <?php } ?>
            <?php if($role==='admin' || $role==='supervisor'){ ?>
              <a class="delete" href="?delete=<?= $l['id'] ?>" onclick="return confirm('Delete log?')">Delete</a>
            <?php } ?>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
