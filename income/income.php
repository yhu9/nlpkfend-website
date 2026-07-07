<!DOCTYPE HTML>
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

function titleChanger(){
    var text = document.getElementById("mySelect").value;
    var view = document.getElementById("view").value;
    year = text.slice(-4);
    document.getElementById("title").innerHTML = "<h1>Income Report By " + view + " " +  year + "</h1>";
}

</script>
<br><br><br>
<div class='title' id='title'>
<h1>Income Report By Category 2018</h1>
</div>
    <div class="menu_color">
    <hr>
        <div class="menu">
            <span ><a class="button" href="../homepage.php">Homepage</a></span>
            <span ><a class="button" href="income.php">Income Report</a></span>
            <span ><a class="button" href="add/addIncome_page.php">Add Income</a></span>
            <span ><a class="button" href="search/searchIncome_page.php">Search Income</a></span>
            <span ><a class="button" href="../logout.php">Logout</a></span>
        </div>
        <hr>
    
        <div class='sorter' style='float:left; width:10%;'>
        <b>Show View By: </b>
        <select class='selectpicker' id="view" style='width:100%; font-size:15px;' onchange="contentChanger2(); titleChanger();">
            <option value='Category' selected>Category</option>
            <option value='Bank Account'>Bank Account</option>
        </select>
        </div>

        <div class='sorter' style='float:left; width:10%;'>
        <b>Sort By: </b>
        <select class='selectpicker' id="mySelect" style='width:100%; font-size:15px;' onchange="contentChanger2(); titleChanger();">
            <?php
            $date = date("Y");
            for($i = $date; $i >= $date - 5; $i--)
                echo "<option value='$i'>$i";
            ?>
        </select>
        </div>
</div>

<div class='content'>
<p id="content"></p>
</div>

<?php
include("/var/www/html/config.php");
$db = connect();
checkAdvancedSession(5);

//Create the query
include("queries.php");

//show income report for each year
$date = date("Y");
$view1 = "Category";
$view2 = "Bank Account";
for($i = $date; $i >= $date - 5; $i--){
    echo "<div id='$view1$i' hidden>";
    showIncomeReportByCategory($db,$i);
    echo "</div>";
    echo "<div id='$view2$i' hidden>";
    showIncomeReportByAccount($db,$i);
    echo "</div>";
}

//default income shown for this year
echo "<script type='text/javascript'>
    var x = document.getElementById('$view1$date').innerHTML;
    document.getElementById(\"content\").innerHTML = \"\" + x;
    </script>";
?>

</body>
</html> 


