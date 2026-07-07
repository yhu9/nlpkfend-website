
<?php

function payroll_basic(){
    
    $sql1 = "SELECT first_name,last_name,Payroll.* FROM Employee,Payroll";
    $sql2 = "WHERE Employee.employeeID = fk_employeeID";
    $sql3 = "AND period_start > DATE_ADD(NOW(),INTERVAL -6 MONTH)";
    $sql4 = "ORDER BY last_name";

    $sql = "$sql1 $sql2 $sql3 $sql4";

    return($sql);
}

?>
