<?php 
case "clubteamsexternal":

  $currentUser = mysqli_fetch_assoc(getCurrentUser());

  echo '<script type="text/javascript" src="js/club.js"></script>';
  
  echo fetchText("External Teams","header2");
  echo showMessages($info,$warning,$error);
  
  $query = ref_mysql_query("SELECT * FROM `calendars` WHERE `clubid`='".$currentUser['clubid']."' ORDER BY `team`");
  
  echo fetchText("Teams:","header3");
  
  while($row = mysqli_fetch_assoc($query)){
    echo '<a href="';
    echo $row['address'];
    echo '">';
    echo $row['team'];
    echo '</a>';
    echo '<br>';
  }
  
break;

case "clubteams":

  require("class/refereeplan.class.club.php");
  
  $currentUser = mysqli_fetch_assoc(getCurrentUser());

  echo '<script type="text/javascript" src="js/club.js"></script>';
  echo '<link rel="stylesheet" type="text/css" href="css/club.css">';
  echo '<div id="removeTeamPlaceHolder" title="'.fetchText("Remove Team??").'">'.fetchText("Are you sure you want to delete this team??").'<div id="removeTeamId"></div></div>';
  echo '<div id="editTeamPlaceHolder" title="'.fetchText("Edit Team").'">
	  <div id="editTeamNameHolder">'.fetchText("Team Name:").'</div><input id="editTeamName">
	  <div id="editTeamContactHolder">'.fetchText("Contact:").'</div>
	  <div id="editTeamId"></div>
	  <input type="hidden" id="editOrigTeamName">
	  <select id="editTeamContactId">
	    <option value="0">'.fetchText("No Contact").'</option>';
	    $query = ref_mysql_query("SELECT * FROM `users` WHERE `clubid`='".$currentUser['clubid']."' ORDER BY `name` ASC");
	    while($user = mysqli_fetch_assoc($query)){
		echo '<option value="'.$user["id"].'">'.$user["name"].'</option>';
	    }
	  echo '<select>
	  <div id="editTeamMessageHolder"></div></div></div>';
  
  echo fetchText("Club Teams","header2");
  echo '<div id="newTeamPlaceHolder" title="'.fetchText("New Team").'">
	  <div id="teamNameHolder">'.fetchText("Team Name:").'</div><input id="newTeamName">
	  <div id="teamContactHolder">'.fetchText("Contact:").'</div>
	  <select id="newTeamContactId">
	    <option value="0">'.fetchText("No Contact").'</option>';
	    $query = ref_mysql_query("SELECT * FROM `users` WHERE `clubid`='".$currentUser['clubid']."' ORDER BY `name` ASC");
	    while($user = mysqli_fetch_assoc($query)){
		echo '<option value="'.$user["id"].'">'.$user["name"].'</option>';
	    }
	  echo '<select>
	  <div id="teamMessageHolder"></div>
	</div>';
  echo '<a href="#" id="teamCreate" class="teamCreate">'.fetchText("New Team","header3").'</a>';
  
  $query = ref_mysql_query("SELECT * FROM `teams` WHERE (`clubid`='".$currentUser['clubid']."' OR `clubid`='-1') ORDER BY `name` ASC");
  
  while($row = mysqli_fetch_assoc($query)){
      
      if(!in_array($row['name'],getHiddenTeams())){
      
	    $teams[] = new teamObj($row);
      
      }

   }
   
   echo '<ul class="teamList">';
		
        foreach($teams as $team){
	      echo $team;
	}
	

   echo '</ul>';
   
break;

?>
