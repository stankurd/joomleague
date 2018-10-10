<?php

/**
 * inherits from line
 */
class area extends line
{
	//function area()
    public function __construct()
	{
		$this->type      = "area";
	}
	
	/**
	 * the fill colour
	 */
	//function set_fill_colour( $colour )
	public function set_fill_colour( $colour )
	{
		$this->fill = $colour;
	}
	
	/**
	 * sugar: see set_fill_colour
	 */
	//function fill_colour( $colour )
	public function fill_colour( $colour )
	{
		$this->set_fill_colour( $colour );
		return $this;
	}
	
	//function set_fill_alpha( $alpha )
	public function set_fill_alpha( $alpha )
	{
		$tmp = "fill-alpha";
		$this->$tmp = $alpha;
	}
	
	//function set_loop()
	public function set_loop()
	{
		$this->loop = true;
	}
}
