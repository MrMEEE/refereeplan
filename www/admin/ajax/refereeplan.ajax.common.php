<?php

require("../connect.php");
require("../refereeplan.common.functions.php");
getIncludes();

switch($_POST['action']){

    case "fetchText":
	  
	  $string = fetchText($_POST['string'],"javascript");
	  
	  $json = '[ { "text": "'.$string.'" } ]';
	  
	  echo $json;
    
    break;
    
    case "logon":
    
	  if(mysql_num_rows(getCurrentUser("POST")) > 0){
		$status = "0";
		session_start();
		$_SESSION['rpusername']=$_POST['username'];
		$_SESSION['rppasswd']=$_POST['password'];
		session_write_close();
	  }else{
		$status = "1";
	  }
	  	  
	  $json = '[ { "status": "'.$status.'" } ]';
	  
	  echo $json;
    
    break;
    
    case "logout":
	  session_unset();
	  session_destroy();
    break;

}

?>