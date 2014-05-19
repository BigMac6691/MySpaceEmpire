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
			$stmt = $this->db->prepare("SELECT user_id, alias FROM users WHERE email = ? AND password = ? AND ".
							"effective <= current_timestamp AND expiry > current_timestamp AND update_uid IS NULL;");
			$stmt->bind_param("ss", $p["email"], $p["password"]);
		}
		elseif($sql_id == "get_user_details") 
		{
			$stmt = $this->db->prepare("SELECT * FROM users WHERE alias = ? AND effective <= current_timestamp AND expiry > current_timestamp AND update_uid IS NULL;");
			$stmt->bind_param("s", $p["validated_user"]);
		}
		elseif($sql_id == "joinable_games")
		{
			$stmt = $this->db->prepare("SELECT g.* FROM game as g LEFT OUTER JOIN players as p ON g.game_id = p.game_id AND p.user_id = ? ".
							"WHERE g.game_start < current_timestamp AND g.game_end > current_timestamp AND p.user_id IS NULL;");
			$stmt->bind_param("s", $p["validated_user_id"]);
		}
		elseif($sql_id == "joined_games")
		{
			$stmt = $this->db->prepare("SELECT g.* FROM game as g, players as p WHERE g.game_id = p.game_id AND p.user_id = ? ".
							"AND g.game_start < current_timestamp AND g.game_end > current_timestamp;");
			$stmt->bind_param("s", $p["validated_user_id"]);
		}
		elseif($sql_id == "game_stars")
		{
			$stmt = $this->db->prepare("SELECT * FROM stars WHERE game_id = ?;");
			$stmt->bind_param("i", $p["game_id"]);
		}
		elseif($sql_id == "game_wormholes")
		{
			$stmt = $this->db->prepare("SELECT * FROM wormholes WHERE game_id = ?;");
			$stmt->bind_param("i", $p["game_id"]);
		}
		elseif($sql_id == "game_planets")
		{
			$stmt = $this->db->prepare("SELECT * FROM planets WHERE game_id = ?;");
			$stmt->bind_param("i", $p["game_id"]);
		}
		elseif($sql_id == "game_civilizations")
		{
			$stmt = $this->db->prepare("SELECT * FROM civilization WHERE game_id = ?;");
			$stmt->bind_param("i", $p["game_id"]);
		}
		elseif($sql_id == "get_potential_home_planets")
		{
			$stmt = $this->db->prepare("SELECT p.* FROM planets as p, (SELECT star_id, sum(planet_owner) as taken FROM planets WHERE game_id = ? GROUP BY star_id) as p2 ".
							"WHERE p.game_id = ? AND p.star_id = p2.star_id AND p2.taken = 0;");
			$stmt->bind_param("ii", $p["game_id"], $p["game_id"]);
		}
		elseif($sql_id == "get_player_details")
		{
			$stmt = $this->db->prepare("SELECT * FROM players WHERE game_id = ? AND user_id = ?;");
			$stmt->bind_param("ii", $p["game_id"], $_SESSION["validated_user_id"]);
		}
		elseif($sql_id == "get_game_data")
		{
			$stmt = $this->db->prepare("SELECT * FROM game WHERE game_id = ?;");
			$stmt->bind_param("i", $p["game_id"]);
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
		elseif($sql_id == "joinable_games")
			$q = "SELECT * FROM game;";
		elseif($sql_id == "get_ship_type_data")
			$q = "SELECT * FROM ship_type;";
		else
			$this->handleError(11, $sql_id);
		
		$r = $this->db->query($q) or $this->handleError(10, $sql_id);
		
		return $this->processResult($r, $sql_id);
	}
}
?>