<?php 
case "refereeplanupdate":
    
    echo '<script type="text/javascript" src="js/games.js"></script>';
    echo '<link rel="stylesheet" type="text/css" href="css/games.css">';
    
    $config = getConfiguration();
    echo fetchText("Update Games","header2");
        
    mysql_query("UPDATE `config` SET `value`=now() WHERE `name`='lastupdated'");
    
    echo '<input type="submit" name="syncNow" id="syncNow" value="'.fetchText("Syncronize").'" onclick="javascript:doSync(); this.disabled=true; return false;"><br><br>
	  <input type="hidden" name="syncAction">
	  <input type="hidden" name="syncTeamId">
	  <input type="hidden" name="syncTeamUrl">';
    
    echo '<div id="status"></div><br>';
    echo '<div id="progressbar"></div><br>';
    echo '<div id="log"></div><br>';
    
    
break;

case "refereeplanupcomminggames":
    
    echo '<script type="text/javascript" src="js/games.js"></script>';
    echo '<link rel="stylesheet" type="text/css" href="css/games.css">';
    echo '<div id="dialog-confirm" title="'.fetchText("Delete Game??").'">'.fetchText("Are you sure you want to delete this game??").'</div>';
    
    require("class/refereeplan.class.games.php");
    
    $query = mysql_query("SELECT * FROM `games` WHERE CURDATE() <= `date` AND `homegame`= 1 ORDER BY `date`,`time` ASC ");
    $query2 = mysql_query("SELECT * FROM `games` WHERE `date` = '0000-00-00' AND `homegame`= 1 ORDER BY `date`,`time` ASC ");

    $games = array();
    
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
