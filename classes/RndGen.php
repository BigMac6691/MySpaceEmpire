<?php
class RndGen
{
	private $RND_MULTIPLER = 25214903917;
	private $RND_ADDEND = 11;
	private $RND_MAX = 4294967296; // 2^32
	
	private $seed;
	
	public function __construct($s)
	{
		$this->seed = $s;
	}
	
	// Required because PHP modulus operator does not work with unsigned INT,
	// you keep getting division by zero errors if you try.
	private function modulo($a, $b) 
	{
    	return $a - $b * floor($a / $b);
 	} 
	
	public function nextInt()
	{
		$this->seed = $this->modulo(($this->seed * $this->RND_MULTIPLER + $this->RND_ADDEND), $this->RND_MAX);
	
		return $this->seed;
	}

	public function nextFloat()
	{
		return $this->nextInt() / $this->RND_MAX;
	}
}