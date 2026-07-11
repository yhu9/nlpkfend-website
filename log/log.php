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
<h1>Log Information</h1>
</div>

<div class="menu_color">
<hr>
    <div class="menu">
        <span ><a class="button" href="../homepage.php">Homepage</a></span>
        <span ><a class="button" href="log.php">Log Info</a></span>
        <span ><a class="button" href="search/searchLog_page.php">Search Log</a></span>
        <span ><a class="button" href="../logout.php">Logout</a></span>
    </div>
<hr>
<div class='sorter'>

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

//Show the results
$num_rows = count($logData['data'] ?? []);

//show 1000 most recent logs
showLogBasic($db);

////////////////////////////////////////////////////////////////////////////
if (isset($result) && $result instanceof mysqli_result) $result->free();
$db->close();

?>

</body>
</html> 
