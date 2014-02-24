$(document).ready(function(){
	 
	// Configuring the delete confirmation dialog
	$("#newTeamPlaceHolder").dialog({
		resizable: true,
		height:130,
		width:400,
		modal: true,
		autoOpen:false,
		dialogClass: 'newTeamDialog',
		buttons: {
			'Delete item': function() {
				
				$.post("ajax/refereeplan.ajax.games.php",{"action":"delete","id":currentGame.data('id')},function(msg){
					currentGame.fadeOut('fast');
				})
				
				$(this).dialog('close');
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	});
	
	$('.teamCreate').click(function(event){
		event.preventDefault();
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

