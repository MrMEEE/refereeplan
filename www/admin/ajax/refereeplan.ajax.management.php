<?php
require_once("refereeplan.ajax.common.php");
getIncludes();

$currentUser = mysqli_fetch_assoc($GLOBALS['link'],getCurrentUser());

switch($_POST['action']){
  
  case "getClubInfo":
  
	$query = ref_mysql_query("SELECT * FROM `config` WHERE `id`='".$_POST['id']."'");
	
	$club = mysqli_fetch_assoc($GLOBALS['link'],$query);
	
	$json = '[ { "name": "'.$club['clubname'].'", "id": "'.$club['id'].'" } ]';
	
	echo $json;
  
  break;

	
}

?>
