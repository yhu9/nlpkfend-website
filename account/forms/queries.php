
<?php

function searchForm($db,$aid){

    $query = "SELECT formID as fid,fk_accountID as aid, date, file, size,description FROM Form WHERE fk_accountID = $aid";

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
    }else{
        echo "Error getting Form Data!<br>\n";
        echo "Error: ". mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

function getform_fields($db){
    $query = "SELECT * FROM Form LIMIT 1";

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
    }else{
        echo "Error getting Form Data!<br>\n";
        echo "Error: ". mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

function getformByID($db,$id){
    $query = "SELECT * FROM Form WHERE formID = $id";

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
    }else{
        echo "Error getting Form Data!<br>\n";
        echo "Error: ". mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

function showDeletableForm($data,$fields){
    echo "<div class='datahandler'>";
    echo "<form method='POST' action='execute_deleteForm.php'>";
    $aid = $data[0]['aid'];
    echo "<input type='hidden' name='aid' value=$aid>";
    echo "<table class='data' align=\"center\">";
    echo "<tr >\n";

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
    echo "<th>DEL</th></tr>";

    //create the content data
    if(count($data) > 0){
        foreach($data as $row){
            $id = $row['fid'];
            echo "<tr class='data' onclick=\"post('/account/forms/update/search_update.php',{'id':$id})\">\n";
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
            echo "<td><button name='id' value='$id'><img style='with:15px;height:15px;background-color:red;' src='/images/x_mark.png'></button></td>\n";
            echo "</tr>\n";
        }
    }else{
        foreach($fields as $f){
            echo "<td></td>\n";
        }
    }
    echo "</table>";
    echo "</form>";
    echo "</div>";
}
?>
