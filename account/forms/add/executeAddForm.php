<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../../mystyle.css">
    <script type='text/javascript' src="/js/js_main.js"></script>
    </head>
    <body>
        <h1>Confirmation Page</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="/homepage.php">Homepage</a></span>
                <span ><a class="button" href="/account/account.php">Account Info</a></span>
                <span ><a class="button" href="/logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
            //connect to database
            include("../../../config.php");
            include("../../../upload.php");
            include("../queries.php");
            $db = connect();

            //get primary key field from post
            $id = $_POST['id'];
            $data = getform_fields($db);

            //Initialize and create the add form sql statement
            $sql1 = "INSERT INTO Form (";
            $sql2 = "VALUES (";
            $is_first = 1;
            foreach($data['fields'] as $field){
                if($field->name != "formID"){
                    $str_fieldname = mysqli_real_escape_string($db,$field->name);
                    $val = "";
                }
                
                if($field->name == 'fk_accountID'){
                    $val = $id;
                }elseif($field->name == 'file'){
                    $val = $_FILES['file']['name'];

                    //get name of the input field which is just the field name
                    $name = $field->name;
                    $target_dir = "../../../resources/nlp_data/account/$id/";
                    if($val != '')
                        uploader($name,$target_dir);
                }elseif($field->name == 'size'){
                    $val = $_FILES['file']['size'];
                }else
                    $val = mysqli_real_escape_string($db,is_array($_POST[$field->name] ?? '')?'':($_POST[$field->name] ?? ''));

                if($val != "" and $val != "--"){
                    if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                        $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                        $field->type == 246){
                        if($is_first == 1){
                            $sql1 .= "$str_fieldname";
                            $sql2 .= "$val";
                            $is_first= 0;
                        }
                        else{
                            $sql1 .= ",$str_fieldname";
                            $sql2 .= ",$val";
                        }
                    }else{
                        if($is_first == 1){
                            $sql1 .= "$str_fieldname";
                            $sql2 .= "\"$val\"";
                            $is_first= 0;
                        }
                        else{
                            $sql1 .= ",$str_fieldname";
                            $sql2 .= ",\"$val\"";
                        }
                    } 
                }
            }
            $sql1 .= ")";
            $sql2 .= ")";

            //Create the combined sql statement and execute the addition of the new form
            $sql = "$sql1 $sql2";
            $result = mysqli_query($db,$sql);

            //Check to make sure the INSERT statement executed
            if($result !== false){
                echo "<h3 align=\"center\">Successfully added new form!</h3>";

                //get last inserted form
                $formData = getAccountByID($db,$id);

                //show form data inserted
                $aid = $formData['data'][0]['fk_accountID'];
                if($aid == '')
                    showAdvancedData($formData['data'],$formData['fields'],"Form Information","/account/viewDetails.php");
                else
                    showAdvancedData2($formData['data'],$formData['fields'],"Form Information","/account/viewDetails.php",$aid);
            }else{
                echo "<div class='error'>";
                echo "sql statement: " .$sql;
                echo "<br><br><br><br>";
                echo("<u>Could not add the new form: <b>" .mysqli_error($db). "</b></u>");
                echo "</div>";
            }

            if (isset($result) && $result instanceof mysqli_result) $result->free();
            $db->close();
        ?>
    </body>
</html>
