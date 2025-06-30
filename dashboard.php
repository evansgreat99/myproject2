<?php
session_start();
if (!isset($_SESSION['is_admin'])) {
  header("Location: admin_login.php");
  exit;
}
include '../backend/db.php';

// Search logic
$search = "";
$bookings = [];

if (isset($_GET['search'])) {
  $search = trim($_GET['search']);
  $like = "%" . $search . "%";
  $stmt = $conn->prepare("
    SELECT b.*, u.name AS user_name 
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    WHERE u.name LIKE ? OR b.service LIKE ? OR b.staff_name LIKE ?
    ORDER BY b.date DESC
  ");
  $stmt->bind_param("sss", $like, $like, $like);
  $stmt->execute();
  $bookings = $stmt->get_result();
} else {
  $bookings = $conn->query("
    SELECT b.*, u.name AS user_name 
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    ORDER BY b.date DESC
  ");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Bookings</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <h2 class="centered">Admin Dashboard – All Bookings</h2>

  <div class="admin-links centered">
    <a href="dashboard.php">All Bookings</a> |
    <a href="users.php">Users</a> |
    <a href="services.php">Services</a> |
    <a href="logout.php">Logout</a>
  </div>

  <div class="centered" style="margin-bottom: 20px;">
    <form method="GET" action="dashboard.php">
      <input type="text" name="search" placeholder="Search by user, service or staff" value="<?= htmlspecialchars($search) ?>" />
      <button type="submit">Search</button>
    </form>
  </div>

  <div class="booking-table">
    <table>
      <thead>
        <tr>
          <th>User</th>
          <th>Service</th>
          <th>Date</th>
          <th>Time</th>
          <th>Staff</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($bookings->num_rows > 0) {
          while ($row = $bookings->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['user_name']) . "</td>
                    <td>" . htmlspecialchars($row['service']) . "</td>
                    <td>" . $row['date'] . "</td>
                    <td>" . date("g:i A", strtotime($row['time'])) . "</td>
                    <td>" . ($row['staff_name'] ?: 'Any') . "</td>
                    <td>" . $row['status'] . "</td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='6'>No bookings found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
