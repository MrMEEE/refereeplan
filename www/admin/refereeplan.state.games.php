<?php 
case "refereeplanupdate":
    $config = getConfiguration();
    echo fetchText("Update Games","header2");
        
    mysql_query("UPDATE `config` SET `value`=now() WHERE `name`='lastupdated'");
        
    $javascript .= 'function doSync(){
			document.mainForm.syncAction.value="getTeams";
			$.ajax({type: "POST", url: "ajax/refereeplan.ajax.games.php",async:false,dataType: "json",data: $("#mainForm").serialize() ,success: function(data){
				for (var i = 0, len = data.length; i < len; i++) {
				      
				      $("#status").fadeOut( 400 );
				      $("#status").empty();
				      progress = 100/data.length*i;
				      $( "#progressbar" ).progressbar({
						value: progress
				      });
				      $("#status").append("'.fetchText("Syncing team: ").'"+data[i].name).fadeIn( 400 );
				      document.mainForm.syncTeamId.value=data[i].id;
				      document.mainForm.syncTeamUrl.value=data[i].address;
				      document.mainForm.syncAction.value="syncTeam";
				      $.ajax({type: "POST", url: "ajax/refereeplan.ajax.games.php",async:false,dataType: "json",data:$("#mainForm").serialize() ,success: function(syncdata){
					    $("#log").append(data[i].name+":<br>");
					    for (var j = 0, len = syncdata.length; j < len; j++) {
						  $("#log").append(syncdata[j].text+"<br>").fadeIn( 400 );
					    }
				      }});
				      $("#status").empty();
				      $("#status").append("'.fetchText("Synced all Games.").'");
				}
				$( "#progressbar" ).progressbar({
						value: 100
				});
				
			},error: function(xhr, status, err) {
				alert(status + ": " + err);
			}           
       
			});
			
			document.mainForm.syncAction.value="";
			
		    }';
    
    echo '<input type="submit" id="syncNow" value="'.fetchText("Syncronize").'" onclick="javascript:doSync(); this.disabled=true; return false;"><br><br>
	  <input type="hidden" name="syncAction">
	  <input type="hidden" name="syncTeamId">
	  <input type="hidden" name="syncTeamUrl">';
    
    echo '<div id="status"></div><br>';
    echo '<div id="progressbar"></div><br>';
    echo '<div id="log"></div><br>';
    
    
break;
?>
