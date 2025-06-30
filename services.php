<?php
session_start();
if (!isset($_SESSION['is_admin'])) {
  header("Location: admin_login.php");
  exit;
}
include '../backend/db.php';

// Add service
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'], $_POST['price'])) {
  $name = trim($_POST['name']);
  $description = trim($_POST['description']);
  $price = floatval($_POST['price']);

  $stmt = $conn->prepare("INSERT INTO services (name, description, price) VALUES (?, ?, ?)");
  $stmt->bind_param("ssd", $name, $description, $price);
  $stmt->execute();
  header("Location: services.php");
  exit;
}

// Delete service
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $conn->query("DELETE FROM services WHERE id = $id");
  header("Location: services.php");
  exit;
}

// Fetch all services
$services = $conn->query("SELECT * FROM services ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Services - Admin Panel</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <h2 class="centered">Manage Spa Services</h2>

  <div class="admin-links centered">
    <a href="dashboard.php">All Bookings</a> |
    <a href="users.php">Users</a> |
    <a href="services.php">Services</a> |
    <a href="logout.php">Logout</a>
  </div>

  <div class="form-container" style="max-width: 500px;">
    <h3>Add New Service</h3>
    <form method="POST">
      <input type="text" name="name" placeholder="Service Name" required><br>
      <textarea name="description" placeholder="Description" rows="3"></textarea><br>
      <input type="number" name="price" placeholder="Price (e.g., 50.00)" step="0.01" required><br>
      <button type="submit">Add Service</button>
    </form>
  </div>

  <h3 class="centered">Available Services</h3>
  <div class="booking-table">
    <table>
      <thead>
        <tr>
          <th>Service</th>
          <th>Description</th>
          <th>Price</th>
          <th>Added On</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        while ($row = $services->fetch_assoc()) {
          echo "<tr>
                  <td>" . htmlspecialchars($row['name']) . "</td>
                  <td>" . htmlspecialchars($row['description']) . "</td>
                  <td>KES " . number_format($row['price'], 2) . "</td>
                  <td>" . $row['created_at'] . "</td>
                  <td><a href='services.php?delete=" . $row['id'] . "' onclick=\"return confirm('Delete this service?')\">Delete</a></td>
                </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
