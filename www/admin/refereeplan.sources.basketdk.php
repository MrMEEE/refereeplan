<?php

function getSourceInfo(){

  return fetchText("Source plugin for resultater.basket.dk.");

}

function getClubIDs($clubid = 'current'){
  
  $config=getConfiguration($clubid);

  return explode(',',$config['clubid']);
}

function addAllTeams(){
    
    list ($clubs,$ids) = getClubs();
    
    $addedteams=0;

    $activeClubs = ref_mysql_query("SELECT * FROM `config` WHERE `enabled`='1'");
 
    while($activeClub = mysqli_fetch_assoc($activeClubs)){

      $url = "http://resultater.basket.dk/tms/Turneringer-og-resultater/Forening-Holdoversigt.aspx?ForeningsId=".$activeClub['clubid'];
      $input = @file_get_contents($url) or die("Could not access url: $url");
     //error_log($ids[$activeClub['clubid']]);
    
      $regexp = "PuljeId=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
      $regexp2 = "RaekkeId=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
      $regexp3 = "HoldId=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
    
      preg_match_all("/$regexp/siU", $input, $matches);
      preg_match_all("/$regexp2/siU", $input, $matches2);
      preg_match_all("/$regexp3/siU", $input, $teamids);
  
    $j=0;
    foreach ($matches[2] as $urls){
      $name=$matches2[3][$j];
      if(!mysqli_num_rows(ref_mysql_query("SELECT * FROM `calendars` WHERE `address` = 'http://resultater.basket.dk/tms/Turneringer-og-resultater/Pulje-Komplet-Kampprogram.aspx?PuljeId=$urls' AND `clubid`='".$activeClub['clubid']."'"))){
        ref_mysql_query("INSERT into calendars (`id`,`address`, `team`,`clubid`) VALUES ('".$teamids[2][$j]."','http://resultater.basket.dk/tms/Turneringer-og-resultater/Pulje-Komplet-Kampprogram.aspx?PuljeId=$urls', '".fixCharacters($name)."','".$activeClub['clubid']."')");
        $addedteams++;
      }
      $j=$j+1;
    }
    }

    return $addedteams;
}

function setBasketDKValidation(){

    $url = "http://resultater.basket.dk/tms/Turneringer-og-resultater/Soegning.aspx";
    
    $useragent = "Mozilla/5.0 (X11; Linux x86_64; rv:10.0) Gecko/20100101 Firefox/10.0";

    $ch = curl_init();

    // set user agent
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // grab content from the website
    $content = curl_exec($ch);
    curl_close($ch);

    $doc = new DOMDocument();
    $doc->loadHTML($content);

    foreach( $doc->getElementsByTagName('input') as $item){
       $params[$item->getAttribute('name')] =  $item->getAttribute('value');
    }

    #var_dump($params);

    $validation = array('url' => "http://resultater.basket.dk/tms/Turneringer-og-resultater/Soegning.aspx",
		    'viewstate' => urlencode($params['__VIEWSTATE']),
		    'eventtarget' => "",
		    'eventargument' => "",
		    'eventvalidation' => urlencode($params['__EVENTVALIDATION']),
		    'viewstategenerator' => $params['__VIEWSTATEGENERATOR'],
                    'user-agent' => "Mozilla/5.0 (X11; Linux x86_64; rv:10.0) Gecko/20100101 Firefox/10.0"
		    );

    #var_dump($validation);

    return $validation;

}

