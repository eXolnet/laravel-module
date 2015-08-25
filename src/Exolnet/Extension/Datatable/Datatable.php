<?php namespace Exolnet\Extension\Datatable;

class Datatable extends \Chumper\Datatable\Datatable
{
	public function query($query)
	{
		return new QueryEngine($query);
	}
}