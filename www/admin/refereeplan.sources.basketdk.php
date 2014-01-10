<?php

function getSourceInfo(){

  return fetchText("Source plugin for resultater.basket.dk.");

}

function getClubIDs(){
  
  $config=getConfiguration();

  return explode(',',$config['clubids']);
}

function addAllTeams(){
    
    $config=getConfiguration();

    $clubids=getClubIDs();
    
    $addedteams=0;
    for($i = 0, $size = count($clubids); $i < $size; ++$i){
      $url = "http://resultater.basket.dk/tms/Turneringer-og-resultater/Forening-Holdoversigt.aspx?ForeningsId=".$clubids[$i];
      $input .= @file_get_contents($url) or die("Could not access url: $url");
    }
    
    $regexp = "PuljeId=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
    $regexp2 = "RaekkeId=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
    
    preg_match_all("/$regexp/siU", $input, $matches);
    preg_match_all("/$regexp2/siU", $input, $matches2);
  
    $i=0;
    foreach ($matches[2] as $urls){
      $name=$matches2[3][$i];
      if(!mysql_num_rows(mysql_query("SELECT * FROM `calendars` WHERE `address` = 'http://resultater.basket.dk/tms/Turneringer-og-resultater/Pulje-Komplet-Kampprogram.aspx?PuljeId=$urls'"))){
        mysql_query("INSERT into calendars (`address`, `team`) VALUES ('http://resultater.basket.dk/tms/Turneringer-og-resultater/Pulje-Komplet-Kampprogram.aspx?PuljeId=$urls', '".fixCharacters($name)."')");
        $addedteams++;
      }
      $i=$i+1;
    }

    return $addedteams;
}

function setBasketDKValidation(){

    $info = explode("<br>",file_get_contents("http://www.dommerplan.dk/info.php"));

    $validation = array('url' => "http://resultater.basket.dk/tms/Turneringer-og-resultater/Soegning.aspx",
		    'viewstate' => $info[0],
		    'eventtarget' => "",
		    'eventargument' => "",
		    'eventvalidation' => $info[1]
		    );

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
            '__EVENTTARGET'=>$validation['eventtarget'],
            '__EVENTARGUMENT'=>$validation['eventargument'],
            '__EVENTVALIDATION'=>$validation['eventvalidation'],
            'ctl00%24ContentPlaceHolder1%24Soegning%24Search'=>'rdClub',
            'ctl00%24ContentPlaceHolder1%24Soegning%24txtSelectedCenterSearchModule'=>'2',
            'ctl00%24ContentPlaceHolder1%24Soegning%24ddlGender'=>'1',
            'ctl00%24ContentPlaceHolder1%24Soegning%24ddlDivision'=>'2',
            'ctl00%24ContentPlaceHolder1%24Soegning%24ddlSeason'=>'0',
            'ctl00%24ContentPlaceHolder1%24Soegning%24txtClubName'=>'',
            'ctl00%24ContentPlaceHolder1%24Soegning%24btnSearchClub'=>'S%C3%B8g',
            'ctl00%24ContentPlaceHolder1%24Soegning%24txtStadiumName'=>'',
            'ctl00%24ContentPlaceHolder1%24Soegning%24CommitteeName'=>'',
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

function getTeamNames(){

      $config = getConfiguration();
      $clubids = getClubIDs();
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

function syncTeam($teamid,$teamurl){

	$config = getConfiguration();
	$teamnames = getTeamNames();
	$clubids = getClubIDs();
	$courts = array();
	$returns = array();
	
	for($i = 0, $size = count($clubids); $i < $size; ++$i){
	    $courts = array_merge(getCourts($clubids[$i]),$courts);
	}
	
	if(!mysql_num_rows(mysql_query("SELECT * FROM teams WHERE name = 'DBBF'"))){
	    mysql_query("INSERT INTO teams SET name='DBBF'");
	}
	
	$dbbfentry=mysql_fetch_assoc(mysql_query("SELECT * FROM teams WHERE name = 'DBBF'"));
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
	      $returns[] = "Unavailable League/Team";
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
	      
		if (stristr($teamname,"grandprix") || stristr($teamname,"st")){
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

		if((in_array(trim($hometeam),$teamnames)) || (in_array(trim($awayteam),$teamnames))){
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
				$updatequery .=" WHERE id='$id'";
				mysql_query($updatequery);
			}
		      
			$fulldate= $cols->item(1)->nodeValue;
			$fulldate = str_replace("\n", "", $fulldate);
			$fulldate = str_replace("\r", "", $fulldate);
			$fulldate = str_replace(" ", "", $fulldate );

			$date = "20";
			$date .= substr($fulldate,6,2);
			$date .= "-";
			$date .= substr($fulldate,3,2);
			$date .= "-";
			$date .= substr($fulldate,0,2);
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
			
			$returns[] = $text;

			if(mysql_num_rows(mysql_query("SELECT id FROM games WHERE id = '$id'"))) {
				mysql_query("UPDATE `games` set place='$place' WHERE id='$id'");
				$query=mysql_fetch_assoc(mysql_query("SELECT * FROM games WHERE id = '$id'"));
				$oldtext=$query['text'];
				$olddate=$query['date'];
				$oldtime=$query['time'];
				$oldathome=$query['homegame'];
				$oldteamid=$query['team'];
				$oldresult=$query['result'];
				$oldgrandprix=$query['grandprix'];
				if($oldtext==$text && $olddate==$date && substr($oldtime,0,5)==$time && $oldathome==$athome && $oldteamid==$teamid && $oldresult==$result && $oldgrandprix==$grandprix && $status!=4){
					mysql_query("UPDATE `games` set dt_added=now() WHERE id='$id'");
				}else{
					if($oldtext!=$text && $olddate==$date && substr($oldtime,0,5)==$time){
						$returns[] = fetchText("Updating Info for Game: ").$id;
					}else{
						$returns[] = fetchText("Changes to game: ").$id;
						$gamechanged=1;
					}
					if($status != 4){
						if($gamechanged){
							mysql_query("UPDATE games SET status='2' WHERE id = '$id'");
						}
					}else{
						if($gamechanged){
							mysql_query("UPDATE games SET status='$status' WHERE id = '$id'");
						}
					}
					mysql_query("UPDATE games SET text='$text',date='$date',time='$time',dt_added=now(),homegame='$athome',team='$teamid',result='$result',grandprix='$grandprix' WHERE id = '$id'");
				}
			}else{
				if($status!=6){
					$status=1;
				}
				mysql_query("INSERT INTO games (`id`, `text`, `date`, `time`, `status`, `tableteam3id`, `place`, `homegame`,`team`,`grandprix`) VALUES ('$id', '$text', '$date', '$time','$status',9999,'$place','$athome','$teamid','$grandprix')");
				if($athome){
					$returns[] = fetchText("Adding Homegame: ").$id;
				}else{
					$returns[] = fetchText("Adding Awaygame: ").$id;
				}
				$gamechanged=1;
			} 
		}      
	
	
	
	}
	if($gamechanged==0){
		$returns[] = fetchText("No changes.");
	}
	}
	return $returns;

}


?>