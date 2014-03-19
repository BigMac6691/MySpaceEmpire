<?php
require_once("DBALRoot.php");

class DBSelects extends DBALRoot
{
	public function select($sql_id, $p)
	{
		$stmt = $this->db->stmt_init();
		
		if($sql_id == "check_for_exisiting_alias")
		{
			$stmt = $this->db->prepare("SELECT count(*) as cnt FROM users WHERE alias = ? AND ".
							"effective <= current_timestamp AND expiry > current_timestamp AND update_uid IS NULL;");
			$stmt->bind_param("s", $p["alias"]);
		}
		elseif($sql_id == "check_for_exisiting_email") 
		{
			$stmt = $this->db->prepare("SELECT count(*) as cnt FROM users WHERE email = ? AND ".
							"effective <= current_timestamp AND expiry > current_timestamp AND update_uid IS NULL;");
			$stmt->bind_param("s", $p["email"]);
		}
		elseif($sql_id == "validate_logon") 
		{
			$stmt = $this->db->prepare("SELECT alias FROM users WHERE email = ? AND password = ? AND ".
							"effective <= current_timestamp AND expiry > current_timestamp AND update_uid IS NULL;");
			$stmt->bind_param("ss", $p["email"], $p["password"]);
		}
		else
			$this->handleError(31, $sql_id);
		
		$stmt->execute() or $this->handleError(30, $sql_id);
		$result = $stmt->get_result();
		$stmt->close();
		
		return $this->processResult($result, $sql_id);
	}
	
	public function query($sql_id)
	{
		$q = "";
		
		if($sql_id == "current_timestamp")
		   $q = "SELECT current_timestamp as db_timestamp;";
		else
			$this->handleError(11, $sql_id);
		
		$r = $this->db->query($q) or $this->handleError(10, $sql_id);
		
		return $this->processResult($r, $sql_id);
	}
}
?>