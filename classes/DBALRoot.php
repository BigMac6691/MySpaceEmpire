<?php
require_once('classes/ServerRoot.php');

class DBALRoot extends ServerRoot
{
	private $db;
	
	public function __construct()
	{
		$this->db = new mysqli("localhost", "mse_player", "helloq66") or $this->handleError(1);
		$this->db->select_db("myspaceempire") or $this->handleError(2);
	}
	
	public function __destruct()
	{
		$this->db->close();
	}
	
	protected function processResult($result, $sql_id)
	{
		$rows = array();
		
		for($i = 0, $n = $result->num_rows; $i < $n; $i++)
		   array_push($rows, $result->fetch_assoc());
		
		$result->free();
		
		return $rows;
	}
	
	protected function handleError($eid, $sql_id)
	{
		date_default_timezone_set('America/Toronto');
		
		$dt_array = explode(" ", microtime());
		$micro = $dt_array[0] * 1000000;
		$log_msg = "[".date('d H:i:s', $dt_array[1]).".{$micro}] DAL error id=[{$eid}], query id=[{$sql_id}], ";
		$err_msg = "";
		
		if($eid == 1)
		   $err_msg = $err_msg."Unable to initialize database connection.";
		elseif($eid == 2)
		   $err_msg = $err_msg."Unable to select database.";
		elseif($eid == 3)
		   $err_msg = $err_msg."Missing request parameters.";
		elseif($eid == 4)
		   $err_msg = $err_msg."Fatal DAL error.";
		elseif ($eid == 10) 
		   $err_msg = $err_msg."Error processing query.";
		elseif ($eid == 11) 
		   $err_msg = $err_msg."Unknown query request.";
		elseif ($eid == 20) 
		   $err_msg = $err_msg."Error inserting data.";
		elseif ($eid == 21) 
		   $err_msg = $err_msg."Unknown insert request.";
		elseif ($eid == 30) 
		   $err_msg = $err_msg."Error selecting data.";
		elseif ($eid == 31) 
		   $err_msg = $err_msg."Unknown select request.";
		elseif ($eid == 40) 
		   $err_msg = $err_msg."Error updating data.";
		elseif ($eid == 41) 
		   $err_msg = $err_msg."Unknown update request.";
		else
		   $err_msg = $err_msg."Unknown error id.";
		
		$log_msg = $log_msg.$err_msg.", database error[".$this->db->error."]".PHP_EOL;
		
		error_log($log_msg, 3, $_SERVER['DOCUMENT_ROOT']."/../LuzchemLogs/log_".date('Y_m', $dt_array[1]).".log");
		
		exit(json_encode(array('status' => 'ERROR', 'msg' => "[".date('Y-m-d H:i:s', $dt_array[1]).".{$micro}] ".$err_msg, 'resp' => array())));
	}
}