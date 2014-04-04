<?php

class gameObj{

      private $data;
      
      /* The constructor */
      public function __construct($par){
	    if(is_array($par))
		  $this->data = $par;
      }      

      public function __toString(){
	    
	    $currentUser = mysql_fetch_assoc(getCurrentUser());
	    
	    $teamlists["refereeteam1"] = " ";
	    $teamlists["refereeteam2"] = " ";
	    $teamlists["tableteam1"] = " ";
	    $teamlists["tableteam2"] = " ";
	    $teamlists["tableteam3"] = " ";
	    $result = ref_mysql_query("SELECT id, name FROM teams WHERE (`clubid`='".$currentUser['clubid']."' OR `clubid`='-1') ORDER BY name ASC");
	    
	    while(list($id, $name)=mysql_fetch_row($result)) {
		  foreach ($teamlists as $key => $value){
			if(!(preg_match('/table/',$key) && $name=="DBBF"))
			if($this->data[$key.'id']==$id){
			      $teamlists[$key].= "<option value=\"".$id."\" selected>".$name."</option>";
			}else{
			      $teamlists[$key].= "<option value=\"".$id."\">".$name."</option>"; 
			}
		  }
	    }
	    
	    $date=substr($this->data['date'],8,2);
	    $date.="/";
	    $date.=substr($this->data['date'],5,2);
                $date.="-";
                $date.=substr($this->data['date'],0,4);
		$return = "";

		/*if($this->data['refereeteam1id']!='0' || $this->data['refereeteam2id']=='0' || $this->data['tableteam1id']=='0' || $this->data['tableteam2id']=='0' || $this->data['tableteam3id']=='0'){
		    if($this->data['status'] != '1'){
			ref_mysql_query("UPDATE games SET status='1' WHERE gameid = '".$this->data['gameid']."'");
			$this->data['status']='1';
		    }
		}*/
		
		switch($this->data['status']){
		case 0:  // OK
		    $return.= '<li id="game-'.$this->data['gameid'].'" class="game ok">';
		    break;
		case 1:  // New
		    $return.= '<li id="game-'.$this->data['gameid'].'" class="game new">';
		      break;
		case 2:  // Changed
		    $return.= '<li id="game-'.$this->data['gameid'].'" class="game changed">';
		    break;
		case 3:
		    $return.= '<li id="game-'.$this->data['gameid'].'" class="game cancelled">';
		    break;
		case 4:
		    $return.= '<li id="game-'.$this->data['gameid'].'" class="game moved">';
		    break;
		}

		$day="";
		$date=substr($this->data['date'],8,2);
		$date.="/";
		$date.=substr($this->data['date'],5,2);
		$date.="-";
		$date.=substr($this->data['date'],0,4);
		$dateformat=substr($this->data['date'],0,4);
		$dateformat.=substr($this->data['date'],5,2);
		$dateformat.=substr($this->data['date'],8,2);
		
		switch(date("D",strtotime($dateformat))){
			case "Mon":
				$day=fetchText("Monday");
				break;  
			case "Tue":
				$day=fetchText("Tuesday");
				break;
			case "Wed":
				$day=fetchText("Wednesday");
				break;
			case "Thu":
				$day=fetchText("Thursday");
				break;
			case "Fri":
				$day=fetchText("Friday");
				break;  
			case "Sat":
				$day=fetchText("Saturday");
				break;   
			case "Sun":
				$day=fetchText("Sunday");
				break;
		}
		
		if($this->data['referee1name'] == ""){
			$dbbfref1="<br>";
		}else{
			$dbbfref1="DBBF:".$this->data['referee1name'];
		}
		
		if($this->data['referee2name'] == ""){
			$dbbfref2="<br>";
		}else{
			$dbbfref2="DBBF:".$this->data['referee2name'];
		}
		
		$confirmed1 = '<img width="15px" src="img/';
		
		if($this->data['ref1confirmed'] == "1"){
		
			$confirmedstatus1 = "disabled";		
			$confirmed1 .= 'add.png" title="'.fetchText("Refereeduty has been confirmed").'">';
			
		}else{
		
			$confirmed1 .= 'remove.png" title="'.fetchText("Refereeduty has NOT been confirmed").'">';
		
		}
		
		$confirmed2 = '<img width="15px" src="img/';
		
		if($this->data['ref2confirmed'] == "1"){
		
			$confirmedstatus2 = "disabled";
			$confirmed2 .= 'add.png" title="'.fetchText("Refereeduty has been confirmed").'">';
		}else{
		
			$confirmed2 .= 'remove.png" title="'.fetchText("Refereeduty has NOT been confirmed").'">';
		
		}
		
		if($this->data['status'] == 2){
			$acknowledge = '<a href="#" class="acknowledge">'.fetchText("Move OK").'</a>';
		}else{
			$acknowledge = "";
		}
		
		$return .= '<table class="gameinfo">
			      <tr>
			      <td class="id-title">'.fetchText("Game number:").' <div class="number">'.$this->data['gameid'].'</div></td>
			      <td class="time-title">'.fetchText("Time:").' <div class="time">'.$this->data['time'].'</div></td>
			      <td class="date-title">'.fetchText("Date:").' <div class="date">'.$day.', '.$date.'</div></td>
			      <td class="place-title">'.fetchText("Place:").' <div class="place">'.$this->data['place'].'</div></td>
			      <td class="delete"><a href="#" class="delete">'.fetchText("Delete").'</a></td>
			      </tr>
			      <tr>
			      <td colspan="4">'.fetchText("Description:").' <div class="text">'.$this->data['text'].'</div></td>
			      <td>'.$acknowledge.'</td>
			      </tr>
			    </table>
			    <table id="dutiesinfo-'.$this->data['gameid'].'" class="dutiesinfo" hidden="true">
			    <div class="actions">
			      <tr>
			       <td width="25%">
				'.fetchText("Referee Table:").' 
			       </td>
			       <td width="25%">
			        '.fetchText("Referee #1:").'
			       </td>
			       <td rowspan="2">
			        '.trim($dbbfref1).'
			       </td>
			       <td rowspan="2" width="5%">
			        '.$confirmed1.'
			       </td>
			      </tr>
			      <tr>
			       <td>
			        <select name="table1" id="table1Select">
				  <option value="0">'.fetchText("Choose a Team").'</option>
				  '.$teamlists["tableteam1"].'
				</select>
			       </td>
			       <td>
			        <select align="left" name="referee1" id="referee1Select" '.$confirmedstatus1.'>
				  <option value="0">'.fetchText("Choose a Team").'</option>
				  '.$teamlists["refereeteam1"].'
				</select>
			       </td>
			      </tr>
			      <tr>
			       <td>
				'.fetchText("Referee Table:").'
			       </td>
			       <td>
			        '.fetchText("Referee #2:").'
			       </td>
			       <td rowspan="2">
			        '.trim($dbbfref2).'
			       </td>
			       <td rowspan="2">
				'.$confirmed2.'
			       </td>
			      </tr>
			      <tr>
			       <td>
			        <select name="table2" id="table2Select">
				  <option value="0">'.fetchText("Choose a Team").'</option>
				    '.$teamlists["tableteam2"].'
				  </select>
			       </td>
			       <td>
			        <select name="referee2" id="referee2Select" '.$confirmedstatus2.'>
				  <option value="0">'.fetchText("Choose a Team").'</option>
				  '.$teamlists["refereeteam2"].'
				  </select>
			       </td>
			      </tr>
			      <tr>
			      <td>
			       '.fetchText("Shot Clock:").'
			      </td>
			      </tr>
			      <tr>
			       <td>
			       <select name="table3" id="table3Select">
				 <option value="0">'.fetchText("Choose a Team").'</option>
				 '.$teamlists["tableteam3"].'
				</select>
			       </td>
			      </tr>
			    </div>
			    </table>
			    
			    
			  </li>';
			
		 return $return;
	}
	
