
<?php
include('db_connect.php');
$yg_content = $_GET['yg'];
$sql = "INSERT INTO `hlb_yeargroup` (`yg_crsid`, `yg_yeargroup`, `yg_id`) VALUES ('".$_SERVER['REMOTE_USER']."','".$yg_content."',NULL)";
$res = $con_sbr->query($sql);
header('Location: ../');
?>