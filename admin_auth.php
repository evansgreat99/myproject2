<?php
session_start();
include '../backend/db.php';

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, password FROM admin_users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $admin = $result->fetch_assoc();
  if (password_verify($password, $admin['password'])) {
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['is_admin'] = true;
    header("Location: dashboard.php");
    exit;
  }
}
echo "Invalid login.";
?>
