<?php
require 'db_connect.php';
if(!isset($_SESSION['username'])) header("Location: login.php");
$role = $_SESSION['role'];
if($role!=='admin' && $role!=='supervisor') die('Access denied');

// Add
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_flat'])){
    $building_id = (int)$_POST['building_id'];
    $flat_number = $conn->real_escape_string($_POST['flat_number']);
    $floor = (int)$_POST['floor'];
    $block = $conn->real_escape_string($_POST['block']);
    $area = (int)$_POST['area_sqft'];
    $conn->query("INSERT INTO flats (building_id,flat_number,floor,block,area_sqft) VALUES ($building_id,'$flat_number',$floor,'$block',$area)");
}

// Delete
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM flats WHERE id=$id");
}

$buildings = $conn->query("SELECT * FROM buildings");
$flats = $conn->query("SELECT f.*, b.name AS building_name FROM flats f LEFT JOIN buildings b ON f.building_id=b.id ORDER BY f.id DESC");
?>
<link rel="stylesheet" href="style.css">
<div class="header"><div class="brand">Manage Flats</div><div><a class="logout" href="dashboard.php">Back</a></div></div>
<div class="container">
  <div class="card">
    <form method="post">
      <div class="form-row">
        <select name="building_id" required>
          <option value="">Select building</option>
          <?php while($b=$buildings->fetch_assoc()){ ?>
            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
          <?php } ?>
        </select>
        <input name="flat_number" placeholder="Flat number" required>
        <input name="floor" type="number" placeholder="Floor">
        <input name="block" placeholder="Block">
        <input name="area_sqft" type="number" placeholder="Area (sqft)">
      </div>
      <button class="btn" name="add_flat" type="submit">➕ Add Flat</button>
    </form>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>ID</th><th>Building</th><th>Flat No</th><th>Floor</th><th>Block</th><th>Area</th><th>Action</th></tr></thead>
      <tbody>
      <?php while($f=$flats->fetch_assoc()){ ?>
        <tr>
          <td><?= $f['id'] ?></td>
          <td><?= htmlspecialchars($f['building_name']) ?></td>
          <td><?= htmlspecialchars($f['flat_number']) ?></td>
          <td><?= $f['floor'] ?></td>
          <td><?= htmlspecialchars($f['block']) ?></td>
          <td><?= $f['area_sqft'] ?></td>
          <td class="action">
            <?php if($role==='admin' || $role==='supervisor'){ ?>
              <a class="delete" href="?delete=<?= $f['id']?>" onclick="return confirm('Delete flat?')">Delete</a>
            <?php } ?>
          </td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
  </div>
</div>