function getClubs(){

    $config = getConfiguration();
  
    if($config["debug"] == 0){
	error_reporting(0);
    }

    $validation = setBasketDKValidation();

    $url = $validation['url'];

    $fields = array(
      '__VIEWSTATE'=>$validation['viewstate'],
      '__VIEWSTATEGENERATOR'=>$validation['viewstategenerator'],
      '__EVENTVALIDATION'=>$validation['eventvalidation'],
      'ctl00%24ContentPlaceHolder1%24Soegning%24Search'=>'rdClub',
      'ctl00%24ContentPlaceHolder1%24Soegning%24btnSearchClub'=>'Søg'
    );


    foreach($fields as $key=>$value){ 
        $fields_string .= $key.'='.$value.'&'; 
    }

    rtrim($fields_string,'&');
    
    $useragent = "Mozilla/5.0 (X11; Linux x86_64; rv:10.0) Gecko/20100101 Firefox/10.0";

    $ch = curl_init();
    
    curl_setopt($ch,CURLOPT_USERAGENT,$useragent);
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HTTPHEADER,Array("Content-Type: application/x-www-form-urlencoded; charset=utf-8")); 
    curl_setopt($ch,CURLOPT_TIMEOUT,5);


    $result = curl_exec($ch);

    $dom = new DOMDocument();
    
    $page = '
    <html>
    <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>Dommer Sync</title>
    </head>
    <body></body>
    </html>
    ';

    $page .= $result;
    $html = @$dom->loadHTML($page);

    $dom->preserveWhiteSpace = false;

    $tables = $dom->getElementsByTagName('table');

    $rows = $tables->item(0)->getElementsByTagName('tr');

    foreach ($rows as $row){

        $cols = $row->getElementsByTagName('a');

        $clubnames[] = trim($cols->item(1)->nodeValue);

        unset($colsarray);

        foreach ($cols as $col){
        
                $colsarray[] = $col->getAttribute('href');

	}

        $id=explode("=",$colsarray[0]);

        $clubids[] = $id[1];

    }


    curl_close($ch);

    return array ($clubnames,$clubids);

}

function getAllCourts(){

    $config = getConfiguration();
  
    if($config["debug"] == 0){
	error_reporting(0);
    }

    $validation = setBasketDKValidation();

    $url = $validation['url'];

    $fields = array(
            '__VIEWSTATE'=>$validation['viewstate'],
            '__EVENTTARGET'=>$validation['eventtarget'],
            '__EVENTARGUMENT'=>$validation['eventargument'],
            '__EVENTVALIDATION'=>$validation['eventvalidation'],
            'ctl00%24ContentPlaceHolder1%24Soegning%24Search'=>'rbStadium',
            'ctl00%24ContentPlaceHolder1%24Soegning%24txtSelectedCenterSearchModule'=>'3',
            'ctl00%24ContentPlaceHolder1%24Soegning%24ddlGender'=>'1',
            'ctl00%24ContentPlaceHolder1%24Soegning%24ddlDivision'=>'2',
            'ctl00%24ContentPlaceHolder1%24Soegning%24ddlSeason'=>'0',
            'ctl00%24ContentPlaceHolder1%24Soegning%24txtClubName'=>'',
            'ctl00%24ContentPlaceHolder1%24Soegning%24txtStadiumName'=>'',
            'ctl00%24ContentPlaceHolder1%24Soegning%24btnSearchStadium'=>'S%C3%B8g',
            'ctl00%24ContentPlaceHolder1%24Soegning%24txtCommitteeName'=>'',
            'ctl00%24ContentPlaceHolder1%24Soegning%24txtMatchNumber'=>'',
            'ctl00%24ContentPlaceHolder1%24Soegning%24txtRefereeName'=>'',
            'ctl00%24ContentPlaceHolder1%24Soegning%24ddlTournaments_Tournament'=>'0'
     );

      foreach($fields as $key=>$value){ 
	      $fields_string .= $key.'='.$value.'&'; 
      }

      rtrim($fields_string,'&');

      $ch = curl_init();

      curl_setopt($ch,CURLOPT_URL,$url);
      curl_setopt($ch,CURLOPT_POST,count($fields));
      curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      curl_setopt($ch,CURLOPT_HTTPHEADER,Array("Content-Type: application/x-www-form-urlencoded")); 
      curl_setopt($ch,CURLOPT_TIMEOUT,5);


      $result = curl_exec($ch);

      $dom = new DOMDocument();
	  
      //load the html  
      $page = '
      <html>
      <head>
      <meta http-equiv="content-type" content="text/html; charset=utf-8">
      <title>Dommer Sync</title>
      </head>
      <body></body>
      </html>
      ';

      $page .= $result;
      $html = @$dom->loadHTML($page);

      $dom->preserveWhiteSpace = false;

      $tables = $dom->getElementsByTagName('table');

      $xpath = new DOMXPath($dom);

      $tags = $xpath->query("//a[contains(@id,'hlStadium')]");

      foreach($tags as $tag){

      $courts[] = trim($tag->nodeValue);

      }

      return $courts;

      curl_close($ch);

}

