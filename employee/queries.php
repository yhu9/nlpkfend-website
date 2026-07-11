
<?php

//Get basic information
function getEmployeeBasic($db){
    $data = array();
    $sql = "SELECT employeeID,last_name,first_name,sex,DOB,age,status,date_start,job_description,preffered_phone,email_address FROM Employee ORDER BY status,last_name,first_name";
    $result = mysqli_query($db,$sql);

    if($result !== false){
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
        $fields = mysqli_fetch_fields($result);
    }else{
        echo "Error: getting basic employee query<br>\n";
    }

    $return = array();
    $return['data'] = $data;
    $return['fields']= $fields;

    return $return;
}

//get employee by id
function getEmployee($db,$id){
    $data = array();
    $sql = "SELECT * FROM Employee WHERE employeeID = $id";
    $result = mysqli_query($db,$sql);

    if($result !== false){
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
        $fields = mysqli_fetch_fields($result);
    }else{
        echo "Error: getting basic employee query<br>\n";
    }

    $return = array();
    $return['data'] = $data;
    $return['fields']= $fields;

    return $return;
}

//get inactive employees
function getEmployeeInactive($db){
    $data = array();
    $sql = "SELECT employeeID,last_name,first_name,sex,DOB,age,status,date_start,date_end,job_description,preffered_phone,email_address FROM Employee WHERE status = 'inactive' ORDER BY status,last_name,first_name";
    $result = mysqli_query($db,$sql);

    if($result !== false){
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
        $fields = mysqli_fetch_fields($result);
    }else{
        echo "Error: getting basic employee query<br>\n";
    }

    $return = array();
    $return['data'] = $data;
    $return['fields']= $fields;

    return $return;
}

//gets active employees
function getEmployeeActive($db){
    $data = array();
    $sql = "SELECT employeeID,last_name,first_name,sex,DOB,age,status,date_start,job_description,preffered_phone,email_address FROM Employee WHERE status = 'active' ORDER BY status,last_name,first_name";
    $result = mysqli_query($db,$sql);

    if($result !== false){
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
        $fields = mysqli_fetch_fields($result);
    }else{
        echo "Error: getting basic employee query<br>\n";
    }

    $return = array();
    $return['data'] = $data;
    $return['fields']= $fields;

    return $return;
}

//Show the attendance sheet
function showDeleteableEmployee($db,$data,$fields){

    $found = count($data);
    echo "<u>$found records found</u><br>\n";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";
    foreach ($fields as $f){
        echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
    }
        echo "<th>DELETE THIS EMPLOYEE</th>\n";
    echo "</tr>";
        
    foreach($data as $row){
        echo "<tr>\n";
        $idData = getEmployeeByName($db,$row['first_name'],$row['last_name']);
        $id = $idData['data'][0]['employeeID'];
        foreach($fields as $f){
            if(strpos($f->name,'date') !== false OR $f->name == 'DOB'){
                $date = new DateTime($row[$f->name]);
                echo "<td>" . $date->format('m-d-Y')  ."</td>\n";
            }elseif(strpos($f->name,'time') !== false or strpos($f->name,'Time') !== false){
                $time = new DateTime($row[$f->name]);
                echo "<td>" . $time->format('h:i:s A')  ."</td>\n";
            }else{
                echo "<td>" . $row[$f->name] ."</td>\n";
            }
        }
        echo "<td><button class='circularsmall' name='id' value='$id'>--</button></td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
}

//detailedView page
function showDetailedData($db,$eid){

    $data = getEmployee($db,$eid);

    echo "<form action='/upload.php' method='POST' enctype='multipart/form-data' target='_blank'>\n";
    echo "<table class='data'>";

    //Button to edit the data. Requires that there be an update file
    echo "<tr><th colspan=2>";
    echo "<div align=\"center\"><b>Employee Information</b></div>";
    echo "<div align=\"right\">";
    echo "<button formaction='/scheduler/add/addSchedule_page.php' style='width:20px; height:20px;' title='Add Schedule' name='id' value=$eid>+</button>\n";
    echo "<button formaction='/employee/update/search_update.php' name='id' value=$eid><img style='width:15px; height:15px;' src=\"/images/edit.png\"></button>\n";
    echo "<button formaction='/employee/delete/search_delete.php' name='id' value=$eid><img style='width:15px; height:15px;' src=\"/images/x_mark.png\"></button>\n";
    echo "</div>";
    echo "</th></tr>";

    foreach ($data['fields'] as $f){
        echo "<tr onclick=\"post('update/search_update.php',{'id':$eid})\">\n";
        if(strpos($f->name,'ID') !== false){
            $pos = strpos($f->name,'ID');
            $newstr = substr_replace($f->name, " ", $pos, 0);
            echo "<th>". $newstr ."</th>\n";
        }else{
            echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
        }

        foreach($data['data'] as $row){
            $val = $row[$f->name];
            if(strpos($f->name,'date') !== false or $f->name == 'DOB'){
                if($val != ''){
                    $date = new DateTime($row[$f->name]);
                    $val = $date ? $date->format('m-d-Y') : "";
                }else
                    $val = '';
            }else
                $val = $row[$f->name];
            echo "<td>$val</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
    echo "</form>";
}

?>
