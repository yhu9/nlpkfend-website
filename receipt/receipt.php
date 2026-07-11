<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    <script type='text/javascript' src="/js/js_main.js"></script>
</head>
<body>
<h1>Receipts By Most Recent</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../homepage.php">Homepage</a></span>
                <span ><a class="button" href="receipt.php">Receipts</a></span>
                <span ><a class="button" href="search/searchReceipt_page.php">Search Receipt</a></span>
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
$data = receipt_basic($db);

//Show the query
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$num_rows = ($result instanceof mysqli_result ? mysqli_num_rows($result) : 0);
echo "<div class='datahandler'>";
showDataWithLimit2($data['data'],$data['fields'],1000);
echo "</div>";
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
if (isset($result) && $result instanceof mysqli_result) $result->free();
$db->close();
?>

</body>
</html> 
