<?php
require 'db_connect.php';
if(!isset($_SESSION['username'])) header("Location: login.php");
$role = $_SESSION['role'];
if($role!=='admin' && $role!=='supervisor') die('Access denied');

// Add
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_resident'])){
    $flat_id = (int)$_POST['flat_id'];
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $conn->query("INSERT INTO residents (flat_id,name,phone,email) VALUES ($flat_id,'$name','$phone','$email')");
}

// Delete
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM residents WHERE id=$id");
}

$flats = $conn->query("SELECT * FROM flats");
$res = $conn->query("SELECT r.*, f.flat_number FROM residents r LEFT JOIN flats f ON r.flat_id=f.id ORDER BY r.id DESC");
?>
<link rel="stylesheet" href="style.css">
<div class="header"><div class="brand">Manage Residents</div><div><a class="logout" href="dashboard.php">Back</a></div></div>
<div class="container">
  <div class="card">
    <form method="post">
      <div class="form-row">
        <select name="flat_id" required>
          <option value="">Select flat</option>
          <?php while($fl=$flats->fetch_assoc()){ ?>
            <option value="<?=$fl['id']?>"><?=htmlspecialchars($fl['flat_number'])?></option>
          <?php } ?>
        </select>
        <input name="name" placeholder="Resident name" required>
        <input name="phone" placeholder="Phone">
        <input name="email" placeholder="Email">
      </div>
      <button class="btn" name="add_resident" type="submit">➕ Add Resident</button>
    </form>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>ID</th><th>Name</th><th>Flat</th><th>Phone</th><th>Email</th><th>Action</th></tr></thead>
      <tbody>
        <?php while($r=$res->fetch_assoc()){ ?>
        <tr>
          <td><?= $r['id'] ?></td>
          <td><?= htmlspecialchars($r['name']) ?></td>
          <td><?= htmlspecialchars($r['flat_number']) ?></td>
          <td><?= htmlspecialchars($r['phone']) ?></td>
          <td><?= htmlspecialchars($r['email']) ?></td>
          <td class="action">
            <?php if($role==='admin' || $role==='supervisor'){ ?>
              <a class="delete" href="?delete=<?= $r['id'] ?>" onclick="return confirm('Delete resident?')">Delete</a>
            <?php } ?>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