	public static function changeTeam($id, $team, $teamlist){
	
		$currentUser = mysql_fetch_assoc(getCurrentUser());
		
		switch($teamlist){
			case '1':
				$idlist="refereeteam1id";
			        break;
			case '2':
                                $idlist="refereeteam2id";
                                break;
			case '3':
				$idlist="tableteam1id";
                                break;
			case '4':
                                $idlist="tableteam2id";
                                break;
			case '5':
                                $idlist="tableteam3id";
                                break;	
		}
		$team = self::esc($team);
		if(!$team) throw new Exception("Wrong update text!");
		$game=mysql_fetch_assoc(ref_mysql_query("SELECT * FROM games WHERE gameid = '$id' AND `clubid`='".$currentUser['clubid']."'"));
		$status=$game['status'];
		ref_mysql_query("UPDATE games SET $idlist='".$team."' WHERE gameid=".$id." AND `clubid`='".$currentUser['clubid']."'");
		if($status=='2'){
		    ref_mysql_query("UPDATE games SET status='1' WHERE gameid=".$id." AND `clubid`='".$currentUser['clubid']."'");
		    $status='1';
		}
		
		$game=mysql_fetch_assoc(ref_mysql_query("SELECT * FROM games WHERE gameid = '$id' AND `clubid`='".$currentUser['clubid']."'"));
		if($status=='1' && $game['refereeteam1id']!='0' && $game['refereeteam2id']!='0' && $game['tableteam1id']!='0' && $game['tableteam2id']!='0' && $game['tableteam3id']!='0'){
		    ref_mysql_query("UPDATE games SET status='0' WHERE gameid = '".$game['gameid']."' AND `clubid`='".$currentUser['clubid']."'");
		}
	
		if(mysql_affected_rows($GLOBALS['link'])!=1)
			throw new Exception("Couldn't update item!");
	}
		
