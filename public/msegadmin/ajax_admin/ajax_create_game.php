<?php
require ("../../../includes/ensure_admin_logon.php");
require ("../../../classes/DBAdmin.php");
require ("../../../classes/RndGen.php");

class CreateGame
{
	private $parms, $rnd, $sector_width, $sector_stars, $wormholes, $star_names;
	private $db;

	public function __construct($names)
	{
		$this->star_names = $names;
	}

	public function processRequest()
	{
		$this->parms = json_decode($_POST['parms'], true);
		$this->rnd = new RndGen($this->parms['game_seed']);
		$this->sector_width = 1 / $this->parms['sector_size'];
		$this->wormholes = array();
		$this->sector_stars = array();

		$this->makeSectors();
		$this->nameStars();
		
		if($this->parms["connect_sectors"])
			$this->connectSectors();
		
		$this->saveData();
		
		// print_r($this->wormholes);
		// print_r($this->sector_stars);
	}

	private function assignEmptySectors($ts)
	{
		$empty_count = $this->parms["empty_sectors"];
		$empty_list = array();
		
		while($empty_count > 0)
		{
			$empty = floor($ts * $this->rnd->nextFloat());
			
			if(!in_array($empty, $empty_list))
				$empty_list[--$empty_count] = $empty;
		}
		
		return $empty_list;
	}
	
	private function makeSectors()
	{
		$total_sectors = $this->parms['sector_size'] * $this->parms['sector_size'];
		$empty = $this->assignEmptySectors($total_sectors);

		for($s = 0; $s < $total_sectors; $s++)
		{
			if(!in_array($s, $empty))
			{
				$xo = $s % $this->parms['sector_size'];
				$yo = floor($s / $this->parms['sector_size']);

				$this->sector_stars[$s] = $this->makeStars($xo, $yo);
			}
		}
	}

	private function makeStars($xo, $yo)
	{
		$star_count = $this->parms['star_density'] + floor($this->parms['star_density'] * $this->rnd->nextFloat());
		$stars = array();
		$buffer = $this->sector_width * 0.05;

		for($i = 0; $i < $star_count; )
		{
			$x = ($xo * $this->sector_width) + $this->sector_width * $this->rnd->nextFloat();
			$y = ($yo * $this->sector_width) + $this->sector_width * $this->rnd->nextFloat();

			$test = 0;

			foreach($stars as $star)
			{
				$dx = $star['star_x'] - $x;
				$dy = $star['star_y'] - $y;

				if(sqrt($dx * $dx + $dy * $dy) > $buffer)
					$test++;
			}

			if($test == 0)
				$stars[$i++] = array("star_x" => $x, "star_y" => $y);
		}

		foreach($stars as &$star)
			$star["planets"] = $this->makePlanets($star);
		
		if($this->parms["connect_stars"])
			$this->connectStars($stars);

		return $stars;
	}

	private function connectStars(&$stars)
	{
		$dist = $this->calculateDistances($stars, $stars);
		$connected = array(0);
		
		while(count($connected) < count($stars))
		{
			$star_count = count($stars);
			$min_i = -1; $min_j = -1;
			$min_dist = 2;
			
			for($i = 0; $i < count($connected); $i++)
				for($j = 0; $j < $star_count; $j++)
					if($i != $j && !in_array($j, $connected) && $dist[$i][$j] < $min_dist)
					{
						$min_i = $i;
						$min_j = $j;
						$min_dist = $dist[$i][$j];
					}
			
			$connected[] = $min_j;
			$d = 0; $wormhole_x = 0; $wormhole_y = 0;
			
			while($d < 0.75)
			{
				$wormhole_x = $this->rnd->nextFloat();
				$wormhole_y = $this->rnd->nextFloat();
				
				$d = sqrt($wormhole_x * $wormhole_x + $wormhole_y * $wormhole_y);
			}
			
			$this->wormholes[] = array("from_star" => &$stars[$min_i], "to_star" => &$stars[$min_j], "wormhole_x" => $wormhole_x, "wormhole_y" => $wormhole_y);
		}
	}
	
