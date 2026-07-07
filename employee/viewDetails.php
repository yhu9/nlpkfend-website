<!DOCTYPE HMTL>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    <script type='text/javascript' src="/js/js_main.js"></script>
</head>

<body>
<h1>Detailed Employee View</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../homepage.php">Homepage</a></span>
                <span ><a class="button" href="employee.php">Employee Info</a></span>
                <span ><a class="button" href="add/addEmployee_page.php">Add Employee</a></span>
                <span ><a class="button" href="search/searchEmployee_page.php">Search Employee</a></span>
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

//get the employee Data basic query
$id = $_POST['id'];
$employeeData = getEmployeeByID($db,$id);

//Show the results
echo "<div class='datahandler'>";
showDetailedData($db,$id);
echo "</div>";

////////////////////////////////////////////////////////////////////////////
$result->free();
$db->close();

?>

</body>
</html> 
