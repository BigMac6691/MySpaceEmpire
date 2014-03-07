<?php
require_once("../../classes/ServerRoot.php");
require("../../classes/DBSelects.php");

class AJAX_Validate_Account extends ServerRoot
{
	private $dal;
	
	public function __construct()
	{
		$this->dal = new DBSelects();
	} 
	
	public function processRequest()	
	{
		if(!isset($_POST['parms']))
			return json_encode(array('status' => 'ERROR', 'msg' => 'Parameter missing = [parms].'));
	
		$parms = json_decode($_POST['parms'], true);
	
		date_default_timezone_set('America/Toronto');
	
		if($parms['requestType'] == "check_alias")
			return $this->checkAlias($parms);
		else if($parms['requestType'] == "check_email")
			return $this->checkEmail($parms);
		else
			return json_encode(array('status' => 'ERROR', 'msg' => 'Unknown request type = ['.$parms['requestType'].'].' ));
	}
	
	function checkAlias($p)
	{
		$result = $this->dal->select("check_for_exisiting_alias", $p);
		
		return json_encode(array('status' => 'OK', 'msg' => 'alias', 'resp' => $result));
	}
	
	function checkEmail($p)
	{
		$result = $this->dal->select("check_for_exisiting_email", $p);
		
		return json_encode(array('status' => 'OK', 'msg' => 'email', 'resp' => $result));
	}
}

$v = new AJAX_Validate_Account();
	
echo $v->processRequest();
?>