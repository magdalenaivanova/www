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
		if(!isset($currentUser['mng_id']) || $currentUser['mng_id'] == null) {
			$_SESSION['user_mng_id'] = $currentUser['user_id'];
		} else {
			$_SESSION['user_mng_id'] = $currentUser['mng_id'];
		}
	}
	redirect();
	return;
}

if (empty($_GET['action'])) {
	echo $LANG["noactiongiven"];
	return;
}

if (isset($_GET['action']) && $_GET['action'] == 'addemployee' ) {
	echo "<h2>".$LANG["addemployee"]."</h2>";
	echo "<div class=\"col_5\">";
	echo "<form name=\"addNewEmployee\" action=\"action.php\" method=\"GET\">";
		echo "<div class=\"group\">";
		echo "<label for=\"firstname\">First name</label>";
	    echo "<input name=\"firstname\" type=\"text\" required></input>";
		echo "</div>";
	
	    echo "<div class=\"group\">";
	    echo "<label for=\"lastname\">Last name</label>";
	    echo "<input name=\"lastname\" type=\"text\" required></input>";
		echo "</div>";
	
		echo "<div class=\"group\">";
		echo "<label for=\"username\">Username</label>";
	    echo "<input name=\"username\" type=\"text\" required></input>";
	    echo "</div>";

	    echo "<div class=\"group\">";
	    echo "<label for=\"email\">Email</label>";
	    echo "<input name=\"email\" type=\"email\" required></input>";
	    echo "</div>";
	
	    echo "<input type=\"hidden\" name=\"mng_id\" type=\"hidden\" value=\"".$_SESSION['user_mng_id']."\"></input>";
	    echo "<input type=\"hidden\" name=\"action\" value=\"submitemployee\"></input>";

	    echo "<input type=\"submit\" name=\"submit\" value=\"Add Employee\"></input>";
    echo "</form>";
	echo "</div>";

	return;
}

if (isset($_GET['submit']) && $_GET['action'] == 'submitemployee' && !empty($_GET['firstname']) && !empty($_GET['lastname']) && !empty($_GET['username']) && !empty($_GET['email'])) {
	
	$firstname=htmlspecialchars($_GET['firstname']);
	$lastname=htmlspecialchars($_GET['lastname']);
	$username=htmlspecialchars($_GET['username']);
	$email=htmlspecialchars($_GET['email']);
	$success = $dbConnection->addNewEmployee($firstname, $lastname, $username, $email, $_SESSION['user_mng_id']);
	if ($success == true) {
		echo "<div class=\"notice success\">You successfully registered new employee: ".$firstname." ".$lastname.". He or she will receive a confirmation e-mail.</div>";
		echo "<form action=\"./\"><input type=\"submit\" value=\"Back\" /></form>";
	} else {
		echo "<div class=\"notice error\">Registration of new employee failed. Check your input or contact administration.</div>";
		echo "<form action=\"./\"><input type=\"submit\" value=\"Back\" /></form>";
	}
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
	    echo "<input name=\"duedate\" type=\"text\" id=\"datepicker\" value=\"".$task["due_date"]."\"></input>\n";
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

	$assignerId=$_SESSION['user_id'];

	if($assigneeId=="") {
		$assigneeId = NULL;
		$assignerId = NULL;
	}

	#Validating priority. Only 4 possibilities.
	if ($priority != "1" && $priority != "2" && $priority != "3" && $priority != "4") {
		$priority = 2;
	}
	
	$dbConnection->addNewTask($taskName, $priority, 'open', $dueDate, $description, $assignee_id, $_SESSION['user_mng_id'], $_SESSION['user_id']);
	redirect();
	return;

}

if (isset($_GET['action']) && !empty($_GET['id']) && ($_GET['action'] == "progress" || $_GET['action'] == "done" || $_GET['action'] == "delete")) {
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

echo "<div class=\"notice error\">".$LANG["noactiongiven"]."<div/>";
echo "<div><a href=\"./\">Go back!</a></div>";
?>
