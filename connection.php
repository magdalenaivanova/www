<?php

	include('config.php');

	class DatabaseConnection {
		// private $user   = "root";
		// private $pass   = "";

		private $host   = "localhost";
		private $user   = null;
		private $pass   = null;

		private $db     = "backlog_db";
		private $conn = null;
		private $validUserStmt = null;
		private $addNewTaskStmnt = null;
		private $listTasksByStatus = null;
		private $getUserByIdStmt = null;
		private $listEmployeesByManager = null;
		private $getTaskById = null;
		private $updateTaskStmnt = null;
		private $updateTaskStatusStmnt = null;
		private $addNewUserStmnt = null;
		private $changePasswordStmnt = null;
		private $finishTaskStmnt = null;

		public function __construct() {
			$this->user   = $GLOBALS['DBUSER'];
			$this->pass   = $GLOBALS['DBPASSWORD'];
			$this->conn = new PDO("mysql:host=$this->host;dbname=$this->db",$this->user,$this->pass);

			$this->validUserStmt = $this->conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
			$this->getUserByIdStmt = $this->conn->prepare("SELECT * FROM users WHERE user_id = ?");

			$this->addNewTaskStmnt = $this->conn->prepare("INSERT INTO tasks (task_id, task_name, priority, status, due_date, date_added, description, assignee_id, assignee_mng_id, creator_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$this->addNewUserStmnt = $this->conn->prepare("INSERT INTO users (user_id, username, password, mng_id, email, first_name, last_name) VALUES (?, ?, ?, ?, ?, ?, ?)");

			$this->listTasksByStatus = $this->conn->prepare("SELECT * FROM tasks WHERE status = ? AND assignee_mng_id = ?");
			$this->listEmployeesByManager = $this->conn->prepare("SELECT * FROM users WHERE mng_id = ? || user_id = ?");
			$this->getTaskById = $this->conn->prepare("SELECT * FROM tasks WHERE task_id = ?");

			$this->updateTaskStmnt = $this->conn->prepare("UPDATE tasks SET task_name = ?,  priority = ?, due_date = ?, description = ?, assignee_id = ? WHERE task_id = ?");
			$this->updateTaskStatusStmnt = $this->conn->prepare("UPDATE tasks SET status = ? WHERE task_id = ?");
			$this->finishTaskStmnt = $this->conn->prepare("UPDATE tasks SET status = ?, done_date =? WHERE task_id = ?");
			$this->changePasswordStmnt = $this->conn->prepare("UPDATE users SET password = ? WHERE user_id = ? AND password = ?");

		}
		
		public function getConnection() {
			return $this->conn;
		}
		
		public function closeConnection() {
			$this->conn = null;
		}

		public function getUser($username, $password) {
			$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
			$this->validUserStmt->execute(array($username, $password));
			return $this->validUserStmt->fetch(PDO::FETCH_ASSOC);
		}

		public function getTask($taskid) {
			$this->getTaskById->execute(array($taskid));
			return $this->getTaskById->fetch(PDO::FETCH_ASSOC);
		}

		public function addNewTask($taskName, $priority, $status, $dueDate, $description, $assignee_id, $mng_id, $creator_id) {
			$dateAdded = date('Y-m-d');
			$this->addNewTaskStmnt->execute(array(NULL, $taskName,  $priority, 'open', $dueDate, $dateAdded, $description, $assignee_id, $mng_id, $creator_id));
		}

		public function getTasksByStatus($status, $mngId) {
			$this->listTasksByStatus->execute(array($status, $mngId));
			return $this->listTasksByStatus->fetchAll(PDO::FETCH_ASSOC);
		}

		public function getEmployees($mngId) {
			$this->listEmployeesByManager->execute(array($mngId, $mngId));
			return $this->listEmployeesByManager->fetchAll(PDO::FETCH_ASSOC);
		}

		public function getUserById($userId) {
			$this->getUserByIdStmt->execute(array($userId));
			return $this->getUserByIdStmt->fetch(PDO::FETCH_ASSOC);
		}

		public function updateTask($taskid, $taskName, $priority, $dueDate, $description, $assignee_id, $assignee_mng_id) {
			//$dbConnection->updateTask($taskid, $taskName, $priority, $dueDate, $description, $assigneeId, $_SESSION['user_mng_id']);
			$this->updateTaskStmnt->execute(array($taskName, $priority, $dueDate, $description, $assignee_id, $taskid));
		}

		public function deleteTask($taskid) {
			$today=date('Y-m-d');
			$this->finishTaskStmnt->execute(array("deleted", $today, $taskid));
		}

		public function finishTask($taskid) {
			$today=date('Y-m-d');
			$this->finishTaskStmnt->execute(array("closed", $today, $taskid));
		}

		public function openTask($taskid) {
			$this->updateTaskStatusStmnt->execute(array("progress", $taskid));
		}

		public function addNewEmployee($firstname, $lastname, $username, $email, $mng_id) {
			// (user_id, username, password, mng_id, email, first_name, last_name)
			$password = "qwerty";
			//$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
			return $this->addNewUserStmnt->execute(array(NULL, $username, $password, $mng_id, $email, $firstname, $lastname));
		}

		public function changeUserPassword($user_id, $oldPassword, $newPassword) {
			return $this->changePasswordStmnt->execute(array($newPassword, $user_id, $oldPassword));
		}

	}
?>