<?php
namespace Repository;
use \PDO;
class UserRepository
{
    private $connection;
    
    public function __construct(PDO $connection = null)
    {
        @include "data/db-config.inc.php";
        @include "../data/db-config.inc.php";
        @include "../../data/db-config.inc.php";
        @include "../../../data/db-config.inc.php";
		
        $this->connection = $connection;
        if ($this->connection === null) {
            $this->connection = new PDO(
  //                  'mysql:host=localhost;dbname=pdo_example', 'root', 'root'
                    'mysql:host='.$db["Server"].';port=' . $db["Port"] . ';dbname='.$db["User"]., .$db["Password"]., .$db["DB_Name"].
                );
            $this->connection->setAttribute(
                PDO::ATTR_ERRMODE, 
                PDO::ERRMODE_EXCEPTION
            );
        }
    }
    public function find($id)
    {
        $stmt = $this->connection->prepare('
            SELECT "Note", notes.* 
             FROM agenda_note 
             WHERE id = :id
        ');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        // Set the fetchmode to populate an instance of 'User'
        // This enables us to use the following:
        //     $user = $repository->find(1234);
        //     echo $user->firstname;
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Note');
        return $stmt->fetch();
    }
    public function findAll()
    {
        $stmt = $this->connection->prepare('
            SELECT * FROM agenda_note
        ');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Note');
        
        // fetchAll() will do the same as above, but we'll have an array. ie:
        //    $users = $repository->findAll();
        //    echo $users[0]->firstname;
        return $stmt->fetchAll();
    }
    public function save(\User $note)
    {
        // If the ID is set, we're updating an existing record
        if (isset($note->id)) {
            return $this->update($note);
        }
        $stmt = $this->connection->prepare('
            INSERT INTO agenda_note 
                (username, firstname, lastname, email) 
            VALUES 
                (:username, :firstname , :lastname, :email)
        ');
        $stmt->bindParam(':username', $user->username);
        $stmt->bindParam(':firstname', $user->firstname);
        $stmt->bindParam(':lastname', $user->lastname);
        $stmt->bindParam(':email', $user->email);
        return $stmt->execute();
    }
    public function update(\User $user)
    {
        if (!isset($user->id)) {
            // We can't update a record unless it exists...
            throw new \LogicException(
                'Cannot update user that does not yet exist in the database.'
            );
        }
        $stmt = $this->connection->prepare('
            UPDATE users
            SET username = :username,
                firstname = :firstname,
                lastname = :lastname,
                email = :email
            WHERE id = :id
        ');
        $stmt->bindParam(':username', $user->username);
        $stmt->bindParam(':firstname', $user->firstname);
        $stmt->bindParam(':lastname', $user->lastname);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':id', $user->id);
        return $stmt->execute();
    }
}
/**
 * Class that operate on table 'agenda_note'. Database Mysql.
 *
 * @author: http://phpdao.com
 * @date: 2015-11-08 18:27
 */
class NoteMySqlDAO implements NoteDAO{

	/**
	 * Get Domain object by primary key
	 *
	 * @param String $id primary key
	 * @return AgendaNoteMySql 
	 */
	public function load($id){
		
        // Etablissement de la connexion à MySQL
        $mysql=new MySQL();
        $Connexion=$mysql->getPDO();
        // Préparation de la requête
        $sql=$Connexion->prepare("select * from agenda_note where id=:id");
        try {
            // On envoi la requête
            $sql->execute(array("id"=>$id));

			// Convert query result to an array of domain objects
			$entities = array();
			foreach ($result as $row) {
				$id = $row['user_id'];
				$entities[$id] = $this->buildDomainObject($row);
			}
			return $entities;
                

        } catch( Exception $e ){
            $Log=new Log(array(
                "traitement"=>"User->getUserDB", 
                "erreur"=>$e->getMessage(),
                "requete"=>"select * from user where id=".$id
            ));
            $Log->Save();
            return 'Erreur de requête : '.$e->getMessage();
        }

	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM agenda_note';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM agenda_note ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param agendaNote primary key
 	 */
	public function delete($id){
		$sql = 'DELETE FROM agenda_note WHERE id = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param AgendaNoteMySql agendaNote
 	 */
	public function insert($agendaNote){
		$sql = 'INSERT INTO agenda_note (title, start, end, mere_id, creat_id, creat_date, modif_id, modif_date, lieu, detail, pers_concernnees, couleur, email, contact_associe, email_contact, partage, disponibilite, rappel, rappel_coef, periodicite) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->set($agendaNote->title);
		$sqlQuery->set($agendaNote->start);
		$sqlQuery->set($agendaNote->end);
		$sqlQuery->setNumber($agendaNote->mereId);
		$sqlQuery->setNumber($agendaNote->creatId);
		$sqlQuery->set($agendaNote->creatDate);
		$sqlQuery->setNumber($agendaNote->modifId);
		$sqlQuery->set($agendaNote->modifDate);
		$sqlQuery->set($agendaNote->lieu);
		$sqlQuery->set($agendaNote->detail);
		$sqlQuery->setNumber($agendaNote->persConcernnees);
		$sqlQuery->set($agendaNote->couleur);
		$sqlQuery->setNumber($agendaNote->email);
		$sqlQuery->setNumber($agendaNote->contactAssocie);
		$sqlQuery->setNumber($agendaNote->emailContact);
		$sqlQuery->setNumber($agendaNote->partage);
		$sqlQuery->setNumber($agendaNote->disponibilite);
		$sqlQuery->setNumber($agendaNote->rappel);
		$sqlQuery->setNumber($agendaNote->rappelCoef);
		$sqlQuery->setNumber($agendaNote->periodicite);

		$id = $this->executeInsert($sqlQuery);	
		$agendaNote->id = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param AgendaNoteMySql agendaNote
 	 */
	public function update($agendaNote){
		$sql = 'UPDATE agenda_note SET title = ?, start = ?, end = ?, mere_id = ?, creat_id = ?, creat_date = ?, modif_id = ?, modif_date = ?, lieu = ?, detail = ?, pers_concernnees = ?, couleur = ?, email = ?, contact_associe = ?, email_contact = ?, partage = ?, disponibilite = ?, rappel = ?, rappel_coef = ?, periodicite = ? WHERE id = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->set($agendaNote->title);
		$sqlQuery->set($agendaNote->start);
		$sqlQuery->set($agendaNote->end);
		$sqlQuery->setNumber($agendaNote->mereId);
		$sqlQuery->setNumber($agendaNote->creatId);
		$sqlQuery->set($agendaNote->creatDate);
		$sqlQuery->setNumber($agendaNote->modifId);
		$sqlQuery->set($agendaNote->modifDate);
		$sqlQuery->set($agendaNote->lieu);
		$sqlQuery->set($agendaNote->detail);
		$sqlQuery->setNumber($agendaNote->persConcernnees);
		$sqlQuery->set($agendaNote->couleur);
		$sqlQuery->setNumber($agendaNote->email);
		$sqlQuery->setNumber($agendaNote->contactAssocie);
		$sqlQuery->setNumber($agendaNote->emailContact);
		$sqlQuery->setNumber($agendaNote->partage);
		$sqlQuery->setNumber($agendaNote->disponibilite);
		$sqlQuery->setNumber($agendaNote->rappel);
		$sqlQuery->setNumber($agendaNote->rappelCoef);
		$sqlQuery->setNumber($agendaNote->periodicite);

		$sqlQuery->setNumber($agendaNote->id);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM agenda_note';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByTitle($value){
		$sql = 'SELECT * FROM agenda_note WHERE title = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByStart($value){
		$sql = 'SELECT * FROM agenda_note WHERE start = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByEnd($value){
		$sql = 'SELECT * FROM agenda_note WHERE end = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByMereId($value){
		$sql = 'SELECT * FROM agenda_note WHERE mere_id = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByCreatId($value){
		$sql = 'SELECT * FROM agenda_note WHERE creat_id = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByCreatDate($value){
		$sql = 'SELECT * FROM agenda_note WHERE creat_date = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByModifId($value){
		$sql = 'SELECT * FROM agenda_note WHERE modif_id = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByModifDate($value){
		$sql = 'SELECT * FROM agenda_note WHERE modif_date = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByLieu($value){
		$sql = 'SELECT * FROM agenda_note WHERE lieu = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByDetail($value){
		$sql = 'SELECT * FROM agenda_note WHERE detail = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByPersConcernnees($value){
		$sql = 'SELECT * FROM agenda_note WHERE pers_concernnees = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByCouleur($value){
		$sql = 'SELECT * FROM agenda_note WHERE couleur = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByEmail($value){
		$sql = 'SELECT * FROM agenda_note WHERE email = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByContactAssocie($value){
		$sql = 'SELECT * FROM agenda_note WHERE contact_associe = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByEmailContact($value){
		$sql = 'SELECT * FROM agenda_note WHERE email_contact = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByPartage($value){
		$sql = 'SELECT * FROM agenda_note WHERE partage = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByDisponibilite($value){
		$sql = 'SELECT * FROM agenda_note WHERE disponibilite = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByRappel($value){
		$sql = 'SELECT * FROM agenda_note WHERE rappel = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByRappelCoef($value){
		$sql = 'SELECT * FROM agenda_note WHERE rappel_coef = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}

	public function queryByPeriodicite($value){
		$sql = 'SELECT * FROM agenda_note WHERE periodicite = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByTitle($value){
		$sql = 'DELETE FROM agenda_note WHERE title = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByStart($value){
		$sql = 'DELETE FROM agenda_note WHERE start = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByEnd($value){
		$sql = 'DELETE FROM agenda_note WHERE end = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByMereId($value){
		$sql = 'DELETE FROM agenda_note WHERE mere_id = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByCreatId($value){
		$sql = 'DELETE FROM agenda_note WHERE creat_id = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByCreatDate($value){
		$sql = 'DELETE FROM agenda_note WHERE creat_date = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByModifId($value){
		$sql = 'DELETE FROM agenda_note WHERE modif_id = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByModifDate($value){
		$sql = 'DELETE FROM agenda_note WHERE modif_date = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByLieu($value){
		$sql = 'DELETE FROM agenda_note WHERE lieu = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByDetail($value){
		$sql = 'DELETE FROM agenda_note WHERE detail = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByPersConcernnees($value){
		$sql = 'DELETE FROM agenda_note WHERE pers_concernnees = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByCouleur($value){
		$sql = 'DELETE FROM agenda_note WHERE couleur = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByEmail($value){
		$sql = 'DELETE FROM agenda_note WHERE email = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByContactAssocie($value){
		$sql = 'DELETE FROM agenda_note WHERE contact_associe = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByEmailContact($value){
		$sql = 'DELETE FROM agenda_note WHERE email_contact = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByPartage($value){
		$sql = 'DELETE FROM agenda_note WHERE partage = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByDisponibilite($value){
		$sql = 'DELETE FROM agenda_note WHERE disponibilite = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByRappel($value){
		$sql = 'DELETE FROM agenda_note WHERE rappel = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByRappelCoef($value){
		$sql = 'DELETE FROM agenda_note WHERE rappel_coef = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByPeriodicite($value){
		$sql = 'DELETE FROM agenda_note WHERE periodicite = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return AgendaNoteMySql 
	 */
	protected function readRow($row){
		$agendaNote = new AgendaNote();
		
		$agendaNote->id = $row['id'];
		$agendaNote->title = $row['title'];
		$agendaNote->start = $row['start'];
		$agendaNote->end = $row['end'];
		$agendaNote->mereId = $row['mere_id'];
		$agendaNote->creatId = $row['creat_id'];
		$agendaNote->creatDate = $row['creat_date'];
		$agendaNote->modifId = $row['modif_id'];
		$agendaNote->modifDate = $row['modif_date'];
		$agendaNote->lieu = $row['lieu'];
		$agendaNote->detail = $row['detail'];
		$agendaNote->persConcernnees = $row['pers_concernnees'];
		$agendaNote->couleur = $row['couleur'];
		$agendaNote->email = $row['email'];
		$agendaNote->contactAssocie = $row['contact_associe'];
		$agendaNote->emailContact = $row['email_contact'];
		$agendaNote->partage = $row['partage'];
		$agendaNote->disponibilite = $row['disponibilite'];
		$agendaNote->rappel = $row['rappel'];
		$agendaNote->rappelCoef = $row['rappel_coef'];
		$agendaNote->periodicite = $row['periodicite'];

		return $agendaNote;
	}
	
	protected function getList($sqlQuery){
		$tab = QueryExecutor::execute($sqlQuery);
		$ret = array();
		for($i=0;$i<count($tab);$i++){
			$ret[$i] = $this->readRow($tab[$i]);
		}
		return $ret;
	}
	
	/**
	 * Get row
	 *
	 * @return AgendaNoteMySql 
	 */
	protected function getRow($sqlQuery){
		$tab = QueryExecutor::execute($sqlQuery);
		if(count($tab)==0){
			return null;
		}
		return $this->readRow($tab[0]);		
	}
	
	/**
	 * Execute sql query
	 */
	protected function execute($sqlQuery){
		return QueryExecutor::execute($sqlQuery);
	}
	
		
	/**
	 * Execute sql query
	 */
	protected function executeUpdate($sqlQuery){
		return QueryExecutor::executeUpdate($sqlQuery);
	}

	/**
	 * Query for one row and one column
	 */
	protected function querySingleResult($sqlQuery){
		return QueryExecutor::queryForString($sqlQuery);
	}

	/**
	 * Insert row to table
	 */
	protected function executeInsert($sqlQuery){
		return QueryExecutor::executeInsert($sqlQuery);
	}
}
?>