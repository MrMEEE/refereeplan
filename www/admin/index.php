<?php

require("connect.php");
require("refereeplan.common.functions.php");

$config = getConfiguration();

$language = getLanguage();

getIncludes();

showHeader();

showNavigation();

showContent($currentState);

showFooter();

?>