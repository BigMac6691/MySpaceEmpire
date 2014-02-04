<?php
class ServerRoot
{
	protected function trace($msg)
	{
		$dt_array = explode(" ", microtime());
		$micro = $dt_array[0] * 1000000;
		$log_msg = "[".date('d H:i:s', $dt_array[1]).".{$micro}] TRACE=>";
		
		$log_msg = $log_msg.$msg.PHP_EOL;
		
		error_log($log_msg, 3, $_SERVER['DOCUMENT_ROOT']."/../logs/log_".date('Y_m', $dt_array[1]).".log");
	}
	
	protected function traceVarDump($var, $label)
	{
		$vdump = $label.PHP_EOL;
		
		if(count($var) == 0)
			$vdump = $vdump." [Var count is zero.] Var Value=[".$var."]".PHP_EOL;
		else		
			foreach($var as $k => $v)
				$vdump = $vdump.$k."==".$v.PHP_EOL;
		
		$this->trace($vdump);
	}
}
