<?php
require("refereeplan.ajax.common.php");
require("../class/refereeplan.class.games.php");

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

$id = (int)$_POST['id'];

switch($_POST['action']){
	case 'delete':
		gameObj::delete($id);
	break;
	case 'rearrange':
		gameObj::rearrange($_POST['positions']);
	break;	
	case 'edit':
		$value = $_POST['value'];
		if ($_POST['param'] == "date"){
		      $fulldate = $_POST['value'];
		      $date = substr($fulldate,6,4);
		      $date .= "-";
		      $date .= substr($fulldate,3,2);
		      $date .= "-";
		      $date .= substr($fulldate,0,2);
		      $value = $date;
		}
		
		gameObj::edit($id,$value,$_POST['param']);
	break;
	
	case 'editreferee1team':
		gameObj::changeTeam($id,$_POST['team'],1);
	break;
	case 'editreferee2team':
		gameObj::changeTeam($id,$_POST['team'],2);
	break;
	case 'edittable1team':
                gameObj::changeTeam($id,$_POST['team'],3);
        break;
	case 'edittable2team':
		gameObj::changeTeam($id,$_POST['team'],4);
        break;
	case 'edittable3team':
                gameObj::changeTeam($id,$_POST['team'],5);
        break;
	
	case 'new':
		gameObj::createNew($_POST['text']);
	break;
}

?>