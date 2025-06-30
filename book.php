<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  echo "You must be logged in to book.";
  exit;
}

$user_id = $_SESSION['user_id'];
$service = $_POST['service'];
$date = $_POST['date'];
$time = $_POST['time'];
$staff = $_POST['staff'];

$stmt = $conn->prepare("INSERT INTO bookings (user_id, service, date, time, staff_name) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $user_id, $service, $date, $time, $staff);

if ($stmt->execute()) {
  echo "Booking successful! <a href="backend/history.php">View your bookings</a>
";
} else {
  echo "Booking failed: " . $stmt->error;
}
?>
