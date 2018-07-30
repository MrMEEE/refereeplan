<?php

function showHeader(){

  $config = getConfiguration();

  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <head>
        <body bgcolor="silver">
        <title>'.$config['clubname'].' Dommerbordsplan</title>
        <link rel="stylesheet" type="text/css" href="css/general.css">
        <script type="text/javascript" src="js/sha256.js"></script>
        <script type="text/javascript" src="js/jquery-1.10.2.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.10.3.js"></script>
        <script type="text/javascript" src="js/general.js"></script>
        <link rel="stylesheet" href="css/jquery-ui-1.10.3-ui-lightness.css">
        <meta charset="utf-8">';

  global $currentState;
  $currentState = $_POST['nextState'];
  
  echo '<form id="mainForm" name="mainForm" method="post">
          <input type="hidden" name="nextState" value="'.$currentState.'">';
  echo '<table class="main" align="center" bgcolor="white" width="60%">
          <tr>
	    <td></td>
            <td>'.fetchText($config['clubname'],"header1")
            .'
            </td>
            <td align="right">
              <img height="70px" src="img/logo.jpg">
            </td>
          </tr>';

}

function showFooter(){

  echo '</td>
      </table>
      </form>
    </html>';
    
}

function showNavigation(){

  echo '<tr>
          <td colspan="3" align="center">';
  echo '<nav id="menu-wrap">';
  echo '<meta name="viewport" content="width=device-width">';

  echo '<link rel="stylesheet" type="text/css" href="css/navigation.css">';
  echo '<script type="text/javascript" src="js/navigation.js"></script>';
  echo '<ul id="menu">';
  
  showNavigationChildren(0);
  
  echo '</ul>';
  echo '</nav>';

}

function showNavigationChildren($parent){
  
  $currentUser = getCurrentUser();
  
  if(mysqli_num_rows($currentUser) > 0){
    $user = mysqli_fetch_assoc($currentUser);
    $mysqli_select_children = "SELECT * FROM `navigation` WHERE `parent`='".$parent."' ORDER BY `order`,`id`";
    $query = ref_mysql_query($mysqli_select_children);
    while ($child = mysqli_fetch_array($query)) {
        if(($user['accesslevel'] >= $child['accesslevel'])){
	  if($child["disabled"]){
  	  echo '<li><a href="#">'.fetchText($child["title"]).'</a>
  		  <ul>';
	  }else{
	      echo '<li><a href="#" onclick="javascript:changeState(\''.$child["name"].'\')">'.fetchText($child["title"]).'</a>
	        <ul>';
	  }
        
	showNavigationChildren($child["id"]);
	echo '</ul>
	      </li>';
        }
    }
  
  }

}

function showContent($state){

  $currentState = $state;
  
  echo '<table width=90%>
          <tr>
            <td>';
 
  $currentUser = getCurrentUser();
 
  if(mysqli_num_rows($currentUser) == 0){
	  session_start();
	  
          echo fetchText("Please log in.","header2");
          echo '<center>'.fetchText("Username").'<br><input name="username" class="username" type="text" id="username"><br><br>
		'.fetchText("Password").'<br><input name="password" class="password" type="password" id="password"><br><br>
		'.fetchText("Club").'<br><select name="clubSelect" class="clubSelect" id="clubSelect">
		<option value="-1">'.fetchText("Select Club").'</option>';
	  
	  $clubs = ref_mysql_query("SELECT * FROM `config` WHERE `enabled`='1'");
	  while($club = mysqli_fetch_assoc($clubs)){
	    
	     echo '<option value="'.$club["id"].'">'.$club["clubname"].'</option>';
	  
	  }
		
	  echo '</select><br><br><input type="submit" class="loginButton" name="loginButton" value="Login"></center>';
		
	  echo '<div id="wrongUserPass" title="'.fetchText("False credentials").'"><div id="messageHolder">'.fetchText("Wrong Username or Password").'</div></div>';
	  
  }else{
        $user = mysqli_fetch_assoc($currentUser);
         
        $nav = mysqli_fetch_assoc(ref_mysql_query("SELECT * FROM `navigation` WHERE `name`='".$state."'"));
        if($user['accesslevel'] >= $nav['accesslevel']){

	  $showstate = "switch(".$state."){";
	    
	    foreach (glob("refereeplan.state.*.php") as $filename){
	    
	      $showstate .= str_replace('?>','',str_replace('<?php','',file_get_contents($filename)));
	    
	    }

	  $showstate .= 'default:
	      echo "Content not found...";
	      break;
	  
	  }';
	  
	  eval($showstate);
       }else{
          echo "No Access...";
       }
  
  }
  
  echo '<br><br>';
  
  echo '   </td>
         </tr>
       </table>';

  echo '<script>'.$javascript.'</script>';
}

