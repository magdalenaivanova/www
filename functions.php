<?php


$LANG=NULL;
require('language.en.php');
require('connection.php');

session_start();

$dbConnection = new DatabaseConnection();

$closed=0;
$havetasks = 0;
error_reporting(0);

function showLoginForm($actionpage) {
    echo "<form name=\"login\" action=\"action.php\" method=\"post\">";
    echo "<label><b>Username</b></label>";
    echo '<input type="text" placeholder="Enter Username" name="username" required><br/>';

    echo "<label><b>Password</b></label>";
    echo '<input type="password" placeholder="Enter Password" name="password" required><br/>';

    echo "<input type=\"submit\" name=\"submit\" value=\"Login\"></input>";
    echo"</form>";
}

function showinputform($actionpage) {
    global $LANG, $dbConnection;
    $today = date('Y-m-d');
    $employees = $dbConnection->getEmployees($_SESSION['user_mng_id']);
    echo "<table class=\"default\">";

    echo "<tr>";
    echo "<th>".$LANG["task"]."</th>";
    echo "<th>".$LANG["priority"]."</th>";
    echo "<th>".$LANG["duedate"]."</th>";
    echo "<th>".$LANG["assignee"]."</th>";
    echo "<th></th>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>";
    echo "<form name=\"edit\" action=\"action.php\" method=\"GET\">";
    echo "<input name=\"task\" size=40 type=\"text\" placeholder=\"".$LANG["tasktodo"]."\" required></input>";
    echo "</td><td>";
    echo "<select name=\"prio\">\n";
        echo "<option value=\"2\">".$LANG["normal"]."</option>\n";
        echo "<option value=\"1\">".$LANG["high"]."</option>\n";
        echo "<option value=\"3\">".$LANG["low"]."</option>\n";
        echo "<option value=\"4\">".$LANG["onhold"]."</option>\n";
        echo "</select>\n";
    echo "</td><td>";
    echo "<input name=\"duedate\" type=\"text\" id=\"datepicker\" value=\"${today}\"></input>\n";
    echo "</td><td>";
    echo "<input type=\"hidden\" name=\"action\" value=\"add\"></input>";
    echo "<input name=\"date_added\" type=\"hidden\" value=\"${today}\" required></input>\n";
        echo "<select name=\"assignee_id\">\n";
            echo "<option value=\"\"></option>\n";
            foreach ($employees as $user => $employee) {
                echo "<option value=\"".$employee['user_id']."\">".$employee['first_name']." ".$employee['last_name']."</option>\n";
            }
        echo "</select>\n";
    echo "</td><td>";
    echo "<input type=\"submit\" name=\"submit\" value=\"".$LANG["addtask"]."\"></input>";
    echo "</form>";
    echo "</table>";
}

function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ((array) $arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }
    array_multisort($sort_col, $dir, $arr);
}

# return-the-number-of-days-between-two-dates/
function dateDiff($start, $end) {
    $start_ts = strtotime($start);
    $end_ts = strtotime($end);
    $diff = $end_ts - $start_ts;
    return round($diff / 86400);
}


