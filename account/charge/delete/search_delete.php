<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../../mystyle.css">
</head>
<body>
<h1>Delete Charge Form</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../../account.php">Account Info</a></span>
                <span ><a class="button" href="../charge.php">All Charges</a></span>
                <span ><a class="button" href="../search/searchCharge_page.php">Search Charge</a></span>
                <span ><a class="button" href="../../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <br><br>
        <h2><b>THIS ACTION CANNOT BE TAKEN BACK!<br></b></h2>

    <?php
        //connect to database
        include("../../../config.php");
        $db = connect();
        checkSession();

        //get field values for charge table
        //names from POST are Table column names
        include("../queries.php");
    
        //get the post information (PID) of payment
        $id = $_POST['id'];

        $sql1 = "SELECT chargeID,student_1,student_2,student_3,amount,date,time,description FROM Account,Charge";
        $sql2 = "WHERE accountID = fk_accountID AND chargeID = $id";

        $sql = "$sql1 $sql2";
        $result = mysqli_query($db,$sql);

        //Show the results and save the values as hidden fields
        //hidden fields:
        //count
        //row1,row2,row3,row4,...
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if($result !== false){
            echo "<form action=\"execute_deleteCharge.php\" method=\"POST\">\n";
            $found = mysqli_num_rows($result);
            echo "<input type='hidden' name='count' value=$found>\n";

            //get the data
            $finfo = $result->fetch_fields();
            $data = array();
            while($row = mysqli_fetch_array($result)){
                $data[]=$row;
            }

            //show the results
            showDeleteableCharge($db,$data,$finfo);
            echo "</form>\n";
        }
        else{
            echo("Query: $sql <br>");
            echo("Error searching: ". mysqli_error($db));
        }

        echo "<br><br>\n";

        $result->free();
        $db->close();
    ?>



</body>
</html> 


