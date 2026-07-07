<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../../mystyle.css">
</head>
<body>
<h1>Child Care Contracts/Authorization</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../../student.php">Student Table</a></span>
                <span ><a class="button" href="../cca.php">CCA Table</a></span>
                <span ><a class="button" href="../add/addCCA_page.php">Add CCA</a></span>
                <span ><a class="button" href="../search/searchCCA_page.php">Search CCA</a></span>
                <span ><a class="button" href="../../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Connect to database
        include("../../../config.php");
        $db = connect();

        //Get field values for cca
        $sql = "SELECT * FROM CCA";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $finfo = $result->fetch_fields();
            $sql1 = "SELECT first_name,last_name,FT,PT,CCA.start_date,CCA.end_date,assistance,NLPS_tuition,state_payment,COALESCE(NLPS_tuition - state_payment,0) AS tuition FROM CCA,Student";
            $sql2 = "WHERE studentID = fk_studentID";
            $sql3 = "ORDER BY last_name,end_date DESC";

            //Show the results
            $sql = "$sql1 $sql2 $sql3";
            $result = mysqli_query($db,$sql);

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if($result !== false){
                $found = mysqli_num_rows($result);

                //get the data
                $data = array();
                while($row = mysqli_fetch_array($result)){
                    $data[] = $row;
                }

                //get the fields
                $finfo = $result->fetch_fields();

                //show the information
                showData($data,$finfo);

            }
            else{
                echo("Query: ".$sql ."<br>\n");
                echo("Error searching: ". mysqli_error($db));
            }

        }else{
            echo "query: $sql <br>";
            echo "Could not access database: ". mysqli_error($db);
        }

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////

        $result->free();
        $db->close();
        ?>
		
</body>
</html> 



