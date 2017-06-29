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
#toon editformulier 
	$taskid=htmlspecialchars($_GET['id']);
	$found=0;
	echo "<h2>".$LANG["edit"]."</h2>";
	foreach ($json_a as $item => $task) {
		if ($item == $taskid) {
		$found = 1;

    echo "<table class=\"striped\">";
    echo "<tr>";
    echo "<th>".$LANG["task"]."</th>";
    echo "<th>".$LANG["priority"]."</th>";
    echo "<th>".$LANG["duedate"]."</th>";
    echo "<th>".$LANG[""]."</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>";
    echo "<form name=\"edit\" action=\"action.php\" method=\"GET\">";
    echo "<input name=\"task\" type=\"text\" value=\"".$task["task"]."\" ></input>";
    echo "</td><td>";
    echo "<select name=\"prio\">\n";
        echo "<option value=\"2\">".$LANG["normal"]."</option>\n";
        echo "<option value=\"1\">".$LANG["high"]."</option>\n";
        echo "<option value=\"3\">".$LANG["low"]."</option>\n";
        echo "<option value=\"4\">".$LANG["onhold"]."</option>\n";
        echo "</select>\n";
    echo "</td><td>";
    echo "<input name=\"duedate\" type=\"text\" value=\"".$task["duedate"]."\"></input>\n";
    echo "</td><td>";
    echo "<input type=\"hidden\" name=\"action\" value=\"update\"></input>";
    echo "<input name=\"dateadded\" type=\"hidden\" value=\"".$task["dateadded"]."\"></input>\n";
    echo "<input type=\"hidden\" name=\"id\" value=\"".  $item ."\"></input>";
    echo "<input type=\"submit\" name=\"submit\" value=\"".$LANG["updatetask"]."\"></input>";
    echo "</form>";
    echo "</table>";
		}
	}		
		
	if ($found == 0) {
		echo $LANG["etasknotfound"];
	}
	return;
}

if (isset($_GET['submit']) && $_GET['action'] == 'update' && !empty($_GET['id']) && !empty($_GET['task']) && !empty($_GET['prio'])) {
#update task
	$taskid=htmlspecialchars($_GET['id']);
	$senttask=htmlspecialchars($_GET['task']);
	$duedate=htmlspecialchars($_GET['duedate']);
	# If the due date is empty we replace it with a dash. 
	if (empty($duedate) || !preg_match('/([0-9]{2}-[0-9]{2}-[0-9]{4})/',$duedate)) {
		$duedate = "-";
	} 
	
	$priority=htmlspecialchars($_GET['prio']);

	#Validating priority. Only 4 possibilities.
	if ($priority != "1" && $priority != "2" && $priority != "3" && $priority != "4") {
		$priority = 2;
	}
	foreach ($json_a as $item => $task) {
		if ($item == $taskid) {
			$found = 1;
			$current = file_get_contents($file);
			$current = json_decode($current, TRUE);
			$json_update["tasks"]["$item"] = array("task" => $senttask, "status" => "open", "duedate" => $duedate, "dateadded" => $task["dateadded"], "priority" => $priority);
			$replaced = array_replace_recursive($current, $json_update);
			$replaced = json_encode($replaced);
			if(file_put_contents($file, $replaced, LOCK_EX)) {
				echo $LANG["taskupdated"];
				echo $LANG["redirected"];
				redirect();
			} else {
				echo $LANG["eupdatetask"];
			}
		}
	}
	if ($found==0) {
		echo $LANG["etasknotfound"];
		echo $LANG["redirected"];
		redirect();
	}
	
} elseif (isset($_GET['submit']) && $_GET['action'] == 'add' && !empty($_GET['task']) && !empty($_GET['prio'])) {
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

} elseif (isset($_GET['action']) && $_GET['action'] == 'progress' && !empty($_GET['id'])) {
	#task is done
	$taskid=htmlspecialchars($_GET['id']);
	$vandaag=date('m-d-Y');
	foreach ($json_a as $item => $task) {
		if ($item == $taskid) {
			$found = 1;
			$current = file_get_contents($file);
			$current = json_decode($current, TRUE);
			#donedate???
			$json_progress["tasks"]["$taskid"] = array("task" => $task['task'], "status" => "progress", "duedate" => $task["duedate"], "dateadded" => $task["dateadded"], "priority" => $task["priority"], "donedate" => $vandaag);
			$progress = array_replace_recursive($current, $json_progress);
			$progress = json_encode($progress);
			if(file_put_contents($file, $progress, LOCK_EX)) {
				echo $LANG["progress"];
				echo $LANG["redirected"];
				redirect();
			} else {
				echo $LANG["etasknotdone"];
				echo $LANG["redirected"];
				redirect();
			}
		}
	}
	if ($found==0) {
		echo $LANG["etasknotfound"];
		echo $LANG["redirected"];
		redirect();
	}
} elseif (isset($_GET['action']) && $_GET['action'] == 'done' && !empty($_GET['id'])) {
	#task is done
	$taskid=htmlspecialchars($_GET['id']);
	$vandaag=date('m-d-Y');
	foreach ($json_a as $item => $task) {
		if ($item == $taskid) {
			$found = 1;
			$current = file_get_contents($file);
			$current = json_decode($current, TRUE);
			$json_done["tasks"]["$taskid"] = array("task" => $task['task'], "status" => "closed", "duedate" => $task["duedate"], "dateadded" => $task["dateadded"], "priority" => $task["priority"], "donedate" => $vandaag);
			$done = array_replace_recursive($current, $json_done);
			$done = json_encode($done);
			if(file_put_contents($file, $done, LOCK_EX)) {
				echo $LANG["taskdone"];
				echo $LANG["redirected"];
				redirect();
			} else {
				echo $LANG["etasknotdone"];
				echo $LANG["redirected"];
				redirect();
			}
		}
	}
	if ($found==0) {
		echo $LANG["etasknotfound"];
		echo $LANG["redirected"];
		redirect();
	}
} elseif (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($_GET['id'])) {
#delete task
	#task is done
	$taskid=htmlspecialchars($_GET['id']);
	foreach ($json_a as $item => $task) {
		if ($item == $taskid) {
			$found = 1;
			$current = file_get_contents($file);
			$current = json_decode($current, TRUE);
			$json_delete["tasks"]["$taskid"] = array("task" => $task['task'], "status" => "deleted", "duedate" => $task["duedate"], "dateadded" => $task["dateadded"], "priority" => $task["priority"]);
			$done = array_replace_recursive($current, $json_delete);
			$done = json_encode($done);
			if(file_put_contents($file, $done, LOCK_EX)) {
				echo $LANG["taskdeleted"];
				echo $LANG["redirected"];
				redirect();
			} else {
				echo $LANG["etasknotdeleted"];
				echo $LANG["redirected"];
				redirect();
			}
		}
	}
	if ($found==0) {
		echo $LANG["etasknotdeleted"];
		echo $LANG["redirected"];
		redirect();
	}
} else {
	// echo "<meta http-equiv=\"refresh\" content=\"5/>";
	echo "<div class=\"notice error\">".$LANG["noactiongiven"]."<div/>";
	echo "<div><a href=\"./\">Go back!</a></div>";

	// redirect();
		// sleep(1);
	// echo $LANG["noactiongiven"];
}	

?>
