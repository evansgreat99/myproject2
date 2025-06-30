<?php
session_start();
include 'db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
  echo "Please <a href='login.html'>login</a> to view your bookings.";
  exit;
}

$user_id = $_SESSION['user_id'];

// Fetch bookings
$stmt = $conn->prepare("SELECT service, date, time, staff_name, status FROM bookings WHERE user_id = ? ORDER BY date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Bookings - Serenity Spa</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <h2 class="centered">Your Booking History</h2>

  <div class="booking-table">
    <table>
      <thead>
        <tr>
          <th>Service</th>
          <th>Date</th>
          <th>Time</th>
          <th>Staff</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td><?= htmlspecialchars($row['service']) ?></td>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td><?= htmlspecialchars(date("g:i A", strtotime($row['time']))) ?></td>
            <td><?= htmlspecialchars($row['staff_name']) ?: "Any" ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
