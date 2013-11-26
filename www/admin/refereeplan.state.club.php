<?php 
case "clubteamsdbbf":
  
  $config = getConfiguration();
  $javascript .= 'function removeTeam(teamid){
		      answer = confirm("'.fetchText("Are you sure that you want to remove this team/league??").'");
		      if (answer !=0){
		          document.mainForm.removeTeam.value = teamid;
		          document.mainForm.submit();
                      }
                  }

	function removeAllTeams(){
                answer = confirm("'.fetchText("Are you sure that you want to remove all teams/leagues??").'");
                if (answer !=0){
                        document.mainForm.removeAllTeams.value = true;
                        document.mainForm.submit();
                }
        }

        function addAllTeams(){
                document.mainForm.addAllTeams.value = true;
                document.mainForm.submit();
        }';
  
  if($_POST["removeTeam"]){
  
    mysql_query("DELETE FROM `calendars` WHERE `id`='".$_POST["removeTeam"]."'");
    $warning = "Team/League was removed.";
  
  }
  
  if($_POST["removeAllTeams"]){
    
    mysql_query("DELETE FROM `calendars`");
    $warning = "All of the Teams and Leagues has been removed.";
    
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
  echo fetchText("Teams/Persons","header1");
break;
?>
