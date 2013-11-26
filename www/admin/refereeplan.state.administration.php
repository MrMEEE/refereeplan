<?php 
case "configuration":

  $clublist = "<option value=\":\">".fetchText("No Club selected/Remove Sister Club")."</option>";
  list ($clubs,$ids) = getClubs();
  
  $clubids = getClubIDs();
  for ($k = 0; $k < count($clubs); $k++){
	$clubnames[$ids[$k]]=$clubs[$k];
	$clublist.= "<option value=\"".$ids[$k].":".fixCharacters($clubs[$k])."\">".fixCharacters($clubs[$k])."</option>";
  }

  $javascript .= 'function addSisterClubs(select){
			var id = Math.floor( Math.random()*99999 );
			$("#sisterClubs").append(\'<select id="sisterClub-\'+id+\'" name="clubselect[]" onchange="javascript:changeClubs()">'.addslashes($clublist).'</select><br>\');
			$("#sisterClub-"+id+\' option[value^="\'+select+\':"]\').attr("selected", true);
		}
		  
		  function changeClubs(){
			document.mainForm.changeClub.value=1;
			$.post("ajax/refereeplan.ajax.administration.php", $("#mainForm").serialize());
			document.mainForm.changeClub.value="";
			$("#message").text("'.fetchText("Settings were updated").'").show().fadeOut(1000);
		  }
		  
		  function changeUpdatesUrl(){
			document.mainForm.submit();
		  }';
  
  echo fetchText("Configuration","header2");
  
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
  
  echo '<div id="message"></div>';
  
  echo fetchText("Select Club:","header3");
  
  echo '<select name="clubselect[]" onchange="changeClubs();">'.
          $clublist
        .'</select>';
  
  echo fetchText("Sister Clubs:","header3");
  
  echo '<div id="sisterClubs">';
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

    echo '<select name="clubselect[]" onchange="javascript:changeClubs()">
	   '.$clublist.' 
	  </select><br>';
  }
  
  echo '</div>';
  
  echo '<br><a href="#" onclick="javascript:addSisterClubs(0);"><img width="15px" src="img/add.png">'.fetchText("Add Sister Club").'</a><br>';
  
  echo fetchText("URL for Updates:","header3");
  
  echo '<input type="text" onchange="javascript:changeUpdatesUrl();" name="updatesUrl" value="'.$config['updatesurl'].'">';
  
  echo '<input type="hidden" name="changeClub">
        <input type="hidden" name="addSisterClub">';
   
break;
?>