<?php
require("../../includes/ensure_user_logon.php");
require_once("../../classes/ServerRoot.php");
require("../../classes/RndGen.php");
require("../../classes/DBSelects.php");
require("../../classes/DBInserts.php");

date_default_timezone_set("America/Toronto");

class AJAX_User_Home extends ServerRoot
{
	private $ds, $di;
	private $rnd;
	
	public function __construct()
	{
		$this->ds = new DBSelects();
		$this->di = new DBInserts();
		$this->rnd = new RndGen(time());
	}
	
	public function processRequest()	
	{
		if(!isset($_POST['parms']))
			return json_encode(array('status' => 'ERROR', 'msg' => 'Parameter missing = [parms].'));
	
		$parms = json_decode($_POST['parms'], true);
	
		if($parms['requestType'] == "join_game")
			return $this->joinGame($parms);
		else
			return json_encode(array('status' => 'ERROR', 'msg' => 'Unknown request type = ['.$parms['requestType'].'].' ));
	}
	
	private function joinGame($p)
	{
		$this->di->create("join_game", $p);
		
		$planets = $this->ds->select("get_potential_home_planets", $p);
		$index = floor(count($planets) * $this->rnd->nextFloat());
		
		$this->di->update("set_home_planet", $planets[$index]);
		
		$civilization = array("game_id" => $p["game_id"], "planet_id" => $planets[$index]["planet_id"], "population" => 5000, "industry" => 100, "shipyard" => 10);
		$this->di->create("create_civilization", $civilization);
		
		return json_encode(array('status' => 'OK', 'msg' => 'Joined Game!', 'resp' => $p["game_id"]));
	} 
}

$v = new AJAX_User_Home();
	
echo $v->processRequest();
?>