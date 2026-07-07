<!DOCTYPE HTML>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="/mystyle.css">
    <link rel="stylesheet" type="text/css" href="/css/detail_print.css">
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
    year = text.slice(-4);
    document.getElementById("title").innerHTML = "<h1>Expenditure Report " + year + "</h1>";
}

</script>
<h1>Detailed Student Information</h1>
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
    <select class='selectpicker' id="mySelect" style='width:20%; font-size:15px;' onchange="contentChanger();titleChanger();">
        <?php
        $date = date("Y");
        for($i = $date; $i >= $date - 5; $i--)
            echo "<option value='show$i'>show $i";
        ?>
    </select>
    </div>
    <hr>
</div>

<div class='content'>
<p id="content"></p>
</div>

    <?php
        //connect to database
        include("/var/www/html/config.php");
        include("queries.php");
        $db = connect();
        checkAdvancedSession(3);

        //get account information and show it
        $id = $_POST['id'];
        if($id != ''){
            //show expenditure report for each year
            $date = date("Y");
            for($i = $date; $i >= $date - 5; $i--){
                echo "<div id='show$i' hidden>";
                showDetailedStudent($db,$id,$i);
                echo "</div>";
            }
        }

        //default detailed view being show is for this year
    echo "<script type='text/javascript'>
        var x = document.getElementById('show$date').innerHTML;
        document.getElementById(\"content\").innerHTML = \"\" + x;
        </script>";
    ?>

</body>
</html>
