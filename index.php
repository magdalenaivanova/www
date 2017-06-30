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
	echo "<li><a href=\"#trashtab\">".$LANG["trash"]."</a></li>";
	if($_SESSION['user_mng_id'] == $_SESSION['user_id']) {
		echo "<li><a href=\"action.php?action=addemployee\">Add Team Member</a></li>";
	}
	echo "</ul>";
}

# New task
echo "<div class=\"menu-content\" id=\"maintab\" >";
echo "<h3>".$LANG["addtask"]."</h3>";
echo "<p>";
showinputform("action.php");
echo "</p>";
echo "</div>";

# Open tasks
echo "<div class=\"menu-content\" id=\"todotab\">";
echo "<h3>".$LANG["todo"]."</h3>";
listtasks("open");
echo "</div>";

# In progress tasks
echo "<div class=\"menu-content\" id=\"progresstab\">";
echo "<h3>".$LANG["progress"]."</h3>";
listtasks("progress");
echo "</div>";

# Closed tasks
echo "<div class=\"menu-content\" id=\"finishedtab\">";
echo "<h3>".$LANG["finishedtasks"]."</h3>";
listtasks("closed");
echo "</div>";

# Deleted tasks
echo "<div class=\"menu-content\" id=\"trashtab\">";
echo "<h3>".$LANG["trash"]."</h3>";
listtasks("deleted");
echo "</div>";

# Information
echo "<div class=\"menu-content\" id=\"infotab\">";
echo "<h3>".$LANG["about"]."</h3>";
echo "<p><ul><li>Desislava Asenova - 61838</li><li>Magdalena Ivanova - 61786</li></ul</p>";
echo "</div>";

echo "</div><!--col_12 -->";

include("footer.php");
