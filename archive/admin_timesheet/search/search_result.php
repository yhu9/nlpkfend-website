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
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../timesheet.php">Punch Table</a></span>
                <span ><a class="button" href="../add/addPunch_page.php">Add Punch</a></span>
                <span ><a class="button" href="../delete/deletePunch_page.php">Delete Punch</a></span>
                <span ><a class="button" href="../update/updatePunch_page.php">Update Punch</a></span>
                <span ><a class="button" href="../search/searchPunch_page.php">Search Punch</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
            //connect to database
            include("../../config.php");
            include("../queries.php");
            $db = connect();

            //get field values for Punch to add
            //names from POST are Table column names
            $sql = "SELECT * FROM Punch";
            $result = mysqli_query($db,$sql);
            if($result !== false){
                $finfo = $result->fetch_fields();
                //Check if one or more Employee is found using that query
                if (isset($result) && $result instanceof mysqli_result) $result->free();
                $first_name = mysqli_real_escape_string($db,$_POST["first_name"]);
                $last_name = mysqli_real_escape_string($db,$_POST["last_name"]);

                //search database for employee from first and last name
                //function in queries.php
                $data = getEmployeeSearchData($db,$first_name,$last_name);

                //Show the employee data
                //function in queries.php
                echo "<h2 align='center'><u>Employee for which timesheet is shown</u></h2>\n";
                showData($data['data'],$data['fields']);

                //Intialize variables
                $month = mysqli_real_escape_string($db,$_POST['month']);
                $day = mysqli_real_escape_string($db,$_POST['day']);
                $year= mysqli_real_escape_string($db,$_POST['year']);
                $days = cal_days_in_month(CAL_GREGORIAN,$month,$year);

                //CHECK to see if the number of rows found is only 1 otherwise something is wrong
                if(count($data['data']) == 1){
                    //save the employeeID for later use
                    $employeeID = $data['data'][0]['employeeID'];

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
                            $clockin_data = getPunchData($db,$employeeID,$year,$month,$i,'sign in');
                            $clockout_data = getPunchData($db,$employeeID,$year,$month,$i,'sign out');
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
                        $clockin_data = getPunchData($db,$employeeID,$year,$month,$day,'sign in');
                        $clockin_fields = getPunchFields($db);

                        //Get clock out data
                        $clockout_data = getPunchData($db,$employeeID,$year,$month,$day,'sign out');
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
                            foreach($in_fields as $field){
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
        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
        ?>
    </body>
</html>
