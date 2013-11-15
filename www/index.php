<?php

require("connect.php");
require("refereeplan.common.functions.php");

$config = getConfiguration();

showHeader();

showNavigation();

showContent($currentState);

showFooter();

?>