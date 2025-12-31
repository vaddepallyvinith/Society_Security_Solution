<?php
require 'db_connect.php';
if(!isset($_SESSION['username'])) header("Location: login.php");
$role = $_SESSION['role'];
if($role!=='admin' && $role!=='supervisor') die('Access denied');

// Add
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_building'])){
    $name = $conn->real_escape_string($_POST['name']);
    $address = $conn->real_escape_string($_POST['address']);
    $floors = (int)$_POST['total_floors'];
    $conn->query("INSERT INTO buildings (name,address,total_floors) VALUES ('$name','$address',$floors)");
}

// Delete
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM buildings WHERE id=$id");
}

$res = $conn->query("SELECT * FROM buildings ORDER BY id DESC");
?>
<link rel="stylesheet" href="style.css">
<div class="header"><div class="brand">Manage Buildings</div><div><a class="logout" href="dashboard.php">Back</a></div></div>
<div class="container">
  <div class="card">
    <form method="post">
      <div class="form-row">
        <input name="name" placeholder="Building name" required>
        <input name="address" placeholder="Address">
        <input name="total_floors" type="number" placeholder="Total floors">
      </div>
      <button class="btn" name="add_building" type="submit">➕ Add Building</button>
    </form>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>ID</th><th>Name</th><th>Address</th><th>Floors</th><th>Action</th></tr></thead>
      <tbody>
        <?php while($r=$res->fetch_assoc()){ ?>
        <tr>
          <td><?= $r['id'] ?></td>
          <td><?= htmlspecialchars($r['name']) ?></td>
          <td><?= htmlspecialchars($r['address']) ?></td>
          <td><?= $r['total_floors'] ?></td>
          <td class="action">
            <?php if($role==='admin' || $role==='supervisor'){ ?>
              <a class="delete" href="?delete=<?= $r['id']?>" onclick="return confirm('Delete building?')">Delete</a>
            <?php } ?>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
