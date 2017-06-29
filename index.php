<?php


include("header.php");

session_start();
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin'] == true) {
	echo "</ul>";
	showLoginForm("action.php");
	return;
}else{
	echo "<ul class=\"menu vertical\">";
	echo "<li style=\"margin-bottom:20%\"><a href=\"logout.php\">Logout</a></li>";
	echo "<li><a href=\"#maintab\">".$LANG["addtask"]."</a></li>";
	echo "<li><a href=\"#todotab\">".$LANG["tasks"]."</a></li>";
	echo "<li><a href=\"#progresstab\">".$LANG["progress"]."</a></li>";
	echo "<li><a href=\"#finishedtab\">".$LANG["finishedtasks"]."</a></li>";
	echo "<li><a href=\"#thrashtab\">".$LANG["thrash"]."</a></li>";
	echo "</ul>";
}



echo "<div id=\"maintab\" class=\"menu-content\">";
echo "<h3>".$LANG["addtask"]."</h3>";
echo "<p>";
showinputform("action.php");
echo "</p>";
echo "</div> <!-- tab div -->";


echo "<div class=\"menu-content\" id=\"todotab\">";
echo "<h3>".$LANG["todo"]."</h3>";
listtasks("open", $_SESSION['user_mng_id']);
echo "</div> <!-- tab div -->";


echo "<div class=\"menu-content\" id=\"progresstab\">";
echo "<h3>".$LANG["progress"]."</h3>";
listtasks("progress", $_SESSION['user_mng_id']);
echo "</div> <!-- tab div -->";

echo "<div class=\"menu-content\" id=\"finishedtab\">";
echo "<h3>".$LANG["finishedtasks"]."</h3>";
listtasks("closed", $_SESSION['user_mng_id']);
echo "</div> <!-- tab div -->";

echo "<div class=\"menu-content\" id=\"thrashtab\">";
echo "<h3>".$LANG["thrash"]."</h3>";
listtasks("deleted", $_SESSION['user_mng_id']);
echo "</div> <!-- tab div -->";

echo "<div class=\"menu-content\" id=\"infotab\">";
echo "<h3>".$LANG["about"]."</h3>";
echo "<p><ul><li>Desislava Asenova - 61838</li><li>Magdalena Ivanova - 61786</li></ul</p>";
echo "</div> <!-- tab div-->";


echo "</div><!--col_12 -->";

include("footer.php");
