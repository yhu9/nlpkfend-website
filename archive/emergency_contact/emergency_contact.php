<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Emergency_Contact Table</h1>
        <a href="../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../homepage.php">Homepage</a></span>
                <span ><a class="button" href="add/addEmergency_Contact_page.php">Add Emergency_Contact</a></span>
                <span ><a class="button" href="delete/deleteEmergency_Contact_page.php">Delete Emergency_Contact</a></span>
                <span ><a class="button" href="update/updateEmergency_Contact_page.php">Update Emergency_Contact</a></span>
                <span ><a class="button" href="search/searchEmergency_Contact_page.php">Search Emergency_Contact</a></span>
                <span ><a class="button" href="../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

<?php
include("../config.php");
$db = connect();
checkAdvancedSession(3);

//Create the query
include("queries.php");
$sql = emergency_contact_basic();
$result = mysqli_query($db,$sql);
$finfo = $result->fetch_fields();

//Show the query
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
if($result !== false){
    $num_rows = ($result instanceof mysqli_result ? mysqli_num_rows($result) : 0);
    echo "<b>Records Found: $num_rows<b>";
echo "<table class='data' align=\"center\">";
echo "<tr>";
foreach ($finfo as $field){
	echo "<th>". $field->name ."</th>";
}
echo "</tr>";
$i=0;
while($row = mysqli_fetch_array($result)){
    echo "<tr>";
    for($i=0; $i < mysqli_num_fields($result); $i++){
        echo "<td>" . $row[$finfo[$i]->name] ."</td>";
    }
    echo "</tr>";
    if($i > 1000)
    $i++;
}
echo "</table>";
}
else{
    echo "Query: $sql<br>\n";
    echo "Error accessing data: ".mysqli_error($db) ."<br>\n";
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
if (isset($result) && $result instanceof mysqli_result) $result->free();
$db->close();
?>

</body>
</html> 
