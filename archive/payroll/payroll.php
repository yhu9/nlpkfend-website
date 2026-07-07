<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Payroll Table</h1>
        <a href="../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../homepage.php">Homepage</a></span>
                <span ><a class="button" href="add/addPayroll_page.php">Add Payroll</a></span>
                <span ><a class="button" href="delete/deletePayroll_page.php">Delete Payroll</a></span>
                <span ><a class="button" href="update/updatePayroll_page.php">Update Payroll</a></span>
                <span ><a class="button" href="search/searchPayroll_page.php">Search Payroll</a></span>
                <span ><a class="button" href="../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <br>Note: This shows the last 6 months<br>
<?php
include("../config.php");
$db = connect();
checkAdvancedSession(3);

//Create the query
include("queries.php");
$sql = payroll_basic();
$result = mysqli_query($db,$sql);
$finfo = $result->fetch_fields();

//Show the query
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
if($result !== false){
    $num_rows = mysqli_num_rows($result);
    echo "<br><b>Records Found: $num_rows<b>";
    echo "<table class='data' align='center'>";
    echo "<tr>";
    foreach ($finfo as $field){
        echo "<th>". $field->name ."</th>\n";
    }
    echo "</tr>";
    $i=0;
    while($row = mysqli_fetch_array($result)){
        echo "<tr>";
        for($i=0; $i < mysqli_num_fields($result); $i++){
            echo "<td>" . $row[$finfo[$i]->name] ."</td>\n";
        }
        echo "</tr>\n";
        if($i > 1000)
        $i++;
    }
    echo "</table>";
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
