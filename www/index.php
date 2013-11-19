<?php

require("connect.php");
require("refereeplan.common.functions.php");

$config = getConfiguration();

getIncludes();

showHeader();

showNavigation();

showContent($currentState);

showFooter();

?>