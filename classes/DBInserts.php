<?php
require_once("DBALRoot.php");

class DBInserts extends DBALRoot
{
	public function create($sql_id, $p)
	{
		$stmt = $this->db->stmt_init();
		
		if($sql_id == "create_user")
		{
			$stmt = $this->db->prepare("INSERT INTO users VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, null, null);");
			$stmt->bind_param("isssssssssss", 
					$p["user_id"], $p["alias"], $p["email"], $p["password"], $p["color1"], $p["color2"], $p["color3"], $p["icon"], 
					$p["effective"], $p["expiry"], $p["create_dttm"], $p["alias"]);
		}
		elseif($sql_id == "join_game")
		{
			$stmt = $this->db->prepare("INSERT INTO players VALUES(?, ?, 0, 100, current_timestamp);");
			$stmt->bind_param("id", $_SESSION["validated_user_id"], $p["game_id"]);
		}
		elseif($sql_id == "create_civilization")
		{
			$stmt = $this->db->prepare("INSERT INTO civilization VALUES(0, ?, ?, ?, ?, ?, ?);");
			$stmt->bind_param("iiiiii", $p["game_id"], $p["planet_id"], $_SESSION["validated_user_id"], $p["population"], $p["industry"], $p["shipyard"]);
		}
		else
			$this->handleError(21, $sql_id);
		
		$stmt->execute() or $this->handleError(20, $sql_id);
		$stmt->close();
		
		return array('id' => $this->db->insert_id);
	}
	
	public function update($sql_id, $p)
	{
		if($sql_id == "set_home_planet")
		{
			$stmt = $this->db->prepare("UPDATE planets SET planet_owner = ? WHERE game_id = ? AND star_id = ? AND planet_id = ?;");
			$stmt->bind_param("iiii", $_SESSION["validated_user_id"], $p["game_id"], $p["star_id"], $p["planet_id"]);
		}
		else
			$this->handleError(41, $sql_id);
		
		$stmt->execute() or $this->handleError(40, $sql_id);
		$stmt->close();	
	}
}
?>