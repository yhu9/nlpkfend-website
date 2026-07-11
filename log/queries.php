
<?php

//Get basic information
function getLogBasic($db){
    $data = array();
    $sql = "SELECT pk_info,table_name,date,time,`function`,old_data,new_data,users_logged_in FROM Log ORDER BY date DESC,time DESC LIMIT 1000";
    $result = mysqli_query($db,$sql);

    if($result !== false){
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
        $fields = mysqli_fetch_fields($result);
    }else{
        echo "Error: getting basic log query<br>\n";
    }

    $return = array();
    $return['data'] = $data;
    $return['fields']= $fields;

    return $return;
}

function showLogBasic($db){
    $data = getLogBasic($db);
    if(count($data['data'] ?? []) > 0 and count($data['fields'] ?? []) > 0){
        $found = count($data['data'] ?? []);
        echo "<u>$found records found</u><br>\n";
        echo "<table class='datasmall' align=\"center\">";

        //show the table columns
        echo "<tr>\n";
        foreach ($data['fields'] as $f){
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
        foreach($data['data'] as $row){
            //assumes that the first field is the primary key field
            $id = $row[$data['fields'][0]->name];
            $pk_field = $data['fields'][0]->name;
            $table = $row['table_name'];
            echo "<tr onclick=\"post('viewDetails.php',{'id':$id,'table':'$table','pk_field':'$pk_field'})\">\n";

            foreach($data['fields'] as $f){
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
                    echo "<td title='$title' wrap>" . $row[$f->name] ."</td>\n";
                }
            }
            echo "</tr>\n";
        }
        echo "</table>";
    }else
        echo "<h1>Error using the advanced show function (data,fields,title,postdir)</h1>";
}

//get log by id
function getLog($db,$table,$id){
    $data = array();
    $pk_name = strtolower($table) ."ID";
    $sql = "SELECT * FROM $table WHERE $pk_name = $id";
    $result = mysqli_query($db,$sql);

    if($result !== false){
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
        $fields = mysqli_fetch_fields($result);
    }else{
        echo "Error: getting current info<br>\n";
    }

    $return = array();
    $return['data'] = $data;
    $return['fields']= $fields;

    return $return;
}

//Show the attendance sheet
function showDeleteableLog($db,$data,$fields){
    $found = count($data ?? []);
    echo "<u>$found records found</u><br>\n";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";
    foreach ($fields as $f){
        echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
    }
        echo "<th>DELETE THIS log</th>\n";
    echo "</tr>";
        
    foreach($data as $row){
        echo "<tr>\n";
        $idData = getLogByName($db,$row['first_name'],$row['last_name']);
        $id = $idData['data'][0]['logID'];
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
function showDetailedData($db,$table,$id){

    $data = getLog($db,$table,$id);
    echo "<form action='/upload.php' method='POST' enctype='multipart/form-data' target='_blank'>\n";
    echo "<table class='data'>";

    //Button to edit the data. Requires that there be an update file
    echo "<tr><th colspan=2>";
    echo "<div align=\"center\"><b>Log Information</b></div>";
    echo "<div align=\"right\">";
    echo "</div>";
    echo "</th></tr>";

    foreach ($data['fields'] as $f){
        echo "<tr>\n";
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
