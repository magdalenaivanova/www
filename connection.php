<?php
	class DatabaseConnection {
		private $host   = "localhost";
		private $db     = "backlog_db";
		private $user   = "root";
		private $pass   = "";
		private $conn = null;
		private $validUserStmt = null;
		private $addNewTaskStmnt = null;
		private $listTasksByStatus = null;
		private $getUserByIdStmt = null;
		private $listEmployeesByManager = null;
		private $getTaskById = null;
		private $updateTaskStmnt = null;
		private $updateTaskStatusStmnt = null;

		public function __construct() {
			$this->conn = new PDO("mysql:host=$this->host;dbname=$this->db",$this->user,$this->pass);
			$this->validUserStmt = $this->conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
			$this->getUserByIdStmt = $this->conn->prepare("SELECT * FROM users WHERE user_id = ?");

			$this->addNewTaskStmnt = $this->conn->prepare("INSERT INTO tasks (task_id, task_name, priority, status, due_date, date_added, description, assignee_id, assignee_mng_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$this->listTasksByStatus = $this->conn->prepare("SELECT * FROM tasks WHERE status = ? AND assignee_mng_id = ?");
			$this->listEmployeesByManager = $this->conn->prepare("SELECT * FROM users WHERE mng_id = ?");
			$this->getTaskById = $this->conn->prepare("SELECT * FROM tasks WHERE task_id = ?");
			// public function updateTask($taskid, $taskName, $priority, $dueDate, $description, $assignee_id, $assignee_mng_id) {

			$this->updateTaskStmnt = $this->conn->prepare("UPDATE tasks SET task_name = ?,  priority = ?, due_date = ?, description = ?, assignee_id = ? WHERE task_id = ?");
			$this->updateTaskStatusStmnt = $this->conn->prepare("UPDATE tasks SET status = ? WHERE task_id = ?");

		}
		
		public function getConnection() {
			return $this->conn;
		}
		
		public function closeConnection() {
			$this->conn = null;
		}

		public function getUser($username, $password) {
			$this->validUserStmt->execute(array($username, $password));
			return $this->validUserStmt->fetch(PDO::FETCH_ASSOC);
		}

		public function getTask($taskid) {
			$this->getTaskById->execute(array($taskid));
			return $this->getTaskById->fetch(PDO::FETCH_ASSOC);
		}

		public function addNewTask($taskName, $priority, $status, $dueDate, $description, $assignee_id, $mng_id) {
			$dateAdded = date('Y-m-d');
			$this->addNewTaskStmnt->execute(array(NULL, $taskName,  $priority, 'open', $dueDate, $dateAdded, $description, $assignee_id, $mng_id));
		}

		public function getTasksByStatus($status, $mngId) {
			$this->listTasksByStatus->execute(array($status, $mngId));
			return $this->listTasksByStatus->fetchAll(PDO::FETCH_ASSOC);
		}

		public function getEmployees($mngId) {
			$this->listEmployeesByManager->execute(array($mngId));
			return $this->listEmployeesByManager->fetchAll(PDO::FETCH_ASSOC);
		}

		public function getUserById($userId) {
			$this->getUserByIdStmt->execute(array($userId));
			return $this->getUserByIdStmt->fetch(PDO::FETCH_ASSOC);
		}

		public function updateTask($taskid, $taskName, $priority, $dueDate, $description, $assignee_id, $assignee_mng_id) {
			$this->updateTaskStmnt->execute(array($taskName, $priority, $dueDate, $description, $assignee_id, $taskid));
		}

		public function deleteTask($taskid) {
			$this->updateTaskStatusStmnt->execute(array("deleted", $taskid));
		}

		public function finishTask($taskid) {
			$this->updateTaskStatusStmnt->execute(array("closed", $taskid));
		}

		public function openTask($taskid) {
			$this->updateTaskStatusStmnt->execute(array("progress", $taskid));
		}
	}
?>