function getConfiguration(){
  
  $currentUser = mysqli_fetch_assoc(getCurrentUser());

  $config = mysqli_fetch_array(ref_mysql_query("SELECT * FROM `config` WHERE `id`='".$currentUser['clubid']."'"));
  
  foreach($config as $key=>$value){
  
    $configarray[$key] = $value ;
    
  }
  
  $configs = ref_mysql_query("SELECT `name`,`value` FROM `commonconfig`");
  
  while($config = mysqli_fetch_assoc($configs)){
  
    $configarray[$config["name"]] = $config["value"] ;
  
  }
  
  return $configarray;
  
}

function getLanguage(){

  $config = getConfiguration();
  
  $language = array();
  if (file_exists("refereeplan.lang.".$config['language'].".php")){
      $handle = fopen("refereeplan.lang.".$config['language'].".php", "r");
  }else{
      $handle = fopen("../refereeplan.lang.".$config['language'].".php", "r");
  }
  if ($handle) {
      while (($line = fgets($handle)) !== false) {
	  $thisline = explode("¤",$line);
	  $language[$thisline[0]] = $thisline[1];
      }
  }
  
  return $language;

}

function translateText($text){

  if(array_key_exists($text,$GLOBALS['language'])){
        return str_replace("\n","",$GLOBALS['language'][$text]);
  }else{
  	return $text;
  }

}

function fetchText($text,$type="text"){

  if(!isset($GLOBALS['language'])){
    $GLOBALS['language'] = getLanguage();
  }

  switch($type){
  
    case "header1":
    
      return "<h1>".translateText($text)."</h1>";
    
    break;

    case "header2":
        
      return "<h2>".translateText($text)."</h2>";
                  
    break;

    case "header3":
    
      return "<h3>".translateText($text)."</h3>";
      
    break;
    
    case "javascript":
    
      return stringToJava(translateText($text));
    
    break;
    
    default:

      return translateText($text);
        
    break;

  }

}

function showMessages($info,$warning,$error){

  $messages = "";

  if($info != ""){
    $messages .= '<font color="green">'.fetchText($info).'</font><br>';
  }
  
  if($warning != ""){
    $messages .= '<font color="orange">'.fetchText($warning).'</font><br>';
  }
  
  if($error != ""){  
    $messages .= '<font color="red">'.fetchText($error).'</font><br>';
  }

  return $messages;

}

function getIncludes(){

  $config = getConfiguration();
  if($config["gamesource"] != ""){
    require_once("refereeplan.sources.".$config["gamesource"].".php");
  }

}

function fixCharacters($text){

  return htmlentities($text,ENT_COMPAT,"UTF-8");

}

function stringToJava($text){

  $array = array(
    "&aring;" => "å",
    "&oslash;" => "ø",
    "&aelig" => "æ",
    "&Aring;" => "Å",
    "&Oslash;" => "Ø",
    "&AElig" => "Æ"
  );
  
  foreach ($array as $key => $value) {
      $text = str_replace($key,$value,$text);
  }
  
  return $text;

}

function getCurrentUser($scope="SESSION"){

  if($scope == "POST"){
      $username = stripslashes($_POST['username']);
      $password = stripslashes($_POST['password']);
      $club = stripslashes($_POST['club']);
  }else{
      session_start();
      $username = stripslashes($_SESSION['rpusername']);
      $password = stripslashes($_SESSION['rppasswd']);
      $club = stripslashes($_SESSION['rpclubid']);
  }
  $username = mysqli_real_escape_string($GLOBALS['link'],$username);
  $password = mysqli_real_escape_string($GLOBALS['link'],$password);
  $club = mysqli_real_escape_string($GLOBALS['link'],$club);

  $currentuser = ref_mysql_query("SELECT * FROM `users` WHERE `username`='".$username."' AND `passwd`='".$password."' AND `clubid`='".$club."'");
  
  return $currentuser;

}

function ref_mysql_query($query){

  //error_log($query);
  //error_log(mysqli_error($GLOBALS['link']));
  if((strpos($query, 'INSERT INTO') !== false) || (strpos($query, 'UPDATE') !== false) || (strpos($query, 'DELETE FROM') !== false)){
      
      $action = substr($query,0,6);
      if($action == "UPDATE"){
	    $rest = substr($query,7);
      }else{
	    $rest = substr($query,12);
      }
      
      $rest_array = explode(" ",$rest);
      
      $table = str_replace("`","",str_replace("'","",implode(' ',array_slice($rest_array, 0, 1))));      
      $parameters = str_replace("`","",str_replace("'","",implode(' ',array_slice($rest_array, 1))));
      
      $user = mysqli_fetch_assoc(getCurrentUser());
            
      $querystr = "INSERT INTO `log` (`time`,`action`,`parameters`,`table`,`userid`) VALUES (NOW(),'".$action."','".$parameters."','".$table."','".$user['id']."')";
      error_log($querystr);
      mysqli_query($GLOBALS['link'],$querystr);
  }
  
  return mysqli_query($GLOBALS['link'],$query);

}

?>
