<html>
    <head>
    <link rel="stylesheet" type="text/css" href="/mystyle.css">
    </head>
    <body>
    <section style='position: relative; display:inline; width: 25%; border:1px solid;' >
    <div style='float:right;' >
    <h3 style='display:inline; text-align:left;' >NLP Tuition ==> Daily Fee + Monthly Fee + Reg Fee</h3>
    <h3 style='display:inline;'>State Payment ==> Daily Fee + Monthly Fee + Reg Fee</h3>
    </div>
    </section>
        <h1>Add Contract Form</h1>
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
                //connect to database
                include("/var/www/html/config.php");
                include("/var/www/html/student/cca/queries.php");
                $db = connect();
                $sess = checkAdvancedSession(3);

                //pass the database information to javascript only if session passes
                if($sess == 1){
                    $sdata = queryActiveStudents($db);
                    echo "<script type='text/javascript'>\n";
                    echo 'var phpdata = ' . json_encode($sdata['data']) .';' ;
                    echo "</script>\n";
                }else{
                    echo "<p class='error'>THERE IS A PROBLEM WITH YOUR CONNECTION <br> PLEASE LOG OUT AND LOG BACK IN</p>";
                }


                //check if any id info came with table info from post
                $id = $_POST['id'];
                $aid = $_POST['aid'];

                if($id != ''){
                    $sdata = getStudentByID($db,$id);
                    showData($sdata['data'],$sdata['fields']);
                    echo "<br><br><br>\n";
                }elseif($aid != ''){
                    $sdata = queryStudentAccount($db,$aid);
                    showData($sdata['data'],$sdata['fields']);
                }
                echo "<script type='text/javascript'>\n";
                echo 'var defaultselect = ' . json_encode($sdata['data']) .';' ;
                echo "</script>\n";

