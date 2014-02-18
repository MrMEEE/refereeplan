<?php

function showGamesLegend(){

      return '<center><table>
	    <tr>
	      <td width=300>
		<li style="color: #80FF99;list-style: square;font-size: 22px;"><font color="000000" size="2px">'.fetchText("Game with assigned table and referees").'</font></li>
	      </td>
	      <td width=300>
		<li style="color: #FFD633;list-style: square;font-size: 22px;"><font color="000000" size="2px">'.fetchText("Game wihtout assigned table and referees").'</font></li>
	      </td>
	    </tr>
	    <tr>
	      <td>
		<li style="color: #FF9980;list-style: square;font-size: 22px;"><font color="000000" size="2px">'.fetchText("Changed/Moved Game").'</font></li>
	      </td>
	      <td>
		<li style="color: #FFB1FF;list-style: square;font-size: 22px;"><font color="000000" size="2px">'.fetchText("Cancelled Game").'</font></li>
	      </td>   
	    </tr>
	    <tr>
	      <td>
		<li style="color: #ff6501;list-style: square;font-size: 22px;"><font color="000000" size="2px">'.fetchText("Postponed Game").'</font></li>
	      </td>
	      <td>
	      </td>   
	    </tr>
	    </table>
	    </center><br>';

}

function showGamesCommon(){

    $return .= '<script type="text/javascript" src="js/games.js"></script>';
    $return .= '<link rel="stylesheet" type="text/css" href="css/games.css">';
    $return .= '<div id="dialog-confirm" title="'.fetchText("Delete Game??").'">'.fetchText("Are you sure you want to delete this game??").'</div>';

    return $return;
}

?>