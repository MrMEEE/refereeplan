$(document).ready(function(){
    
	// Configuring the delete confirmation dialog
	
	var dialog_buttons = {}; 
	dialog_buttons[fetchText("Create Team")] = function(){
	     if($('#newTeamName').val() == ""){
		$('#teamMessageHolder').text(fetchText("Team Name is empty")).fadeIn(500).fadeOut(500).fadeIn(500).fadeOut(500);
	     }else{
		$.ajax({type: "POST", url: "ajax/refereeplan.ajax.club.php",context: this,async:true,dataType: "json",data: {'action':'checkTeamExists','name':$('#newTeamName').val()} ,success: function(data){
			if(data[0].exists > 0){
			      $('#teamMessageHolder').text(fetchText("Team already exists")).fadeIn(500).fadeOut(500).fadeIn(500).fadeOut(500);
			}else{
			      $.ajax({type: "POST", url: "ajax/refereeplan.ajax.club.php",async:true,dataType: "json",data: {'action':'createTeam','name':$('#newTeamName').val(),'contactid':$('#newTeamContactId').val()},success: function(data){
				  $('.teamList').append('<li id="team-'+data[0].id+'"class="teamListElement"><img class="deleteTeam" width="15px" src="img/remove.png"><img class="editTeam" width="15px" src="img/edit.png"> '+$('#newTeamName').val()+'</li>');
			      }
				
			      });
			      
			      $(this).dialog('close');
			}
			
		},error: function(xhr, status, err) {
		  alert(status + ": " + err);
		}           
      		});
	     }
	}
	dialog_buttons[fetchText("Cancel")] = function(){ $(this).dialog('close'); }   

	var editTeamdialog_buttons = {};
	
	editTeamdialog_buttons[fetchText("Edit Team")] = function(){
	     if($('#editTeamName').val() == ""){
		  $('#editTeamMessageHolder').text(fetchText("Team Name is empty")).fadeIn(500).fadeOut(500).fadeIn(500).fadeOut(500);
	     }else{
		  $.ajax({type: "POST", url: "ajax/refereeplan.ajax.club.php",context: this,async:true,dataType: "json",data: {'action':'checkTeamExists','name':$('#editTeamName').val()} ,success: function(data){
			if(data[0].exists > 0 && ($('#editTeamName').val() != $('#editOrigTeamName').val())){
			      $('#editTeamMessageHolder').text(fetchText("Team already exists")).fadeIn(500).fadeOut(500).fadeIn(500).fadeOut(500);
			}else{
			      $.ajax({type: "POST", url: "ajax/refereeplan.ajax.club.php",async:true,dataType: "json",data: {'action':'editTeam','name':$('#editTeamName').val(),'contactid':$('#editTeamContactId').val(),'id':$('#editTeamId').val().replace('team-','')}});
			      $('.teamName-'+$('#editTeamId').val().replace('team-','')).text($('#editTeamName').val());
			      $(this).dialog('close');
			}
		  }
		});
		  
			      
	  
	    }
	}
	
	editTeamdialog_buttons[fetchText("Cancel")] = function(){ $(this).dialog('close'); }
	
	var removeTeamdialog_buttons = {};
	removeTeamdialog_buttons[fetchText("Remove Team")] = function(){
	      $.post("ajax/refereeplan.ajax.club.php",{'action':'removeTeam','id':$('#removeTeamId').val().replace('team-','')});
	      $("#" + $('#removeTeamId').val()).remove();
	      $(this).dialog('close');
	}
	removeTeamdialog_buttons[fetchText("Cancel")] = function(){ $(this).dialog('close'); }
	
	$("#editTeamPlaceHolder").dialog({
		resizable: true,
		height:130,
		width:400,
		modal: true,
		autoOpen:false,
		dialogClass: 'editTeamDialog',
		buttons: editTeamdialog_buttons
	});
	
	$("#removeTeamPlaceHolder").dialog({
		resizable: true,
		height:130,
		width:400,
		modal: true,
		autoOpen:false,
		dialogClass: 'removeTeamDialog',
		buttons: removeTeamdialog_buttons
	});
	
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
	
	$( document ).on( "click", ".editTeam", function() {
	    setTeamInfo($(this).closest('.teamListElement').attr('id').replace('team-',''))
	    $('#editTeamId').val($(this).closest('.teamListElement').attr('id'));
	    $("#editTeamPlaceHolder").dialog('open');
	});
	
	$( document ).on( "click", ".deleteTeam", function() {
	    $('#removeTeamId').val($(this).closest('.teamListElement').attr('id'));
	    $("#removeTeamPlaceHolder").dialog('open');
	  
	});
	
});


function setTeamInfo(id){
	
	$.ajax({type: "POST", url: "ajax/refereeplan.ajax.club.php",async:true,dataType: "json",data: {'action':'getTeamInfo','id':id},success: function(data){
		$('#editOrigTeamName').val(data[0].name);
		$('#editTeamName').val(data[0].name);
		$('#editTeamContactId').val(data[0].contactid);
	}
				
	});
  
}

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

