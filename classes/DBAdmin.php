<?php
require_once("DBALRoot.php");

class DBAdmin extends DBALRoot
{
	public function select($sql_id, $p)
	{
		if($sql_id == "validate_admin")
		{
			$stmt = $this->db->prepare("SELECT * FROM admins WHERE admin_id = ? AND password = ?;");
			$stmt->bind_param("ss", $p["uid"], $p["password"]);
		}
		elseif($sql_id == "list_components")
		{
			$stmt = $this->db->prepare("SELECT build_type_id, name FROM build_type WHERE 1;");
			// $stmt->bind_param("ss", $p["uid"], $p["password"]);
		}
		elseif($sql_id == "get_build_type_data")
		{
			$stmt = $this->db->prepare("SELECT * FROM build_type WHERE build_type_id = ?;");
			$stmt->bind_param("i", $p["build_type_id"]);
		}
		else
			$this->handleError(31, $sql_id);
		
		$stmt->execute() or $this->handleError(30, $sql_id);
		$result = $stmt->get_result();
		$stmt->close();
		
		return $this->processResult($result, $sql_id);
	}
	
	public function insert($sql_id, $p)
	{
		if($sql_id == "create_game")
		{
			$stmt = $this->db->prepare("INSERT INTO game VALUES(?, ?, ?, ?, ?, ?);");
			$stmt->bind_param("isissi", $p['game_id'], $p['game_name'], $p['game_seed'], $p['game_start'], $p['game_end'], $p['game_turn']);
		}
		elseif($sql_id == "create_component")
		{
			$stmt = $this->db->prepare("INSERT INTO build_type VALUES(0, ?, ?);");
			$stmt->bind_param("ss", $p['name'], $p['type_attributes']);
		}
		else
			$this->handleError(21, $sql_id);
		
		$stmt->execute() or $this->handleError(20, $sql_id);
		$stmt->close();
		
		return array('id' => $this->db->insert_id);
	}
	
	public function insertStars($sql_id, &$stars, $game_id)
	{
		$stmt = $this->db->prepare("INSERT INTO stars VALUES(0, ?, ?, ?, ?);");
		
		foreach($stars as &$star)
		{
			$stmt->bind_param("sidd", $star['star_name'], $game_id, $star['star_x'], $star['star_y']);
			$stmt->execute() or $this->handleError(50, $sql_id);
			$star["star_id"] = $this->db->insert_id;
		}
		
		$stmt->close();
	}
	
	public function insertPlanets($sql_id, &$planets, $game_id, $star_id)
	{
		$stmt = $this->db->prepare("INSERT INTO planets VALUES(0, ?, ?, ?, ?, 0, ?, ?);");
		
		foreach($planets as &$planet)
		{
			$stmt->bind_param("siiidi", $planet['planet_name'], $planet['planet_seed'], $game_id, $star_id, $planet['orbit_radius'], $planet['orbit_period']);
			$stmt->execute() or $this->handleError(51, $sql_id);
			$planet["planet_id"] = $this->db->insert_id;
		}
		
		$stmt->close();
	}
	
	public function insertWormholes($sql_id, &$wormholes, $game_id)
	{
		$stmt = $this->db->prepare("INSERT INTO wormholes VALUES(0, ?, ?, ?, ?, ?);");
		
		foreach($wormholes as &$wormhole)
		{
			$stmt->bind_param("iiidd", $game_id, $wormhole['from_star']["star_id"], $wormhole['to_star']["star_id"], $wormhole["wormhole_x"], $wormhole["wormhole_y"]);
			$stmt->execute() or $this->handleError(52, $sql_id);
			$wormhole["wormhole_id"] = $this->db->insert_id;
		}
		
		$stmt->close();
	}
	
	protected function handleError($eid, $sql_id)
	{
		date_default_timezone_set('America/Toronto');
		
		$dt_array = explode(" ", microtime());
		$micro = $dt_array[0] * 1000000;
		$log_msg = "[".date('d H:i:s', $dt_array[1]).".{$micro}] DAL error id=[{$eid}], query id=[{$sql_id}], ";
		$err_msg = "";
		
		if($eid == 50)
		   $err_msg = $err_msg."Unable to create star.";
		elseif($eid == 51)
		   $err_msg = $err_msg."Unable to create planet.";
		elseif($eid == 52)
		   $err_msg = $err_msg."Unable to create wormhole.";
		else
		   parent::handleError($eid, $sql_id);
		
		$log_msg = $log_msg.$err_msg.", database error[".$this->db->error."]".PHP_EOL;
		
		error_log($log_msg, 3, $_SERVER['DOCUMENT_ROOT']."/../logs/log_".date('Y_m', $dt_array[1]).".log");
		
		exit(json_encode(array('status' => 'ERROR', 'msg' => "[".date('Y-m-d H:i:s', $dt_array[1]).".{$micro}] ".$err_msg, 'resp' => array())));
	}
}
?>