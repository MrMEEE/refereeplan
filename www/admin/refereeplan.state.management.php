<?php 

case "managementclubs":

  echo '<script type="text/javascript" src="js/management.js"></script>';
  echo '<link rel="stylesheet" type="text/css" href="css/management.css">';

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

  echo fetchText("Configuration","header2");
  
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