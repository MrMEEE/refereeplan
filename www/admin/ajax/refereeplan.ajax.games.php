<?php
require_once("refereeplan.ajax.common.php");
require_once("../class/refereeplan.class.games.php");

error_log("!!!!!!!!!!!!!!!!!!!!".print_r($_POST, true));

switch($_POST['syncAction']){

  case "syncTeam":
        getIncludes();
	
	$returns = syncTeam($_POST['syncTeamId'],$_POST['syncTeamUrl'],$_POST['syncClubId']);
	
	$json = '[ ';
	
	foreach($returns as $return){
	
	      $json .= '{ "text": "'.$return.'" }, ';
	
	}
	
	$json = substr_replace($json ,"",-2);
	$json .= " ]";
	echo $json;

  break;
  case "getAllTeams":

  list ($clubs,$ids) = getClubs();

  //echo $clubs[array_search($row['clubid'],$ids)];

  $activeteamquery = ref_mysql_query("SELECT * FROM `config` WHERE `enabled`='1'");  

  while($team = mysqli_fetch_assoc($activeteamquery)){

     $activeteams .= $team['id'].",";

  }
 
  $activeteams = substr_replace($activeteams ,"",-1); 

  $calendars = ref_mysql_query("SELECT * FROM `calendars` WHERE `clubid` IN (".$activeteams.")");

  if(mysqli_num_rows($calendars) < 1){
     
     $json = '[ { "id": "", "name": "'.fetchText("No Teams").'", "address": "", "clubid": "", "clubname": ""} ]';

  }else{

      $json = '[ ';

      while($cal=mysqli_fetch_assoc($calendars)){
         
          $json .= '{ "id": "'.$cal["id"].'", "name": "'.$cal["team"].'", "address": "'.$cal["address"].'", "clubid": "'.$cal["clubid"].'", "clubname": "'.$clubs[array_search($cal["clubid"],$ids)].'" }, ';
      
       }
       $json = substr_replace($json ,"",-2);
       $json .= " ]";

  }
      
  echo $json;

  break;

  case "getTeams":
      
      $currentUser = mysqli_fetch_assoc(getCurrentUser());
      
      $calendars = ref_mysql_query("SELECT * FROM `calendars` WHERE `clubid`='".$currentUser['clubid']."'");
      
      if(mysqli_num_rows($calendars) < 1){

	    $json = '[ { "id": "", "name": "'.fetchText("No Teams").'", "address": "" } ]';
      
      }else{
	    
	    $json = '[ ';

	    while($cal=mysqli_fetch_assoc($calendars)){
	    
		  $json .= '{ "id": "'.$cal["id"].'", "name": "'.$cal["team"].'", "address": "'.$cal["address"].'" }, ';
	    
	    }
	    
	    $json = substr_replace($json ,"",-2);
	    $json .= " ]";
	    
      }
      echo $json;
 
  break;
}

$id = (int)$_POST['id'];
$currentUser = mysqli_fetch_assoc(getCurrentUser());


if(isset($_POST['action'])){
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
	
	case 'getclass':
		
		$game = mysqli_fetch_assoc(ref_mysql_query("SELECT * FROM `games` WHERE `gameid`='".$_POST['id']."' AND `clubid`='".$currentUser['clubid']."'"));
		
		switch($game['status']){
		
		case 0:  // OK
		    $class = "game ok";
		    break;
		case 1:  // New
		    $class = "game new";
		      break;
		case 2:  // Changed
		    $class = "game changed";
		    break;
		case 3:
		    $class = "game cancelled";
		    break;
		case 4:
		    $class = "game moved";
		    break;
		}
		
		$json = '[ { "class": "'.$class.'" } ]';
	
		echo $json;
	
	break;
	
	case 'acknowledgemove':
		$game = mysqli_fetch_assoc(ref_mysql_query("SELECT * FROM `games` WHERE `gameid`='".$_POST['id']."' AND `clubid`='".$currentUser['clubid']."'"));
		
		if($game['status'] == 2){
		    ref_mysql_query("UPDATE games SET status='1' WHERE gameid=".$game['gameid']." AND `clubid`='".$currentUser['clubid']."'");
		    $status='1';
		    if($game['refereeteam1id']!='0' && $game['refereeteam2id']!='0' && $game['tableteam1id']!='0' && $game['tableteam2id']!='0' && $game['tableteam3id']!='0'){
			  ref_mysql_query("UPDATE games SET status='0' WHERE gameid = '".$game['gameid']."' AND `clubid`='".$currentUser['clubid']."'");
		    }
		}
	break;
	
	case "getGameURL":
  
	      $url = getGameURL($_POST['gameid']);
	
	      $json = '[ { "url": "'.$url.'" } ]';
	      
	      echo $json;
    
	break;
	
}
}

?>
