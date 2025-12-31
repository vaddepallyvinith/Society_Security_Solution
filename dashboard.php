<?php
// dashboard.php - improved header handling and robust include
// Use require_once so missing db_connect.php is obvious
require_once __DIR__ . '/db_connect.php';

// Ensure a session is active (db_connect may already call session_start)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard - Society Security</title>
<style>
/* (same CSS as before) */
* { margin:0; padding:0; box-sizing:border-box; font-family:"Poppins",sans-serif; }
body { background: linear-gradient(135deg,#74ebd5,#ACB6E5); min-height:100vh; color:#333; }
.header { background: rgba(255,255,255,0.2); padding:20px 30px; display:flex; justify-content:space-between; align-items:center; backdrop-filter: blur(10px); box-shadow:0 5px 15px rgba(0,0,0,0.15); }
.header h1 { font-size:28px; color:#fff; letter-spacing:1px; }
.header .welcome { font-size:16px; color:#f8f8f8; }
.logout { background: linear-gradient(45deg,#ff416c,#ff4b2b); color:white; padding:8px 16px; border-radius:8px; text-decoration:none; font-weight:500; transition:0.3s; }
.logout:hover { background: linear-gradient(45deg,#ff9966,#ff5e62); box-shadow:0 4px 12px rgba(255,255,255,0.3); }
.role-box { text-align:center; padding:15px; color:#fff; font-weight:500; background:rgba(0,0,0,0.25); border-radius:10px; margin:20px auto; width:fit-content; backdrop-filter: blur(8px); }
.dashboard-container { display:grid; grid-template-columns:repeat(auto-fit,minmax(230px,1fr)); gap:25px; padding:40px; }
.card { background:white; border-radius:20px; padding:25px; text-align:center; box-shadow:0 6px 20px rgba(0,0,0,0.1); transition:0.3s; cursor:pointer; }
.card:hover { transform:translateY(-5px); background: linear-gradient(135deg,#667eea,#764ba2); color:white; }
.card h3 { margin-top:10px; font-size:18px; }
.card img { width:70px; height:70px; margin-bottom:10px; border-radius:50%; transition:transform 0.3s; }
.card:hover img { transform:scale(1.1); }
</style>
</head>
<body>

<div class="header">
  <div>
    <h1>🏙️ Society Security Dashboard</h1>
    <div class="welcome">Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></div>
  </div>
  <a href="logout.php" class="logout">Logout</a>
</div>

<div class="role-box">Role: <?= htmlspecialchars(ucfirst($_SESSION['role'] ?? '')) ?></div>

<div class="dashboard-container">
<?php if(($_SESSION['role'] ?? '') === 'admin'): ?>
  <a href="manage_buildings.php" class="card">
    <img src="https://cdn-icons-png.flaticon.com/512/888/888879.png" alt="buildings">
    <h3>Manage Buildings</h3>
  </a>
  <a href="manage_flats.php" class="card">
    <img src="https://cdn-icons-png.flaticon.com/512/3917/3917038.png" alt="flats">
    <h3>Manage Flats</h3>
  </a>
  <a href="manage_residents.php" class="card">
    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="residents">
    <h3>Manage Residents</h3>
  </a>
  <a href="manage_staff.php" class="card">
    <img src="https://cdn-icons-png.flaticon.com/512/1995/1995574.png" alt="staff">
    <h3>Manage Staff</h3>
  </a>
  <a href="manage_maintenance.php" class="card">
    <img src="https://cdn-icons-png.flaticon.com/512/1250/1250689.png" alt="maintenance">
    <h3>Maintenance Records</h3>
  </a>

<?php elseif(($_SESSION['role'] ?? '') === 'supervisor'): ?>
  <a href="manage_flats.php" class="card">
    <img src="https://cdn-icons-png.flaticon.com/512/3582/3582313.png" alt="flats">
    <h3>View & Manage Flats</h3>
  </a>
  <a href="manage_visitors_normal.php" class="card">
    <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="normal visitors">
    <h3>Normal Visitors</h3>
  </a>
  <a href="manage_visitors_regular.php" class="card">
    <img src="https://cdn-icons-png.flaticon.com/512/888/888853.png" alt="regular visitors">
    <h3>Regular Visitors</h3>
  </a>
  <a href="manage_staff.php" class="card">
    <img src="https://cdn-icons-png.flaticon.com/512/619/619175.png" alt="staff">
    <h3>Manage Staff</h3>
  </a>

<?php else: /* resident */ ?>
  <a href="manage_visitors_normal.php" class="card">
    <img src="https://cdn-icons-png.flaticon.com/512/3595/3595455.png" alt="approve visitors">
    <h3>Approve/Reject Visitors</h3>
  </a>
  <a href="manage_maintenance.php" class="card">
    <img src="https://cdn-icons-png.flaticon.com/512/1250/1250689.png" alt="maintenance">
    <h3>Check Maintenance</h3>
  </a>
<?php endif; ?>
</div>

</body>
</html>
