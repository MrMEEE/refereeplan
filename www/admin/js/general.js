$(document).ready(function(){
  
      $("#wrongUserPass").dialog({
		resizable: false,
		height:130,
		width:400,
		modal: true,
		autoOpen:false,
		buttons: {
			Ok: function() {
				$(this).dialog('close');
			}
		}
      });
      
      $('.loginButton').on('click',null,function(event){
	    event.preventDefault();
	    $.ajax({type: "POST", url: "ajax/refereeplan.ajax.common.php",async:false,dataType: "json",data:{ action: "logon", username: $("#username").val(), password: $("#password").val() } ,success: function(data){
		  if(data[0].status == 1){
			$("#wrongUserPass").dialog('open');
		  }else{
			location.reload();
		  }
	    }});
      });
  
});

function fetchText(string){
      var newtext;
      
      $.ajax({type: "POST", url: "ajax/refereeplan.ajax.common.php",async:false,dataType: "json",data:{ action: "fetchText", string: string } ,success: function(data){
	newtext = data[0].text;
	
      }});
      
      return newtext;
  
}