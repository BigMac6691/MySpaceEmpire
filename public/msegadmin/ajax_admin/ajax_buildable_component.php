<?php
require ("../../../includes/ensure_admin_logon.php");
require ("../../../classes/DBAdmin.php");

class BuildableComponents
{
	private $parms;
	private $db;

	public function __construct()
	{
		$this->db = new DBAdmin();
	}

	public function processRequest()
	{
		if(!isset($_POST['parms']))
			return json_encode(array('status' => 'ERROR', 'msg' => 'Parameter missing = [parms].'));
	
		if (get_magic_quotes_gpc())
			$this->parms = json_decode(stripslashes($_POST['parms']), true);
		else
			$this->parms = json_decode($_POST['parms'], true);
		
		date_default_timezone_set('America/Denver');
	
		if($this->parms['requestType'] == "getRecordDetails")
			return $this->getRecordDetails();
		else if($this->parms['requestType'] == "setRecordDetails")
			return $this->setRecordDetails();
		else if($this->parms['requestType'] == "deleteRecordDetails")
			return $this->deleteRecordDetails();
		else
			return json_encode(array('status' => 'ERROR', 'msg' => 'Unknown request type = ['.$parms['requestType'].']. '.$_POST['parms']));
	}
	
	protected function getRecordDetails()
	{
		$rows = $this->db->select("get_build_type_data", $this->parms);
	   
	   $j = json_encode(array('status' => 'OK', 'msg' => 'OK', 'resp' => $rows));

	   return $j;
	}
	
	protected function setRecordDetails()
	{
		if($this->parms['build_type_id'] == 0)
			$rows = $this->db->insert("create_component", $this->parms);
		else
			$rows = $this->db->select("get_build_type_data", $this->parms);
	   
	   $j = json_encode(array('status' => 'OK', 'msg' => 'OK', 'resp' => $rows));

	   return $j;
	}
	
	protected function deleteRecordDetails()
	{
		$j = json_encode(array('status' => 'OK', 'msg' => 'OK', 'resp' => "{test:delete}"));

	   return $j;
	}
}

$instance = new BuildableComponents();

echo $instance->processRequest();
?>