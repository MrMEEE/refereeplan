<?php

function showHeader(){

  $config = getConfiguration();

  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <head>
        <body bgcolor="silver">
        <title>'.$config['clubname'].' Dommerbordsplan</title>
        <link rel="stylesheet" type="text/css" href="css/general.css">
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
  
  if(mysql_num_rows(getCurrentUser()) > 0){

    $mysql_select_children = "SELECT * FROM `navigation` WHERE `parent`='".$parent."' ORDER BY `order`,`id`";
    $query = mysql_query($mysql_select_children);
    while ($child = mysql_fetch_array($query)) {
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

function showContent($state){
  
  echo '<table width=90%>
          <tr>
            <td>';
  
  if(mysql_num_rows(getCurrentUser()) == 0){
	  session_start();
	  echo fetchText("Please log in.","header2");
	  echo '<center>'.fetchText("Username").'<br><input name="username" class="password" type="text" id="username"><br><br>
		'.fetchText("Password").'<br><input name="password" class="password" type="text" id="password"><br><br>
		<input type="submit" class="loginButton" name="loginButton" value="Login"></center>';
		
	  echo '<div id="wrongUserPass" title="'.fetchText("False credentials").'"><div id="messageHolder">'.fetchText("Wrong Username or Password").'</div></div>';
	  
  }else{
  
	  $showstate = "switch(".$state."){";
	    
	    foreach (glob("refereeplan.state.*.php") as $filename){
	    
	      $showstate .= str_replace('?>','',str_replace('<?php','',file_get_contents($filename)));
	    
	    }

	  $showstate .= 'default:
	      echo "Content not found...";
	      break;
	  
	  }';
	  
	  eval($showstate);
  
  }
  
  echo '<br><br>';
  
  echo '   </td>
         </tr>
       </table>';

  echo '<script>'.$javascript.'</script>';
}

function getConfiguration(){

  $configs = mysql_query("SELECT `name`,`value` FROM `config`");
  
  while($config = mysql_fetch_assoc($configs)){
  
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
  }else{
      session_start();
      $username = stripslashes($_SESSION['rpusername']);
      $password = stripslashes($_SESSION['rppasswd']);
  }
  $username = mysql_real_escape_string($username);
  $password = mysql_real_escape_string($password);
  
  $currentuser = mysql_query("SELECT * FROM `users` WHERE `username`='".$username."' AND `passwd`='".$password."'");
  
  return $currentuser;

}

?>