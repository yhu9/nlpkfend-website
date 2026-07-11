<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    <link rel="stylesheet" type="text/css" href="print.css">
    <script type='text/javascript' src="/js/js_main.js"></script>
</head>
<body>
<h1>All Charges</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../account.php">Account Info</a></span>
                <span ><a class="button" href="charge.php">All Charges</a></span>
                <span ><a class="button" href="search/searchCharge_page.php">Search Charge</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

<?php
include("../../config.php");
$db = connect();
checkAdvancedSession(3);

//Create the query
include("queries.php");
$sql = charge_basic();
$result = mysqli_query($db,$sql);
$finfo = $result->fetch_fields();

//Show the query
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($result !== false){
    $data = array();
    while($row = mysqli_fetch_array($result)){
        $data[] = $row;
    }

    showDataWithLimit2($data,$finfo,1000);
}else{
    echo "Query: $sql<br>\n";
    echo "Error accessing data: ".mysqli_error($db) ."<br>\n";
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
if (isset($result) && $result instanceof mysqli_result) $result->free();
$db->close();
?>

</body>
</html> 