function getTeamNames($club = 'current'){

      $config = getConfiguration($club);
      $clubids = getClubIDs($club);
      foreach ($clubids as $clubid){
	    $url = "http://resultater.basket.dk/tms/Turneringer-og-resultater/Forening-Holdoversigt.aspx?ForeningsId=".$clubid;

	    $ch = curl_init();
	    curl_setopt($ch,CURLOPT_URL,$url);
	    curl_setopt($ch,CURLOPT_POST,count($fields));
	    curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	    curl_setopt($ch,CURLOPT_HTTPHEADER,Array("Content-Type: application/x-www-form-urlencoded")); 
	    curl_setopt($ch,CURLOPT_TIMEOUT,5);

	    $result = curl_exec($ch);
	    $dom = new DOMDocument();
    
	    //load the html  
	    $page = '
	    <html>
	    <head>
	    <meta http-equiv="content-type" content="text/html; charset=utf-8">
	    <title>Dommer Sync</title>
	    </head>
	    <body></body>
	    </html>
	    ';

	    $page .= $result;
	    $html = @$dom->loadHTML($page);
	    $xpath = new DOMXPath($dom);		
	    $tags = $xpath->query("//a[contains(@id,'hlTeam')]");

	    foreach($tags as $tag){
		  if(!in_array(trim($tag->nodeValue),$teams)){
			$teams[] = trim($tag->nodeValue);
		  }
	    }
      }
      return $teams;
}

function getCourts($club){

      $config = getConfiguration();

      $address = "http://resultater.basket.dk/tms/Turneringer-og-resultater/Forening-Information.aspx?ForeningsId=".$club;
      $content  = file_get_contents($address);
      $dom = new DOMDocument();
      $page = '<html>
	      <head> 
	      <meta http-equiv="content-type" content="text/html; charset=utf-8">
	      <title>Dommer Sync</title>
	      </head>
	      <body></body>
	      </html>';

      $page .= $content;
      $html = $dom->loadHTML($page);
      $xpath = new DOMXPath($dom);

      $tags = $xpath->query('//div[@id="ctl00_ContentPlaceHolder1_Forening1_pnlStadium"]/table/tr/td/table/tr/td/a[@title="Information for spillestedet"]');
      foreach ($tags as $tag) {
	    $courts[] = (trim($tag->nodeValue));
      }

      $gyms = $config['gyms'];
      $gyms = explode(",",$gyms);

      foreach ($gyms as $gym){
	    $courts[] = trim($gym);
      }

return $courts;

}

function getGameURL($gameid){

      return "http://resultater.basket.dk/tms/Turneringer-og-resultater/Kamp-Information.aspx?KampId=".getGame($gameid);

}