?>

    <script type="text/javascript">
    var count = 0;

    function copayCalculator(param){
        rownum = param.target.myParam;
        var rowname1 = "copay_" + rownum;
        var rowname2 = "NLPS_tuition_" + rownum;
        var rowname3 = "state_payment_" + rownum;

        var copayref  = document.getElementById(rowname1);
        var nlp_tuitionref = document.getElementById(rowname2);
        var state_paymentref = document.getElementById(rowname3);

        var nlp_tuit = nlp_tuitionref.value;
        var state_payment = state_paymentref.value;
        var copay = nlp_tuit - state_payment;
        if(copay < 0)
            copay = 0.00;

        copay = parseFloat(Math.round(copay * 100) / 100).toFixed(2);

        copayref.innerHTML = "$ " + copay;
    }


    function addRow(tableID){
        count += 1;
        var tableref = document.getElementById(tableID);
        var newRow = tableref.insertRow(-1);

        //create the name select column
        var newCell = newRow.insertCell(-1);
        var sel = document.createElement('select');
        sel.classList.add("full");
        sel.name = 'fk_studentID_' + count;
        var opt = document.createElement('option'); //blank option
        sel.appendChild(opt);
        for(var i = 0; i < phpdata.length; i++){    //make options for select
            var id = phpdata[i]['studentID'];
            var first_name = phpdata[i]['first_name'];
            var last_name = phpdata[i]['last_name'];
            var full_name = last_name + ', ' + first_name;
            var opt = document.createElement('option');
            opt.innerHTML = full_name;
            opt.value = id;
            sel.appendChild(opt);
        }
        newCell.appendChild(sel);
        
        //create assistance form
        var newCell = newRow.insertCell(-1);
        var input1 = document.createElement('select');
        input1.classList.add('full');
        input1.name = 'assistance_' + count;
        var opt1 = document.createElement('option');
        var opt2 = document.createElement('option');
        var opt3 = document.createElement('option');
        var opt4 = document.createElement('option');
        var opt5 = document.createElement('option');
        var opt6 = document.createElement('option');
        opt1.innerHTML = 'SELF';
        opt2.innerHTML = 'P1';
        opt3.innerHTML = 'P2';
        opt4.innerHTML = 'CITC';
        opt5.innerHTML = 'OCS';
        opt6.innerHTML = 'OTHER';
        input1.appendChild(opt1);
        input1.appendChild(opt2);
        input1.appendChild(opt3);
        input1.appendChild(opt4);
        input1.appendChild(opt5);
        input1.appendChild(opt6);
        newCell.appendChild(input1);

        //create st date form
        var newCell = newRow.insertCell(-1);
        var input2 = document.createElement('INPUT');
        input2.setAttribute("type",'date');
        input2.name = 'start_date_' + count;
        newCell.appendChild(input2);

        //create end date form
        var newCell = newRow.insertCell(-1);
        var input3 = document.createElement('INPUT');
        input3.setAttribute("type",'date');
        input3.name = 'end_date_' + count;
        newCell.appendChild(input3);

        //create FT Form
        var newCell = newRow.insertCell(-1);
        var sel = document.createElement('select');
        sel.classList.add('tall');
        sel.name = "FT_" + count;
        var option = document.createElement('option');
        option.innerHTML= '0';
        sel.appendChild(option);
        var option = document.createElement('option');
        option.innerHTML= 'MONTH';
        sel.appendChild(option);
        for(i = 1; i <= 31; i++){
            var tmp = document.createElement('option');
            tmp.innerHTML = "" + i;
            sel.appendChild(tmp);
        }
        newCell.appendChild(sel);

        //create PT Form
        var newCell = newRow.insertCell(-1);
        var input = document.createElement('select');
        input.classList.add('tall');
        input.name = "PT_" + count;
        var option = document.createElement('option');
        option.innerHTML= '0';
        input.appendChild(option);
        var option = document.createElement('option');
        option.innerHTML= 'MONTH';
        input.appendChild(option);
        for(i = 1; i <= 31; i++){
            var option = document.createElement('option');
            option.innerHTML= "" + i;
            input.appendChild(option);
        }
        newCell.appendChild(input);

        //create SAT Form
        /*
        var newCell = newRow.insertCell(-1);
        var input = document.createElement('select');
        input.classList.add('tall');
        input.name = "Sat_" + count;
        var option = document.createElement('option');
        option.innerHTML= '0';
        input.appendChild(option);
        for(i = 1; i <= 6; i++){
            var option = document.createElement('option');
            option.innerHTML= "" + i;
            input.appendChild(option);
        }
    newCell.appendChild(input);
        */

        //NLPS Tuition form
        var newCell = newRow.insertCell(-1);
        var text = document.createTextNode("$ ");
        var input4 = document.createElement('INPUT');
        input4.addEventListener("change",copayCalculator,false);
        input4.myParam = count;
        input4.classList.add('tall');
        input4.setAttribute("type",'number');
        input4.setAttribute("step",'any');
        input4.setAttribute("min",'0');
        input4.name = "NLPS_tuition_" + count;
        input4.id = "NLPS_tuition_" + count;
        input4.value = "0.00";
        input4.setAttribute("min","0.00");
        newCell.appendChild(text);
        newCell.appendChild(input4);
        
        //State payment form
        var newCell = newRow.insertCell(-1);
        var text = document.createTextNode("$ ");
        var input5 = document.createElement('INPUT');
        input5.addEventListener("change",copayCalculator,false);
        input5.myParam = count;
        input5.classList.add('tall');
        input5.setAttribute("type",'number');
        input5.setAttribute("step",'any');
        input5.setAttribute("min",'0');
        input5.name = "state_payment_" + count;
        input5.id = "state_payment_" + count;
        input5.value = "0.00";
        input5.setAttribute("min","0.00");
        newCell.appendChild(text);
        newCell.appendChild(input5);

        //copay is auto calcuated through a onchange updater on the text
        var newCell = newRow.insertCell(-1);
        var par = document.createElement("p");
        par.classList.add('RED');
        par.innerHTML = "NLPS Tuition - State Payment";
        par.id = "copay_" + count;
        newCell.appendChild(par);

        //State payment form
        var newCell = newRow.insertCell(-1);
        var text = document.createTextNode("$ ");
        var input5 = document.createElement('INPUT');
        input5.classList.add('tall');
        input5.setAttribute("type",'text');
        input5.name = "note_" + count;
        input5.id = "note_" + count;
        newCell.appendChild(input5);
        
        //keep track of the number of rows to pass as post
        var rowcount = document.getElementById('rowcount');
        rowcount.value = count;
    }

    </script>

    <form method='POST'>
    <input type='hidden' name='count' id='rowcount' value=0>
    <table style='width:70%;' class='addcontractform' BORDER="1" id='add_form'>
        <tr><th rowspan=2 style='vertical-align:middle !important;'>
        <button class='button' type='button' style='width: 100%; background-color:#ffffff;' onclick="addRow('add_form')">+ Insert New Student +</button>
        </th><th style='text-align: center;' colspan=9>Contract Information</th>
        <tr><th>Assistance</th><th>St Date</th><th>End Date</th><th>FT</th><th>PT</th><th>NLP Tuition</th><th>State Payment</th><th>CO-PAY</th><th>Note</th></tr>
    </table>
    <br><br>
    <input type='submit' value='ADD CONTRACT NOW' formaction="executeAddCCA.php">
    </form>
    </body>
</html>
