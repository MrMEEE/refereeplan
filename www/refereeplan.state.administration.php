<?php 
case "configuration":
  
  $javascript .= 'function addSisterClubs(){
			document.mainForm.addSisterClub.value = 1;
		        document.mainForm.submit();
		  }
		  
		  function changeClubs(id){
			document.mainForm.changeClub.value = id;
		        document.mainForm.submit();
		  }';
  
  if(isset($_POST["changeClub"])){
    $clubinfo = explode(":",$_POST["clubselect0"]);
    $clubids = $clubinfo[0];
    $clubname = $clubinfo[1];
    $i = 1;
    while(isset($_POST["clubselect".$i])){
       if($_POST["clubselect".$i] != ":"){
          $clubinfo = explode(":",$_POST["clubselect".$i]);
          $clubids .= ",".$clubinfo[0];
       }
       $i++;
    }
    mysql_query("UPDATE `config` SET `value`='".$clubname."' WHERE `name`='clubname'");
    mysql_query("UPDATE `config` SET `value`='".$clubids."' WHERE `name`='clubids'");
    
  }
  
  
  echo fetchText("Configuration","header2");
  list ($clubs,$ids) = getClubs();
  
  $clubids = getClubIDs();
  
  if($clubids[0] == ""){
      $clublist.= "<option value=\":\" selected>".fetchText("No Club selected.")."</option>";
  }else{
      $clublist.= "<option value=\":\">".fetchText("No Club selected.")."</option>";
  }
  for ($i = 0; $i < count($clubs); $i++){
      if($ids[$i]==$clubids[0]){
	  $clublist.= "<option value=\"".$ids[$i].":".fixCharacters($clubs[$i])."\" selected>".fixCharacters($clubs[$i])."</option>";
      }else{
	  $clublist.= "<option value=\"".$ids[$i].":".fixCharacters($clubs[$i])."\">".fixCharacters($clubs[$i])."</option>";
      }
  }
  
  echo fetchText("Select Club:","header3");
  
  echo '<select name="clubselect0" onchange="changeClubs(0);">'.
          $clublist
        .'</select>';
  
  echo fetchText("Sister Clubs:","header3");
  
  for ($i = 1; $i <= count($clubids)-1; $i++){
    if($clubids[$i]==""){
      $clublist = "<option value=\":\" selected>".fetchText("No Club selected/Remove Sister Club")."</option>";
    }else{
      $clublist = "<option value=\":\">".fetchText("No Club selected/Remove Sister Club")."</option>";
    }
   
    for ($k = 0; $k < count($clubs); $k++){
      if($ids[$k]==$clubids[$i]){
	$clublist.= "<option value=\"".$ids[$k].":".fixCharacters($clubs[$k])."\" selected>".fixCharacters($clubs[$k])."</option>";
      }else{
	$clublist.= "<option value=\"".$ids[$k].":".fixCharacters($clubs[$k])."\">".fixCharacters($clubs[$k])."</option>";
      }
    }

    echo '<select name="clubselect'.$i.'" onchange="javascript:changeClubs('.$i.')">
	   '.$clublist.' 
	  </select><br>';
  }
  
  if($_POST['addSisterClub'] == 1){

    $clublist = "<option value=\":\" selected>".fetchText("No Club selected/Remove Sister Club")."</option>";

    for ($k = 0; $k < count($clubs); $k++){
      $clublist.= "<option value=\"".$ids[$k].":".fixCharacters($clubs[$k])."\">".fixCharacters($clubs[$k])."</option>";
    }

    echo '<select name="clubselect'.$i.'" onchange="javascript:changeClubs('.$i.')">
	  '.$clublist.' 
	 </select><br>';

  }
  
  echo '<br><a href="#" onclick="javascript:addSisterClubs();"><img width="15px" src="img/add.png">'.fetchText("Add Sister Club").'</a><br>';
  
  
  echo '<input type="hidden" name="changeClub">
        <input type="hidden" name="addSisterClub">';
   
break;
?>