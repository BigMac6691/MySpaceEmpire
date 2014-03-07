<?php
require_once("DBALRoot.php");

class DBInserts extends DBALRoot
{
	public function create($sql_id, $p)
	{
		$stmt = $this->db->stmt_init();
		
		if($sql_id == "create_user")
		{
			$stmt = $this->db->prepare("INSERT INTO users VALUES(?, ?, ?, ?, ?, ?,?, ?, ?,?, ?, ?, null, null);");
			$stmt->bind_param("isssssssssssss", 
					$p["user_id"], $p["alias"], $p["email"], $p["password"], $p["color1"], $p["color2"], $p["color3"], $p["icon"], 
					$p["effective"], $p["expiry"], $p["create_dttm"], $_SESSION['validated_user']);
		}
		else
			$this->handleError(21, $sql_id);
		
		$stmt->execute() or $this->handleError(20, $sql_id);
		$stmt->close();
		
		return array('id' => $this->db->insert_id);
	}
}
?>