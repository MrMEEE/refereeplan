$(document).ready(function(){
  
  var editDialog_buttons = {};
  
  editDialog_buttons[fetchText("Edit Club")] = function(){
    
    alert();
  }
  
  editDialog_buttons[fetchText("Cancel")] = function(){ $(this).dialog('close'); }
  
  $("#editClubPlaceHolder").dialog({
		resizable: true,
		height:130,
		width:400,
		modal: true,
		autoOpen:false,
		dialogClass: 'editDialog',
		buttons: editDialog_buttons
  });
  
  $( document ).on( "click", ".clubListElement", function() {
    
      setClubInfo($(this).closest('.clubListElement').attr('id').replace('club-',''));
      $("#editClubPlaceHolder").dialog('open');

  });
  

});


function changeGameSource(){
	$.post("ajax/refereeplan.ajax.administration.php", {'changeSource':$("#sourceSelector :selected").text()});
	showUpdated();
}

function setClubInfo(id){

	$.ajax({type: "POST", url: "ajax/refereeplan.ajax.management.php",async:true,dataType: "json",data: {'action':'getClubInfo','id':id},success: function(data){
		$('#editClubNameHolder').text(data[0].name);
                if(data[0].enabled == "1"){
                  $('#clubActive').attr('checked', true);
                }else{
		  $('#clubActive').attr('checked', false); 
		}
	}
	});
}
