<?php
require_once("refereeplan.ajax.common.php");
getIncludes();

switch($_POST['action']){

  case "checkTeamExists":
        
        $query = ref_mysql_query("SELECT * FROM `teams` WHERE `name`='".$_POST['name']."'");
        
        $exists = mysql_num_rows($query);

        $json = '[ { "exists": "'.$exists.'" } ]';
        
        echo $json;
	
  break;
  
  case "createTeam":
  
	$currentUser = mysql_fetch_assoc(getCurrentUser());
  
	ref_mysql_query("INSERT INTO `teams` (`name`,`contactid`,`clubid`) VALUES ('".$_POST['name']."','".$_POST['contactid']."','".$currentUser['clubid']."')");
  
  break;
  
  /*
	$returns = syncTeam($_POST['syncTeamId'],$_POST['syncTeamUrl']);
	
	$json = '[ ';
	
	foreach($returns as $return){
	
	      $json .= '{ "text": "'.$return.'" }, ';
	
	}
	
	$json = substr_replace($json ,"",-2);
	$json .= " ]";
	echo $json;

  
  */
	
}

?>