<?php
require("refereeplan.ajax.common.php");

switch($_POST['syncAction']){

  case "syncTeam":
        getIncludes();
	
	$returns = syncTeam($_POST['syncTeamId'],$_POST['syncTeamUrl']);
	
	$json = '[ ';
	
	foreach($returns as $return){
	
	      $json .= '{ "text": "'.$return.'" }, ';
	
	}
	
	$json = substr_replace($json ,"",-2);
	$json .= " ]";
	echo $json;

  break;
  case "getTeams":
  
      $calendars = mysql_query("SELECT * FROM `calendars`");
      $json = '[ ';

      while($cal=mysql_fetch_assoc($calendars)){
      
	    $json .= '{ "id": "'.$cal["id"].'", "name": "'.$cal["team"].'", "address": "'.$cal["address"].'" }, ';
      
      }
      
      $json = substr_replace($json ,"",-2);
      $json .= " ]";
      echo $json;
 
  break;
}

?>