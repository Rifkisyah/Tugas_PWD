<?php
    $timeout = 1800; // 30 minutes
    if(isset($_SESSION['last_activity']) && (time() -  $_SESSION['last_activity'] > $timeout)){
        session_unset(); // unset $_SESSION variables for the run-time
        session_destroy(); // destroy session data in storage
        header("location: ../admin/signin.php?error=session_expired");
    }
    
    $_SESSION['last_activity'] = time(); // update last activity time stamp
?>