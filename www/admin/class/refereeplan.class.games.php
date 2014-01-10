<?php

class gameObj{
		
	private $data;
	
	/* The constructor */
	public function __construct($par){
		if(is_array($par))
			$this->data = $par;
	}
		
	public function __toString(){
	
		$refereeteamlist1="";
		$refereeteamlist2="";
		$tableteamlist1="";
		$tableteamlist2="";
		$tableteamlist3="";
		
		$result=mysql_query("select id, name from teams order by name asc");
		
		while(list($id, $name)=mysql_fetch_row($result)) {
		        if($this->data['refereeteam1id']==$id){
		        	$refereeteamlist1.= "<option value=\"".$id."\" selected>".$name."</option>";
		        }
		        else{
		        	$refereeteamlist1.= "<option value=\"".$id."\">".$name."</option>"; 
		        }
		        
		        if($this->data['refereeteam2id']==$id){
                                $refereeteamlist2.= "<option value=\"".$id."\" selected>".$name."</option>";
                        }
                        else{
                                $refereeteamlist2.= "<option value=\"".$id."\">".$name."</option>"; 
                        }
		}
		
                $result=mysql_query("select id, name from teams order by name asc");
		while(list($id, $name)=mysql_fetch_row($result)) {
                        if($this->data['tableteam1id']==$id){
                                $tableteamlist1.= "<option value=\"".$id."\" selected>".$name."</option>";
                        }
                        else{
                                $tableteamlist1.= "<option value=\"".$id."\">".$name."</option>"; 
                        }
                        
                        if($this->data['tableteam2id']==$id){
                                $tableteamlist2.= "<option value=\"".$id."\" selected>".$name."</option>";
                        }
                        else{
                                $tableteamlist2.= "<option value=\"".$id."\">".$name."</option>"; 
                        }
                
			if($this->data['tableteam3id']==$id){
                                $tableteamlist3.= "<option value=\"".$id."\" selected>".$name."</option>";
                        }
                        else{
                                $tableteamlist3.= "<option value=\"".$id."\">".$name."</option>"; 
                        }
                }
                
                $date=substr($this->data['date'],8,2);
                $date.="/";
                $date.=substr($this->data['date'],5,2);
                $date.="-";
                $date.=substr($this->data['date'],0,4);
		$return = "";
		
		if($this->data['status']=='1' && $this->data['refereeteam1id']!='0' && $this->data['refereeteam2id']!='0' && $this->data['tableteam1id!']!='0' && $this->data['tableteam2id']!='0' && $this->data['tableteam3id']!='0'){
		    mysql_query("UPDATE games SET status='0' WHERE id = '".$this->data['id']."'");
		    $this->data['status']='0';
		}
		switch($this->data['status']){
		case 0:  // OK
		    $return.= '<li id="game-'.$this->data['id'].'" class="game ok">';
		    break;
		case 1:  // New
		    $return.= '<li id="game-'.$this->data['id'].'" class="game new">';
		      break;
		case 2:  // Changed
		    $return.= '<li id="game-'.$this->data['id'].'" class="game changed">';
		    break;
		case 3:
		    $return.= '<li id="game-'.$this->data['id'].'" class="game cancelled">';
		    break;
		case 4:
		    $return.= '<li id="game-'.$this->data['id'].'" class="game moved">';
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
			$confirmed1 .= 'add.png" title="Dommertjansen er bekræftet">';
			
		}else{
		
			$confirmed1 .= 'remove.png" title="Dommertjansen er IKKE bekræftet">';
		
		}
		
		$confirmed2 = '<img width="15px" src="img/';
		
		if($this->data['ref2confirmed'] == "1"){
		
			$confirmedstatus2 = "disabled";
			$confirmed2 .= 'add.png" title="Dommertjansen er bekræftet">';
		}else{
		
			$confirmed2 .= 'remove.png" title="Dommertjansen er IKKE bekræftet">';
		
		}
			
		$return .= '
				
				Kampnummer: <div class="number"><a href="gotoGame.php?gameID='.$this->data['id'].'" target="_blank">'.$this->data['id'].'</a></div>
				Dato: <div class="date">'.$date.'</div>
				<div class="day">'.$day.'</div>
				Tidspunkt: <div class="time">'.$this->data['time'].'</div>
				Beskrivelse: <div class="text">'.$this->data['text'].'</div>
				Hal: <div class="place">'.$this->data['place'].'</div>
				
				<div class="actions">
				
					<div style="position:absolute; right:150px; width:200px;">
						Dommerbord: <form name="tableteam1" action="" class="tableteam1">
                                                <select name="table1" id="table1Select">
                                                  <option value="0">Vælg et hold</option>
                                                  '.$tableteamlist1.'
                                                </select>
                                                </form>
                                                <br><br>
                                                Dommerbord: <form name="tableteam2" action="" class="tableteam2">
                                                <select name="table2" id="table2Select">
                                                  <option value="0">Vælg et hold</option>
                                                  '.$tableteamlist2.'
                                                 </select>
                                                 </form>
								
						<a href="#" class="delete">Delete</a>
						<a href="#" class="edit">Edit</a>
					</div>
					<div style="position:absolute; right:0px; width:175px;">
						
						<text align="right">1.Dommer: <form name="refereeteam1" action="" class="refereeteam1">
						<select align="left" name="referee1" id="referee1Select" '.$confirmedstatus1.'>
						  <option value="0">Vælg et hold</option>
						  '.$refereeteamlist1.'
						</select>
						'.$confirmed1.'
						</form>						
						'.trim($dbbfref1).'
						2.Dommer: <form name="refereeteam2" action="" class="refereeteam2">
						<select name="referee2" id="referee2Select" '.$confirmedstatus2.'>
                        		          <option value="0">Vælg et hold</option>
                                		  '.$refereeteamlist2.'
                                		 </select>
                                		 '.$confirmed2.'
						</form>
						'.trim($dbbfref2).'
						<br>24. Sekunder: <form name="tableteam3" action="" class="tableteam3">
						<select name="table3" id="table3Select">
                                                  <option value="0">Vælg et hold</option>
                                                  '.$tableteamlist3.'
                                                 </select>
                                                </form>
                                                </text>
					</div>	
				</div> <!-- actions -->				
			</li>';
		 return $return;
	}
	
