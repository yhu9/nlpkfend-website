

<?php
include_once("../config.php");
$conn = connect();
$id = $_POST['aid'];
$note = $_POST['note'];

$sql_query = "UPDATE Account SET note = '$note' WHERE accountID = $id";
mysqli_query($conn,$sql_query);

echo "NOTE UPDATED FOR ACCOUNT $id";

return false;
?>
