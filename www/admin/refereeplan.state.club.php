<?php 
case "clubteamsexternal":

  $currentUser = mysql_fetch_assoc(getCurrentUser());

  echo '<script type="text/javascript" src="js/club.js"></script>';
  
  if($_POST["removeTeam"]){
  
    ref_mysql_query("DELETE FROM `calendars` WHERE `id`='".$_POST["removeTeam"]."' AND `clubid`='".$currentUser['clubid']."'");
    $warning = fetchText("Team/League was removed.");
  
  }
  
  if($_POST["removeAllTeams"]){
    
    ref_mysql_query("DELETE FROM `calendars` WHERE `clubid`='".$currentUser['clubid']."'");
    $warning = fetchText("All of the Teams and Leagues has been removed.");
    
  }
  
  if($_POST["addAllTeams"]){
    
    $newteams = addAllTeams($currentUser['clubid']);
    
    if($newteams==0){
      $info = fetchText("No new Teams or Leagues.");
    }else{
      $info = $newteams;
      $info .= fetchText(" new Team(s)/League(s) was added."); 
    }
  
  }
  
  echo fetchText("External Teams","header2");
  echo showMessages($info,$warning,$error);
  echo '<br><a href="javascript:addAllTeams()">';
  echo fetchText("Add all of the Clubs Teams and Leagues");
  echo '</a><br>';
  echo '<br><a href="javascript:removeAllTeams()">';
  echo fetchText("Remove all of the Clubs Teams and Leagues");
  echo '</a><br><br>';
  
  $query = ref_mysql_query("SELECT * FROM `calendars` WHERE `clubid`='".$currentUser['clubid']."' ORDER BY `team`");
  
  echo fetchText("Teams:","header3");
  
  while($row = mysql_fetch_assoc($query)){
    echo '<a href="';
    echo $row['address'];
    echo '">';
    echo $row['team'];
    echo '</a>';
    echo ' - ';
    echo '<a href="javascript:void(removeTeam('.$row['id'].'))">'.fetchText("Remove").'</a>';
    echo '<br>';
  }
  
  echo '<input type="hidden" name="removeTeam">
        <input type="hidden" name="removeAllTeams">
        <input type="hidden" name="addAllTeams">';

break;

case "clubteams":

  require("class/refereeplan.class.club.php");

  echo '<script type="text/javascript" src="js/club.js"></script>';
  echo '<link rel="stylesheet" type="text/css" href="css/club.css">';
  
  echo fetchText("Club Teams","header2");
  echo '<div id="newTeamPlaceHolder" title="'.fetchText("New Team").'">
	  <div id="teamNameHolder">'.fetchText("Team Name:").'</div><input id="newTeamName">
	  <div id="teamContactHolder">'.fetchText("Contact:").'</div>
	  <select id="newTeamContactId">
	    <option value="0">'.fetchText("No Contact").'</option>';
	    $query = ref_mysql_query("SELECT * FROM `users` ORDER BY `name` ASC");
	    while($user = mysql_fetch_assoc($query)){
		echo '<option value="'.$user["id"].'">'.$user["name"].'</option>';
	    }
	  echo '<select>
	  <div id="teamMessageHolder"></div>
	</div>';
  echo '<a href="#" id="teamCreate" class="teamCreate">'.fetchText("New Team","header3").'</a>';
  
  $query = ref_mysql_query("SELECT * FROM `teams` ORDER BY `name` ASC");

  while($row = mysql_fetch_assoc($query)){
  
      $teams[] = new teamObj($row);
    /*if($row['name']!="-"){
	echo $row['name'];
	echo ' - <a href="javascript:teamsChangeUser('.$row['id'].',\''.$row['name'].'\')">'.fetchText("Change Contact").'</a>';
	echo ' - <a href="javascript:teamsChangeName('.$row['id'].',\''.$row['name'].'\')">'.fetchText("Change Name").'</a>';
	echo ' - <a href="javascript:teamsRemove('.$row['id'].')">Fjern</a>';	
	echo "<br>";
    }*/
   }
   
   echo '<ul class="teamList">';
		
        foreach($teams as $team){
	      echo $team;
	}
	

   echo '</ul>';
   
break;

?>
