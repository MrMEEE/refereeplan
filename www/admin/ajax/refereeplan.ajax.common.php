<?php

require("../connect.php");
require("../refereeplan.common.functions.php");
getIncludes();

switch($_POST['action']){

    case "fetchText":
	  
	  $string = fetchText($_POST['string'],"javascript");
	  
	  $json = '[ { "text": "'.$string.'" } ]';
	  
	  echo $json;
    
    break;

}

?>