	public static function edit($id, $text, $type){
	
		$currentUser = mysql_fetch_assoc(getCurrentUser());
		
		echo '<script language="javascript">confirm("'.$text.'")</script>;';
		$text = self::esc($text);
		if(!$text) throw new Exception("Wrong update text!");
		
		ref_mysql_query("UPDATE games SET $type='".$text."' WHERE gameid=".$id." AND `clubid`='".$currentUser['clubid']."'");
		
		if(mysql_affected_rows($GLOBALS['link'])!=1)
			throw new Exception("Couldn't update item!");
	}

	
	public static function delete($id){
	
		$currentUser = mysql_fetch_assoc(getCurrentUser());
		
		ref_mysql_query("DELETE FROM games WHERE gameid=".$id." AND `clubid`='".$currentUser['clubid']."'");
		
		if(mysql_affected_rows($GLOBALS['link'])!=1)
			throw new Exception("Couldn't delete item!");
	}
	
		
	public static function createNew($text){
	
		$currentUser = mysql_fetch_assoc(getCurrentUser());
		
		$text = self::esc($text);
		if(!$text) throw new Exception("Wrong input data!");
		
		$posResult = ref_mysql_query("SELECT MAX(position)+1 FROM games WHERE `clubid`='".$currentUser['clubid']."'");
		
		if(mysql_num_rows($posResult))
			list($position) = mysql_fetch_array($posResult);

		//if(!$position) 
		$position = 1;

		ref_mysql_query("INSERT INTO games SET text='".$text."',time='00:00:00',position = ".$position.",`clubid`='".$currentUser['clubid']."'");

		if(mysql_affected_rows($GLOBALS['link'])!=1)
			throw new Exception("Error inserting Game!");
		
		// Creating a new Game and outputting it directly:
		
		echo (new gameObj(array(
			'id'	=> mysql_insert_id($GLOBALS['link']),
			'text'	=> $text
		)));
		
		
		exit;
	}
	
	
	public static function esc($str){
		
		if(ini_get('magic_quotes_gpc'))
			$str = stripslashes($str);
		
		return mysql_real_escape_string(strip_tags($str));
	}

}

?>
