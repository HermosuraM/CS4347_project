<?php
session_start(); include 'db.php';
$u=$_POST['username'];
$r=$conn->query("SELECT * FROM USER WHERE username='$u'");
if($r->num_rows>0){
 $_SESSION['user']=$u;
 if($u=='admin'){ header("Location: admin_panel.php"); }
 else{ header("Location: user_dashboard.php"); }
}else echo "Invalid";
?>