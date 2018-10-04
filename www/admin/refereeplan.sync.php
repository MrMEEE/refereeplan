<?php

require("connect.php");
require("refereeplan.common.functions.php");
require("refereeplan.sources.basketdk.php");

error_reporting(E_ERROR | E_PARSE);

echo "Removing all Calendars\n";

ref_mysql_query("DELETE FROM `calendars`");

echo "Adding all Calendars\n";

addAllTeams();

$teams = ref_mysql_query("SELECT * FROM `calendars`");

while($team = mysqli_fetch_assoc($teams)){

	echo "Syncing: ".$team['team']."\n";

	$return = syncTeam($team['id'],$team['address'],$team['clubid']);

	foreach($return as $line){
	
		echo $line."\n";

	}

}