	public static function changeTeam($id, $team, $teamlist){
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
		$status=mysql_fetch_assoc(mysql_query("SELECT status FROM games WHERE id = '$id'"));
		$status=$status['status'];
		mysql_query("   UPDATE games
				SET $idlist='".$team."'
				WHERE id=".$id);
		if($status=='2'){
		mysql_query("   UPDATE games
				SET status='1'
				WHERE id=".$id);
		}
	
		if(mysql_affected_rows($GLOBALS['link'])!=1)
			throw new Exception("Couldn't update item!");
	}
		
	public static function edit($id, $text, $type){
		echo '<script language="javascript">confirm("'.$text.'")</script>;';
		$text = self::esc($text);
		if(!$text) throw new Exception("Wrong update text!");
		
		mysql_query("UPDATE games SET $type='".$text."' WHERE id=".$id);
		
		if(mysql_affected_rows($GLOBALS['link'])!=1)
			throw new Exception("Couldn't update item!");
	}

	
	public static function delete($id){
		
		mysql_query("DELETE FROM games WHERE id=".$id);
		
		if(mysql_affected_rows($GLOBALS['link'])!=1)
			throw new Exception("Couldn't delete item!");
	}
	
		
	public static function createNew($text){
		
		$text = self::esc($text);
		if(!$text) throw new Exception("Wrong input data!");
		
		$posResult = mysql_query("SELECT MAX(position)+1 FROM games");
		
		if(mysql_num_rows($posResult))
			list($position) = mysql_fetch_array($posResult);

		//if(!$position) 
		$position = 1;

		mysql_query("INSERT INTO games SET text='".$text."',time='00:00:00',position = ".$position);

		if(mysql_affected_rows($GLOBALS['link'])!=1)
			throw new Exception("Error inserting Game!");
		
		// Creating a new Game and outputting it directly:
		
		echo (new gameObj(array(
			'id'	=> mysql_insert_id($GLOBALS['link']),
			'text'	=> $text
		)));
		
		
		exit;
	}
	
	/*
		A helper method to sanitize a string:
	*/
	
	public static function esc($str){
		
		if(ini_get('magic_quotes_gpc'))
			$str = stripslashes($str);
		
		return mysql_real_escape_string(strip_tags($str));
	}
	
} // closing the class definition

?>