	private function connectSectors()
	{
		$ss = $this->parms["sector_size"];
		$sector_count = $ss * $ss;
		
		for($i = 0; $i < $sector_count; $i++)
		{
			$col = $i % $ss;
			$row = floor($i / $ss);
			$wormhole = array();
			
			if($col > 0)
				$wormhole = $this->connectFromToSector($this->sector_stars[$i], $this->sector_stars[$i - 1]);
			
			if($col < $ss - 1)
				$wormhole = $this->connectFromToSector($this->sector_stars[$i], $this->sector_stars[$i + 1]);
			
			if($row > 0)
				$wormhole = $this->connectFromToSector($this->sector_stars[$i], $this->sector_stars[$i - $ss]);
			
			if($row < $ss - 1)
				$wormhole = $this->connectFromToSector($this->sector_stars[$i], $this->sector_stars[$i + $ss]);
			
			$d = 0; $wormhole_x = 0; $wormhole_y = 0;
			
			while($d < 0.75)
			{
				$wormhole_x = $this->rnd->nextFloat();
				$wormhole_y = $this->rnd->nextFloat();
				
				$d = sqrt($wormhole_x * $wormhole_x + $wormhole_y * $wormhole_y);
			}
			
			$wormhole["wormhole_x"] = $wormhole_x;
			$wormhole["wormhole_y"] = $wormhole_y;
			$this->wormholes[] = $wormhole;
		}
	}

	private function connectFromToSector(&$from_sector, &$to_sector)
	{
		if($this->parms["connect_stars"]) // if connect stars is true then connect closest else connect random
		{
			$dist = $this->calculateDistances($from_sector, $to_sector);
			$min_dist = 2; $min_i = -1; $min_j = -1;
			
			for($i = 0; $i < count($from_sector); $i++)
				for($j = 0; $j < count($to_sector); $j++)
					if($i != $j && $dist[$i][$j] < $min_dist)
					{
						$min_dist = $dist[$i][$j];
						$min_i = $i;
						$min_j = $j;
					}
					
			return array("from_star" => &$from_sector[$min_i], "to_star" => &$to_sector[$min_j]);
		}
		else
			return array("from_star" => &$from_sector[floor(count($from_sector) * $this->rnd->nextFloat())], "to_star" => &$to_sector[floor(count($to_sector) * $this->rnd->nextFloat())]);
	}
	
	private function calculateDistances($a1, $a2)
	{
		$count1 = count($a1);
		$count2 = count($a2);
		$dist = array(array());
		
		for($i = 0; $i < $count1; $i++)
			for($j = 0; $j < $count2; $j++)
			{
				$dx = $a1[$i]["star_x"] - $a2[$j]["star_x"];
				$dy = $a1[$i]["star_y"] - $a2[$j]["star_y"];
				$dist[$i][$j] = sqrt($dx * $dx + $dy * $dy);
			}
		
		return $dist;
	}

	private function makePlanets($star)
	{
		$planet_count = round($this->parms['planet_density'] / 2) + round($this->parms['planet_density'] * $this->rnd->nextFloat());
		$planets = array();
		$range = 1 / $planet_count;
		$period = 4;
		
		for($p = 0; $p < $planet_count; $p++)
		{
			$period += round($this->rnd->nextFloat() * pow(($p + 1.5), 2) + 3);
			$planets[$p] = array("orbit_radius" => ($this->rnd->nextFloat() * 0.9 * $range + 0.1 * $range + $p * $range), 
										"orbit_period" => $period,
										"planet_seed" => $this->rnd->nextInt());
		}

		return $planets;
	}
	
	private function nameStars()
	{
		$name_count = count($this->star_names);
		
		foreach($this->sector_stars as &$stars)
			foreach($stars as &$star)
			{
				$star["star_name"] = $this->star_names[floor($name_count * $this->rnd->nextFloat())];
				$planet_count = count($star["planets"]);
				
				for($i = 0; $i < $planet_count; $i++)
					$star["planets"][$i]["planet_name"] = $star["star_name"]."-".($i + 1);
			}
	}
	
	private function saveData()
	{
		$this->db = new DBAdmin();
		
		$this->parms["game_id"] = 0;
		$this->parms["game_turn"] = 0;
		
		$game_id = $this->db->insert("create_game", $this->parms);
		
		foreach($this->sector_stars as &$stars)
			$this->db->insertStars("create_stars", $stars, $game_id["id"]);
		
		foreach($this->sector_stars as &$stars)
			foreach($stars as &$star)
				$this->db->insertPlanets("create_planets", $star["planets"], $game_id["id"], $star["star_id"]);
				
		$this->db->insertWormholes("create_wormholes", $this->wormholes, $game_id["id"]);
	}
}

require ("../../../includes/starlist.php");
$cg = new CreateGame($star_names);

$cg->processRequest();
?>