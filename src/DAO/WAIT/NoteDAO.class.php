<?php
/**
 * Intreface DAO
 *
 * @author: http://phpdao.com
 * @date: 2015-11-08 18:27
 */
interface AgendaNoteDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return AgendaNote 
	 */
	public function load($id);

	/**
	 * Get all records from table
	 */
	public function queryAll();
	
	/**
	 * Get all records from table ordered by field
	 * @Param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn);
	
	/**
 	 * Delete record from table
 	 * @param agendaNote primary key
 	 */
	public function delete($id);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param AgendaNote agendaNote
 	 */
	public function insert($agendaNote);
	
	/**
 	 * Update record in table
 	 *
 	 * @param AgendaNote agendaNote
 	 */
	public function update($agendaNote);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByTitle($value);

	public function queryByStart($value);

	public function queryByEnd($value);

	public function queryByMereId($value);

	public function queryByCreatId($value);

	public function queryByCreatDate($value);

	public function queryByModifId($value);

	public function queryByModifDate($value);

	public function queryByLieu($value);

	public function queryByDetail($value);

	public function queryByPersConcernnees($value);

	public function queryByCouleur($value);

	public function queryByEmail($value);

	public function queryByContactAssocie($value);

	public function queryByEmailContact($value);

	public function queryByPartage($value);

	public function queryByDisponibilite($value);

	public function queryByRappel($value);

	public function queryByRappelCoef($value);

	public function queryByPeriodicite($value);


	public function deleteByTitle($value);

	public function deleteByStart($value);

	public function deleteByEnd($value);

	public function deleteByMereId($value);

	public function deleteByCreatId($value);

	public function deleteByCreatDate($value);

	public function deleteByModifId($value);

	public function deleteByModifDate($value);

	public function deleteByLieu($value);

	public function deleteByDetail($value);

	public function deleteByPersConcernnees($value);

	public function deleteByCouleur($value);

	public function deleteByEmail($value);

	public function deleteByContactAssocie($value);

	public function deleteByEmailContact($value);

	public function deleteByPartage($value);

	public function deleteByDisponibilite($value);

	public function deleteByRappel($value);

	public function deleteByRappelCoef($value);

	public function deleteByPeriodicite($value);


}
?>