$(document).ready(function(){
	 
	// Configuring the delete confirmation dialog
	
	var dialog_buttons = {}; 
	dialog_buttons[fetchText("Create Team")] = function(){
	     var closeDialog = 0;
	     if($('#newTeamName').val() == ""){
		$('#teamMessageHolder').text(fetchText("Team Name is empty")).fadeIn(500).fadeOut(500).fadeIn(500).fadeOut(500);
	     }else{
		$.ajax({type: "POST", url: "ajax/refereeplan.ajax.club.php",context: this,async:true,dataType: "json",data: {'action':'checkTeamExists','name':$('#newTeamName').val()} ,success: function(data){
			if(data[0].exists > 0){
			      $('#teamMessageHolder').text(fetchText("Team already exists")).fadeIn(500).fadeOut(500).fadeIn(500).fadeOut(500);
			}else{
			      closeDialog = 1;
			      $.ajax({type: "POST", url: "ajax/refereeplan.ajax.club.php",async:true,dataType: "json",data: {'action':'createTeam','name':$('#newTeamName').val(),'contactid':$('#newTeamContactId').val()}        });
			      $(this).dialog('close');
			}
			
		},error: function(xhr, status, err) {
		  alert(status + ": " + err);
		}           
      		});
	     }
	}
	dialog_buttons[fetchText("Cancel")] = function(){ $(this).dialog('close'); }   
	$('#instanceDialog').dialog({ buttons: dialog_buttons });
  
	$("#newTeamPlaceHolder").dialog({
		resizable: true,
		height:130,
		width:400,
		modal: true,
		autoOpen:false,
		dialogClass: 'newTeamDialog',
		buttons: dialog_buttons
	});
	
	$('.teamCreate').click(function(event){
		event.preventDefault();
		$('#newTeamName').val("");
		$('#newTeamContactId').val(0);
		$("#newTeamPlaceHolder").dialog('open');
	});
	
});

function removeTeam(teamid){
	answer = confirm(fetchText("Are you sure that you want to remove this team/league??","javascript"));
	if (answer !=0){
		document.mainForm.removeTeam.value = teamid;
		document.mainForm.submit();
	}
}

function removeAllTeams(){
	answer = confirm(fetchText("Are you sure that you want to remove all teams/leagues??","javascript"));
	if (answer !=0){
		document.mainForm.removeAllTeams.value = true;
		document.mainForm.submit();
	}
}

function addAllTeams(){
	document.mainForm.addAllTeams.value = true;
	document.mainForm.submit();
}

