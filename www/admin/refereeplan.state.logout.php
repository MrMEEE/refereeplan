<?php 
case "logout":
    mysqli_query($GLOBALS['link'],"INSERT INTO `logons` (`username`,`passwdhash`,`time`,`status`) VALUES ('".$_SESSION['rpusername']."','".$_SESSION['rppasswd']."',NOW(),'2')");
    session_unset();
    session_destroy();
    header("Location: ./");exit;
break;

?>
