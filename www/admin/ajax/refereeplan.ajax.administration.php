<?php
  require("refereeplan.ajax.common.php");
  
  if(isset($_POST["changeClub"])){
    
    $clubinfo = explode(":",$_POST["clubselect"][0]);
    $clubname = $clubinfo[1];
    foreach($_POST["clubselect"] as $club){
       if($club != ":"){
          $clubinfo = explode(":",$club);
          $clubids_arr[] = $clubinfo[0];
       } 
    }
    $clubids=implode(",",$clubids_arr);
    file_put_contents ( "test" , $clubids );

    mysql_query("UPDATE `config` SET `value`='".$clubname."' WHERE `name`='clubname'");
    mysql_query("UPDATE `config` SET `value`='".$clubids."' WHERE `name`='clubids'");  

  }
?>