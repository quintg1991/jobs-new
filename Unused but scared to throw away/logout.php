<?php
  session_start();
  unset($_SESSION['user_id']);
  unset($_SESSION['role']);
  unset($_SESSION['initials']);
  header('Location: login.php');

?>