<?php 
case "configuration":

  $clublist = "<option value=\":\">".fetchText("No Club selected/Remove Sister Club")."</option>";
  list ($clubs,$ids) = getClubs();
  
  $clubids = getClubIDs();
  
  foreach($clubids as $id){
  	$clubslist .= '"'.$id.'",';
  }
  
  $clubslist = substr($clubslist, 0, -1);
  
  for ($k = 0; $k < count($clubs); $k++){
	$clubnames[$ids[$k]]=$clubs[$k];
	$clublist.= "<option value=\"".$ids[$k].":".fixCharacters($clubs[$k])."\">".fixCharacters($clubs[$k])."</option>";
  }

  $javascript .= 'function addSisterClubs(select){
			var id = Math.floor( Math.random()*99999 );
			$("#sisterClubs").append(\'<select id="sisterClub-\'+id+\'" name="clubselect[]" onchange="javascript:changeClubs()">'.addslashes($clublist).'</select>\');
			$("#sisterClub-"+id+\' option[value^="\'+select+\':"]\').attr("selected", true);
		}
		  
		  function changeClubs(){
			document.mainForm.changeClub.value=1;
			$.post("ajax/refereeplan.ajax.administration.php", $("#mainForm").serialize());
			document.mainForm.changeClub.value="";
			$("#message").text("'.fetchText("Settings were updated").'").show().fadeOut(1000);
			var inputs = document.getElementById("sisterClubs").childNodes;
			//var inputs = $("#sisterClubs select");
			for (x=0;x<=inputs.length;x++){
				    //alert(inputs[x].getAttribute("id"));
				    var ti=inputs[x].selectedIndex;
				    var op=inputs[x].options;
				    //alert(op[ti].value);
				    if(op[ti].value == ":"){
					  $("#"+inputs[x].getAttribute("id")).remove();
				    }
			}
			
		  }
		  
		  function changeUpdatesUrl(){
			document.mainForm.submit();
		  }
		  
		  function generateClubs(){
			var clubs=['.$clubslist.'];
			for (var i=0,len=clubs.length; i<len; i++){
				addSisterClubs(clubs[i]);		
			}
		  }
		  $(document).ready(function() {
			generateClubs();
		  });
		  ';
  
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
  

  
  echo '<table width="100%">
	  <tr>
	    <td width="50%">';
  
  echo fetchText("Select Club:","header3");
  
  echo '</td>
	<td width="50%" align="right">';
	
  echo '<div id="message"></div>';
  
  echo '</td>
	</tr>
	</table>';
  
  echo '<select name="clubselect[]" onchange="changeClubs();">'.
          $clublist
        .'</select>';
  
  echo fetchText("Sister Clubs:","header3");
  
  echo '<div id="sisterClubs"></div>';
  echo '<br><a href="#" onclick="javascript:addSisterClubs(0);"><img width="15px" src="img/add.png">'.fetchText("Add Sister Club").'</a><br>';
  
  echo fetchText("URL for Updates:","header3");
  
  echo '<input type="text" onchange="javascript:changeUpdatesUrl();" name="updatesUrl" value="'.$config['updatesurl'].'">';
  
  echo '<input type="hidden" name="changeClub">
        <input type="hidden" name="addSisterClub">';
   
break;
?>