function getGame($gameid){

      $config = getConfiguration();

      $validation = setBasketDKValidation();

      $fields = array(
            '__VIEWSTATE'=>$validation['viewstate'],
            '__EVENTTARGET'=>$validation['eventtarget'],
            '__EVENTARGUMENT'=>$validation['eventargument'],
            '__EVENTVALIDATION'=>$validation['eventvalidation'],
            'ctl00%24ContentPlaceHolder1%24Soegning%24Search'=>'rbMatch',
            'ctl00%24ContentPlaceHolder1%24Soegning%24txtSelectedCenterSearchModule'=>'4',
            'ctl00%24ContentPlaceHolder1%24Soegning%24ddlGender'=>'1',
            'ctl00%24ContentPlaceHolder1%24Soegning%24ddlDivision'=>'2',
            'ctl00%24ContentPlaceHolder1%24Soegning%24ddlSeason'=>'0',
            'ctl00%24ContentPlaceHolder1%24Soegning%24txtClubName'=>'',
            'ctl00%24ContentPlaceHolder1%24Soegning%24btnSearchClub'=>'S%C3%B8g',
            'ctl00%24ContentPlaceHolder1%24Soegning%24txtStadiumName'=>'',
            'ctl00%24ContentPlaceHolder1%24Soegning%24txtCommitteeName'=>'',
            'ctl00%24ContentPlaceHolder1%24Soegning%24txtMatchNumber'=>$gameid,
            'ctl00%24ContentPlaceHolder1%24Soegning%24btnSearchMatchNumber'=>'S%C3%B8g',
            'ctl00%24ContentPlaceHolder1%24Soegning%24txtRefereeName'=>'',
            'ctl00%24ContentPlaceHolder1%24Soegning%24ddlTournaments_Tournament'=>'0'
      );

      $url = $validation['url'];

      foreach($fields as $key=>$value){
	    $fields_string .= $key.'='.$value.'&';
      }

      rtrim($fields_string,'&');

      $ch = curl_init();

      curl_setopt($ch,CURLOPT_URL,$url);
      curl_setopt($ch,CURLOPT_POST,count($fields));
      curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      curl_setopt($ch,CURLOPT_HTTPHEADER,Array("Content-Type: application/x-www-form-urlencoded")); 
      curl_setopt($ch,CURLOPT_TIMEOUT,5);


      $result = curl_exec($ch);

      $dom = new DOMDocument();

      //load the html
      $page = '
      <html>
      <head>
      <meta http-equiv="content-type" content="text/html; charset=utf-8">
      <title>Dommer Sync</title>
      </head>
      <body></body>
      </html>
      ';

      $page .= $result;
      $html = @$dom->loadHTML($page);

      $dom->preserveWhiteSpace = false;


      $tables = $dom->getElementsByTagName('table');

      $rows = $tables->item(0)->getElementsByTagName('tr');

      foreach ($rows as $row){

	      $cols = $row->getElementsByTagName('a');

	      $clubnames[] = trim($cols->item(1)->nodeValue);

	      unset($colsarray);

	      foreach ($cols as $col){

		      $colsarray[] = $col->getAttribute('href');

	      }

	      $id=explode("=",$colsarray[0]);

	      $clubids[] = $id[1];

	      return $id[1];

      }
}

