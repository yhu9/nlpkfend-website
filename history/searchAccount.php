<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Account Table</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../account.php">Account Table</a></span>
                <span ><a class="button" href="../add/addAccount_page.php">Add Account</a></span>
                <span ><a class="button" href="../delete/searchAccount.php">Delete Account</a></span>
                <span ><a class="button" href="../update/searchAccount.php">Edit Account</a></span>
                <span ><a class="button" href="../search/searchAccount_page.php">Search Account</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <br><br>

    <?php
        //connect to database
        include("../../config.php");
        include("../queries.php");
        $db = connect();
        checkAdvancedSession(3);

        //get field values for account table
        //names from POST are Table column names
        $sql1 = "SELECT accountID,student_1,student_2,student_3,student_4,student_5,student_6,student_7,status,authorization,note FROM Account";
        $sql2 = "ORDER BY status, student_1";

        //create line $sql3
        $ORDERBY = mysqli_real_escape_string($db,$_POST['orderby']);
        if($ORDERBY != ""){
            $sql2 = "ORDER BY $ORDERBY";
        }

        $sql = "$sql1 $sql2";
        $result = mysqli_query($db,$sql);

        //Show the results and save the values as hidden fields
        //hidden fields:
        //count
        //row1,row2,row3,row4,...
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if($result !== false){
            echo "<form action=\"execute_updateAccount.php\" method=\"POST\">\n";
            $found = ($result instanceof mysqli_result ? mysqli_num_rows($result) : 0);
            echo "<input type='hidden' name='count' value=$found>\n";

            //get the data
            $finfo = $result->fetch_fields();
            $data = array();
            while($row = mysqli_fetch_array($result)){
                $data[]=$row;
            }


            //save post information
            $tmp = 0;
            foreach($data as $row){
                $val = $row["accountID"];
                echo "<input type='hidden' name=\"row$tmp\" value=$val>\n";
                $tmp++;
            }

            //show the results
            showEditableData($data,$finfo);
            echo "<input type='submit' value='Update Values'>\n";
            echo "</form>\n";

        }
        else{
            echo("Query: $sql <br>");
            echo("Error searching: ". mysqli_error($db));
        }

        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
    ?>


</body>
</html> 

