<?php 
case "clubteamsdbbf":

  echo '<script type="text/javascript" src="js/club.js"></script>';
  
  if($_POST["removeTeam"]){
  
    mysql_query("DELETE FROM `calendars` WHERE `id`='".$_POST["removeTeam"]."'");
    $warning = fetchText("Team/League was removed.");
  
  }
  
  if($_POST["removeAllTeams"]){
    
    mysql_query("DELETE FROM `calendars`");
    $warning = fetchText("All of the Teams and Leagues has been removed.");
    
  }
  
  if($_POST["addAllTeams"]){
    
    $newteams = addAllTeams();
    
    if($newteams==0){
      $info = fetchText("No new Teams or Leagues.");
    }else{
      $info = $newteams;
      $info .= fetchText(" new Team(s)/League(s) was added."); 
    }
  
  }
  
  echo fetchText("Teams from DBBF","header2");
  echo showMessages($info,$warning,$error);
  echo '<br><a href="javascript:addAllTeams()">';
  echo fetchText("Add all of the Clubs Teams and Leagues");
  echo '</a><br>';
  echo '<br><a href="javascript:removeAllTeams()">';
  echo fetchText("Remove all of the Clubs Teams and Leagues");
  echo '</a><br><br>';
  
  $query = mysql_query("SELECT * FROM `calendars` ORDER BY `team`");
  
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
  echo fetchText("Teams","header2");

  $query = mysql_query("SELECT * FROM `teams` ORDER BY `name` ASC");

  while($row = mysql_fetch_assoc($query)){
    if($row['name']!="-"){
	echo $row['name'];
	    echo ' - <a href="javascript:changeType('.$row['id'].'';
	    if($row['person']){
		echo ',0)">Person</a>';
	    }else{
		echo ',1)">Hold</a>';
	    }
	    echo ' - <a href="javascript:openWindowTeams('.$row['id'].',\''.$row['name'].'\')">Skift Tilknyttede Hold</a>';
	    echo ' - <a href="javascript:openWindowName('.$row['id'].',\''.$row['name'].'\')">Ændre Navn</a>';
	    echo ' - <a href="javascript:void(ConfirmChoice('.$row['id'].'))">Fjern</a>';	
	    echo "<br>";
    }
}
break;

case "clubrefs":
  echo fetchText("Referees","header2");
break;
?>
