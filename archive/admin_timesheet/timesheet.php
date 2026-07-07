<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Punch Table</h1>
        <a href="../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../homepage.php">Homepage</a></span>
                <span ><a class="button" href="">Punch Table</a></span>
                <span ><a class="button" href="add/addPunch_page.php">Add Punch</a></span>
                <span ><a class="button" href="delete/deletePunch_page.php">Delete Punch</a></span>
                <span ><a class="button" href="update/updatePunch_page.php">Update Punch</a></span>
                <span ><a class="button" href="search/searchPunch_page.php">Search Punch</a></span>
                <span ><a class="button" href="../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

<?php
include("../config.php");
$db = connect();
checkAdvancedSession(3);

//Create the query
include("./queries.php");
$sql = punch_basic();
$result = mysqli_query($db,$sql);
$finfo = $result->fetch_fields();

//Show the query
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($result !== false){

    $data = array();
    while($row = mysqli_fetch_array($result))
        $data[] = $row;

    showData($data,$finfo);

    echo "<h3>This page shows last two months</h3>";
    echo "<b>Records Found: $num_rows<br>";
    $num_rows = mysqli_num_rows($result);
}else{
    echo "Query: $sql<br>\n";
    echo "Error accessing data: ".mysqli_error($db) ."<br>\n";
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
$result->free();
$db->close();
?>

</body>
</html> 
