<?php 


case "refereeplanupdate":

    echo '<script type="text/javascript" src="js/games.js"></script>';
    echo '<link rel="stylesheet" type="text/css" href="css/games.css">';

    $config = getConfiguration();
    echo fetchText("Update Games","header2");

    ref_mysql_query("UPDATE `config` SET `value`=now() WHERE `name`='lastupdated'");

    echo '<input type="submit" name="syncNow" id="syncNow" value="'.fetchText("Syncronize").'" onclick="javascript:doSync(); this.disabled=true; return false;"><br><br>
          <input type="hidden" name="syncAction">
          <input type="hidden" name="syncTeamId">
          <input type="hidden" name="syncTeamUrl">
          <input type="hidden" name="syncClubId">';

    echo '<div id="status"></div><br>';
    echo '<div id="progressbar"></div><br>';
    echo '<div id="log"></div><br>';


break;

case "managementteams":
 
  $currentUser = mysqli_fetch_assoc(getCurrentUser());  

  echo '<script type="text/javascript" src="js/club.js"></script>';

  if($_POST["removeTeam"]){

    ref_mysql_query("DELETE FROM `calendars` WHERE `id`='".$_POST["removeTeam"]."' AND `clubid`='".$currentUser['clubid']."'");
    $warning = fetchText("Team/League was removed.");

  }

  if($_POST["removeAllTeams"]){

    ref_mysql_query("DELETE FROM `calendars`");
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

  $query = ref_mysql_query("SELECT * FROM `calendars` ORDER BY `clubid`,`team`");

  echo fetchText("Teams:","header3");

  list ($clubs,$ids) = getClubs();

  while($row = mysqli_fetch_assoc($query)){
    echo $clubs[array_search($row['clubid'],$ids)];
    echo ' - ';
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

case "managementclubs":
  
  echo '<script type="text/javascript" src="js/management.js"></script>';
  echo '<link rel="stylesheet" type="text/css" href="css/management.css">';
  
   echo '<div id="editClubPlaceHolder" title="'.fetchText("Edit Club").'">
	  <div id="editClubNameHolder"></div>
	  <input type="hidden" id="editClubId">
	  <div id="editClubMessageHolder"></div></div></div>';

  require("class/refereeplan.class.management.php");

  $config = getConfiguration();

  echo fetchText("Clubs","header2");

  list ($clubs,$ids) = getClubs();
  
  $clubnames = array();
  
  for ($k = 0; $k < count($clubs); $k++){
	if($clubs[$k] != ""){
	      $club = array();
	      $club['id'] = $ids[$k];
	      $club['name'] = $clubs[$k];
	      $clublist[] = new clubObj($club);
              if(mysqli_num_rows(ref_mysql_query("SELECT * FROM `config` WHERE `id`='".$club['id']."'")) == 0){
              	ref_mysql_query("INSERT INTO `config` (`id`,`clubname`,`clubid`,`language`) VALUES ('".$club['id']."','".$club['name']."','".$club['id']."','dk')");
              }
	}
  }
  
  echo '<ul class="clubList">';
		
        foreach($clublist as $club){
	      echo $club;
	}
	

  echo '</ul>';
  
break;

case "managementconfig":

  echo '<script type="text/javascript" src="js/management.js"></script>';
  echo '<link rel="stylesheet" type="text/css" href="css/management.css">';
  
  echo '<table width="100%">
	  <tr>
	    <td width="50%">';
  echo fetchText("Configuration","header2");
  echo '</td>
	<td width="50%" align="right">';

  echo '<div id="message"></div>';
  
  echo '</td>
	</tr>
	</table>';
  
  echo fetchText("Game Source:","header3");
  echo '<select id="sourceSelector" onchange="javascript:changeGameSource();">';
  foreach(glob("refereeplan.sources.*.php") as $source){
     $sourcename = explode(".",$source);
     if($sourcename[2] == $config['gamesource']){
	  $selected = "selected";
     }else{
	  $selected = "";
     }
     echo '<option value="'.$sourcename[2].'" '.$selected.'>'.$sourcename[2].'</option>'; 
  }	
  echo '</select><br><br>';
  echo getSourceInfo();
  echo '<br><br>';
  
  echo fetchText("URL for Updates:","header3");
  
  echo '<input type="text" onchange="javascript:changeUpdatesUrl();" name="updatesUrl" value="'.$config['updatesurl'].'">';
  
  
break;

?>
