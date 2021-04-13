<?php

namespace Models;

abstract class Models
{
	protected $db;
	protected $table;

	public function __construct()
	{
		$this->$db = \Database::dbConnnet();
	}

	public function findAll()
	{

	}

	public function find()
	{

	}

	public function add()
	{

	}

	public function delete()
	{

	}

	public function a()
	{

	}



}