<?php
	class DatabaseConnection {
		private $host   = "localhost";
		private $db     = "www";
		private $user   = "root";
		private $pass   = "";
		private $conn = null;
		
		public function __construct() {
			$this->conn = new PDO("mysql:host=$this->host;dbname=$this->db",$this->user,$this->pass);
		}
		
		public function getConnection() {
			return $this->conn;
		}
		
		public function closeConnection() {
			$conn = null;
		}
	}
?>