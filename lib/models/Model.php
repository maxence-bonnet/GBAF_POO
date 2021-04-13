<?php

namespace Models;

abstract class Model
{
	protected $db;
	protected $table;

	public function __construct()
	{
		$this->db = \Database::dbConnect();
	}

	/**
	 * Récupère tous les éléments d'une table, classement optionnel 
	 */
	public function findAll(?string $orderBy = "")
	{
		$sql = "SELECT * FROM {$this->table}";
		if($orderBy) {
			$sql .= " ORDER BY " . $orderBy;
		}
		$results = $this->db->query("$sql");
		$items = $results->fetchAll();
		return $items;
	}

	/**
	 * Récupère un élément d'une table en fonction de son id_{$this->table}
	 */
	public function find(int $id)
	{
		$result = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_{$this->table} = :id");
		$result->execute(['id' => $id]);
		$item = $q->fetch();
		return $item ;
	}

	/**
	 * Supprime un élément dans une table
	 */
	public function delete(int $id)
	{
		$q = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
		$q->execute(['id' => $id]);
	}
}