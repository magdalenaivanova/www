<?php
	class DatabaseConnection {
		private $host   = "localhost";
		private $db     = "backlog_db";
		private $user   = "root";
		private $pass   = "";
		private $conn = null;
		private $validUserStmt = null;
		private $addNewTaskStmnt = null;


		public function __construct() {
			$this->conn = new PDO("mysql:host=$this->host;dbname=$this->db",$this->user,$this->pass);
			$this->validUserStmt = $this->conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
			$this->addNewTaskStmnt = $this->conn->prepare("INSERT INTO tasks (task_id, task_name, priority, status, due_date, description, assignee_id, assignee_mng_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
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
			$this->addNewTaskStmnt->execute(array(NULL, $taskName,  $priority, 'open', $dueDate, $description, $assignee_id, $mng_id));
		}

	}
?>