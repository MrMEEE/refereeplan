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
		$_SESSION['rpclubid']=$_POST['club'];
		session_write_close();
	  }else{
		$status = "1";
	  }
	  
	  mysql_query("INSERT INTO `logons` (`username`,`passwdhash`,`time`,`status`,`clubid`) VALUES ('".$_POST['username']."','".$_POST['password']."',NOW(),'".$status."','".$_POST['club']."')");
	  	  
	  $json = '[ { "status": "'.$status.'" } ]';
	  
	  echo $json;
    
    break;

}

?>