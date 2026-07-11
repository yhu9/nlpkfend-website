<!DOCTYPE HMTL>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>

<body>
<h1>DATA VIEW</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../homepage.php">Homepage</a></span>
                <span ><a class="button" href="log.php">Log Info</a></span>
                <span ><a class="button" href="search/searchLog_page.php">Search Log</a></span>
                <span ><a class="button" href="../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

<?php
//connect to database
include("../config.php");
$db = connect();
checkAdvancedSession(3);

//Create the query
include("queries.php");

//get the log Data basic query
$table = $_POST['table'];
$pk = $_POST['pk_field'];
$id = $_POST['id'];

//Show the results
echo "<div class='datahandler'>";
showDetailedData($db,$table,$id);
echo "</div>";

////////////////////////////////////////////////////////////////////////////
if (isset($result) && $result instanceof mysqli_result) $result->free();
$db->close();

?>

</body>
</html> 
