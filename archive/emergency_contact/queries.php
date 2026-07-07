
<?php

function emergency_contact_basic(){
    
    $sql1 = "SELECT first_name,last_name,Emergency_Contact.* FROM Emergency_Contact,Student,Student_to_Emergency_Contact";
    $sql2 = "WHERE studentID = fk_studentID AND emergency_contactID = fk_emergency_contactID";
    $sql3 = "ORDER BY last_name";

    $sql = "$sql1 $sql2 $sql3";

    return($sql);
}

?>
