<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="/mystyle.css">
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
<h1>Student Information</h1>
</div>
        <div class="menu_color">
            <hr>
            <div class="menu">
                <span ><a class="button" href="../homepage.php">Homepage</a></span>
                <span ><a class="button" href="student.php">Student Info</a></span>
                <span ><a class="button" href="cca/cca.php">Contracts/Authorizations</a></span>
                <span ><a class="button" href="add/addStudent_page.php">Add Student</a></span>
                <span ><a class="button" href="search/searchStudent_page.php">Search Student</a></span>
                <span ><a class="button" href="../logout.php">Logout</a></span>
            </div>
            <hr>

            <div class='sorter'>
            <b>Sort By: </b>
            <select class='selectpicker' id="mySelect" style='width:20%; font-size:15px;' onChange="contentChanger()">
                <option value='showAll'>Inactive + Active
                <!--option value='showRoom'>Room Order-->
                <option value='showActive' selected>Last Name
                <option value='showInactive'>Inactive Students
                <option value='showPhysical'>Physicals Due This Month
                <option value='showAuthorization'>Authorizations Due
                <option value='showAllergies'>Allergy List
            </select>
            </div>
        </div>

<div class='content'>
<form method='POST'>
<p id="content"></p>
</form>
</div>

<?php
include("/var/www/html/config.php");
$db = connect();
checkAdvancedSession(3);

//include the query
include("queries.php");

//get the student Data basic query
$studentData = getStudentBasic($db);
//$studentByRoom = getStudentByRoom($db);
$studentAllData = getStudentsAll($db);
$studentInactiveData = getStudentInactive($db);
$studentPhysicalData = getExpiredPhysical($db);
$studentAllergies = getAllergies($db);

//Show the results
$num_rows = count($studentData['data'] ?? []);
$total_students = count($studentAllData['data'] ?? []);

if($total_students == 0){
    //The Student table is empty (e.g. a fresh database). Show a friendly notice
    //in every "Sort By" view instead of the generic renderer error, and prompt
    //the user to add a student.
    $notice = "<h2>There are no students in the Student table.</h2>\n"
            . "<p>Please <a href='add/addStudent_page.php'>add a student</a> to get started.</p>\n";
    foreach(array('showActive','showRoom','showAll','showPhysical','showInactive','showAllergies','showAuthorization') as $view){
        echo "<div id='$view' hidden>$notice</div>\n";
    }
}else{
    echo "<div id='showActive' hidden>";
    showAdvancedData($studentData['data'],$studentData['fields'],"Active Students","/account/viewDetails.php");

    echo "</div>";
    echo "<div id='showRoom' hidden>";
    //showAdvancedData($studentByRoom['data'],$studentByRoom['fields'],"Room Order","/account/viewDetails.php");
    echo "</div>";

    //echo "<div id='showAll' hidden>";
    //showAdvancedData($studentAllData['data'],$studentAllData['fields'],"All Students","/account/viewDetails.php");
    //echo "</div>";

    echo "<div id='showPhysical' hidden>";
    showAdvancedData($studentPhysicalData['data'],$studentPhysicalData['fields'],"Physicals Due This Month","/account/viewDetails.php");

    echo "</div>";
    echo "<div id='showInactive' hidden>";
    showAdvancedData($studentInactiveData['data'],$studentInactiveData['fields'],"Inactive Students","/account/viewDetails.php");
    echo "</div>";
    echo "<div id='showAllergies' hidden>";
    showAdvancedData($studentAllergies['data'],$studentAllergies['fields'],"Allergy List","/account/viewDetails.php");
    echo "</div>";

    echo "<div id='showAuthorization' hidden>";
    showLateAuthorization($db);
    echo "</div>";
}

echo "<script type='text/javascript'>
    var x = document.getElementById('showActive').innerHTML;
    document.getElementById(\"content\").innerHTML = \"\" + x;
    </script>";
////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////

$db->close();

?>

</body>
</html> 
