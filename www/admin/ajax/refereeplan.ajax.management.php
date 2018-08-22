<?php
require_once("refereeplan.ajax.common.php");
getIncludes();

$currentUser = mysqli_fetch_assoc(getCurrentUser());

switch($_POST['action']){
  
  case "getClubInfo":
  
	$query = ref_mysql_query("SELECT * FROM `config` WHERE `id`='".$_POST['id']."'");
	
	$club = mysqli_fetch_assoc($query);
	
	$json = '[ { "name": "'.$club['clubname'].'", "id": "'.$club['id'].'", "enabled": "'.$club['enabled'].'" } ]';
	
	echo $json;
  
  break;

	
}

?>
