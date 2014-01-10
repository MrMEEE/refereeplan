$(document).ready(function(){
	
	var currentGame;
	
	// Configuring the delete confirmation dialog
	$("#dialog-confirm").dialog({
		resizable: false,
		height:130,
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
                                '<a class="saveChanges" href="#">Save</a> or <a class="discardChanges" href="#">Cancel</a>'+
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
                                '<a class="saveChangesDate" href="#">Save</a> or <a class="discardChangesDate" href="#">Cancel</a>'+
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
                container.append(
                        '<div class="editGame">'+
                                '<a class="saveChangesTime" href="#">Save</a> or <a class="discardChangesTime" href="#">Cancel</a>'+
                        '</div>'
                );
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

                $('<input type="place">').val(container.text()).appendTo(container.empty());

                // Appending the save and cancel links:
                container.append(
                        '<div class="editGame">'+
                                '<a class="saveChangesPlace" href="#">Save</a> or <a class="discardChangesPlace" href="#">Cancel</a>'+
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

	$('.game').on('click','a.delete',function(){
		$("#dialog-confirm").dialog('open');
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

	
	$('.game form.refereeteam1').change(function(){
		var team = currentGame.find("#referee1Select").val();
		$.post("ajax/refereeplan.ajax.games.php",{'action':'editreferee1team','id':currentGame.data('id'),'team':team});
	});
        $('.game form.refereeteam2').change(function(){
                var team = currentGame.find("#referee2Select").val();
                $.post("ajax/refereeplan.ajax.games.php",{'action':'editreferee2team','id':currentGame.data('id'),'team':team});
        });
        $('.game form.tableteam1').change(function(){
                var team = currentGame.find("#table1Select").val();
                $.post("ajax/refereeplan.ajax.games.php",{'action':'edittable1team','id':currentGame.data('id'),'team':team});
        });
        $('.game form.tableteam2').change(function(){
                var team = currentGame.find("#table2Select").val();
                $.post("ajax/refereeplan.ajax.games.php",{'action':'edittable2team','id':currentGame.data('id'),'team':team});
        });
        $('.game form.tableteam3').change(function(){
                var team = currentGame.find("#table3Select").val();            
                $.post("ajax/refereeplan.ajax.games.php",{'action':'edittable3team','id':currentGame.data('id'),'team':team});
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
	
}); // Closing $(document).ready()