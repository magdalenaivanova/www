<?php


include("header.php");

session_start();
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin'] == true) {
	echo "</ul>";
	showLoginForm("action.php");
	return;
}else{
	echo "<li><a href=\"logout.php\">Logout</a></li>";
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

listtasks($json_a,"open","table");

echo "</div> <!-- tab div -->";




echo "<div class=\"menu-content\" id=\"progresstab\">";
echo "<h3>".$LANG["progress"]."</h3>";

listtasks($json_a,"progress","table");

echo "</div> <!-- tab div -->";

echo "<div class=\"menu-content\" id=\"finishedtab\">";

echo "<h3>".$LANG["finishedtasks"]."</h3>";
	listtasks($json_a,"closed","table");

echo "</div> <!-- tab div -->";

echo "<div class=\"menu-content\" id=\"thrashtab\">";
echo "<h3>".$LANG["thrash"]."</h3>";

listtasks($json_a,"deleted","table");

echo "</div> <!-- tab div -->";



echo "<div class=\"menu-content\" id=\"infotab\">";
echo "<h3>".$LANG["about"]."</h3>";

echo "<p><ul><li>Desislava Asenova - 61838</li><li>Magdalena Ivanova - 61786</li></ul</p>";

echo "</div> <!-- tab div-->";

echo "</div><!--col_12 -->";

include("footer.php");
