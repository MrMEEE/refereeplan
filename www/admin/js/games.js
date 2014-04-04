$(document).ready(function(){
	
	var currentGame;
	
	// Configuring the delete confirmation dialog
	$("#dialog-confirm").dialog({
		resizable: false,
		height:130,
		width:400,
		modal: true,
		autoOpen:false,
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
	
	$('.game .number').on('click',null,function(){
	      currentGame = $(this).closest('.game');
	      $.ajax({type: "POST", url: "ajax/refereeplan.ajax.games.php",async:true,dataType: "json",data: {'action':'getGameURL','gameid':currentGame.find('.number').text()} ,success: function(data){
		  window.open(data[0].url,'_blank');	
		},error: function(xhr, status, err) {
		  alert(status + ": " + err);
		}           
      		});
	      
	});
	
	// When a double click occurs, just simulate a click on the edit button:
	$('.game .text').on('dblclick',null,function(){

		var container = currentGame.find('.text');

                if(!currentGame.data('origText'))
                {
                        // Saving the current value of the Game so we can
                        // restore it later if the user discards the changes:

                        currentGame.data('origText',container.text());
                }
                else
                {
                        // This will block the edit button if the edit box is already open:
                        return false;
                }

                $('<input type="text">').val(container.text()).appendTo(container.empty());
                // Appending the save and cancel links:
                container.append(
			'<div class="editGame">'+
                                '<a class="saveChanges" href="#">'+fetchText("Save")+'</a> '+fetchText("or")+' <a class="discardChanges" href="#">'+fetchText("Cancel")+'</a>'+
                        '</div>'
                );
	});
	
	$('.game .date').on('dblclick',null,function(){   

                var container = currentGame.find('.date');

                if(!currentGame.data('origDate'))
                {
                        // Saving the current value of the Game so we can
                        // restore it later if the user discards the changes:

                        currentGame.data('origDate',container.text());
                }
                else
                {   
                        // This will block the edit button if the edit box is already open:
                        return false;
                }

                $('<input type="date">').val(container.text()).appendTo(container.empty());

                // Appending the save and cancel links:
                container.append(
                        '<div class="editGame">'+
                                '<a class="saveChangesDate" href="#">'+fetchText("Save")+'</a> '+fetchText("or")+' <a class="discardChangesDate" href="#">'+fetchText("Cancel")+'</a>'+
                        '</div>'
                );
        });

	$('.game .time').on('dblclick',null,function(){   

                var container = currentGame.find('.time');

                if(!currentGame.data('origTime'))
                {
                        // Saving the current value of the Game so we can
                        // restore it later if the user discards the changes:

                        currentGame.data('origTime',container.text());
                }
                else
                {   
                        // This will block the edit button if the edit box is already open:
                        return false;
                }

                $('<input type="time">').val(container.text()).appendTo(container.empty());

                // Appending the save and cancel links:
                container.append('<div class="editGame">'+'<a class="saveChangesTime" href="#">'+fetchText("Save")+'</a> '+fetchText("or")+' <a class="discardChangesTime" href="#">'+fetchText("Cancel")+'</a>'+'</div>');
        });
	
	$('.game .place').on('dblclick',null,function(){   

                var container = currentGame.find('.place');

                if(!currentGame.data('origPlace'))
                {
                        // Saving the current value of the Game so we can
                        // restore it later if the user discards the changes:

                        currentGame.data('origPlace',container.text());
                }
                else
                {   
                        // This will block the edit button if the edit box is already open:
                        return false;
                }
		var text = container.text().replace(/\n/g, ' ');
                $('<input type="place">').val(text).appendTo(container.empty());

                // Appending the save and cancel links:
                container.append(
                        '<div class="editGame">'+
                                '<a class="saveChangesPlace" href="#">'+fetchText("Save")+'</a> '+fetchText("or")+' <a class="discardChangesPlace" href="#">'+fetchText("Cancel")+'</a>'+
                        '</div>'
                );
        });

	$('.game').on('click','a',function(e){
									   
		currentGame = $(this).closest('.game');
		currentGame.data('id',currentGame.attr('id').replace('game-',''));
		
		//e.preventDefault();
	});

	$('.game').on('click','form',function(e){
		currentGame = $(this).closest('.game');
	        currentGame.data('id',currentGame.attr('id').replace('game-',''));
	               
	        //e.preventDefault();
	});

	$('.game').on('click',null,function(e){
                currentGame = $(this).closest('.game');
                currentGame.data('id',currentGame.attr('id').replace('game-',''));
                       
                //e.preventDefault();
        });  
		
	// Listening for a click on a delete button:

	$('.game .delete').on('click',null,function(event){
		event.preventDefault();
		$("#dialog-confirm").dialog('open');
	});
	
	$('.game .acknowledge').on('click',null,function(event){
		event.preventDefault();
		var id = $(this).closest('.game').attr('id').replace("game-","");
		$.post("ajax/refereeplan.ajax.games.php",{async:false,'action':'acknowledgemove','id':id}, function(){
		  setClass(id);
		  });
		$(this).closest('.game .acknowledge').text("");
		
		
	});
	
	$('.game').on('click','a.edit',function(){

		var container = currentGame.find('.text');
		
		if(!currentGame.data('origText'))
		{
			// Saving the current value of the Game so we can
			// restore it later if the user discards the changes:
			
			currentGame.data('origText',container.text());
		}
		else
		{
			// This will block the edit button if the edit box is already open:
			return false;
		}
		
		$('<input type="text">').val(container.text()).appendTo(container.empty());
		
		// Appending the save and cancel links:
		container.append(
			'<div class="editGame">'+
				'<a class="saveChanges" href="#">Save</a> or <a class="discardChanges" href="#">Cancel</a>'+
			'</div>'
		);
		
	});
	
	// The cancel edit link:
	
	$('.game').on('click','a.discardChanges',function(){
		currentGame.find('.text')
					.text(currentGame.data('origText'))
					.end()
					.removeData('origText');
	});
	
	// The save changes link:
	
	$('.game').on('click','a.saveChanges',function(){
		var text = currentGame.find("input[type=text]").val();
		
		$.post("ajax/refereeplan.ajax.games.php",{'action':'edit','id':currentGame.data('id'),'value':text,'param':'text'});
		
		currentGame.removeData('origText')
					.find(".text")
					.text(text);
	});

        // The cancel edit link:

        $('.game').on('click','a.discardChangesDate',function(){
                currentGame.find('.date')
                                        .text(currentGame.data('origDate'))
                                        .end()
                                        .removeData('origDate');
        });

        // The save changes link:

        $('.game').on('click','a.saveChangesDate',function(){
                var date = currentGame.find("input[type=date]").val();
                $.post("ajax/refereeplan.ajax.games.php",{'action':'edit','id':currentGame.data('id'),'value':date,'param':'date'});

                currentGame.removeData('origDate')    
                                        .find(".date")
                                        .text(date);  
        });

        // The cancel edit link:

        $('.game').on('click','a.discardChangesTime',function(){
                currentGame.find('.time')
                                        .text(currentGame.data('origTime'))
                                        .end()
                                        .removeData('origTime');
        });

        // The save changes link:

        $('.game').on('click','a.saveChangesTime',function(){
                var time = currentGame.find("input[type=time]").val();
                $.post("ajax/refereeplan.ajax.games.php",{'action':'edit','id':currentGame.data('id'),'value':time,'param':'time'});

                currentGame.removeData('origTime')    
                                        .find(".time")
                                        .text(time);  
        });
	
	// The cancel edit link:

        $('.game').on('click','a.discardChangesPlace',function(){
                currentGame.find('.place')
                                        .text(currentGame.data('origPlace'))
                                        .end()
                                        .removeData('origPlace');
        });

        // The save changes link:

        $('.game').on('click','a.saveChangesPlace',function(){
                var place = currentGame.find("input[type=place]").val();
                $.post("ajax/refereeplan.ajax.games.php",{'action':'edit','id':currentGame.data('id'),'value':place,'param':'place'});

                currentGame.removeData('origPlace')    
                                        .find(".place")
                                        .text(place);  
        });

	
	$('.game #referee1Select').change(function(){
		var team = currentGame.find("#referee1Select").val();
		$.post("ajax/refereeplan.ajax.games.php",{async:false,'action':'editreferee1team','id':currentGame.data('id'),'team':team}, function(){setClass(currentGame.data('id'));});
		
	});
        $('.game #referee2Select').change(function(){
                var team = currentGame.find("#referee2Select").val();
                $.post("ajax/refereeplan.ajax.games.php",{async:false,'action':'editreferee2team','id':currentGame.data('id'),'team':team}, function(){setClass(currentGame.data('id'));});
        });
        $('.game #table1Select').change(function(){
                var team = currentGame.find("#table1Select").val();
                $.post("ajax/refereeplan.ajax.games.php",{async:false,'action':'edittable1team','id':currentGame.data('id'),'team':team}, function(){setClass(currentGame.data('id'));});
        });
        $('.game #table2Select').change(function(){
                var team = currentGame.find("#table2Select").val();
                $.post("ajax/refereeplan.ajax.games.php",{async:false,'action':'edittable2team','id':currentGame.data('id'),'team':team}, function(){setClass(currentGame.data('id'));});
        });
        $('.game #table3Select').change(function(){
                var team = currentGame.find("#table3Select").val();            
                $.post("ajax/refereeplan.ajax.games.php",{async:false,'action':'edittable3team','id':currentGame.data('id'),'team':team}, function(){setClass(currentGame.data('id'));});
        });

	
	var timestamp=0;
	$('#addButton').click(function(e){

		if((new Date()).getTime() - timestamp<1000) return false;
		
		$.post("ajax/refereeplan.ajax.games.php",{'action':'new','text':'New Game Item. Doubleclick to Edit.','rand':Math.random()},function(msg){

			// Appending the new game and fading it into view:
			$(msg).hide().appendTo('.gameList').fadeIn();
		});

		// Updating the timestamp:
		timestamp = (new Date()).getTime();
		
		e.preventDefault();
	});

	$("li[id^=game-]").mouseenter(function(e){
		var gameid = $(this).attr('id').replace("game-","");
		
		$("#dutiesinfo-"+gameid).show();
	});
	
	$("li[id^=game-]").mouseleave(function(e){
		var gameid = $(this).attr('id').replace("game-","");
		
		$("#dutiesinfo-"+gameid).hide();
	});
	
}); 

function doSync(){
			$("#log").empty();
			document.mainForm.syncAction.value="getTeams";
			$.ajax({type: "POST", url: "ajax/refereeplan.ajax.games.php",async:true,dataType: "json",data: $("#mainForm").serialize() ,success: function(data){
				syncTeams(data,data.length,1);
				
			},error: function(xhr, status, err) {
				alert(status + ": " + err);
			}           
       
			});
				
}

function syncTeams(data,length,count){
  
	if (data.length > 0) {
		var syncItem = data.shift();
		
		$("#status").fadeOut( 400 );
		$("#status").empty();
		progress = 100/length*count;
		$( "#progressbar" ).progressbar({
			value: progress
		});
		
		count++;
		$("#status").append(fetchText("Syncing team: ")+syncItem.name).fadeIn( 400 );
		document.mainForm.syncTeamId.value=syncItem.id;
		document.mainForm.syncTeamUrl.value=syncItem.address;
		document.mainForm.syncAction.value="syncTeam";
		
		$.ajax({type: "POST", url: "ajax/refereeplan.ajax.games.php",async:true,dataType: "json",data:$("#mainForm").serialize() ,success: function(syncdata){
			for (var j = 0, len = syncdata.length; j < len; j++) {
				$("#log").prepend(syncdata[j].text+"<br>").fadeIn( 400 );
			}
			$("#log").prepend(syncItem.name+":<br>");
			syncTeams(data,length,count);
		}});
	}else{
	  
		$( "#progressbar" ).progressbar({
			value: 100
		});
		$("#status").empty();
		$("#status").append(fetchText("Synced all Games."));
		document.mainForm.syncAction.value="";
		$("#syncNow").prop('disabled', false);
	}
	
  
}

function setClass(id){
  
  		$.ajax({type: "POST", url: "ajax/refereeplan.ajax.games.php",async:true,dataType: "json",data: {'action':'getclass','id':id} ,success: function(data){
			$("#game-"+id).removeClass();
			$("#game-"+id).addClass(data[0].class);
		},error: function(xhr, status, err) {
		  alert(status + ": " + err);
		}           
      		});
  
}