<?php
session_start();
if (!isset($_SESSION['is_admin'])) {
  header("Location: admin_login.php");
  exit;
}
include '../backend/db.php';

$search = "";
if (isset($_GET['search'])) {
  $search = trim($_GET['search']);
  $likeSearch = "%" . $search . "%";
  $stmt = $conn->prepare("SELECT id, name, email, created_at FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC");
  $stmt->bind_param("ss", $likeSearch, $likeSearch);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $result = $conn->query("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Users - Admin Panel</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <h2 class="centered">All Registered Users</h2>

  <div class="admin-links centered">
    <a href="dashboard.php">All Bookings</a> |
    <a href="users.php">Users</a> |
    <a href="services.php">Services</a> |
    <a href="logout.php">Logout</a>
  </div>

  <div class="centered" style="margin-bottom: 20px;">
    <form method="GET" action="users.php">
      <input type="text" name="search" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>" />
      <button type="submit">Search</button>
    </form>
  </div>

  <div class="booking-table">
    <table>
      <thead>
        <tr>
          <th>User ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Joined</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result->num_rows > 0) {
          while ($user = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$user['id']}</td>
                    <td>" . htmlspecialchars($user['name']) . "</td>
                    <td>" . htmlspecialchars($user['email']) . "</td>
                    <td>{$user['created_at']}</td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='4'>No users found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