function syncTeam($teamid,$teamurl,$currentclubid){

	$config = getConfiguration();
	$teamnames = getTeamNames($currentclubid);
	$clubids = getClubIDs($currentclubid);
	$courts = array();
	$returns = array();
	$currentUser = mysqli_fetch_assoc(getCurrentUser());
	
	for($i = 0, $size = count($clubids); $i < $size; ++$i){
	    $courts = array_merge(getCourts($clubids[$i]),$courts);
	}
	
	if(!mysqli_num_rows(ref_mysql_query("SELECT * FROM teams WHERE name = 'DBBF' AND `clubid`='-1'"))){
	    ref_mysql_query("INSERT INTO teams SET name='DBBF',`clubid`='-1'");
	}
	
	if(!mysqli_num_rows(ref_mysql_query("SELECT * FROM teams WHERE name = '-' AND `clubid`='-1'"))){
	    ref_mysql_query("INSERT INTO teams SET name='-',`clubid`='-1'");
	}
	
	$dbbfentry=mysqli_fetch_assoc(ref_mysql_query("SELECT * FROM teams WHERE name = 'DBBF' AND `clubid`='-1'"));
	$dbbfid=$dbbfentry['id'];
	
	$dom = new DOMDocument();  
	$content  = file_get_contents($teamurl);
	$page = '<html>
		<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<title>Dommer Sync</title>
		</head>
		<body></body>
		</html>';
	$page .= $content;
	$html = $dom->loadHTML($page);
	$dom->preserveWhiteSpace = false;   
	$tables = $dom->getElementsByTagName('table');
	$info = $dom->getElementsByTagName('h2');
	$pulje = explode(", ",$info->item(0)->nodeValue);
  
	if ( $tables->length > 1){
		$rows = $tables->item(1)->getElementsByTagName('tr');   
	}elseif($tables->length == 1){
		$rows = $tables->item(0)->getElementsByTagName('tr');
	}
	
	if ( $tables->length < 1){
	      $returns[] = fetchText("Unavailable League/Team");
	}else{
	foreach ($rows as $row){   
		$cols = $row->getElementsByTagName('td');   
		$hometeam = $cols->item(2)->nodeValue;
		$hometeam = str_replace("\n", "", $hometeam);
		$hometeam = str_replace("\r", "", $hometeam);
		$awayteam = $cols->item(3)->nodeValue;
		$awayteam = str_replace("\n", "", $awayteam);
		$awayteam = str_replace("\r", "", $awayteam);
		$awayteam = trim($awayteam);
		$place = $cols->item(4)->nodeValue;
		$place = trim($place);
		$place = str_replace("  ", "", $place);
		$result = $cols->item(5)->nodeValue;
		$result = trim($result);
		$result = str_replace("  ", "", $result);
		$status = $cols->item(6)->nodeValue;   
		$status = str_replace("\n", "", $status);      
		$status = str_replace("\r", "", $status);      
		$status = str_replace(" ", "", $status);
	      
		if($status=="UDS"){
		    $status=4;
		}
	        
                $team = mysqli_fetch_assoc(ref_mysql_query("SELECT * FROM `calendars` WHERE `id`='".$teamid."'"));

		$teamname = $team['team'];

		if (stristr($teamname,"grandprix") || stristr($teamname,"st") || stristr($teamname,"GP")){
                        $grandprix = 1;
		}else{
			$grandprix = 0;
		}

		if(in_array(str_replace(array("\r\n","\r"),"",strtok($place, "\n")),$courts)){
			if($grandprix){
		        	$athome=1;
			}else{
				if(in_array(trim($hometeam),$teamnames)){
					$athome=1;
				}else{
					$athome=0;
				}
			}
		}else{                                
			$athome=0;                                                        
		}

		//if((in_array(trim($hometeam),$teamnames)) || (in_array(trim($awayteam),$teamnames))){
			$id=$cols->item(0)->nodeValue;
			$id=str_replace("\n", "", $id);
			$id=str_replace("\r", "", $id);
			$id=str_replace(" ", "", $id);
			$basketid=getGame($id);
			$dom2 = new DOMDocument();
		      
			$content2 = file_get_contents("http://resultater.basket.dk/tms/Turneringer-og-resultater/Kamp-Information.aspx?KampId=$basketid");
			$page2 = '<html><head>
				<meta http-equiv="content-type" content="text/html; charset=utf-8">
				<title>Dommer Sync</title>
				</head><body></body></html>';
		      
			$page2 .= $content2;
		      
			if(strstr($page2, "1. dommer")){
				$html2 = $dom2->loadHTML($page2);
				$dom2->preserveWhiteSpace = false;
				$tables2 = $dom2->getElementsByTagName('table');
				$rows2 = $tables2->item(0)->getElementsByTagName('tr');
				$refrow1 = $rows2->item(11)->getElementsByTagName('td');
				$ref1 = $refrow1->item(1)->nodeValue;
				$updatequery = "UPDATE games SET refereeteam1id='$dbbfid',referee1name='$ref1'";
				
				if(strstr($page2, "2. dommer")){
					$refrow2 = $rows2->item(12)->getElementsByTagName('td');
					$ref2 = $refrow2->item(1)->nodeValue;
					$updatequery .= ",refereeteam2id='$dbbfid',referee2name='$ref2'";
				}
				$updatequery .=" WHERE gameid='$id' AND `clubid`='".$currentclubid."'";
				ref_mysql_query($updatequery);
			}
		      
			$fulldate= $cols->item(1)->nodeValue;
			$fulldate = str_replace("\n", "", $fulldate);
			$fulldate = str_replace("\r", "", $fulldate);
			$fulldate = str_replace(" ", "", $fulldate );
			
			if($fulldate != ""){
			$date = "20";
			$date .= substr($fulldate,6,2);
			$date .= "-";
			$date .= substr($fulldate,3,2);
			$date .= "-";
			$date .= substr($fulldate,0,2);
			}else{
			$date = "0000-00-00";
			}
			$time = substr($fulldate,13,2);
			if($time == ""){
			    $time = "00";
			}
			$time .= ":";
			if(substr($fulldate,16,2) == ""){
			    $time .= "00";
			}else{
			    $time .= substr($fulldate,16,2);
			}
			$text = trim($hometeam)." : ".$pulje[0].", ".$pulje[1];
			$text .= " vs. ";
			$text .= trim($awayteam);

			if(mysqli_num_rows(ref_mysql_query("SELECT `gameid` FROM `games` WHERE `gameid` = '$id' AND `clubid`='".$currentclubid."'"))) {
				ref_mysql_query("UPDATE `games` set place='$place' WHERE gameid='$id'");
				$query=mysqli_fetch_assoc(ref_mysql_query("SELECT * FROM games WHERE `gameid` = '$id' AND `clubid`='".$currentclubid."'"));
				$oldtext=$query['text'];
				$olddate=$query['date'];
				$oldtime=$query['time'];
				$oldathome=$query['homegame'];
				$oldteamid=$query['team'];
				$oldresult=$query['result'];
				$oldgrandprix=$query['grandprix'];
				if($oldtext==$text && $olddate==$date && substr($oldtime,0,5)==$time && $oldathome==$athome && $oldteamid==$teamid && $oldresult==$result && $oldgrandprix==$grandprix && $status!=4){
					ref_mysql_query("UPDATE `games` set dt_added=now() WHERE `gameid`='$id' AND `clubid`='".$currentclubid."'");
				}else{
					if($oldtext!=$text && $olddate==$date && substr($oldtime,0,5)==$time){
						$returns[] = fetchText("Updating Info for Game: ").$id;
					}else{
						if($olddate!=$date){
							$returns[] = fetchText("Changes to game: ").$id.fetchText(" Date changed.  ").$olddate." -> ".$date;
						}
						if(substr($oldtime,0,5)!=$time){
                                                        $returns[] = fetchText("Changes to game: ").$id.fetchText(" Time changed.");
                                                }
						if($oldathome!=$athome){
                                                        $returns[] = fetchText("Changes to game: ").$id.fetchText(" Homegame status changed");
                                                }
						if($oldteamid!=$teamid){
                                                        $returns[] = fetchText("Changes to game: ").$id.fetchText(" Team changed.")." $oldteamid -> $teamid";
                                                }
						if($oldresult!=$result){
                                                        $returns[] = fetchText("Changes to game: ").$id.fetchText(" Result changed.");
                                                }
                                                if($oldgrandprix!=$grandprix){
                                                        $returns[] = fetchText("Changes to game: ").$id.fetchText(" Grand Prix status changed.");
                                                }
						$gamechanged=1;
					}
					if($status != 4){
						if($gamechanged){
							ref_mysql_query("UPDATE games SET status='2' WHERE gameid = '$id' AND `clubid`='".$currentclubid."'");
						}
					}else{
						if($gamechanged){
							ref_mysql_query("UPDATE games SET status='$status' WHERE gameid = '$id' AND `clubid`='".$currentclubid."'");
						}
					}
					ref_mysql_query("UPDATE games SET text='$text',date='$date',time='$time',dt_added=now(),homegame='$athome',team='$teamid',result='$result',grandprix='$grandprix' WHERE gameid = '$id' AND `clubid`='".$currentclubid."'");
				}
			}else{
				if($status!=6){
					$status=1;
				}

				$query = "INSERT INTO `games` (`gameid`, `text`, `date`, `time`, `status`, `tableteam3id`, `place`, `homegame`,`team`,`grandprix`,`clubid`,`result`,`referee1name`,`referee2name`) VALUES ('$id', '$text', '$date', '$time','$status',0,'$place','$athome','$teamid','$grandprix','$currentclubid','".$result."','".$ref1."','".$ref2."')";
				error_log($query);
				ref_mysql_query($query);
				//mysqli_query($GLOBALS['link'],$query);

				if($athome){
					$returns[] = fetchText("Adding Homegame: ").$id;
				}else{
					$returns[] = fetchText("Adding Awaygame: ").$id;
				}
				$gamechanged=1;
			} 
		//}      
	
	
	
	}
	if($gamechanged==0){
		$returns[] = fetchText("No changes.");
	}
	}
	return $returns;

}

function getHiddenTeams(){

      $teamArray = array(
	    "-",
	    "DBBF"
      );
      
      return $teamArray;

}


?>
