<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    <link rel="stylesheet" type="text/css" href="print.css">
</head>
<body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type='text/javascript' src="/js/js_main.js"></script>
<script>
$(document).ready(function () {
  $('select').each(function () {
    var select = $(this);
    var selectedValue = select.find('option[selected]').val();

    if (selectedValue) {
      select.val(selectedValue);
    } else {
      select.prop('selectedIndex', 0);
    }
  });
});
</script>
<script>
function cellForm(val,name,id){
    var nameid = name.concat(id);
    var cell = document.getElementById(id);

    //clear the content of the cell
    cell.onclick = function() {return false;}
    cell.innerHTML = '';

    //create the form
    var form = document.createElement("INPUT");
    form.classList.add('cellform');
    form.value = val;
    form.setAttribute('name','note');
    form.addEventListener("change",function (){ submitQuery(id,form.value);},false);

    //create the post input
    var id_input = document.createElement('INPUT');
    id_input.setAttribute('type','hidden');
    id_input.setAttribute('name','aid');
    id_input.setAttribute('value',id);
    id_input.setAttribute('id',nameid);

    //append the form
    cell.appendChild(form);
    cell.appendChild(id_input);
}

function submitQuery(id,val){
    var a = new XMLHttpRequest();
    a.open("POST","live_edit.php",true);
    //Send the proper header information along with the request
    a.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    a.onreadystatechange = function() {
        if( this.readyState != 4) return;
        if( this.status != 200) return alert("ERROR "+this.status+" "+this.statusText);
        alert(this.responseText);
    };
    a.send("aid=" + id +"&note=" +val);
}
</script>

<div class='key'>
<div style='width:100%;display:inline-block;'><div style='background-color: #f49542; float:left; width:30px; height:30px;'></div><b>- AUTOPAY</b></div>
<div style='width:100%;display:inline-block;'><div style='background-color: #42ebf4; float:left;width:30px; height:30px;'></div><b>- DROP IN</b></div>
</div>

<h1>Account Information</h1>
<div class="menu_color">
<hr>
    <div class="menu">
        <span ><a class="button" href="../homepage.php">Homepage</a></span>
        <span ><a class="button" href="account.php">Account Info</a></span>
        <span ><a class="button" href="payment/payment.php">All Payments</a></span>
        <span ><a class="button" href="charge/charge.php">All Charges</a></span>
        <span ><a class="button" href="add/addAccount_page.php">Add Account</a></span>
        <span ><a class="button" href="../logout.php">Logout</a></span>
    </div>
<hr>

    <div class='sorter'>
    <b>Sort By: </b>
    <select class='selectpicker' id="mySelect" style='width:10%; font-size:15px;' onChange="contentChanger()">
        <option value='showAll'>All Accounts
        <option value='showActive' selected>Active Accounts
        <option value='showInactive'>Inactive Accounts
        <option value='showAccountsDue'>Balance Due
        <option value='showAuth'>Updated Contracts Due
    </select>
    </div>
</div>

<div class='content'>
<p id="content">
</p>
</div>


<?php
include("../config.php");
$db = connect();
checkAdvancedSession(3);

//Create the query
include("queries.php");

$balance_data = queryAccountsAll($db);
$active_data = queryAccountsActive($db);
$inactive_data = queryAccountsInactive($db);
$due_data = queryAccountsDue($db);
$auth_data = queryLateAuthorization($db);

//get the account Data basic query
echo "<div id='showAll' hidden>";
showAccountData($db,$balance_data['data'],$balance_data['fields'],'all');
echo "</div>";

//get the account Data basic query
echo "<div id='showActive' hidden>";
showAccountData($db,$active_data['data'],$active_data['fields'],'active');
echo "</div>";

//get accounts inactive
echo "<div id='showInactive' hidden>";
showAccountData($db,$inactive_data['data'],$inactive_data['fields'],'inactive');
foreach( $inactive_data['fields'] as $row)
    echo "".$row->name;
echo "</div>";

//get accounts with a balance
echo "<div id='showAccountsDue' hidden>";
showAccountData($db,$due_data['data'],$due_data['fields'],'all');
echo "</div>";

//get accounts authorization end date
echo "<div id='showAuth' hidden>";
showLateAuthorization($auth_data['data'],$auth_data['fields']);
echo "</div>";

////////////////////////////////////////////////////////////////////////////
?>

<script type='text/javascript'>
    var x = document.getElementById("showActive").innerHTML
    document.getElementById("content").innerHTML = "" + x
</script>

</body>
</html> 




