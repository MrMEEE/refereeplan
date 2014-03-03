<?php 
case "logout":
    session_unset();
    session_destroy();
    //unset($_SESSION["POSTDATA"]);
    //header("Refresh:0; url=./");
    header("Location: ./");exit;
break;

?>
