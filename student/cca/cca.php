<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../../mystyle.css">
    <link rel="stylesheet" type="text/css" href="../print.css">
    <script type='text/javascript' src="/js/js_main.js"></script>
</head>
<body>
<div class='title'>
<h1>Child Care Contracts/Authorizations</h1>
</div>

<div class="menu_color">
<hr>
    <div class="menu">
        <span ><a class="button" href="../../homepage.php">Homepage</a></span>
        <span ><a class="button" href="../student.php">Student Info</a></span>
        <span ><a class="button" href="cca.php">Contracts/Authorizations</a></span>
        <span ><a class="button" href="add/addCCA_page.php">Add Contract</a></span>
        <span ><a class="button" href="search/searchCCA_page.php">Search Contract</a></span>
        <span ><a class="button" href="../../logout.php">Logout</a></span>
    </div>
<hr>
    <div class='sorter'>
    <b>Sort By: </b>
    <select class='selectpicker' id="mySelect" style='width:10%; font-size:15px;' onChange="contentChanger()">
        <option value='' selected>All Current Contracts
        <option value='showCurrent'>All Current Contracts
        <option value='showFuture'> All Future Contracts
        <option value='showExpired'> Expiring Contracts
        <option value='showInactive'> All Inactive Students
    </select>
    </div>
</div>

<p id="content"></p>

<?php
include("../../config.php");
$db = connect();
checkAdvancedSession(3);

//include the query
include("queries.php");

//get the cca Data basic query
$currentCCAData = getCurrentCCA($db);
$futureCCAData = getFutureCCA($db);
$inactiveFutureData = getCCAInactive($db);
$expireData = queryExpiringAuthorization($db);

//Show the results
$num_rows = count($currentCCAData['data']);
echo "<div id='showCurrent' hidden>";
showCCAData($currentCCAData['data'],$currentCCAData['fields']);
echo "</div>";

echo "<div id='showFuture' hidden>";
showCCAData($futureCCAData['data'],$futureCCAData['fields']);
echo "</div>";

echo "<div id='showInactive' hidden>";
showCCAData($inactiveFutureData['data'],$inactiveFutureData['fields']);
echo "</div>";

echo "<div id='showExpired' hidden>";
showCCAData($expireData['data'],$expireData['fields']);
echo "</div>";

echo "<script type='text/javascript'>
    var x = document.getElementById('showCurrent').innerHTML;
    document.getElementById(\"content\").innerHTML = \"\" + x;
</script>";

////////////////////////////////////////////////////////////////////////////
$result->free();
$db->close();

?>

</body>
</html> 
