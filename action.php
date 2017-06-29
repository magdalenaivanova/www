<?php

include("header.php");

session_start();

$dbConnection = new DatabaseConnection();
		
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username=htmlspecialchars($_POST['username']);
	$password=htmlspecialchars($_POST['password']);
	session_start();

	$currentUser = $dbConnection->getUser($username, $password);

	if($currentUser != null) {
		$_SESSION['loggedin'] = true;
		$_SESSION['user_id'] = $currentUser['user_id'];
		$_SESSION['user_mng_id'] = $currentUser['mng_id'];
	}
	redirect();
	return;
}

if (empty($_GET['action'])) {
	echo $LANG["noactiongiven"];
	return;
}

if (isset($_GET['action']) && $_GET['action'] == 'edit' ) {
# show edit task form
	$taskid=htmlspecialchars($_GET['id']);
	$found=0;
	echo "<h2>".$LANG["edit"]."</h2>";

	$task = $dbConnection->getTask($taskid);

	if($task == null) {
		echo $LANG["etasknotfound"];
		return;
	}

	$employees = $dbConnection->getEmployees($task['assignee_mng_id']);
	var_dump($task);
    echo "<table class=\"striped\">";
    echo "<tr>";
    echo "<th>".$LANG["task"]."</th>";
    echo "<th>".$LANG["priority"]."</th>";
    echo "<th>".$LANG["assignee"]."</th>";
    echo "<th>".$LANG["duedate"]."</th>";
    echo "<th>".$LANG[""]."</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>";
    echo "<form name=\"edit\" action=\"action.php\" method=\"GET\">";
    	echo "<input type=\"hidden\" name=\"id\" value=\"".$task['task_id']."\" ></input>";
	    echo "<input name=\"task\" type=\"text\" value=\"".$task['task_name']."\" ></input>";
	    echo "</td><td>";
	    echo "<select name=\"prio\">\n";
	        echo "<option value=\"2\">".$LANG["normal"]."</option>\n";
	        echo "<option value=\"1\">".$LANG["high"]."</option>\n";
	        echo "<option value=\"3\">".$LANG["low"]."</option>\n";
	        echo "<option value=\"4\">".$LANG["onhold"]."</option>\n";
	        echo "</select>\n";
	    echo "</td><td>";
	    echo "<select name=\"assignee_id\">\n";
	    	echo "<option value=\"\"></option>\n";
		    foreach ($employees as $user => $employee) {
	        	echo "<option value=\"".$employee['user_id']."\">".$employee['first_name']." ".$employee['last_name']."</option>\n";
	        }
	    echo "</select>\n";
	    echo "</td><td>";
	    echo "<input name=\"duedate\" type=\"text\" value=\"".$task["due_date"]."\"></input>\n";
	    echo "</td><td>";
	    echo "<input type=\"hidden\" name=\"action\" value=\"update\"></input>";
	    echo "<input type=\"hidden\" name=\"dateadded\" type=\"hidden\" value=\"".$task["dateadded"]."\"></input>\n";
	    echo "<input type=\"submit\" name=\"submit\" value=\"".$LANG["updatetask"]."\"></input>";
    echo "</form>";
    echo "</table>";
		
	return;
}

if (isset($_GET['submit']) && $_GET['action'] == 'update' && !empty($_GET['id']) && !empty($_GET['task']) && !empty($_GET['prio'])) {
#update task
	$taskid=htmlspecialchars($_GET['id']);
	$taskName=htmlspecialchars($_GET['task']);
	$dueDate=htmlspecialchars($_GET['duedate']);
	$priority=htmlspecialchars($_GET['prio']);
	$assigneeId=htmlspecialchars($_GET['assignee_id']);

	var_dump($assigneeId);
	if($assigneeId=="") {
		$assigneeId = NULL;
	}

	#Validating priority. Only 4 possibilities.
	if ($priority != "1" && $priority != "2" && $priority != "3" && $priority != "4") {
		$priority = 2;
	}
	
	$dbConnection->updateTask($taskid, $taskName, $priority, $dueDate, $description, $assigneeId, $_SESSION['user_mng_id']);
	redirect();
	return;
	
} 

if (isset($_GET['submit']) && $_GET['action'] == 'add' && !empty($_GET['task']) && !empty($_GET['prio'])) {
	#add task
	
	$taskName=htmlspecialchars($_GET['task']);
	$dueDate=htmlspecialchars($_GET['duedate']);
	$description=htmlspecialchars($_GET['description']);
	$assignee_id=htmlspecialchars($_GET['assignee_id']);
	$priority=htmlspecialchars($_GET['prio']);
	
	#Validating priority. Only 4 possibilities.
	if ($priority != "1" && $priority != "2" && $priority != "3" && $priority != "4") {
		$priority = 2;
	}
	
	// addNewTask($taskName, $priority, $status, $dueDate, $description, $assignee_id)
	$dbConnection->addNewTask($taskName, $priority, 'open', $dueDate, $description, NULL, $_SESSION['user_mng_id']);
	redirect();
	return;

}

if (isset($_GET['action']) && !empty($_GET['id'])) {
	$taskid=htmlspecialchars($_GET['id']);
	$task = $dbConnection->getTask($taskid);
	if ($task==null) {
		echo $LANG["etasknotfound"];
		echo $LANG["redirected"];
		redirect();
		return;
	}

	switch ($_GET['action']) {
		case 'progress': $dbConnection->openTask($taskid); break;
		case 'done': $dbConnection->finishTask($taskid); break;
		case 'delete': $dbConnection->deleteTask($taskid); break;
		default:
			echo "<div class=\"notice error\">".$LANG["noactiongiven"]."<div/>";
			echo "<div><a href=\"./\">Go back!</a></div>";
			break;
	}

	redirect();
	return;
}

?>
