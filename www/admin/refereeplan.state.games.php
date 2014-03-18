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
	  <input type="hidden" name="syncTeamUrl">';
    
    echo '<div id="status"></div><br>';
    echo '<div id="progressbar"></div><br>';
    echo '<div id="log"></div><br>';
    
    
break;

case "refereeplanupcomminggames":
case "refereeplanunassigned":
case "refereeplanrescheduled":
case "refereeplancancelled":
case "refereeplanseason":

    require("class/refereeplan.class.games.php");
    require("refereeplan.common.games.functions.php");
    
    $currentUser = mysql_fetch_assoc(getCurrentUser());
    
    echo showGamesCommon();
    
    switch($currentState){
	  
	  case "refereeplanupcomminggames":
	      echo fetchText("Upcomming Games","header2");
	      $query = ref_mysql_query("SELECT * FROM `games` WHERE CURDATE() <= `date` AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	      $query2 = ref_mysql_query("SELECT * FROM `games` WHERE `date` = '0000-00-00' AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	  break;
	  
	  case "refereeplanunassigned":
	      echo fetchText("Unassigned Games","header2");
	      $query = mysql_query("SELECT * FROM `games` WHERE CURDATE() <= `date` AND `status` = 1 AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	      $query2 = mysql_query("SELECT * FROM `games` WHERE `date` = '0000-00-00' AND `status` = 1 AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	  break;
	  
	  case "refereeplanrescheduled":
	      echo fetchText("Rescheduled Games","header2");
	      $query = mysql_query("SELECT * FROM `games` WHERE CURDATE() <= `date` AND `status` = 2 AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	      $query2 = mysql_query("SELECT * FROM `games` WHERE `date` = '0000-00-00' AND `status` = 2 AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	  break;
	  
	  case "refereeplancancelled":
	      echo fetchText("Cancelled Games","header2");
	      $query = mysql_query("SELECT * FROM `games` WHERE CURDATE() <= `date` AND `status` = 3 AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	      $query2 = mysql_query("SELECT * FROM `games` WHERE `date` = '0000-00-00' AND `status` = 3 AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	  break;
	  
	  case "refereeplanseason":
	      echo fetchText("All Games (Season)","header2");
	      $query = mysql_query("SELECT * FROM `games` WHERE `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	      $query2 = mysql_query("SELECT * FROM `games` WHERE `date` = '0000-00-00' AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	  break;

    }
    
    $games = array();
    
    echo showGamesLegend();
    
    if(mysql_num_rows($query2)){
	while($row = mysql_fetch_assoc($query2)){
	    $games[] = new gameObj($row);
	}
    }

    if(mysql_num_rows($query)){
	while($row = mysql_fetch_assoc($query)){
	    $games[] = new gameObj($row);
	}
    }
    
    echo '<ul class="gameList">';
		
        foreach($games as $game){
	      echo $game;
	}
	

    echo '</ul>';
    
break;



?>
