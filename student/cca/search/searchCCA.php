<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../../mystyle.css">
    <link rel="stylesheet" type="text/css" href="../../print.css">
    <script type='text/javascript' src="/js/js_main.js"></script>
</head>
<body>
<div class='title'>
<h1>Search Results</h1>
</div>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../../student.php">Student Info</a></span>
                <span ><a class="button" href="../cca.php">Contracts/Authorizations</a></span>
                <span ><a class="button" href="../add/addCCA_page.php">Add Contract</a></span>
                <span ><a class="button" href="../search/searchCCA_page.php">Search Contract</a></span>
                <span ><a class="button" href="../../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Connect to database
        include("../../../config.php");
        $db = connect();

        //Get field values for cca
        $sql = "SELECT first_name,last_name,CCA.* FROM CCA,Student";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $finfo = $result->fetch_fields();
            $sql1 = "SELECT studentID,last_name,first_name,assistance,PT,FT,NLPS_tuition,state_payment,CCA.start_date,CCA.end_date,note FROM CCA,Student";
            $sql2 = "WHERE studentID = fk_studentID";
            $sql3 = "ORDER BY last_name,first_name,end_date DESC";

            //Intialize and create line $sql2
            $first_pass = true;
            foreach($finfo as $field){
                $val_postname = "text_$field->name";
                $eq_postname = "eq_$field->name";
                $val = mysqli_real_escape_string($db,$_POST[$val_postname]);
                $eq = mysqli_real_escape_string($db,$_POST[$eq_postname]);

                $condition = "";
                if($val != "" and $val != "--"){
                    //if it is a duplicate field in both student and cca
                    if($field->name =='start_date' or $field->name == 'end_date')
                        $f = "CCA.".$field->name;
                    else
                        $f = $field->name;

                    //if field is a numeric
                    if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                        $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                        $field->type == 246)
                    { 
                        $condition = "$f $eq $val";
                    //Otherwise it needs quotes
                    }else{
                        $condition = "$f $eq '$val'";
                    }
                    $sql2 .= " AND $condition";
                }
            }

            if (isset($result) && $result instanceof mysqli_result) $result->free();
            $sql = "$sql1 $sql2 $sql3";
            $result = mysqli_query($db,$sql);

            //Show the results
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if($result !== false){
                $found = ($result instanceof mysqli_result ? mysqli_num_rows($result) : 0);

                //get the data
                $data = array();
                while($row = mysqli_fetch_array($result)){
                    $data[] = $row;
                }

                //get the fields
                $finfo = $result->fetch_fields();

                //show the information
                showAdvancedData($data,$finfo,'Search Results',"../update/search_update.php");
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

        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
        ?>
		
</body>
</html> 



