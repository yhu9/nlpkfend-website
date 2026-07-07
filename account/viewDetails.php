<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
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

<h1>Detail Account View</h1>
    <div class="menu_color">
    <hr>
        <div class="menu">
            <span ><a class="button" href="../homepage.php">Homepage</a></span>
            <span ><a class="button" href="account.php">Account Info</a></span>
            <span ><a class="button" href="payment/payment.php">All Payments</a></span>
            <span ><a class="button" href="charge/charge.php">All Charges</a></span>
            <span ><a class="button" href="add/addAccount_page.php">Add Account</a></span>
            <span ><a class="button" href="../logout.php">Logout</a></span>
        </div>

    <hr>
    <div class='sorter'>
    <b>Sort By: </b>
    <select class='selectpicker' id="mySelect" style='width:20%; font-size:15px;' onchange="contentChanger();titleChanger();">
        <?php
        $date = date("Y");
        for($i = $date+1; $i >= $date - 5; $i--)
            if($i == $date)
                echo "<option value='show$i' selected>show $i";
            else
                echo "<option value='show$i'>show $i";
        ?>
    </select>
    </div>
    <hr>
    </div>

<div class='content'>
    <div id='content'></div>
</div>

    <?php
        //connect to database
        include("../config.php");
        include("queries.php");
        $db = connect();
        checkAdvancedSession(3);

        //get account information and show it
        $id = $_POST['id'];
        $newstudent = $_POST['newstudent'];

        //buttons for scannable form management
        echo "<form method='POST'>";
        echo "<div style='position:fixed;left:50px;top:100px;width:200px;height:100px;'>";
        echo "<table class='pretty_options' id='option_table'>";
        echo "<tr><th>Manage Paper Forms</th></tr>";
        echo "<tr><td style='width:100%; text-align:left;'><button formaction='/account/forms/search/search.php' name='id' value=$id>View/Edit Forms</td></tr>";
        echo "<tr><td><button formaction='/account/forms/add/addForm_page.php' name='id' style='width:100%;'  value=$id>Add Forms</button></td></tr>\n";
        echo "</table>";
        echo "</div>";
            
        //account information
        echo "<div style='position:fixed;right:50px;bottom:100px;'>";
        echo "<button formaction='/account/delete/search_delete.php' name='id' value=$id class='button' style='width:200px; height:70px;background-color:#ff5050'>DELETE THIS ACCOUNT</button>\n";
        echo "</div>";
        echo "</form>";
        if($id != ''){
            if($newstudent == '1')
                showDetailedStudent($db,$id);
            else{
                //show expenditure report for each year
                $date = date("Y");
                for($i = $date+1; $i >= $date+1 - 6; $i--){
                    //show detailed information about account
                    echo "<div id='show$i' hidden>";
                    showDetailedAccount($db,$id,$i);
                    echo "</div>";
                }
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


