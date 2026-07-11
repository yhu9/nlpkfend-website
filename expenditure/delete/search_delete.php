<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Delete Expenditure Page</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../expenditure.php">Expenditure Report</a></span>
                <span ><a class="button" href="../add/addExpenditure_page.php">Add Expenditure</a></span>
                <span ><a class="button" href="../search/searchExpenditure_page.php">Search Expenditure</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        
        <br><br>
        <h1><b>Warning! This action cannot be taken back!</b></h1>
        <hr><br>

    <?php
        //connect to database
        include("../../config.php");
        include("../queries.php");
        $db = connect();
        checkAdvancedSession(5);

        //get field values for expenditure table
        //names from POST are Table column names
        $sql = "SELECT * FROM Expenditure";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $id = $_POST['id'];

            $sql1 = "SELECT * FROM Expenditure";
            $sql2 = "WHERE expenditureID = $id";
            $finfo = $result->fetch_fields();

            $sql = "$sql1 $sql2";
            $result = mysqli_query($db,$sql);

            //Show the results and save the values as hidden fields
            //hidden fields:
            //count
            //row1,row2,row3,row4,...
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if($result !== false){
                echo "<form action=\"execute_deleteExpenditure.php\" method=\"POST\">\n";
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
                    $val = $row["expenditureID"];
                    echo "<input type='hidden' name=\"row$tmp\" value=$val>\n";
                    $tmp++;
                }

                //show the results
                showDeleteableExpenditure($data,$finfo);
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
        
        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
    ?>

</body>
</html> 


