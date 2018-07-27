<?php 

case "refereeplanupcomminggames":
case "refereeplanunassigned":
case "refereeplanrescheduled":
case "refereeplancancelled":
case "refereeplanseason":

    require("class/refereeplan.class.games.php");
    require("refereeplan.common.games.functions.php");
    
    $currentUser = mysqli_fetch_assoc(getCurrentUser());
    
    echo showGamesCommon();
    
    switch($currentState){
	  
	  case "refereeplanupcomminggames":
	      echo fetchText("Upcomming Games","header2");
	      $query = ref_mysql_query("SELECT * FROM `games` WHERE CURDATE() <= `date` AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	      $query2 = ref_mysql_query("SELECT * FROM `games` WHERE `date` = '0000-00-00' AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	  break;
	  
	  case "refereeplanunassigned":
	      echo fetchText("Unassigned Games","header2");
	      $query = mysqli_query($GLOBALS['link'],"SELECT * FROM `games` WHERE CURDATE() <= `date` AND `status` = 1 AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	      $query2 = mysqli_query($GLOBALS['link'],"SELECT * FROM `games` WHERE `date` = '0000-00-00' AND `status` = 1 AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	  break;
	  
	  case "refereeplanrescheduled":
	      echo fetchText("Rescheduled Games","header2");
	      $query = mysqli_query($GLOBALS['link'],"SELECT * FROM `games` WHERE CURDATE() <= `date` AND `status` = 2 AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	      $query2 = mysqli_query($GLOBALS['link'],"SELECT * FROM `games` WHERE `date` = '0000-00-00' AND `status` = 2 AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	  break;
	  
	  case "refereeplancancelled":
	      echo fetchText("Cancelled Games","header2");
	      $query = mysqli_query($GLOBALS['link'],"SELECT * FROM `games` WHERE CURDATE() <= `date` AND `status` = 3 AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	      $query2 = mysqli_query($GLOBALS['link'],"SELECT * FROM `games` WHERE `date` = '0000-00-00' AND `status` = 3 AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	  break;
	  
	  case "refereeplanseason":
	      echo fetchText("All Games (Season)","header2");
	      $query = mysqli_query($GLOBALS['link'],"SELECT * FROM `games` WHERE `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	      $query2 = mysqli_query($GLOBALS['link'],"SELECT * FROM `games` WHERE `date` = '0000-00-00' AND `homegame`= 1 AND `clubid`='".$currentUser['clubid']."' ORDER BY `date`,`time` ASC");
	  break;

    }
    
    $games = array();
    
    echo showGamesLegend();
    
    if(mysqli_num_rows($query2)){
	while($row = mysqli_fetch_assoc($query2)){
	    $games[] = new gameObj($row);
	}
    }

    if(mysqli_num_rows($query)){
	while($row = mysqli_fetch_assoc($query)){
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
