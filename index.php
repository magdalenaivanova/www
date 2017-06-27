<?php


include("header.php");


echo "<ul class=\"menu vertical\">";
echo "<li><a href=\"#maintab\">".$LANG["tasks"]."</a></li>";
echo "<li><a href=\"#progresstab\">".$LANG["progress"]."</a></li>";
echo "<li><a href=\"#finishedtab\">".$LANG["finishedtasks"]."</a></li>";
echo "<li><a href=\"#thrashtab\">".$LANG["thrash"]."</a></li>";
echo "</ul>";


echo "<div id=\"maintab\" class=\"menu-content\">";
echo "<h3>".$LANG["addtask"]."</h3>";

echo "<p>";

showinputform("action.php");

echo "</p>";

echo "<p>";
echo "<h3>".$LANG["todo"]."</h3>";

listtasks($json_a,"open","table");

echo "</p>";


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

echo "</div><!--col_12 -->";

echo "<div class=\"col_6\">";

echo "<h2>".$LANG["info"] . "</h2><p>".$LANG["infotext"]."</p>";

echo "</div> <!-- col_ -->";


include("footer.php");
