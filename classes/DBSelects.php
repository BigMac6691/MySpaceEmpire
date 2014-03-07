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
		else
			$this->handleError(31, $sql_id);
		
		$stmt->execute() or $this->handleError(30, $sql_id);
		$result = $stmt->get_result();
		$stmt->close();
		
		return $this->processResult($result, $sql_id);
	}
}
?>