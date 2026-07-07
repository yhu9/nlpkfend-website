<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Student Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../homepage.php">Homepage</a></span>
                <span ><a class="button" href="student_balance.php">Search Student Balance</a></span>
                <span ><a class="button" href="../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Connect to database
        include("../config.php");
        $db = connect();
        checkSession();

        //Get field values for student
        $sql = "SELECT * FROM Student";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $finfo = $result->fetch_fields();
            $sql1 = "SELECT * FROM Student";
            $sql2 = "WHERE";
            $sql3 = "ORDER BY";

            //Intialize and create line $sql2
            $first_pass = true;
            foreach($finfo as $field){
                $val = mysqli_real_escape_string($db,$_POST[$field->name]);
                $condition = "";
                if($val != "" and $val != "--"){
                    //if field is a numeric
                    if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                        $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                        $field->type == 246)
                    { 
                        $condition = "$field->name = $val";
                    //Otherwise it needs quotes
                    }else{
                        $condition = "$field->name = '$val'";
                    }

                    if($first_pass)
                        $sql2 .= " $condition";
                    else
                        $sql2 .= " AND $condition";

                    $first_pass = false;
                }
            }

            //create line $sql3
            $ORDERBY = mysqli_real_escape_string($db,$_POST['orderby']);
            if($ORDERBY == ""){
                $sql3 = "ORDER BY last_name";
            }else{
                $sql3 = "ORDER BY $ORDERBY";
            }

            $result->free();
            $sql = "$sql1 $sql2 $sql3";
            $result = mysqli_query($db,$sql);

            $students_found = array();
            //Show the students found and save the results
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if($result !== false){
                $found = mysqli_num_rows($result);

                //get the data
                $finfo = $result->fetch_fields();
                while($row = mysqli_fetch_array($result)){
                    $students_found[] = $row;
                }

                //show the information
                echo "<u>$found records found</u>";
                echo "<table class='data' align=\"center\">";
                echo "<tr>";
                foreach ($finfo as $field){
                    echo "<th>". $field->name ."</th>";
                }
                echo "</tr>";
                $i=0;
                foreach($students_found as $row){
                    echo "<tr>";
                    for($i=0; $i < mysqli_num_fields($result); $i++){
                        echo "<td>" . $row[$finfo[$i]->name] ."</td>";
                    }
                    echo "</tr>";
                    $i++;
                }
                echo "</table>";
            }
            else{
                echo("Query: ".$sql ."<br>\n");
                echo("Error searching: ". mysqli_error($db));
            }

            //create the query to show all payment for those kids
            //create the query to show all charges for those kids
            $result->free();
            $sql2 = "";
            $is_first = true;
            foreach($students_found as $row){
                if($is_first){
                    $sid = $row['studentID'];
                    $condition = "studentID = $sid"; 
                    $sql2 .= "$condition";
                    $is_first = false;
                }else{
                    $sid = $row['studentID'];
                    $condition = "studentID = $sid"; 
                    $sql2 .= " OR $condition";
                }
            }

            //initialize the data structures for holding the information
            $payment_data = array();
            $payment_fields;
            $num_payments;
            $charge_data = array();
            $charge_fields;
            $num_charges;

            //get the data of payments and charges for these kids
            $sql = "SELECT first_name,last_name,Charge.* FROM Student,Charge WHERE studentID = fk_studentID AND ($sql2) ORDER BY date DESC, time DESC";
            $result = mysqli_query($db,$sql);
            if($result !== false){
                $num_charges = mysqli_num_rows($result);
                $charge_fields = mysqli_fetch_fields($result);
                while($row = mysqli_fetch_array($result))
                    $charge_data[] = $row;

            }
            $result->free();

            $sql = "SELECT first_name,last_name,Payment.* FROM Student,Payment WHERE studentID = fk_studentID AND ($sql2) ORDER BY date DESC,time DESC";
            $result = mysqli_query($db,$sql);
            if($result !== false){
                $num_payments = mysqli_num_rows($result);
                $payment_fields = mysqli_fetch_fields($result);
                while($row = mysqli_fetch_array($result))
                    $payment_data[] = $row;
            }
            $result->free();
            echo "<br><hr>";

            //Show the data of payments and charges for these kids
            echo "<p>";
            echo "<table class='data' style='float:left; margin-left:40px;margin-top:20px;'>";
            echo "<caption><b>Payments found: $num_payments<b></caption>\n";
            echo "<tr>";
            foreach ($payment_fields as $field){
                echo "<th>". $field->name ."</th>";
            }
            echo "</tr>";
            $i=0;
            foreach($payment_data as $row){
                echo "<tr>";
                foreach($payment_fields as $field)
                    echo "<td>" . $row[$field->name] ."</td>";
                echo "</tr>";
                $i++;
            }
            echo "</table>";
            
            echo "<table class='data' style='float:right; margin-right:40px;margin-top:20px;'>";
            echo "<caption><b>Charges found: $num_charges<b></caption>\n";
            echo "<tr>";
            foreach ($charge_fields as $field){
                echo "<th>". $field->name ."</th>";
            }
            echo "</tr>";
            $i=0;
            foreach($charge_data as $row){
                echo "<tr>";
                foreach($charge_fields as $field)
                    echo "<td>" . $row[$field->name] ."</td>";
                echo "</tr>";
                $i++;
            }
            echo "</table>";
            echo "</p>";

            //create the query to show the balance for each child
            $result->free();
            echo "<div style='position:absolute; width: 50%; margin-left: 25%; margin-right: 25%;bottom:50px;'>";
            echo "<table class='data'>";
            echo "<caption><b>Student Balance<b></caption>\n";
            echo "<tr>";
            echo "<th>First Name</th>";
            echo "<th>Last Name</th>";
            echo "<th>Balance</th>";
            echo "</tr>";
            foreach($students_found as $row){
                echo "<tr>";
                $sid = $row['studentID'];
                $condition = "studentID = $sid"; 
                $sql1 = "SELECT first_name,last_name";
                $sql2 = "";
                if(count($payment_data) == 0 and count($charge_data) == 0)
                    $sql2 = "SELECT 0 AS Diff";
                elseif(count($payment_data) == 0)
                    $sql2 = "SELECT (SELECT SUM(amount) FROM Charge WHERE studentID=fk_studentID and $condition) AS Diff";
                elseif(count($charge_data) == 0)
                    $sql2 = "SELECT 0 - (SELECT SUM(amount) FROM Payment WHERE studentID=fk_studentID and $condition) AS Diff";
                else
                    $sql2 = "SELECT (SELECT SUM(amount) FROM Charge,Student WHERE studentID=fk_studentID AND studentID = $sid) 
                    - (SELECT SUM(amount) FROM Payment,Student WHERE studentID=fk_studentID AND studentID = $sid) AS Diff";
                $sql = "$sql1,($sql2) AS Balance FROM Student WHERE studentID = $sid";
                $result = mysqli_query($db,$sql);
                if($result !== false){
                    $finfo = mysqli_fetch_fields($result);
                    while($row = mysqli_fetch_array($result)){
                        foreach($finfo as $field){
                            $val = $row[$field->name];
                            if($val == '')
                                echo "<td>" . 0 . "</td>";
                            else
                                echo "<td>" . $row[$field->name] . "</td>";
                        }
                    }
                }
                echo "</tr>";
                $result->free();
            }
            echo "</table>";
            echo "</div>";

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



