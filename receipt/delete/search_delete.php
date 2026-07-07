<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Delete Receipt Page</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
            <span ><a class="button" href="../../homepage.php">Homepage</a></span>
            <span ><a class="button" href="../receipt.php">Receipt Info</a></span>
            <span ><a class="button" href="../search/searchReceipt_page.php">Search Receipt</a></span>
            <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        
        <h1><b>Warning! This action cannot be taken back!</b></h1>
        <hr><br>

    <?php
        //connect to database
        include("../../config.php");
        include("../queries.php");
        $db = connect();
        checkAdvancedSession(3);

        //get field values for receipt table
        //names from POST are Table column names
        $sql = "SELECT * FROM Receipt";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $id = $_POST['id'];

            $sql1 = "SELECT * FROM Receipt";
            $sql2 = "WHERE receiptID = $id";
            $finfo = $result->fetch_fields();

            $sql = "$sql1 $sql2";
            $result = mysqli_query($db,$sql);

            //Show the results and save the values as hidden fields
            //hidden fields:
            //count
            //row1,row2,row3,row4,...
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if($result !== false){
                echo "<form action=\"execute_deleteReceipt.php\" method=\"POST\">\n";
                $found = mysqli_num_rows($result);
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
                    $val = $row["receiptID"];
                    echo "<input type='hidden' name=\"row$tmp\" value=$val>\n";
                    $tmp++;
                }

                //show the results
                showDeleteableReceipt($db,$data,$finfo);
                echo "</form>\n";

            }
            else{
                echo("Query: $sql <br>");
                echo("Error searching: ". mysqli_error($db));
            }

            echo "<br><br>\n";

        }else{
            echo "query: $sql <br>";
            echo "Could not access database: ". mysqli_error($db);
        }
        
        $result->free();
        $db->close();
    ?>

</body>
</html> 


