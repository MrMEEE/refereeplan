<?php
  require("refereeplan.ajax.common.php");
  
  if($_POST["changeSource"] != ""){
  
    mysql_query("UPDATE `config` SET `value`='".$_POST['changeSource']."' WHERE `name`='gamesource'");
  
  }
  
  if(isset($_POST["changeClub"])){
    
    $clubinfo = explode(":",$_POST["clubselect"][0]);
    $clubname = $clubinfo[1];
    foreach($_POST["clubselect"] as $club){
       if($club != ":"){
          $clubinfo = explode(":",$club);
          $clubids_arr[] = $clubinfo[0];
       } 
    }
    $clubids=implode(",",$clubids_arr);
    file_put_contents ( "test" , $clubids );

    mysql_query("UPDATE `config` SET `value`='".$clubname."' WHERE `name`='clubname'");
    mysql_query("UPDATE `config` SET `value`='".$clubids."' WHERE `name`='clubids'");  

  }
  
  if(isset($_POST["updatesUrl"])){
  
    mysql_query("UPDATE `config` SET `value`='".$_POST["updatesUrl"]."' WHERE `name`='updatesurl'");
  
  }
  
  if(isset($_POST["getGyms"])){
  
    $config = getConfiguration();
    $arrGyms = explode(",",$config['gyms']);
    
    switch($_POST["getGyms"]){
    
    case "get":
    
		$json = '{ ';
		$i = 0;
		foreach ($arrGyms as $gym){
		      $json .= '"'.$gym.'" : "'.fixCharacters($gym).'", ';
		      $i++;
		}
		$json = substr_replace($json ,"",-2);
		$json .= "}";
		echo $json;
		
		break;
    
    case "add":
    
		if(!in_array($_POST["gymName"],$arrGyms)){
			if($_POST["gymName"] != ""){
				mysql_query("UPDATE `config` SET `value`='".$config['gyms'].",".fixCharacters($_POST["gymName"])."' WHERE `name`='gyms'");
			}
		}
		
		break;
    case "remove":
    
		if(in_array($_POST["gymName"],$arrGyms)){
			$newgyms = str_replace($_POST["gymName"],"",$config['gyms']);
			$newgyms = str_replace(",,",",",$newgyms);
			mysql_query("UPDATE `config` SET `value`='".$newgyms."' WHERE `name`='gyms'");
		}
		break;
    }
  }
?>