<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Time Sheet</h1>
        <a href="../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../timesheet.php">Clock In/Out</a></span>
                <span ><a class="button" href="view_page.php">View Timesheet</a></span>
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
            //connect to database
            include("../../config.php");
            include("../queries.php");
            $db = connect();
            checkSession();

            //get field values for Punch to add
            //names from POST are Table column names
            $sql = "SELECT * FROM Punch";
            $result = mysqli_query($db,$sql);
            if($result !== false){
                $finfo = $result->fetch_fields();
                //Check if one or more Employee is found using that query
                if (isset($result) && $result instanceof mysqli_result) $result->free();
                $username = mysqli_real_escape_string($db,$_POST["username"]);
                $password = mysqli_real_escape_string($db,$_POST["password"]);
                $sql = "SELECT employeeID,username,level,first_name,last_name FROM admin,Employee WHERE username = '$username' and password= '$password' AND fk_employeeID = employeeID";
                $check_result = mysqli_query($db,$sql);
                if($check_result !== false){
                    //Show the check_results
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    $found = mysqli_num_rows($check_result);
                    $check_finfo = $check_result->fetch_fields();

                    //save the employeeID for later use
                    $employeeID = 0;

                    //Show the employee data
                    echo "<h2 align='center'><u>Employee for which timesheet is shown</u></h2>\n";
                    echo "<u>$found records found</u>";
                    echo "<table class='data' align=\"center\">";
                    echo "<tr>";
                    foreach ($check_finfo as $field){
                        echo "<th>". $field->name ."</th>";
                    }
                    echo "</tr>";
                    $i=0;
                    while($row = mysqli_fetch_array($check_result)){
                        echo "<tr>\n";
                        for($i=0; $i < mysqli_num_fields($check_result); $i++){
                            echo "<td>" . $row[$check_finfo[$i]->name] ."</td>\n";
                            if($check_finfo[$i]->name == 'employeeID'){
                                $employeeID = $row[$check_finfo[$i]->name];
                            }
                        }
                        echo "</tr>\n";
                        $i++;
                    }
                    echo "</table>";

                    //Intialize variables
                    $month = mysqli_real_escape_string($db,$_POST['month']);
                    $day = mysqli_real_escape_string($db,$_POST['day']);
                    $year= mysqli_real_escape_string($db,$_POST['year']);
                    $days = cal_days_in_month(CAL_GREGORIAN,$month,$year);

                    
                    //CHECK to see if the number of rows found is only 1 otherwise something is wrong
                    if(mysqli_num_rows($check_result) == 1){
                        
                        //show monthly timesheet
                        if($day == ""){
                            echo "<br><br>";
                            echo "<table class='data'>";
                            echo "<caption><u><b>$month/$year</b></u></caption>";
                            for($i = 1; $i < $days; $i++){
                                echo "<tr>";
                                $theday = date_create_from_format('Y-m-d',"$year-$month-$i");
                                $dayofweek = $theday->format('l');
                                
                                echo "<th>$month/$i $dayofweek</th>";
                                $clockin_data = getPunchData($db,$employeeID,$year,$month,$i,'clock in');
                                $clockout_data = getPunchData($db,$employeeID,$year,$month,$i,'clock out');
                                $fields = getPunchFields($db);
                                if(count($clockin_data) != count($clockout_data)){
                                    echo "<td>Invalid</td>";
                                }elseif(count($clockin_data) == 0 or count($clockout_data) == 0){
                                    echo "<td>0</td>";
                                }else{
                                    $total = new DateTime('00:00:00');
                                    for($j = 0; $j < count($clockout_data); $j++){
                                        $tstart = $clockin_data[$j]['time'];
                                        $tend = $clockout_data[$j]['time'];
                                        $dtestart = new DateTime($tstart);
                                        $dteend = new DateTime($tend);
                                        $tdiff = $dtestart->diff($dteend);

                                        $total->add($tdiff);
                                    }
                                    echo "<td>".$total->format('H:i:s'). "</td>";
                                }
                                echo "</tr>";
                            }
                            echo "</table>";

                        //Show daily timesheet
                        }else{
                            //Get clock in data
                            $clockin_data = getPunchData($db,$employeeID,$year,$month,$day,'clock in');
                            $clockin_fields = getPunchFields($db);

                            //Get clock out data
                            $clockout_data = getPunchData($db,$employeeID,$year,$month,$day,'clock out');
                            $clockout_fields = getPunchFields($db);

                            //Show the data
                            echo "<table class='data' style='float:left'>";
                            echo "<caption><b>Clock In</b></caption>";
                            echo "<tr>";
                            foreach($clockin_fields as $field){
                                echo "<th>".$field->name."</th>";
                            }
                            echo "</tr>";
                            foreach($clockin_data as $row){
                                echo "<tr>";
                                foreach($clockin_fields as $field){
                                    if($field->name == 'time'){
                                        $val = new DateTime($row[$field->name]);
                                        echo "<td>".$val->format('h:i:s A');
                                    }else{
                                        echo "<td>".$row[$field->name]."</td>";
                                    }
                                }
                                echo "</tr>";
                            }
                            echo "</table>";
                            echo "<table class='data' style='float:right'>";
                            echo "<caption><b>Clock Out</b></caption>";
                            echo "<tr>";
                            foreach($clockout_fields as $field){
                                echo "<th>".$field->name."</th>";
                            }
                            echo "</tr>";
                            foreach($clockout_data as $row){
                                echo "<tr>";
                                foreach($clockout_fields as $field){
                                    if($field->name == 'time'){
                                        $val = new DateTime($row[$field->name]);
                                        echo "<td>".$val->format('h:i:s A');
                                    }else{
                                        echo "<td>".$row[$field->name]."</td>";
                                    }
                                }
                                echo "</tr>";
                            }
                            echo "</table>";

                            //Can't calculate hours if not same number of clock in/out
                            if($clockout_rows != $clockin_rows){
                                echo "<h2 align='center'>Error calculating your total hours! Talk to the administrator!</h2><br>";
                            }else{
                                $total = new DateTime('00:00:00');
                                for($i = 0; $i < count($clockout_data); $i++){
                                    $tstart = $clockin_data[$i]['time'];
                                    $tend = $clockout_data[$i]['time'];
                                    $dtestart = new DateTime($tstart);
                                    $dteend = new DateTime($tend);
                                    $tdiff = $dtestart->diff($dteend);

                                    $total->add($tdiff);
                                }
                                echo "<br>";
                                echo "<h2 align='center'>Your Total Hours:</h2>";
                                echo "<h3 align='center'>".$total->format('H:i:s'). "</h3>\n";
                            }
                        }
                    }else{
                        echo "<h2 align='center'>Number of employees found is WRONG. MUST BE ONE</h2>";
                    }
                }
            //when initial query to find students is not found
            }else{
                echo("sql statement: ".$sql);
                echo "<br>";
                echo("Could not access database fields: <b>" .mysqli_error($db). "</b>");
            }
            if (isset($result) && $result instanceof mysqli_result) $result->free();
            $db->close();
        ?>
    </body>
</html>
