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
                <span ><a class="button" href="student_balance_page.php">Search Student Balance</a></span>
                <span ><a class="button" href="../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Connect to database
        include("../config.php");
        $db = connect();
        checkAdvancedSession(3);

        //Get field values for student
        $sql = "SELECT * FROM Student";
        $result = mysqli_query($db,$sql);
        $students_found = 0;
        if($result !== false){
            $result->free();
            $sql = "SELECT * FROM Student WHERE status='active' ORDER BY last_name";
            $student_data = array();
            $students_found = 0;
            $result = mysqli_query($db,$sql);
            if($result !== false){
                $students_found = mysqli_num_rows($result);
                while($row = mysqli_fetch_array($result)){
                    $student_data[] = $row;
                }
            }

            echo "<form action='../payment/add/addPayment_page.php' method='post'>\n";
            echo "<input style='float:left; margin-right:50px;margin-top:100px;' type='submit' class='rectpretty' value='Add a payment'>\n";
            echo "</form>\n";
            echo "<form action='../charge/add/addCharge_page.php' method='post'>\n";
            echo "<input style='float:left; margin-right:50px; margin-top:100px;' type='submit' class='rectpretty' value='Add a charge'>\n";
            echo "</form>\n";

            //Show the data of students found and their balances
            echo "<table class='data' style='float:left; margin-left: 50px;'>";
            echo "<caption><b>Student Balances Found: $students_found<b></caption>\n";
            echo "<tr>";
            echo "<th>Last Name</th>";
            echo "<th>First Name</th>";
            echo "<th>Balance</th>";
            echo "</tr>";
            foreach($student_data as $row){
                echo "<tr>";
                $sid = $row['studentID'];
                $sql1 = "SELECT last_name,first_name";
                $sql2 = "";

                //get count of student charges and payments
                $charge_count = 0;
                $payment_count = 0;
                $sql = "SELECT * FROM Payment WHERE fk_studentID = $sid";
                $result = mysqli_query($db,$sql);
                if($result !== false)
                    $payment_count = mysqli_num_rows($result);
                $result->free();
                $sql = "SELECT * FROM Charge WHERE fk_studentID = $sid";
                $result = mysqli_query($db,$sql);
                if($result !== false)
                    $charge_count = mysqli_num_rows($result);
                $result->free();

                $sql = "$sql1,($sql2) AS Balance FROM Student WHERE studentID = $sid";
                $result = mysqli_query($db,$sql);
                if($result !== false){
                    $finfo = mysqli_fetch_fields($result);
                    while($tmp = mysqli_fetch_array($result)){
                        foreach($finfo as $field){
                            $val = $tmp[$field->name];
                            if($val == '')
                                echo "<td>" . 0 . "</td>";
                            else
                                echo "<td>" . $tmp[$field->name] . "</td>";
                        }
                    }
                }
                echo "</tr>";
                $result->free();
            }
            echo "</table>";
            echo "</div>";

            //create the query to show the balance for each child

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



