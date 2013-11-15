<?php 
case "clubteamsdbbf":

  $javascript .= 'function removeTeam(teamid){
		      answer = confirm("'.printText("Are you sure that you want to remove this team/league??").'");
		      if (answer !=0){
		          document.mainForm.removeTeam.value = teamid;
		          document.mainForm.submit();
                      }
                  }

	function removeAllTeams(){
                answer = confirm("'.printText("Are you sure that you want to remove all teams/leagues??").'");
                if (answer !=0){
                        document.mainForm.removeAllTeams.value = true;
                        document.mainForm.submit();
                }
        }

        function addAllTeams(){
                document.mainForm.addAllTeams.value = true;
                document.mainForm.submit();
        }';
  echo printText("Teams from DBBF","header1");
  echo showMessages($info,$warning,$error);
  echo '<br><a href="javascript:addAllTeams()">';
  echo printText("Add all of the Clubs Teams and Leagues");
  echo '</a><br>';
  echo '<br><a href="javascript:removeAllTeams()">';
  echo printText("Fjern alle klubbens hold/puljer");
  echo '</a><br>';
  
  echo '<input type="hidden" name="removeTeam">
        <input type="hidden" name="removeAllTeams">
        <input type="hidden" name="addAllTeams">';

break;
case "clubteams":
  echo printText("Teams/Persons","header1");
break;
?>
