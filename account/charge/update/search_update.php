<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../../mystyle.css">
</head>
<body>
<h1>Update Charge Form</h1>
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

    <?php
        //connect to database
        include("../../../config.php");
        $db = connect();
        checkAdvancedSession(3);

        //get field values for charge table
        $sql = "SELECT * FROM Charge";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            //get pid from post
            $pid = $_POST['id'];

            $sql1 = "SELECT * FROM Charge";
            $sql2 = "WHERE chargeID = $pid";
            $sql = "$sql1 $sql2";

            $result = mysqli_query($db,$sql);
            //Show the results and save the values as hidden fields
            //hidden fields:
            //count
            //row1,row2,row3,row4,...
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if($result !== false){
                $finfo = $result->fetch_fields();
                echo "<form action=\"execute_updateCharge.php\" method=\"POST\">\n";
                $found = mysqli_num_rows($result);
                echo "<input type='hidden' name='count' value=$found>\n";

                //get the data
                $data = array();
                while($row = mysqli_fetch_array($result)){
                    $data[]=$row;
                }

                //save post information
                $tmp = 0;
                echo "<input type='hidden' name=\"row$tmp\" value=$pid>\n";

                //show the results
                showEditableData2($data,$finfo);
                echo "<input type='submit' value='Update Values'>\n";
                echo "</form>\n";
            }
            else{
                echo("Query: $sql <br>");
                echo("Error searching: ". mysqli_error($db));
            }

        }else{
            echo "query: $sql <br>\n";
            echo "Could not access database: ". mysqli_error($db);
        }
        
        $result->free();
        $db->close();
    ?>



</body>
</html> 


