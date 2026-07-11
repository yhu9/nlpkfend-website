<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../../mystyle.css">
</head>
<body>
<h1>Delete Payment Form</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../../account.php">Account Info</a></span>
                <span ><a class="button" href="../payment.php">All Payments</a></span>
                <span ><a class="button" href="../search/searchPayment_page.php">Search Payment</a></span>
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

        //get field values for payment table
        //names from POST are Table column names
        include("../queries.php");
            
        //get the post information (PID) of payment
        $id = $_POST['id'];

        $sql1 = "SELECT paymentID,student_1,student_2,student_3,amount,date,time,description,method FROM Account,Payment";
        $sql2 = "WHERE accountID = fk_accountID AND paymentID = $id";

        $sql = "$sql1 $sql2 $sql3";
        $result = mysqli_query($db,$sql);

        //Show the results and save the values as hidden fields
        //hidden fields:
        //count
        //row1,row2,row3,row4,...
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if($result !== false){
            echo "<form action=\"execute_deletePayment.php\" method=\"POST\">\n";
            $found = ($result instanceof mysqli_result ? mysqli_num_rows($result) : 0);
            echo "<input type='hidden' name='count' value=$found>\n";

            //get the data
            $finfo = $result->fetch_fields();
            $data = array();
            while($row = mysqli_fetch_array($result)){
                $data[]=$row;
            }

            //show the results
            showDeleteablePayment($db,$data,$finfo);
            echo "</form>\n";
        }
        else{
            echo("Query: $sql <br>");
            echo("Error searching: ". mysqli_error($db));
        }

        echo "<br><br>\n";


        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
    ?>



</body>
</html> 


