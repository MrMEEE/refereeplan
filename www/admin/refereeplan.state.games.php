<?php 
case "refereeplanupdate":
    $config = getConfiguration();
    echo fetchText("Update Games","header2");
    $teamnames = getTeamNames();
    $clubids = getClubIDs();
    
    $courts = array();
    
    for($i = 0, $size = count($clubids); $i < $size; ++$i){
	$courts = array_merge(getCourts($clubids[$i]),$courts);
    }
    
    mysql_query("UPDATE `config` SET `value`=now() WHERE `name`='lastupdated'");
    
    $calendars = mysql_query("SELECT * FROM `calendars`");
    
    if (mysql_num_rows($calendars) == 0) {
	echo fetchText("No team calendars found, please go to Club->Teams from DBBF.");
	exit;
    }
    if(!mysql_num_rows(mysql_query("SELECT * FROM teams WHERE name = 'DBBF'"))){
	mysql_query("INSERT INTO teams SET name='DBBF'");
    }
    
    $dbbfentry=mysql_fetch_assoc(mysql_query("SELECT * FROM teams WHERE name = 'DBBF'"));
    $dbbfid=$dbbfentry['id'];
    
break;
?>
