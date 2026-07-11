<!DOCTYPE HMTL>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    <link rel="stylesheet" type="text/css" href="print.css">
    <script type='text/javascript' src="/js/js_main.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
<script>
$(document).ready(function () {
  $('select').each(function () {
    var select = $(this);
    var selectedValue = select.find('option[selected]').val();

    if (selectedValue) {
      select.val(selectedValue);
    } else {
      select.prop('selectedIndex', 0);
    }
  });
});
</script>
<div class='title'>
<h1>Employee Information</h1>
</div>

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
<div class='sorter'>
<b>Sort By: </b>
<select class='selectpicker' id="mySelect" style='width:10%; font-size:15px;' onChange="contentChanger()">
    <option value='showActive' selected>All Active Employees
    <option value='showAll'>All Employees
    <option value='showActive'>All Active Employees
    <option value='showInactive'> All Inactive Employees
</select>
</div>
</div>

<p id="content"></p>

<?php
//connect to database
include("../config.php");
$db = connect();
checkAdvancedSession(3);

//Create the query
include("queries.php");

//get the employee Data basic query
$employeeData = getEmployeeBasic($db);
$employeeActiveData = getEmployeeActive($db);
$employeeInactiveData = getEmployeeInactive($db);

//Show the results
$num_rows = count($employeeData['data']);

//keep the content hidden and pick and choose which one you want.
echo "<div id='showAll' hidden>";
showAdvancedData($employeeData['data'],$employeeData['fields'],'','viewDetails.php');
echo "</div>";
echo "<div id='showInactive' hidden>";
showAdvancedData($employeeInactiveData['data'],$employeeInactiveData['fields'],'','viewDetails.php');
echo "</div>";
echo "<div id='showActive' hidden>";
showAdvancedData($employeeActiveData['data'],$employeeActiveData['fields'],'','viewDetails.php');
echo "</div>";

echo "<script type='text/javascript'>
    var x = document.getElementById('showActive').innerHTML;
    document.getElementById(\"content\").innerHTML = \"\" + x;
</script>";
////////////////////////////////////////////////////////////////////////////
if (isset($result) && $result instanceof mysqli_result) $result->free();
$db->close();

?>

</body>
</html> 
