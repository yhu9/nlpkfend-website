<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    <script type='text/javascript'>
        function contentChanger(){
            var contentname = document.getElementById('mySelect').value;
            var y = document.getElementById(contentname).innerHTML;
            document.getElementById("content").innerHTML = "" + y;
        }
    </script>
</head>
<body>
<h1>Schedule Table</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../homepage.php">Homepage</a></span>
                <span ><a class="button" href="schedule.php">Schedule Table</a></span>
                <span ><a class="button" href="add/addSchedule_page.php">Add Schedule</a></span>
                <span ><a class="button" href="search/searchSchedule_page.php">Search Schedule</a></span>
                <span ><a class="button" href="../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>


<select class='selectpicker' id="mySelect" style='width:10%; font-size:15px;' onChange="contentChanger()">
    <option value='today' selected>Today
    <option value='showSunday'>Sunday
    <option value='showMonday'>Monday
    <option value='showTuesday'>Tuesday
    <option value='showWednesday'>Wednesday
    <option value='showThursday'>Thursday
    <option value='showFriday'>Friday
    <option value='showSaturday'>Saturday
</select>

<p id="content"></p>

<?php

include("../config.php");
$db = connect();
checkAdvancedSession(3);

//include the query
include("queries.php");

//Show the results
echo "<div id='today' hidden>";
showSchedule($db,'today');
echo "</div>";

//Show the results
echo "<div id='showSunday' hidden>";
showSchedule($db,'sun');
echo "</div>";

//Show the results
echo "<div id='showMonday' hidden>";
showSchedule($db,'mon');
echo "</div>";

//Show the results
echo "<div id='showTuesday' hidden>";
showSchedule($db,'tue');
echo "</div>";

//Show the results
echo "<div id='showWednesday' hidden>";
showSchedule($db,'wed');
echo "</div>";

//Show the results
echo "<div id='showThursday' hidden>";
showSchedule($db,'thu');
echo "</div>";

//Show the results
echo "<div id='showFriday' hidden>";
showSchedule($db,'fri');
echo "</div>";

//Show the results
echo "<div id='showSaturday' hidden>";
showSchedule($db,'sat');
echo "</div>";

echo "<script type='text/javascript'>
    var x = document.getElementById('today').innerHTML;
    document.getElementById(\"content\").innerHTML = \"\" + x;
</script>";

////////////////////////////////////////////////////////////////////////////
$result->free();
$db->close();

?>

</body>
</html> 
