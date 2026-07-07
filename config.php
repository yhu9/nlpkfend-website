<?php

//function to connect
function connect() {
    $config = parse_ini_file('db.ini');
    $dbhost = $config['dbhost'];
    $dbuser = $config['dbuser'];
    $dbpass = $config['dbpass'];
    $dbname = $config['dbname'];
    $conn = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
    
    if ($conn->connect_error) {
           die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// Function to get the user IP address
function getUserIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

//function to check session
function checkSession(){
    $conn = connect();
    $ip = getUserIP();
    $query = "SELECT * FROM session WHERE ipaddress = '$ip'";
    $result = mysqli_query($conn,$query);
    if(mysqli_num_rows($result) < 1){
        $login = $_SERVER["SITE_HTMLROOT"]."/login.php";
        header("location: $login");
    }
    else {
        $query = "UPDATE session SET login_time = NOW() WHERE ipaddress = '$ip'";
        mysqli_query($conn,$query);
        return 1;
    }
    mysqli_close($conn);
}

//function to check session
function checkAdvancedSession($clearance){
    $conn = connect();
    $ip = getUserIP();
    $level = 0;
    $query = "SELECT level FROM session,admin WHERE ipaddress = '$ip' AND session.username = admin.username";
    $result = mysqli_query($conn,$query);
    if($result !== false){
        while($row = mysqli_fetch_array($result)){
            $level = $row['level'];
            if($level == ''){
                $level = 0;
            }
        }
    }else
        echo "<h1>ERROR Getting Session IP</h1><br>";
    if($level < $clearance){
        $login = $_SERVER["SITE_HTMLROOT"]."/badaccess.php";
        header("location: $login");
    }
    else{
        $query = "UPDATE session SET login_time = NOW() WHERE ipaddress = '$ip'";
        mysqli_query($conn,$query);
        return 1;
    }
    mysqli_close($conn);
}

//function to get day from date('d-m-y')
function day($date=''){
    if($date==''){
        $t=date('d-m-Y');
    } else {
        $t=date('d-m-Y',strtotime($date));
    }

    $dayName = strtolower(date("D",strtotime($t)));
    $dayNum = strtolower(date("d",strtotime($t)));
    $return = floor(($dayNum - 1) / 7) + 1;
    return $return;
}

//function to show the add form
function showAddForm2($db,$data,$fields){
    $id = $_POST['id'];
    $aid = $_POST['aid'];
    echo "<TABLE style='width:500px;' class='form' BORDER=\"1\">";

    //Show form for adding an student
    foreach($fields as $field){
        echo "<tr>";

        //parse the row name for proper output and display field name for each row
        if($field->name == "fk_studentID" OR $field->name == "fk_employeeID"){
            echo "<th>Name</th>";
        }elseif(strpos($field->name,'ID') !== false){
            $pos = strpos($field->name,'ID');
            $newstr = substr_replace($field->name, " ", $pos, 0);
            echo "<th>$newstr</th>\n";
        }else{
            echo "<th>". str_replace('_',' ',$field->name) ."</th>\n";
        }

        //create forms body
        if($field->name == "fk_studentID"){
            $sql = "SELECT * FROM Student WHERE status = 'active' ORDER BY last_name";
            $result = mysqli_query($db,$sql);
            echo "<td>";
            echo "<select style='width:100%;' class='selectpicker' name='$field->name' id='$field->name'>";
            echo "<option></option>";
            while($row = mysqli_fetch_array($result)){
                $fname = $row['first_name'];
                $lname = $row['last_name'];
                $sid = $row['studentID'];
                $name = "$lname, $fname";
                if($sid == $id)
                    echo "<option value=$sid selected>$name</option>";
                else
                    echo "<option value=$sid>$name</option>";
            }
            echo "</td>\n";
        }elseif($field->name == "fk_employeeID"){
            $sql = "SELECT * FROM Employee WHERE status = 'active' ORDER BY last_name";
            $result = mysqli_query($db,$sql);
            echo "<td>";
            echo "<select style='width:100%;' class='selectpicker' name='$field->name'>";
            echo "<option></option>";
            while($row = mysqli_fetch_array($result)){
                $fname = $row['first_name'];
                $lname = $row['last_name'];
                $sid = $row['employeeID'];
                $name = "$lname, $fname";
                echo "<option value=$sid>$name</option>";
            }
            echo "</td>\n";
        }elseif($field->name == 'fk_accountID'){
            if($aid == '')
                echo "<td align=\"center\">Do not use</td>";
            else
                echo "<td><select style='width:100%;' class='selectpicker' name='$field->name'><option>$aid</option></td>";
        }elseif(strpos($field->name,"ID") !== false OR strpos($field->name,'fk_') !== false or $field->name == "age"){
                echo "<td align=\"center\">Do not use</td>";
        }elseif($field->name == "sex"){
            echo "<td align='center'><select class='selectpicker' name='$field->name'>
                <option value='' selected>Male or Female</option>
                <option>M</option>
                <option>F</option>";
            echo "</td>";
        }elseif($field->name == "allow_picture"){
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='$field->name'>
                <option value='' selected>yes or no</option>
                <option>Yes</option>
                <option>No</option>";
            echo "</td>";
        }elseif($field->name == "status"){
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='$field->name'>
                <option selected>active</option>
                <option>inactive</option>";
            echo "</td>";
        }elseif($field->name == "authorization" or $field->name == "provider_name" or $field->name == 'assistance'){
            echo "<td title='$title' align='center'><select class='selectpicker' style='width:100%;' name='$field->name'>";
            echo "<option value='SELF' selected>SELF</option>";
            echo "<option>P1</option>";
            echo "<option>P2</option>";
            echo "<option>CITC</option>";
            echo "<option>OCS</option>";
            echo "<option>OTHER</option>";
            echo "</td>\n";
        }elseif($field->name == "end_date" or $field->name == 'start_date'){
            echo "<td title='$title' align='center'>\n";
            //output if field is required or not
            if($field->flags & 1)
                echo "<input type='date' name=\"$field->name\" required>\n";
            else
                echo "<input type='date' name=\"$field->name\">\n";
            echo "</td>\n";
        }elseif($field->name == 'date'){
            $date = new DateTime();
            $now = $date->format('Y-m-d');
            echo "<td align='center'>\n";
            //output if field is required or not
            if($field->flags & 1)
                echo "<input type='date' value=$now name=\"$field->name\" required>\n";
            else
                echo "<input type='date' value=$now name=\"$field->name\">\n";
            echo "</td>";
        }elseif(strpos($field->name,'date') !== false OR $field->name == "DOB"){
            echo "<td align='center'>\n";
            echo "<input type='date' name=\"$field->name\">\n";
            echo "</td>";
        }elseif(strpos($field->name,'phone') !== false){
            echo "<td align='center'>\n";
            echo "<input class='date' style='width: 30px;float:none;' type='text' size='4' maxlength='3' placeholder='###' name=\"$field->name[num0]\" required> - \n";
            echo "<input class='date' style='width: 40px; float:none;' type='text' size='4' maxlength='3' placeholder='###' name=\"$field->name[num1]\" required> - \n";
            echo "<input class='date' style='width:40px; float:none;' type='text' size='4' maxlength='4' placeholder='####' name=\"$field->name[num2]\" required>\n";
            echo "</td>";
        }elseif($field->name == 'method'){
            echo "<td align='center'><select class='selectpicker' style='width:100%;' name='$field->name'>";
            echo "<option value='' selected></option>";
            echo "<option>cash</option>";
            echo "<option>credit card</option>";
            echo "<option>check</option>";
            echo "</td>";
        }elseif($field->name == "auth_type"){
            echo "<td align='center'><select class='selectpicker' style='width:100%;' name='$field->name'>";
            echo "<option value='SELF'selected>SELF</option>";
            echo "<option>P1</option>";
            echo "<option>P2</option>";
            echo "<option>CITC</option>";
            echo "<option>OCS</option>";
            echo "<option>SELF</option>";
            echo "<option>OTHER</option>";
            echo "</td>\n";
        }elseif($field->name == 'tuition_type'){
            echo "<td align='center'><select class='selectpicker' style='width:100%;' name='$field->name' required>";
            echo "<option>Full</option>";
            echo "<option>Part</option>";
            echo "</td>\n";
        }elseif($field->name == "type"){
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='$field->name'>
                <option value='' selected></option>
                <option>sign in</option>
                <option>sign out</option>";
        echo "</td>";
        }elseif($field->name == 'time'){
            $time = new DateTime();
            $now = $time->format('H:i');
            echo "<td nowrap>";
            echo "<input type='time' name='$field->name' value=$now required>";
            echo "</td>";
        }elseif($field->name == 'time_in' or $field->name == 'time_out' or $field->name == 'lunch_in' or $field->name == 'lunch_out'){
            echo "<td nowrap>";
            echo "<input type='time' name='$field->name'>";
            echo "</td>";
        }elseif($field->name == 'days_of_week'){
            echo "<td>";
            echo "<table border=none>";
            echo "<tr>";
            echo "<td>SUN</td>";
            echo "<td>MON</td>";
            echo "<td>TUE</td>";
            echo "<td>WED</td>";
            echo "<td>THU</td>";
            echo "<td>FRI</td>";
            echo "<td>SAT</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td><input type='checkbox' name='sun' value='y'></td>\n";
            echo "<td><input type='checkbox' name='mon' value='y'></td>\n";
            echo "<td><input type='checkbox' name='tue' value='y'></td>\n";
            echo "<td><input type='checkbox' name='wed' value='y'></td>\n";
            echo "<td><input type='checkbox' name='thu' value='y'></td>\n";
            echo "<td><input type='checkbox' name='fri' value='y'></td>\n";
            echo "<td><input type='checkbox' name='sat' value='y'></td>\n";
            echo "</tr>";
            echo "</table>";
            echo "</td>";
        }elseif(strpos($field->name, 'room') !== false){
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='$field->name'>";
            echo "<option value='' selected>Select Room</option>";
            echo "<option>Purple</option>";
            echo "<option>Rainbow</option>";
            echo "<option>Orange</option>";
            echo "<option>Green</option>";
            echo "<option>Yellow</option>";
            echo "<option>Pink</option>";
            echo "<option>Blue</option>";
            echo "<option>Red</option>";
            echo "<option>S.A 1-3</option>";
            echo "<option>S.A 4-6</option>";
            echo "<option>Saturday</option>";
            echo "<option>DROP IN</option>";
            echo "</td>";
        }elseif($field->name == 'PT' OR $field->name == 'FT'){
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='$field->name'>";
            echo "<option>FT MONTH</option>\n";
            echo "<option>PT MONTH</option>\n";
            echo "<option selected>0</option>\n";
            for($i = 0; $i < 32; $i++){
                echo "<option>$i</option>\n";
            }
            echo "</td>";
        }elseif($field->name == 'expected_tuition' or $field->name == 'NLPS_tuition' or $field->name == 'state_payment'){
            echo "<td align=\"center\" nowrap>$ <input style='width:80%;' type=\"text\" name=\"$field->name\" value='0.00' required></td>";
        }else{
            echo "<td align=\"center\" nowrap><input style='width:100%;' type=\"text\" name=\"$field->name\"></td>";
        }

        //output if field is required or not
        if($field->flags & 1 AND strpos($field->name,'ID') == false){
            echo "<td><img src='/images/red_expoint.png' style='width:20px;height:20px;'></src></td>";
        }
        echo "</tr>";
    }

    //close the table and form
    echo "</TABLE><br>\n";
}
//function to show the add form
function showAddForm($data,$fields){
    echo "<TABLE style='width:500px;' class='form' BORDER=\"1\">";

    //Show form for adding an student
    foreach($fields as $field){
        echo "<tr>";

        //parse the row name for proper output
        if(strpos($field->name,'ID') !== false){
            $pos = strpos($field->name,'ID');
            $newstr = substr_replace($field->name, " ", $pos, 0);
            echo "<th>$newstr</th>\n";
        }else{
            echo "<th>". str_replace('_',' ',$field->name) ."</th>\n";
        }

        //create forms body
        if(strpos($field->name,"ID") !== false OR strpos($field->name,'fk_') !== false or $field->name == "age"){
            echo "<td align=\"center\">Do not use</td>";
        }elseif($field->name == "sex"){
            echo "<td align='center'><select class='selectpicker' name='$field->name'>
                <option value='' selected>Male or Female</option>
                <option>M</option>
                <option>F</option>";
            echo "</td>";
        }elseif($field->name == "allow_picture"){
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='$field->name'>
                <option value='' selected>yes or no</option>
                <option>Yes</option>
                <option>No</option>";
            echo "</td>";
        }elseif($field->name == "status"){
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='$field->name'>
                <option selected>active</option>
                <option>inactive</option>";
            echo "</td>";
        }elseif($field->name == "authorization" or $field->name == "provider_name" or $field->name == 'assistance'){
            echo "<td title='$title' align='center'><select class='selectpicker' style='width:100%;' name='$field->name'>";
            echo "<option value='SELF' selected>SELF</option>";
            echo "<option>P1</option>";
            echo "<option>P2</option>";
            echo "<option>CITC</option>";
            echo "<option>OCS</option>";
            echo "<option>OTHER</option>";
            echo "</td>\n";
        }elseif($field->name == 'file' or $field->name == 'file_location'){
            echo "<td>
                    <input type='file' name='$field->name' accept='.pdf, .png, .jpg, .jpeg, .txt, .mp4, .mov, .flv' id='$field->name'>
                </td\n";
        }elseif($field->name == "end_date" or $field->name == 'start_date'){
            echo "<td title='$title' align='center'>\n";
            echo "<input type='date' name=\"$field->name\">\n";
            echo "</td>\n";
        }elseif($field->name == 'date'){
            $date = new DateTime();
            $now = $date->format('Y-m-d');
            echo "<td align='center'>\n";
            echo "<input type='date' value=$now name=\"$field->name\">\n";
            echo "</td>";
        }elseif(strpos($field->name,'date') !== false OR $field->name == "DOB"){
            echo "<td align='center'>\n";
            echo "<input type='date' name=\"$field->name\">\n";
            echo "</td>";
        }elseif(strpos($field->name,'phone') !== false){
            echo "<td align='center'>\n";
            echo "<input class='date' type='text' size='4' maxlength='3' placeholder='###' name=\"$field->name[num0]\">\n";
            echo "<input class='date' type='text' size='4' maxlength='3' placeholder='###' name=\"$field->name[num1]\">\n";
            echo "<input class='date' type='text' size='4' maxlength='4' placeholder='####' name=\"$field->name[num2]\">\n";
            echo "</td>";
        }elseif($field->name == 'method'){
            echo "<td align='center'><select class='selectpicker' style='width:100%;' name='$field->name'>";
            echo "<option value='' selected></option>";
            echo "<option>cash</option>";
            echo "<option>credit card</option>";
            echo "<option>check</option>";
            echo "</td>";
        }elseif($field->name == "auth_type"){
            echo "<td align='center'><select class='selectpicker' style='width:100%;' name='$field->name'>";
            echo "<option value='SELF'selected>SELF</option>";
            echo "<option>P1</option>";
            echo "<option>P2</option>";
            echo "<option>CITC</option>";
            echo "<option>OCS</option>";
            echo "<option>SELF</option>";
            echo "<option>OTHER</option>";
            echo "</td>\n";
        }elseif($field->name == 'tuition_type'){
            echo "<td align='center'><select class='selectpicker' style='width:100%;' name='$field->name'>";
            echo "<option value='' selected></option>";
            echo "<option>Full</option>";
            echo "<option>Part</option>";
            echo "</td>\n";
        }elseif($field->name == "type"){
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='$field->name'>
                <option value='' selected></option>
                <option>sign in</option>
                <option>sign out</option>";
            echo "</td>";
        }elseif($field->name == "time" or $field->name == 'time_in' or $field->name == 'time_out' or $field->name == 'lunch_in' or $field->name == 'lunch_out'){
            $time = new DateTime();
            $now = $time->format('H:i');
            echo "<td nowrap>";
            echo "<input type='time' name='$field->name' value=$now>";
            echo "</td>";
        }elseif($field->name == 'days_of_week'){
            echo "<td>";
            echo "<table border=none>";
            echo "<tr>";
            echo "<td>SUN</td>";
            echo "<td>MON</td>";
            echo "<td>TUE</td>";
            echo "<td>WED</td>";
            echo "<td>THU</td>";
            echo "<td>FRI</td>";
            echo "<td>SAT</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td><input type='checkbox' name='sun' value='y'></td>\n";
            echo "<td><input type='checkbox' name='mon' value='y'></td>\n";
            echo "<td><input type='checkbox' name='tue' value='y'></td>\n";
            echo "<td><input type='checkbox' name='wed' value='y'></td>\n";
            echo "<td><input type='checkbox' name='thu' value='y'></td>\n";
            echo "<td><input type='checkbox' name='fri' value='y'></td>\n";
            echo "<td><input type='checkbox' name='sat' value='y'></td>\n";
            echo "</tr>";
            echo "</table>";
            echo "</td>";
        }elseif(strpos($field->name, 'room') !== false){
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='$field->name'>";
            echo "<option value='' selected>Select Room</option>";
            echo "<option>Purple</option>";
            echo "<option>Rainbow</option>";
            echo "<option>Orange</option>";
            echo "<option>Green</option>";
            echo "<option>Yellow</option>";
            echo "<option>Pink</option>";
            echo "<option>Blue</option>";
            echo "<option>Red</option>";
            echo "<option>S.A 1-3</option>";
            echo "<option>S.A 4-6</option>";
            echo "<option>Saturday</option>";
            echo "<option>DROP IN</option>";
            echo "</td>";
        }elseif($field->name == 'PT' OR $field->name == 'FT'){
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='$field->name'>";
            echo "<option>FT MONTH</option>\n";
            echo "<option>PT MONTH</option>\n";
            echo "<option selected>0</option>\n";
            for($i = 0; $i < 32; $i++){
                echo "<option>$i</option>\n";
            }
            echo "</td>";
        }elseif($field->name == 'expected_tuition' or $field->name == 'NLPS_tuition' or $field->name == 'state_payment'){
            echo "<td align=\"center\" nowrap>$ <input style='width:80%;' type=\"text\" name=\"$field->name\" value='0.00'></td>";
        }else{
            echo "<td align=\"center\" nowrap><input style='width:100%;' type=\"text\" name=\"$field->name\"></td>";
        }

        if($field->name == "fk_studentID" or $field->name == "fk_employeeID"){
            echo "</tr>";
            echo "<tr>";
            echo "<th>last name</th>";
            echo "<td align=\"center\"><input style='width:100%;' type=\"text\" name='last_name'></td>";
            echo "<td><b>REQUIRED</b></td>";
            echo "</tr><tr>";
            echo "<th>first name</th>";
            echo "<td align=\"center\"><input style='width:100%;' type=\"text\" name='first_name'></td>";
            echo "<td><b>REQUIRED</b></td>";
        }

        //output if field is required or not
        if($field->flags & 1 AND strpos($field->name,'ID') == false){
            echo "<td><b>REQUIRED</b></td>";
        }
        echo "</tr>";
    }

    //close the table and form
    echo "</TABLE><br>\n";
}

function showSearchForm($data,$fields){
    echo "<TABLE class='form' BORDER=\"1\">";

    //Show form for adding an student
    foreach($fields as $field){
        echo "<tr>";
        
        //parse the row name for proper output
        if(strpos($field->name,'ID') !== false){
            $pos = strpos($field->name,'ID');
            $newstr = substr_replace($field->name, " ", $pos, 0);
            echo "<th>$newstr</th>\n";
        }else{
            echo "<th>". str_replace('_',' ',$field->name) ."</th>\n";
        }

        //show the equivalence rule
        echo "<td><select class=\"selectpicker\" name=\"eq_$field->name\">";
        echo "<option value=\"=\" selected>=</option>";
        echo "<option value=\"<\">&lt</option>";
        echo "<option value=\">\">&gt</option>";
        echo "<option value=\">=\">&gt=</option>";
        echo "<option value=\"<=\">&lt=</option>";
        echo "</select></td>";

        //show the body of the form
        if($field->name == "sex"){
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='text_$field->name'>
                <option value='' selected>Male or Female</option>
                <option>M</option>
                <option>F</option>
                ";
        }elseif(strpos($field->name,'date') !== false OR $field->name == "DOB"){
            echo "<td align='center'>\n";
            echo "<input type='date' name=\"text_$field->name\">\n";
            echo "</td>";
        }elseif($field->name == "status"){
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='text_$field->name'>
                <option value='' selected>Choose Status</option>
                <option>active</option>
                <option>inactive</option>
                ";
        }elseif(strpos($field->name, 'room') !== false){
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='text_$field->name'>";
            echo "<option value='' selected>Select Room</option>";
            echo "<option>Purple</option>";
            echo "<option>Rainbow</option>";
            echo "<option>Orange</option>";
            echo "<option>Green</option>";
            echo "<option>Yellow</option>";
            echo "<option>Pink</option>";
            echo "<option>Blue</option>";
            echo "<option>Red</option>";
            echo "<option>S.A 1-3</option>";
            echo "<option>S.A 4-6</option>";
            echo "<option>Saturday</option>";
            echo "<option>DROP IN</option>";
            echo "</td>";
        }elseif($field->name == "type"){
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='text_$field->name'>
                <option value='' selected></option>
                <option>sign in</option>
                <option>sign out</option>";
            echo "</td>";
        }elseif($field->name == "assistance"){
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='text_$field->name'>";
            echo "<option selected>".$row[$field->name]."</option>";
            echo "<option>P1</option>";
            echo "<option>P2</option>";
            echo "<option>CITC</option>";
            echo "<option>OCS</option>";
            echo "<option>SELF</option>";
            echo "</td>\n";
        }elseif($field->name == "auth_type"){
            echo "<td align='center'><select class='selectpicker' style='width:100%;' name='text_$field->name'>";
            echo "<option value='' selected>".$row[$field->name]."</option>";
            echo "<option>P1</option>";
            echo "<option>P2</option>";
            echo "<option>CITC</option>";
            echo "<option>OCS</option>";
            echo "<option>SELF</option>";
            echo "<option>OTHER</option>";
            echo "</td>\n";
        }elseif($field->name == 'tuition_type'){
            echo "<td align='center'><select class='selectpicker' style='width:100%;' name='text_$field->name'>";
            echo "<option value='' selected>".$row[$field->name]."</option>";
            echo "<option>Full</option>";
            echo "<option>Part</option>";
            echo "</td>\n";
        }elseif(strpos($field->name,'time') !== false){
            $time = new DateTime();
            $now = $time->format('h:i A');
            echo "<td nowrap>";
            echo "<input type='time' name='$field->name' value=$now>";
            echo "</td>";
        }elseif($field->name == 'PT' OR $field->name == 'FT'){
            echo "<td align='center'>";
            echo "<select class='selectpicker' name='text_$field->name'>";
            echo "<option>FT MONTH</option>\n";
            echo "<option>PT MONTH</option>\n";
            echo "<option selected>".$row[$field->name]."</option>\n";
            for($i = 0; $i < 32; $i++){
                echo "<option>$i</option>\n";
            }
            echo "</td>";
        }elseif(strpos($field->name,'phone') !== false){
            echo "<td align='center'>\n";
            echo "<input class='date' type='text' size='4' maxlength='3' placeholder='###' name=\"$field->name[num0]\">\n";
            echo "<input class='date' type='text' size='4' maxlength='3' placeholder='###' name=\"$field->name[num1]\">\n";
            echo "<input class='year' type='text' size='4' maxlength='4' placeholder='####' name=\"$field->name[num2]\">\n";
            echo "</td>";
        }elseif(strpos($field->name,'fk_') !== false){
            echo "<td>Do Not Use</td>\n";
        }else{
            echo "<td align=\"center\"><input style='width:100%;' type=\"text\" name=\"text_$field->name\"></td>";
        }

        if($field->name == "fk_studentID" or $field->name == "fk_employeeID"){
            echo "</tr>";
            echo "<tr>\n";
            echo "<th>first name</th>\n";
            echo "<td><select class=\"selectpicker\" name=\"eq_first_name\">\n";
            echo "<option value=\"=\" selected>=</option>\n";
            echo "<option value=\"<\">&lt</option>\n";
            echo "<option value=\">\">&gt</option>\n";
            echo "</select></td>\n";
            echo "<td align=\"center\"><input style='width:100%;' type=\"text\" name='text_first_name'></td>";
            echo "</tr><tr>\n";
            echo "<th>last name</th>\n";
            echo "<td><select class=\"selectpicker\" name=\"eq_last_name\">";
            echo "<option value=\"=\" selected>=</option>";
            echo "<option value=\"<\">&lt</option>";
            echo "<option value=\">\">&gt</option>";
            echo "</select></td>\n";
            echo "<td align=\"center\"><input style='width:100%;' type=\"text\" name='text_last_name'></td>\n";
        }
        echo "</tr>";
    }
    echo "</TABLE><br>";
}

//function to show data given fields
function showDataWithLimit($data,$fields,$max){
    $found = count($data);
    echo "<u>$found records found</u><br>\n";
    if($found > $max)
        echo "only $max records will be shown<br>\n";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";
    foreach ($fields as $f){
        if(strpos($f->name,'ID') !== false){
            $pos = strpos($f->name,'ID');
            $newstr = substr_replace($f->name, " ", $pos, 0);
            echo "<th>". $newstr ."</th>\n";
        }else{
            echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
        }
    }
    echo "</tr>";
    $count = 1;
    foreach($data as $row){
        if($count < $max){
            echo "<tr>\n";
            foreach($fields as $f){
                $title = str_replace('_',' ',$f->name);

                if(strpos($f->name,'date') !== false OR $f->name == 'DOB'){
                    $date = new DateTime($row[$f->name]);
                    echo "<td title='$title'>" . $date->format('m-d-Y')  ."</td>\n";
                }elseif($f->name == 'time' or strpos($f->name,'time') !== false or strpos($f->name,'Time') !== false){
                    $time = new DateTime($row[$f->name]);
                    echo "<td title='$title'>" . $time->format('h:i A')  ."</td>\n";
                }else{
                    echo "<td title='$title'>" . $row[$f->name] ."</td>\n";
                }
            }
            echo "</tr>\n";
        }
        $count;
    }
    echo "</table>";
}

///////////////////////////////////////////////////////////////////////////////////////////////
//function to show data given fields
function showData($data,$fields){
    echo "<div class='datahandler'>";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";

    //create the fields
    foreach ($fields as $f){
        if(strpos($f->name,'ID') !== false){
            $pos = strpos($f->name,'ID');
            $newstr = substr_replace($f->name, " ", $pos, 0);
            echo "<th>". $newstr ."</th>\n";
        }elseif($f->name == 'size'){
            echo "<th> size in bytes</th>";
        }else{
            echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
        }
    }
    echo "</tr>";

    //create the content data
    if(count($data) > 0){
        foreach($data as $row){
            echo "<tr class='data'>\n";
            foreach($fields as $f){
                $title = str_replace('_',' ',$f->name);

                if(strpos($f->name,'date') !== false OR $f->name == 'DOB'){
                    if($row[$f->name] != ''){
                        $date = new DateTime($row[$f->name]);
                        echo "<td title='$title'>" . $date->format('m-d-Y')  ."</td>\n";
                    }else
                        echo "<td></td>\n";
                }elseif($f->name == 'file'){
                    $aid = $row['aid'];
                    if ($aid == '')
                        $aid =$row['fk_accountID'];
                    $filename = $row['file'];
                    $full_path = "/resources/nlp_data/account/$aid/$filename";
                    echo "<td>
                        <a href='$full_path'>$filename</a>
                        </td>";
                }elseif($f->name == 'time' or strpos($f->name,'time') !== false or strpos($f->name,'Time') !== false){
                    if($row[$f->name] != ''){
                        $time = new DateTime($row[$f->name]);
                        echo "<td title='$title'>" . $time->format('h:i A')  ."</td>\n";
                    }else
                        echo "<td></td>\n";
                }else{
                    echo "<td title='$title'>" . $row[$f->name] ."</td>\n";
                }
            }
            echo "</tr>\n";
        }
    }else{
        foreach($fields as $f){
            echo "<td></td>\n";
        }
    }
    echo "</table>";
    echo "</div>";
}

//advanced show function for specific showing
function showAdvancedData($data,$fields,$title,$postdir){
    if(count($data) > 0 and count($fields) > 0){
        $found = count($data);
        echo "<u>$found records found</u><br>\n";
        echo "<table class='data' align=\"center\">";

        //show the table title
        $total = count($fields);
        if($title != '')
            echo "<tr><th colspan=$total><b>$title</b></th></tr>\n";

        //show the table columns
        echo "<tr>\n";
        foreach ($fields as $f){
            if(strpos($f->name,'ID') !== false and strpos($f->name,'fk') !== false){
                $pos = strpos($f->name,'ID');
                $newstr = substr_replace(strtoupper($f->name[3]),"ID",$pos, 0);
                echo "<th>". $newstr ."</th>\n";
            }elseif(strpos($f->name,'ID') !== false){
                $pos = strpos($f->name,'ID');
                $newstr = substr_replace(strtoupper($f->name[0]),"ID",$pos, 0);
                echo "<th>". $newstr ."</th>\n";
            }else{
                echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
            }
        }
        echo "</tr>";

        //show the table content
        foreach($data as $row){
            //assumes that the first field is the primary key field
            $fieldlist = array();
            foreach($fields as $f){
                $fieldlist[] = $f->name;
            }

            $postdir = "/account/viewDetails.php";
            if(in_array("fk_accountID",$fieldlist) and $row["fk_accountID"] != ''){
                $id = $row["fk_accountID"];
                $newstudent = False;
            }elseif(in_array("accountID", $fieldlist) and $row['accountID'] != ''){
                $id = $row["accountID"];
                $newstudent = False;
            }elseif(in_array("studentID",$fieldlist) and $row['studentID'] != ''){
                $id = $row["studentID"];
                $newstudent = True;
            }

            if($newstudent)
                echo "<tr class='data' onclick=\"post('$postdir',{'id':$id, 'newstudent':1})\">\n";
            else
                echo "<tr class='data' onclick=\"post('$postdir',{'id':$id})\">\n";

            foreach($fields as $f){
                $title = str_replace('_',' ',$f->name);

                if(strpos($f->name,'date') !== false OR $f->name == 'DOB'){
                    if($row[$f->name] != ''){
                        $date = new DateTime($row[$f->name]);
                        echo "<td title='$title'>" . $date->format('m-d-Y')  ."</td>\n";
                    }else
                        echo "<td></td>\n";
                }elseif($f->name == 'time' or strpos($f->name,'time') !== false or strpos($f->name,'Time') !== false){
                    if($row[$f->name] != ''){
                        $time = new DateTime($row[$f->name]);
                        echo "<td title='$title'>" . $time->format('h:i A')  ."</td>\n";
                    }else
                        echo "<td></td>\n";
                }elseif($f->name == 'mailing_address' or strpos($f->name,'mailing_address') !== false){
                    echo "<td class='mailingaddress' style='max-width:290px;white-space:normal;'>".$row[$f->name] . "</td>";
                }else{
                    echo "<td title='$title'>" . $row[$f->name] ."</td>\n";
                }
            }
            echo "</tr>\n";
        }
        echo "</table>";
    }else
        echo "<h1>Error using the advanced show function (data,fields,title,postdir)</h1>";
}

//advanced show function for specific showing
function showAdvancedData2($data,$fields,$title,$postdir,$postval){
    if(count($data) > 0 and count($fields) > 0){
        $found = count($data);
        echo "<u>$found records found</u><br>\n";
        echo "<table class='data' align=\"center\">";

        //show the table title
        $total = count($fields);
        if($title != '')
            echo "<tr><th colspan=$total><b>$title</b></th></tr>\n";

        //show the table columns
        echo "<tr>\n";
        foreach ($fields as $f){
            if(strpos($f->name,'ID') !== false){
                $pos = strpos($f->name,'ID');
                $newstr = substr_replace($f->name, " ", $pos, 0);
                echo "<th>". $newstr ."</th>\n";
            }else{
                echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
            }
        }
        echo "</tr>";

        //show the table content
        foreach($data as $row){
            //assumes that the first field is the primary key field
            if($postdir != '')
                echo "<tr class='data' onclick=\"post('$postdir',{'id':$postval})\">\n";
            else
                echo "<tr class='data'>\n";

            foreach($fields as $f){
                $title = str_replace('_',' ',$f->name);

                if(strpos($f->name,'date') !== false OR $f->name == 'DOB'){
                    if($row[$f->name] != ''){
                        $date = new DateTime($row[$f->name]);
                        echo "<td title='$title'>" . $date->format('m-d-Y')  ."</td>\n";
                    }else
                        echo "<td></td>\n";
                }elseif($f->name == 'time' or strpos($f->name,'time') !== false or strpos($f->name,'Time') !== false){
                    if($row[$f->name] != ''){
                        $time = new DateTime($row[$f->name]);
                        echo "<td title='$title'>" . $time->format('h:i A')  ."</td>\n";
                    }else
                        echo "<td></td>\n";
                }else{
                    echo "<td title='$title'>" . $row[$f->name] ."</td>\n";
                }
            }
            echo "</tr>\n";
        }
        echo "</table>";
    }else
        echo "<h1>Error using the advanced show function (data,fields,title,postdir)</h1>";
}
///////////////////////////////////////////////////////////////////////////////////////////
//improved function to show data given fields
function showDataWithLimit2($data,$fields,$max){

    $found = count($data);
    echo "<u>$found records found</u><br>\n";
    if($found > $max)
        echo "only $max records will be shown<br>\n";
    echo "<form method='post'>\n";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";
    foreach ($fields as $f){
        if(strpos($f->name,'ID') !== false){
            $pos = strpos($f->name,'ID');
            $newstr = substr_replace($f->name, " ", $pos, 0);
            echo "<th>". $newstr ."</th>\n";
        }else{
            echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
        }
    }
    echo "<th></th><th></th>";
    echo "</tr>";
    $count = 1;
    foreach($data as $row){
        //get the row id
        $id = $row[$fields[0]->name];

        if($count < $max){
            echo "<tr>\n";
            foreach($fields as $f){
                $title = str_replace('_',' ',$f->name);

                if(strpos($f->name,'date') !== false OR $f->name == 'DOB'){
                    $date = new DateTime($row[$f->name]);
                    echo "<td title='$title'>" . $date->format('m-d-Y')  ."</td>\n";
                }elseif($f->name == 'time' or strpos($f->name,'time') !== false or strpos($f->name,'Time') !== false){
                    $time = new DateTime($row[$f->name]);
                    echo "<td title='$title'>" . $time->format('h:i A')  ."</td>\n";
                }else{
                    echo "<td title='$title'>" . $row[$f->name] ."</td>\n";
                }
            }
            //Button to edit the data. Requires that there be an update file
            echo "<td><button formaction='update/search_update.php' name='id' value=$id><img style='width:15px; height:15px;' src=\"/images/edit.png\"></button></td>\n";
            echo "<td><button formaction='delete/search_delete.php' name='id' value=$id><img style='width:15px; height:15px;' src=\"/images/x_mark.png\"></button></td>\n";

            echo "</tr>\n";
        }
        $count;
    }
    echo "</table>\n";
    echo "</form>\n";
}

//improved show data function which links to the update table if it exists
function showData2($data,$fields){
    $found = count($data);
    echo "<u>$found records found</u><br>\n";
    echo "<form action='/upload.php' method='POST' target='_blank'>\n";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";
    foreach ($fields as $f){
        if(strpos($f->name,'ID') !== false){
            $pos = strpos($f->name,'ID');
            $newstr = substr_replace($f->name, " ", $pos, 0);
            echo "<th>". $newstr ."</th>\n";
        }else{
            echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
        }
    }
    echo "</tr>";

    foreach($data as $row){
        //get the row id
        $id = $row[$fields[0]->name];

        echo "<tr class='data' onclick=\"post('/account/viewDetails.php',{id:$id})\">\n";
        foreach($fields as $f){
            $title = str_replace('_',' ',$f->name);

            if(strpos($f->name,'date') !== false OR $f->name == 'DOB'){
                $val = $row[$f->name];
                if($val != ''){
                    $date = new DateTime($row[$f->name]);
                    echo "<td title='$title'>" . $date->format('m-d-Y')  ."</td>\n";
                }else
                    echo "<td></td>\n";
            }elseif($f->name == 'file_location' or $f->name == 'file'){
                echo "<td style='width:160px;'>";
                if($row[$f->name] == ''){
                    echo "".$row[$f->name];
                }else{
                    $f_location = "/".$row[$f->name];
                    echo "<a href=\"$f_location\">".$row[$f->name]."</a><br>";
                }
                echo "</td>";
            }elseif($f->name == 'time' or strpos($f->name,'time') !== false or strpos($f->name,'Time') !== false){
                if($row[$f->name] != ''){
                    $time = new DateTime($row[$f->name]);
                    echo "<td title='$title'>" . $time->format('h:i A')  ."</td>\n";
                }else
                    echo "<td></td>\n";
            }else{
                echo "<td title='$title'>" . $row[$f->name] ."</td>\n";
            }
        }
        echo "</tr>\n";
    }

    echo "</table>";
    echo "</form>";
}

//function to show Editable data given fields
function showEditableData2($data,$fields){
    //show how many found
    $found = count($data);

        //create a table
        echo "<table class='editable' align=\"center\">";
        echo "<tr>\n";
        echo "</tr>\n";
        foreach ($fields as $f){
            echo "<tr>";
            echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";


        foreach($data as $row){
            $title = str_replace('_',' ',$f->name);
            echo "<td title='$title' style='text-align:left;' nowrap>\n";

            //save the id of each data row
            if($isPK){
                $isPK = false;
                $id = $row[$f->name];
            }

            if(strpos($f->name,'date') !== false OR $f->name == 'DOB'){
                $val = $row[$f->name];
                if($val == ''){
                    echo "<input type='date' name=\"$f->name\">\n";
                }else{
                    echo "<input type='date' name=\"$f->name\" value='$val'>\n";
                }
                echo "</td>\n";
            }elseif($f->name == 'file_location' or $f->name == 'file'){
                if($row[$f->name] == '')
                    $val = "no digital file on record<br>";
                else
                    $val = $row[$f->name];
                echo $val;
                echo "<input align='center' type='hidden' name='$f->name' id='$f->name' value='$val'>";
                echo "</td>\n";
            }elseif($f->name== 'size'){
                $size = $row['size'];
                echo $size;
                echo "<input type='hidden' name='$f->name' value=$size>";
                echo "</td>";
            }elseif($f->name == 'age'){
                $age = $row[$f->name];
                echo "$age";
                echo "<input type='hidden' name='$f->name$id' value='$age'>";
                echo "</td>\n";
            }elseif($f->name == "sex"){
                echo "<select class='selectpicker' name='$f->name$id'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>M</option>";
                echo "<option>F</option>";
                echo "</td>\n";
            }elseif($f->name == 'bank_account'){
                echo "<select class='selectpicker' style='width:100%;' name='$f->name'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>US BANK</option>";
                echo "<option>Alaska Mileage Plan: Visa</option>";
                echo "<option>Alaska Mileage Plan: Bank of America</option>";
                echo "<option>AMERICAN EXPRESS</option>";
                echo "<option>WELLS FARGO</option>";
                echo "</td>";
            }elseif($f->name == "allow_picture"){
                echo "<select class='selectpicker' name='$f->name$id'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>yes</option>";
                echo "<option>no</option>";
                echo "</td>\n";
            }elseif($f->name == "status" AND ($row[$f->name] == "Employee" OR $row[$f->name] == "Student")){
                echo "".$row[$f->name]."";
                echo "<input type='hidden' value='".$row[$f->name]."' name='$f->name$id'>";
                echo "</td>\n";
            }elseif($f->name == "status"){
                echo "<select class='selectpicker' name='$f->name$id' id='status'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>active</option>";
                echo "<option>inactive</option>";
                echo "</td>\n";
            }elseif($f->name == 'PT' OR $f->name == 'FT'){
                echo "<select class='selectpicker' name='$f->name$id'>";
                echo "<option>FT MONTH</option>\n";
                echo "<option>PT MONTH</option>\n";
                echo "<option selected>".$row[$f->name]."</option>\n";
                for($i = 0; $i < 32; $i++){
                    echo "<option>$i</option>\n";
                }
                echo "</td>";
            }elseif($f->name == 'expected_tuition' or $f->name == 'NLPS_tuition' or $f->name == 'state_payment'){
                $val = $row[$f->name];
                echo "$ <input style='width:60%;' value='$val' vatype=\"text\" name=\"$f->name$id\"></td>";
            }elseif($f->name == "type"){
                echo "<select style='width:100%;' class='selectpicker' style='width:100%;' name='$f->name$id'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>clock in</option>";
                echo "<option>clock out</option>";
                echo "</td>\n";
            }elseif($f->name == "auth_type"){
                echo "<select style='width:100%;' class='selectpicker' name='$f->name$id'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>P1</option>";
                echo "<option>P2</option>";
                echo "<option>CITC</option>";
                echo "<option>OCS</option>";
                echo "<option>SELF</option>";
                echo "<option>OTHER</option>";
                echo "</td>\n";
            }elseif($f->name == 'days_of_week'){
                $days = explode(',',$row[$f->name]);
                $dname = array();

                $dname[0] = 'SUN';$dname[1] = 'MON';$dname[2] = 'TUE';
                $dname[3] = 'WED';$dname[4] = 'THU';$dname[5] = 'FRI';
                $dname[6] = 'SAT';

                echo "<table border=none>";
                echo "<tr>";
                foreach($dname as $d)
                    echo "<td>$d</td>";
                echo "</tr>";
                echo "<tr>";

                foreach($dname as $d){
                    $found = false;
                    $name = strtolower($d);
                    $str ='';
                    foreach($days as $d_actual){
                        if($d_actual == "$name"){
                            $found = true;
                            $str = $d_actual;
                            break;
                        }
                    }

                    if($found == true){
                        echo "<td><input type='checkbox' name='$name' value='y' checked></td>\n";
                    }else{
                        echo "<td><input type='checkbox' name='$name' value='y'></td>\n";
                    }
                }
                
                echo "</tr>";
                echo "</table>";
                echo "</td>";
            }elseif($f->name == 'out_in'){
                echo "<select class='selectpicker' name='$f->name'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>+</option>";
                echo "<option>-</option>";
                echo "</td>\n";
            }elseif($f->name == "authorization"){
                echo "<select class='selectpicker' name='$f->name'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>P1</option>";
                echo "<option>P2</option>";
                echo "<option>CITC</option>";
                echo "<option>OCS</option>";
                echo "<option>SELF</option>";
                echo "<option>P1-n</option>";
                echo "<option>P2-n</option>";
                echo "<option>CITC-n</option>";
                echo "<option>OCS-n</option>";
                echo "</td>\n";
            }elseif($f->name == "assistance"){
                echo "<select class='selectpicker' name='$f->name'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>P1</option>";
                echo "<option>P2</option>";
                echo "<option>CITC</option>";
                echo "<option>OCS</option>";
                echo "<option>SELF</option>";
                echo "</td>\n";
            }elseif($f->name == 'tuition_type'){
                echo "<select class='selectpicker' name='$f->name'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>Full</option>";
                echo "<option>Part</option>";
                echo "</td>\n";
            }elseif($f->name == 'time' or strpos($f->name,'time') !== false or strpos($f->name,'Time') !== false or $f->name == 'lunch_in' or $f->name == 'lunch_out'){
                $tmp = $row[$f->name];
                if($tmp == ''){
                    $time = '';
                    echo "<input type='time' name='$f->name'>";
                }else{
                    $time = new DateTime($row[$f->name]);
                    $now = $time->format('h:i A');
                    echo "<input type='time' name='$f->name' value=$now>";
                }
                echo "</td>";

            }elseif(strpos($f->name,'ID') !== false){
                echo "" . $row[$f->name];
                echo "<input type='hidden' value='".$row[$f->name]."' name='$f->name'>";
                echo "</td>\n";
            }elseif(strpos($f->name, 'room') !== false){
                echo "<select class='selectpicker' name='$f->name'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>Purple</option>";
                echo "<option>Rainbow</option>";
                echo "<option>Orange</option>";
                echo "<option>Green</option>";
                echo "<option>Yellow</option>";
                echo "<option>Pink</option>";
                echo "<option>Blue</option>";
                echo "<option>Red</option>";
                echo "<option>S.A 1-3</option>";
                echo "<option>S.A 4-6</option>";
                echo "<option>Saturday</option>";
            echo "<option>DROP IN</option>";
                echo "</td>\n";
            }else{
                echo "<input style='width:97%;' class='editable' type='text' value=\"".$row[$f->name]."\" name='$f->name'></td>\n";
            }
        }
            echo "</tr>";
        }

        echo "</table>";
        echo "<br><br>";
}

//gets the field value of a particular table given its primary key
function getFieldValue($db,$table,$field,$id){
    $lower = strtolower($table);
    $pk = $lower."ID";
    $sql1 = "SELECT $field FROM $table";
    $sql2 = "WHERE $pk = $id";

    $sql = "$sql1 $sql2";

    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "function: getFieldValueByID<br>\n";
        echo "query: $sql<br>\n";
        echo "Error Description: ".mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

//get row from table givin 
function getRowByID($db,$table,$id){
    $lower = strtolower($table);
    $pk = $lower."ID";
    $sql = "SELECT * FROM $table WHERE $pk = $id";
    
    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>\n";
        echo "Error Description: ".mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

//function to get last inserted data
function getLastInsert($db, $table){
    $table_lwer = strtolower($table);
    $table_lwer .= "ID";
    $sql = "SELECT * FROM $table WHERE $table_lwer = LAST_INSERT_ID()";
    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>\n";
        echo "Error Description: ".mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}
//gets the attendance data given a first and last name
function getAccountByID($db,$id){

	$query = "SELECT * FROM Account
    WHERE accountID = $id";

    $result = mysqli_query($db,$query);

    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h2>";
        echo "function: getAccountByID<br>";
        echo "query: $query<br>\n";
        echo "Error Description: ".mysqli_error($db);
        echo "</h2>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

//gets the attendance data given a first and last name
function getAttendanceByID($db,$id,$status){

    $sql = "";
    if($status == 'Employee')
        $sql = "SELECT employee_attendanceID,first_name,last_name,date,time,verifier,type FROM Employee_Attendance,Employee WHERE employeeID = fk_employeeID AND employee_attendanceID = $id";
    elseif($status == 'Student')
        $sql = "SELECT attendanceID,first_name,last_name,date,time,verifier,type FROM Attendance,Student WHERE studentID = fk_studentID AND attendanceID = $id";

    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>\n";
        echo "Error Description: ".mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

//gets the student data given a first and last name
function getStudentByID($db,$id){

    $sql1 = "SELECT Student.*,(TIME(NOW())) AS `current-time` FROM Student";
    $sql2 = "WHERE studentID = $id";

    $sql = "$sql1 $sql2";

    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "function: getStudentByID<br>\n";
        echo "Error Description: ".mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}

//gets the student data given a first and last name
function getCCAByID($db,$id){

    $sql1 = "SELECT CCA.*,(TIME(NOW())) AS `current-time` FROM CCA";
    $sql2 = "WHERE ccaID= $id";

    $sql = "$sql1 $sql2";

    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "function: getCCAByID<br>\n";
        echo "Error Description: ".mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}
//gets the employee data given a first and last name
function getEmployeeByName($db,$first_name,$last_name){
    $sql1 = "SELECT employeeID,first_name,last_name,(TIME(NOW())) AS `current-time` FROM Employee";
    $sql2 = "WHERE first_name = \"$first_name\" AND last_name = \"$last_name\"";
    $sql = "$sql1 $sql2";
    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>\n";
        echo "Error Description: ".mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

//gets the student data given a first and last name
function getStudentByName($db,$first_name,$last_name){

    $sql1 = "SELECT studentID,first_name,last_name,room,(TIME(NOW())) AS `current-time` FROM Student";
    $sql2 = "WHERE first_name LIKE \"$first_name\" AND last_name LIKE \"$last_name\"";
    $sql = "$sql1 $sql2";
    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>\n";
        echo "Error Description: ".mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

//gets the punch data given a first and last name
function getPunchByID($db,$id){

    $sql1 = "SELECT * FROM Punch";
    $sql2 = "WHERE punchID = $id";
    $sql = "$sql1 $sql2";
    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>\n";
        echo "Error Description: ".mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

//gets the employee data given a first and last name
function getEmployeeByID($db,$id){

    $sql1 = "SELECT employeeID,first_name,last_name,(TIME(NOW())) AS `current-time` FROM Employee";
    $sql2 = "WHERE employeeID = $id";
    $sql = "$sql1 $sql2";
    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>\n";
        echo "Error Description: ".mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

//gets the charge data given a first and last name
function getChargeByID($db,$id){

    $sql1 = "SELECT student_1,student_2,student_3,Charge.*,(TIME(NOW())) AS `current-time` FROM Charge,Account";
    $sql2 = "WHERE chargeID = $id AND fk_accountID = accountID";
    $sql = "$sql1 $sql2";
    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>\n";
        echo "Error Description: ".mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}
//gets the payment data given a first and last name
function getPaymentByID($db,$id){

    $sql1 = "SELECT student_1,student_2,student_3,Payment.*,(TIME(NOW())) AS `current-time` FROM Payment,Account";
    $sql2 = "WHERE paymentID = $id AND fk_accountID = accountID";
    $sql = "$sql1 $sql2";
    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>\n";
        echo "Error Description: ".mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

//gets the expenditure by ID
function getExpenditureByID($db,$id){

    $sql = "SELECT * FROM Expenditure WHERE expenditureID = $id";
    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>\n";
        echo "Error Description: ".mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

function queryActiveStudents($db){
    $sql = "SELECT studentID, first_name,last_name FROM Student WHERE status = 'active' ORDER BY last_name";
    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "query: $sql<br>\n";
        echo "Error Description: ".mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}

?>
