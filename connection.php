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

		public function __construct() {
			$this->conn = new PDO("mysql:host=$this->host;dbname=$this->db",$this->user,$this->pass);
			$this->validUserStmt = $this->conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
			$this->getUserByIdStmt = $this->conn->prepare("SELECT * FROM users WHERE user_id = ?");

			$this->addNewTaskStmnt = $this->conn->prepare("INSERT INTO tasks (task_id, task_name, priority, status, due_date, date_added, description, assignee_id, assignee_mng_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$this->listTasksByStatus = $this->conn->prepare("SELECT * FROM tasks WHERE status = ? AND assignee_mng_id = ?");
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

		public function addNewTask($taskName, $priority, $status, $dueDate, $description, $assignee_id, $mng_id) {
			$dateAdded = date('Y-m-d');
			$this->addNewTaskStmnt->execute(array(NULL, $taskName,  $priority, 'open', $dueDate, $dateAdded, $description, $assignee_id, $mng_id));
		}

		public function getTasksByStatus($status, $mngId) {
			$this->listTasksByStatus->execute(array($status, $mngId));
			return $this->listTasksByStatus->fetchAll(PDO::FETCH_ASSOC);
		}

		public function getUserById($userId) {
			$this->getUserByIdStmt->execute(array($userId));
			return $this->getUserByIdStmt->fetch(PDO::FETCH_ASSOC);
		}

	}
?>