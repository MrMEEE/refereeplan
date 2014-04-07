<?php
require_once("refereeplan.ajax.common.php");
getIncludes();

$currentUser = mysql_fetch_assoc(getCurrentUser());

switch($_POST['action']){

  case "checkTeamExists":
        
        $query = ref_mysql_query("SELECT * FROM `teams` WHERE `name`='".$_POST['name']."' AND `clubid`='".$currentUser['clubid']."'");
        
        $exists = mysql_num_rows($query);

        $json = '[ { "exists": "'.$exists.'" } ]';
        
        echo $json;
	
  break;
  
  case "createTeam":
  
	ref_mysql_query("INSERT INTO `teams` (`name`,`contactid`,`clubid`) VALUES ('".$_POST['name']."','".$_POST['contactid']."','".$currentUser['clubid']."')");
	
	$json = '[ { "id": "'.mysql_insert_id().'" } ]';
	
	echo $json;
  
  break;
  
  case "removeTeam":
	
	ref_mysql_query("DELETE FROM `teams` WHERE `id`='".$_POST['id']."'");
  
  break;
  
  case "editTeam":
  
	ref_mysql_query("UPDATE `teams` SET `name`='".$_POST['name']."',`contactid`='".$_POST['contactid']."' WHERE `id`='".$_POST['id']."'");
  
  break;
  
  case "getTeamInfo":
  
	$query = ref_mysql_query("SELECT * FROM `teams` WHERE `id`='".$_POST['id']."'");
	
	$team = mysql_fetch_assoc($query);
	
	$json = '[ { "name": "'.$team['name'].'", "contactid": "'.$team['contactid'].'" } ]';
	
	echo $json;
  
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