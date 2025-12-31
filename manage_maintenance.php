<?php
require 'db_connect.php';
if(!isset($_SESSION['username'])) header("Location: login.php");
$role = $_SESSION['role'];
if($role!=='admin' && $role!=='supervisor' && $role!=='resident') die('Access denied');

// Add maintenance record (supervisor/admin)
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_maintenance']) && ($role==='admin' || $role==='supervisor')){
    $flat_id = (int)$_POST['flat_id'];
    $amount = (float)$_POST['amount'];
    $due_date = $_POST['due_date'] ? "'".$conn->real_escape_string($_POST['due_date'])."'" : 'NULL';
    $paid = isset($_POST['paid']) ? 1 : 0;
    $mode = $conn->real_escape_string($_POST['payment_mode']);
    $paid_date = $paid ? "NOW()" : "NULL";
    $conn->query("INSERT INTO maintenance (flat_id,amount,due_date,paid,paid_date,payment_mode) VALUES ($flat_id,$amount,$due_date,$paid,$paid_date,'$mode')");
}

// Delete
if(($role==='admin' || $role==='supervisor') && isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM maintenance WHERE id=$id");
}

$flats = $conn->query("SELECT * FROM flats");
$records = $conn->query("SELECT m.*, f.flat_number FROM maintenance m LEFT JOIN flats f ON m.flat_id=f.id ORDER BY m.id DESC");
?>
<link rel="stylesheet" href="style.css">
<div class="header"><div class="brand">Maintenance</div><div><a class="logout" href="dashboard.php">Back</a></div></div>
<div class="container">
  <?php if($role==='admin' || $role==='supervisor'){ ?>
  <div class="card">
    <form method="post">
      <div class="form-row">
        <select name="flat_id" required>
          <option value="">Select flat</option>
          <?php while($f=$flats->fetch_assoc()){ ?>
            <option value="<?=$f['id']?>"><?=htmlspecialchars($f['flat_number'])?></option>
          <?php } ?>
        </select>
        <input name="amount" type="number" step="0.01" placeholder="Amount" required>
        <input name="due_date" type="date" placeholder="Due date">
        <input name="payment_mode" placeholder="Payment mode">
        <label style="display:flex;align-items:center;gap:8px"><input type="checkbox" name="paid"> Mark Paid</label>
      </div>
      <button class="btn" name="add_maintenance" type="submit">➕ Add Record</button>
    </form>
  </div>
  <?php } ?>

  <div class="table-wrap">
    <table>
      <thead><tr><th>ID</th><th>Flat</th><th>Amount</th><th>Due Date</th><th>Paid</th><th>Paid Date</th><th>Mode</th><th>Action</th></tr></thead>
      <tbody>
        <?php while($m=$records->fetch_assoc()){ ?>
        <tr>
          <td><?= $m['id'] ?></td>
          <td><?= htmlspecialchars($m['flat_number']) ?></td>
          <td><?= $m['amount'] ?></td>
          <td><?= $m['due_date'] ?></td>
          <td><?= $m['paid'] ? 'Yes':'No' ?></td>
          <td><?= $m['paid_date'] ?></td>
          <td><?= htmlspecialchars($m['payment_mode']) ?></td>
          <td class="action">
            <?php if($role==='admin' || $role==='supervisor'){ ?>
              <a class="delete" href="?delete=<?= $m['id'] ?>" onclick="return confirm('Delete maintenance record?')">Delete</a>
            <?php } ?>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
