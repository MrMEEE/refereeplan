function fetchText(string){
      var newtext;
      
      $.ajax({type: "POST", url: "ajax/refereeplan.ajax.common.php",async:false,dataType: "json",data:{ action: "fetchText", string: string } ,success: function(data){
	newtext = data[0].text;
	
      }});
      
      return newtext;
  
}