<?php

class BaseClass{

	
	protected $conn;
	
	private $dbhost = "localhost";
	private $dbuser = "root";
	private $dbpass = "root";
	private $dbname = "db_opus";
	
	/* constructor */
	function __construct()
	{
		$this->conn = mysql_pconnect( $this->dbhost, $this->dbuser, $this->dbpass) or die(mysql_error());
		mysql_select_db( $this->dbname, $this->conn ) or die( mysql_error() );
		
		
	}

}