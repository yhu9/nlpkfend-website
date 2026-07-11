<!DOCTYPE HTML>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="/mystyle.css">
    <link rel="stylesheet" type="text/css" href="detail_print.css">
    <script type='text/javascript' src="/js/js_main.js"></script>
</head>
<body>
<h1>Detailed Expenditure View</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../homepage.php">Homepage</a></span>
                <span ><a class="button" href="expenditure.php">Expenditure Report</a></span>
                <span ><a class="button" href="add/addExpenditure_page.php">Add Expenditure</a></span>
                <span ><a class="button" href="search/searchExpenditure_page.php">Search Expenditure</a></span>
                <span ><a class="button" href="../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
            //connect to database
            include("/var/www/html/config.php");
            include("queries.php");
            $db = connect();
            checkAdvancedSession(5);

            $year = $_POST['year'];
            $month = $_POST['month'];
            $bank_account = $_POST['bank_account'];
            $category = $_POST['category'];
            $name = $category;
            if($category == '')
                $name = $bank_account;

            $total = showDetails($db,$category,$bank_account,$month,$year);
            
            $months = array('January','February','March','April','May','June','July','August','September','October','November','Dec');

            echo "<br><br><hr><br>";
            
            echo "<h2>Total Expenditure $months[$month] $year: $name</h2>";
            echo "<br><br><h2>$ ".number_format($total, 2)."</h2>";
?>

</body>
</html> 
