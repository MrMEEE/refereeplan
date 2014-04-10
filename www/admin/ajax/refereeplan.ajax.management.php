<?php
require_once("refereeplan.ajax.common.php");
getIncludes();

$currentUser = mysql_fetch_assoc(getCurrentUser());

switch($_POST['action']){
  
  case "getClubInfo":
  
	$query = ref_mysql_query("SELECT * FROM `config` WHERE `id`='".$_POST['id']."'");
	
	$club = mysql_fetch_assoc($query);
	
	$json = '[ { "name": "'.$club['clubname'].'", "id": "'.$club['id'].'" } ]';
	
	echo $json;
  
  break;

	
}

?>