function listtasks($taskstatus) { //,$mngId) {
    global $LANG, $dbConnection;
    $today=date('Y-m-d');
    $havetasks = NULL;
    
    $allTasks = $dbConnection->getTasksByStatus($taskstatus, $_SESSION['user_mng_id']);
    
    echo "<table class=\"sortable striped\">";
    echo "<thead>";
    echo "<tr>";
    echo "<th>".$LANG["priority"]."</th>";
    echo "<th>".$LANG["task"]."</th>";
    echo "<th>".$LANG["daysopen"]."</th>"; 
    echo "<th>".$LANG["duedate"]."</th>";
    echo "<th>".$LANG["assignee"]."</th>";
    echo "<th>".$LANG["assigner"]."</th>";
    echo "<th>".$LANG["act"]."</th>";
    echo "</tr>";
    echo "</thead>";
     
    if(empty($allTasks) || !is_array($allTasks)) {
        echo "<tr><td colspan=7>".$LANG["notasks"]."</td></tr></table>";  
        return;
    }
    
    foreach ($allTasks as $item => $task) {
        echo "<tr>";

        # task priority
        echo "<td>";
        switch ($task["priority"]) {
            case 1:
                echo "<font color = \"red\" >".$LANG["high"]."</font>"; break;
            case 2:
                echo $LANG["normal"]; break;
            case 3:
                echo $LANG["low"]; break;
            case 4:
                echo "<font color = \"#0011ee\" >".$LANG["onhold"]."</font>"; break;
        }
        echo "</td>";

        # task name
        echo "<td>".$task['task_name']."</td>";

        # days open
        $dayopen = NULL;
        if ($taskstatus == "open") {
            $dayopen = dateDiff(str_replace('-', '/',$task["date_added"]),$today);
        } elseif ($taskstatus == "progress"){
            $dayopen = dateDiff(str_replace('-', '/',$task["date_added"]),$today);
        }elseif ($taskstatus == "closed"  || $taskstatus == "deleted" && preg_match('/([0-9]{4}-[0-9]{2}-[0-9]{2})/',$task["done_date"])) {
            $dayopen = dateDiff(str_replace('-', '/',$task["date_added"]),str_replace('-', '/',$task["done_date"]));
           
        } elseif ($taskstatus == "closed" || $taskstatus == "deleted" && !preg_match('/([0-9]{4}-[0-9]{2}-[0-9]{2})/',$task["done_date"])) {
            $dayopen = "-";
        }

        echo "<td>".$dayopen."</td>";

        # due date
        echo "<td>";
        $dayclosed = $task["due_date"];
        switch ($task["due_date"]) {
            case '-': echo "-"; break;
           
            default:
                $matches=NULL;

                if (preg_match('/([0-9]{4}-[0-9]{2}-[0-9]{2})/', $task["due_date"],$matches)) {
                    $taskduedate=$matches[0];
                    $daysclosed = dateDiff($today,str_replace('-', '/',$taskduedate));
                    

                    if ($daysclosed < 0) {
                        $daysclosed = "<u>" .abs($daysclosed) . $LANG["dayslate"] . " (".date('D d M',strtotime(str_replace('-', '/',$taskduedate))).")</u>";
                    } elseif ($daysclosed == 0) {
                        $daysclosed = "<b>".$LANG["today"]." (".date('D d M',strtotime(str_replace('-', '/',$taskduedate))).")</b>";


                    } else {
                            $daysclosed = $daysclosed . $LANG["daysleft"] ." (".date('D d M',strtotime(str_replace('-', '/',$taskduedate))).")";
                    }
                }                            

                if ($taskstatus == "closed" || $taskstatus == "deleted") {
                    echo date('D d M',strtotime(str_replace('-', '/',$taskduedate)));
                } else {
                    echo $daysclosed;
                }
                break;
        }

        echo "</td>";

        $assignee = $dbConnection->getUserById($task['assignee_id']);
        $assigner = $dbConnection->getUserById($task['creator_id']);

        # assignee
        if ($assignee == null) {
            echo "<td>no one</td>";
        } else {
            echo "<td>".$assignee['first_name'] . " " . $assignee['last_name'] ."</td>";
        }
        
        # assigner
        echo "<td>" . $assigner['first_name'] . " " . $assigner['last_name'] . "</td>";

        # actions
        echo "<td>";

        switch ($taskstatus) {
            case 'open':
                showIcon("progress", $task['task_id'], "j");
                echo "  ";
                showIcon("edit", $task['task_id'], "7");
                echo "  ";
                showIcon("delete", $task['task_id'], "T");
                break;

            case 'progress':
                showIcon("done", $task['task_id'], "C");
                echo "  ";
                showIcon("edit", $task['task_id'], "7");
                echo "  ";
                showIcon("delete", $task['task_id'], "T");
                break;

            case 'closed':
                showIcon("delete", $task['task_id'], "T");
                break;

            case 'deleted':
                echo "<span>-</span>";
                break;
        }                                        
        echo "</td>";
        echo "</tr>";
    }

    echo "</table>";
}

function redirect($page = "index.php") {
    echo "<script type=\"text/javascript\">";
    echo "window.location = \"$page\" ";
    echo "</script>";
}

function showIcon($action, $id, $icon) {
    echo "<a href=\"action.php?id=" .$id. "&action=" . $action ."\"><span class=\"icon small darkgray\" data-icon=\" " . $icon . "\"></span></a>";
}

?>