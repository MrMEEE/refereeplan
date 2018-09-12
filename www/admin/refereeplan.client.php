<?php
header("Content-type: text/javascript");

require "connect.php";

$games = mysqli_query($link,"SELECT * FROM `games` WHERE `homegame`='1' AND `clubid`='".$_GET['clubid']."' ORDER BY date,time DESC");
//$games = mysqli_query($link,"SELECT * FROM `games` WHERE `homegame`='1' AND `clubid`='71' ORDER BY date,time DESC");
$teamnames = mysqli_query($link,"SELECT * FROM `teams`");

while($teamname = mysqli_fetch_assoc($teamnames)){

   $teamnamearray[$teamname['id']] = $teamname['name'];

}

$lastweek=0;
$string = "";
while($game = mysqli_fetch_assoc($games)){

$date=substr($game['date'],8,2);
$date.="/";
$date.=substr($game['date'],5,2);
$date.="-";
$date.=substr($game['date'],0,4);
$dateformat=substr($game['date'],0,4);
$dateformat.=substr($game['date'],5,2);
$dateformat.=substr($game['date'],8,2);
$pos=strpos($game['text'],":");
$teams=substr($game['text'],0,$pos);

if($lastweek!=0 && $lastweek!=date("W",strtotime($dateformat))){
    $string .= '</tbody>
      		</table>';
}
if($lastweek!=date("W",strtotime($dateformat))){
    $string .= '<table id="games" class="wp-table-reloaded wp-table-reloaded-id-1" border=1 width=540px>
    		<thead>
		<tr class="row-1 odd">
		  <th class="column-1" width=50px>Uge: '.date("W",strtotime($dateformat)).'</th><th class="column-2" width=60px>Dato</th><th class="column-3">Kamp Beskrivelse</th><th class="column-4" width=50px>Bord</th><th class="column-5" width=50px>Dommer</th><th class="column-6"width=50px>24 sek</th>
		</tr>
		</thead>
		<tbody>';
}

		$day="";
		switch(date("D",strtotime($dateformat))){
			case "Mon":
				$day="Mandag";
				break;
			case "Tue":
				$day="Tirsdag";
				break;
			case "Wed":
				$day="Onsdag";
				break;
			case "Thu":
				$day="Torsdag";
				break;
			case "Fri":
				$day="Fredag";
				break;
			case "Sat":
				$day="Lørdag";
				break;
			case "Sun":
				$day="Søndag";
				break;	
		}
		$string .= '
		<tr class="row-2 even" height=45px>
		<td class="column-1"><a href="admin/gotoGame.php?gameID='.$game['gameid'].'" target="_blank">'.$game['gameid'].'</a></td><td class="column-2">'.$day.'<br>'.$date.'</td><td class="column-3">';
		
		if(($game['status']==3) || ($game['status']==4)){
		    $string .= '<font style="text-decoration:line-through;">';
		}
		
		$string .= $game['text'];
		
		if(($game['status']==3) || ($game['status']==4)){
		    $string .= '</font>';
		    if($game['status']==3){
			$string .= '<br>Kamp Aflyst';
		    }
		    if($game['status']==4){
			$string .= '<br>Kamp Udsat';
		    }
		}
		
		if($game['refereeteam1id']=="1"){
			$ref1="DBBF:".$game['referee1name'];
		}else{
			$ref1=$teamnamearray[$game['refereeteam1id']];
		}
		if($game['refereeteam2id']=="1"){
		        $ref2="DBBF:".$game['referee2name'];
		}else{
			$ref2=$teamnamearray[$game['refereeteam2id']];
		}
		
		$string .= '</td><td class="column-4">'.$teamnamearray[$game['tableteam1id']].'</td><td class="column-5">'.$ref1.'</td><td class="column-6">'.$teamnamearray[$game['tableteam3id']].'</td>
		</tr>
		<tr class="row-2 odd" height=45px>
		<td class="column-1"></td><td class="column-2">'.$game['time'].'</td><td class="column-3">'.$game['place'].'</td><td class="column-4">'.$teamnamearray[$game['tableteam2id']].'</td><td class="column-5">'.$ref2.'</td><td class="column-6"></td>
		</tr>
		<tr class="row-1 odd height=1px">
		
		</tr>
		';
		


  //$mytext = $game['gameid'];

$lastweek=date("W",strtotime($dateformat));

}

$string .= "</table>";

//var_dump($string);

$string = str_replace("'","\'",$string);
$string = trim(preg_replace('/\s\s+/', '\n', $string));
$string = str_replace("\n","\\n",$string);
Print("document.write('".$string."')");
?>
