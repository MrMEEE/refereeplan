<?php 
case "configuration":

  $config = getConfiguration();

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

  $javascript .= 'function showUpdated(){
			$("#message").text("'.fetchText("Settings were updated").'").show().fadeOut(2000);
		}
		function addSisterClubs(select){
			var id = Math.floor( Math.random()*99999 );
			$("#sisterClubs").append(\'<select id="sisterClub-\'+id+\'" name="clubselect[]" onchange="javascript:changeClubs()">'.addslashes($clublist).'</select>\');
			$("#sisterClub-"+id+\' option[value^="\'+select+\':"]\').attr("selected", true);
		}
		  
		  function changeClubs(){
			document.mainForm.changeClub.value=1;
			$.post("ajax/refereeplan.ajax.administration.php", $("#mainForm").serialize());
			document.mainForm.changeClub.value="";
			showUpdated();
			var inputs = document.getElementById("sisterClubs").childNodes;
			for (x=0;x<=inputs.length;x++){
				    var ti=inputs[x].selectedIndex;
				    var op=inputs[x].options;
				    if(op[ti].value == ":"){
					  $("#"+inputs[x].getAttribute("id")).remove();
				    }
			}
			
		  }
		  
		  function changeUpdatesUrl(){
			$.post("ajax/refereeplan.ajax.administration.php", $("#mainForm").serialize());
			showUpdated();
		  }
		  
		  function generateClubs(){
			var clubs=['.$clubslist.'];
			for (var i=1,len=clubs.length; i<len; i++){
				addSisterClubs(clubs[i]);		
			}
		  }
		  
		  function generateGyms(){
			$("#gyms").empty();
			document.mainForm.getGyms.value="get";
			$.ajax({type: "POST", url: "ajax/refereeplan.ajax.administration.php",dataType: "json",data: $("#mainForm").serialize() ,success: function(data){
				$.each(data, function(name,gym){
					if(gym != ""){
						$("#gyms").append("<a href=\"#\" onclick=\"javascript:removeGym(\'"+gym+"\');\"><img width=\"15px\" src=\"img/remove.png\" title=\"'.fetchText("Click to remove.").'\">"+name+"</a><br>");
					}
				});
			},error: function(xhr, status, err) {
				alert(status + ": " + err);
			}           
       
			});
			
			document.mainForm.getGyms.value="";
			
		  }
		  
		  function removeGym(gym){
		  
			document.mainForm.getGyms.value="remove";
			document.mainForm.gymName.value=gym;
			$.post("ajax/refereeplan.ajax.administration.php", $("#mainForm").serialize());
			document.mainForm.getGyms.value="";
			generateGyms();
			showUpdated();
		  
		  }
		  
		  function addGym(){
		  
			document.mainForm.getGyms.value="add";
			document.mainForm.gymName.value=$("#gymSelector :selected").text();
			$.post("ajax/refereeplan.ajax.administration.php", $("#mainForm").serialize());
			generateGyms();
			showUpdated();
			document.mainForm.getGyms.value="";
		  
		  }
		  
		  function changeGameSource(){
			document.mainForm.changeSource.value=$("#sourceSelector :selected").text();
			$.post("ajax/refereeplan.ajax.administration.php", $("#mainForm").serialize());
			document.mainForm.changeSource.value="";
			showUpdated();
		  }
		  
		  function changeLanguage(){
			document.mainForm.changeLanguageSource.value=$("#languageSelector :selected").text();
			$.post("ajax/refereeplan.ajax.administration.php", $("#mainForm").serialize());
			document.mainForm.changeLanguageSource.value="";
			showUpdated();
		  }
		  
		  $(document).ready(function() {
			generateClubs();
			generateGyms();
		  });
		  $("form").bind("keypress", function (e) {
			if (e.keyCode == 13) {
				return false;
			}
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
	<input type="hidden" name="changeSource">
	<input type="hidden" name="changeLanguageSource">
        <input type="hidden" name="addSisterClub">
        <input type="hidden" name="getGyms">
        <input type="hidden" name="gymName">';
        
  echo fetchText("Courts:","header3");
  
  echo '<div id="gyms"></div><br>';
  echo '<select id="gymSelector">
	  <option selected>'.fetchText("Choose Court").'</option>';
	  
  $allcourts = getAllCourts();
  foreach($allcourts as $court){
     echo '<option value="'.fixCharacters($court).'">'.fixCharacters($court).'</option>';
  }
  
  echo '</select><br>';
  echo '<input type="button" onclick="javascript:addGym();" value="'.fetchText("Add").'"><br><br>';
  
  echo fetchText("Game Source:","header3");
  
  echo '<select id="sourceSelector" onchange="javascript:changeGameSource();">';
  foreach(glob("refereeplan.sources.*.php") as $source){
     $sourcename = explode(".",$source);
     if($sourcename[2] == $config['gamesource']){
	  $selected = "selected";
     }else{
	  $selected = "";
     }
     echo '<option value="'.$sourcename[2].'" '.$selected.'>'.$sourcename[2].'</option>'; 
  }	
  echo '</select><br><br>';
  echo getSourceInfo();
  echo '<br><br>';
  
  echo fetchText("Language:","header3");
  
  echo '<select id="languageSelector" onchange="javascript:changeLanguage();">';
  foreach(glob("refereeplan.lang.*.php") as $lang){
     $langname = explode(".",$lang);
     if($langname[2] == $config['language']){
	  $selected = "selected";
     }else{
	  $selected = "";
     }
     echo '<option value="'.$langname[2].'" '.$selected.'>'.$langname[2].'</option>'; 
  }	
  echo '</select><br><br>';
break;
?>