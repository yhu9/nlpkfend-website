<html>
<head>
    <script type='text/javascript' src="/js/js_main.js"></script>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    <link rel="stylesheet" type="text/css" href="../print.css">
</head>
<body>
<div class='title'>
<h1>Search Student Form</h1>
</div>

<div class="menu_color">
<hr>
    <div class="menu">
        <span ><a class="button" href="../../homepage.php">Homepage</a></span>
        <span ><a class="button" href="../student.php">Student Info</a></span>
        <span ><a class="button" href="../cca/cca.php">Contracts/Authorizations</a></span>
        <span ><a class="button" href="../add/addStudent_page.php">Add Student</a></span>
        <span ><a class="button" href="../search/searchStudent_page.php">Search Student</a></span>
        <span ><a class="button" href="../../logout.php">Logout</a></span>
    </div>
<hr>
</div>

        <?php
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Connect to database
        include("../../config.php");
        $db = connect();

        //Get field values for student
        $sql = "SELECT * FROM Student";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $finfo = $result->fetch_fields();
            $sql1 = "SELECT * FROM Student";
            $sql2 = "";
            $sql3 = "ORDER BY";

            //Intialize and create line $sql2
            $first_pass = true;
            foreach($finfo as $field){
                $val_postname = "text_$field->name";
                $eq_postname = "eq_$field->name";
                $val = mysqli_real_escape_string($db,$_POST[$val_postname]);
                $eq = mysqli_real_escape_string($db,$_POST[$eq_postname]);

                $condition = "";
                if($val != "" and $val != "--"){
                    //if field is a numeric
                    if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                        $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                        $field->type == 246)
                    { 
                        $condition = "$field->name $eq $val";
                    //Otherwise it needs quotes
                    }else{
                        $condition = "$field->name $eq '$val'";
                    }

                    if($first_pass)
                        $sql2 .= "WHERE $condition";
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

            //Show the results
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
                showAdvancedData($data,$finfo,'Search Results',"../viewDetails.php");
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



