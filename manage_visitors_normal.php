<?php
require 'db_connect.php';
if(!isset($_SESSION['username'])) header("Location: login.php");
$role = $_SESSION['role'];
// Supervisor can add; Resident can approve/reject; Admin can view/delete

// Add normal visitor (supervisor)
if($role==='supervisor' && $_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_visitor'])){
    $name = $conn->real_escape_string($_POST['visitor_name']);
    $phone = $conn->real_escape_string($_POST['visitor_phone']);
    $purpose = $conn->real_escape_string($_POST['purpose']);
    $flat_id = (int)$_POST['flat_id'];
    $conn->query("INSERT INTO visitors_normal (visitor_name,visitor_phone,purpose,flat_id) VALUES ('$name','$phone','$purpose',$flat_id)");
}

// Approve/reject by resident
if($role==='resident' && isset($_GET['approve'])){
    $id = (int)$_GET['approve'];
    $uid = (int)$_SESSION['id'];
    $conn->query("UPDATE visitors_normal SET status='approved', approved_by_user_id=$uid, approved_at=NOW() WHERE id=$id");
}
if($role==='resident' && isset($_GET['reject'])){
    $id = (int)$_GET['reject'];
    $uid = (int)$_SESSION['id'];
    $conn->query("UPDATE visitors_normal SET status='rejected', approved_by_user_id=$uid, approved_at=NOW() WHERE id=$id");
}

// Admin/Supervisor delete
if(($role==='admin' || $role==='supervisor') && isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM visitors_normal WHERE id=$id");
}

$flats = $conn->query("SELECT * FROM flats");
$vis = $conn->query("SELECT v.*, f.flat_number FROM visitors_normal v LEFT JOIN flats f ON v.flat_id=f.id ORDER BY v.id DESC");
?>
<link rel="stylesheet" href="style.css">
<div class="header"><div class="brand">Normal Visitors</div><div><a class="logout" href="dashboard.php">Back</a></div></div>
<div class="container">
  <?php if($role==='supervisor'){ ?>
  <div class="card">
    <form method="post">
      <div class="form-row">
        <input name="visitor_name" placeholder="Visitor name" required>
        <input name="visitor_phone" placeholder="Phone">
        <select name="flat_id" required>
          <option value="">Assign to flat</option>
          <?php while($f=$flats->fetch_assoc()){ ?>
            <option value="<?=$f['id']?>"><?=htmlspecialchars($f['flat_number'])?></option>
          <?php } ?>
        </select>
        <input name="purpose" placeholder="Purpose">
      </div>
      <button class="btn" name="add_visitor" type="submit">➕ Add Visitor</button>
    </form>
  </div>
  <?php } ?>

  <div class="table-wrap">
    <table>
      <thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Purpose</th><th>Flat</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php while($v=$vis->fetch_assoc()){ ?>
        <tr>
          <td><?= $v['id'] ?></td>
          <td><?= htmlspecialchars($v['visitor_name']) ?></td>
          <td><?= htmlspecialchars($v['visitor_phone']) ?></td>
          <td><?= htmlspecialchars($v['purpose']) ?></td>
          <td><?= htmlspecialchars($v['flat_number']) ?></td>
          <td><?= $v['status'] ?></td>
          <td class="action">
            <?php if($role==='resident' && $v['status']==='pending' && $_SESSION['resident_id']){ 
                 // resident can approve only for their own flat (enforce)
                 // fetch resident's flat
                 $resr = $conn->query("SELECT flat_id FROM residents WHERE id=".(int)$_SESSION['resident_id'])->fetch_assoc();
                 $myflat = $resr ? (int)$resr['flat_id'] : 0;
                 if($myflat === (int)$v['flat_id']){ ?>
                   <a class="edit" href="?approve=<?= $v['id'] ?>">Approve</a>
                   <a class="delete" href="?reject=<?= $v['id'] ?>">Reject</a>
            <?php } } ?>
            <?php if($role==='admin' || $role==='supervisor'){ ?>
              <a class="delete" href="?delete=<?= $v['id']?>" onclick="return confirm('Delete visitor?')">Delete</a>
            <?php } ?>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
