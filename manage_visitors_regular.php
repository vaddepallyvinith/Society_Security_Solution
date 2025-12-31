<?php
require 'db_connect.php';
if(!isset($_SESSION['username'])) header("Location: login.php");
$role = $_SESSION['role'];
if($role!=='admin' && $role!=='supervisor') die('Access denied');

// Add regular visitor/vendor
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_regular'])){
    $name = $conn->real_escape_string($_POST['name']);
    $relation = $conn->real_escape_string($_POST['relation']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $vendor = isset($_POST['vendor']) ? 1:0;
    $code = $conn->real_escape_string($_POST['security_code']);
    $assigned = empty($_POST['assigned_flat_id']) ? 'NULL' : (int)$_POST['assigned_flat_id'];
    $conn->query("INSERT INTO visitors_regular (name,relation,phone,vendor,security_code,assigned_flat_id) VALUES ('$name','$relation','$phone',$vendor,'$code',".($assigned==='NULL'?'NULL':$assigned).")");
}

// Delete
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM visitors_regular WHERE id=$id");
}

$flats = $conn->query("SELECT * FROM flats");
$regs = $conn->query("SELECT vr.*, f.flat_number FROM visitors_regular vr LEFT JOIN flats f ON vr.assigned_flat_id=f.id ORDER BY vr.id DESC");
?>
<link rel="stylesheet" href="style.css">
<div class="header"><div class="brand">Regular Visitors & Vendors</div><div><a class="logout" href="dashboard.php">Back</a></div></div>
<div class="container">
  <div class="card">
    <form method="post">
      <div class="form-row">
        <input name="name" placeholder="Name" required>
        <input name="relation" placeholder="Relation/Role">
        <input name="phone" placeholder="Phone">
        <input name="security_code" placeholder="Security code" required>
        <select name="assigned_flat_id">
          <option value="">Assign to flat (optional)</option>
          <?php while($f=$flats->fetch_assoc()){ ?>
            <option value="<?=$f['id']?>"><?=htmlspecialchars($f['flat_number'])?></option>
          <?php } ?>
        </select>
        <label style="display:flex;align-items:center;gap:8px"><input type="checkbox" name="vendor"> Vendor</label>
      </div>
      <button class="btn" name="add_regular" type="submit">➕ Add Regular</button>
    </form>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Code</th><th>Assigned Flat</th><th>Active</th><th>Action</th></tr></thead>
      <tbody>
        <?php while($r=$regs->fetch_assoc()){ ?>
        <tr>
          <td><?= $r['id'] ?></td>
          <td><?= htmlspecialchars($r['name']) ?></td>
          <td><?= htmlspecialchars($r['phone']) ?></td>
          <td><?= htmlspecialchars($r['security_code']) ?></td>
          <td><?= htmlspecialchars($r['flat_number']) ?></td>
          <td><?= $r['active'] ? 'Yes' : 'No' ?></td>
          <td class="action">
            <a class="delete" href="?delete=<?= $r['id'] ?>" onclick="return confirm('Delete regular visitor?')">Delete</a>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
