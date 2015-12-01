<?php

namespace Modea\DAO;

use Modea\Domain\Note as Note;

class NoteDAO extends DAO
{
    private $connection; // PDO instance
    
    public function __construct(array $donnees = Null )
	{
		$this->setDb();
    }
	
	
	public function setDb(\PDO $connection = null)
	{
		require '../../data/db-config.inc.php';
 		
        if ($this->connection === null) {
            $spb="mysql:host=" . $db["Server"] . ";port=" . $db["Port"] . ";dbname=" . $db["DB_Name"];
            $this->connection = new \PDO( $spb, $db['User'], $db['Password'] );
            $this->connection->setAttribute(
                \PDO::ATTR_ERRMODE, 
                \PDO::ERRMODE_EXCEPTION
            );
        }
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
	
    public function findByUser($id)
    {
        $stmt = $this->connection->prepare('
            SELECT id, title, start, end, color, allDay, mere_id, user_id, creat_id, modif_id, lieu, detail, pers_concern, email, contact_associe, email_contact, partage, disponibilite, rappel, rappel_coef, periodicite, period_1, period_2, period_3 FROM agenda_note WHERE user_id=:user_id
        ');
        $stmt->execute(array("user_id"=>$id));
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        
        $result = $stmt->fetchAll();
        
        $cnt=count($result)-1;
       for ($i=0; $i<=$cnt; $i++) { 
          $result[$i]['start'] = preg_replace('/\s/', 'T', $result[$i]['start']);
          $result[$i]['end'] = preg_replace('/\s/', 'T', $result[$i]['end']);
          if($result[$i]['allDay'] == 0 ) unset($result[$i]['allDay']);
           

        } 
        return $result;
    }
    
    /**
     * Saves a note into the database.
     * 
     * $param \Webnotes\Domain\note $note The note to save
     */
//    public function save(Note $note) {
    public function save(Note $note) {
        $noteData = array(
            'title' => $note->getTitle(),
            'start' => $note->getStart(),
            'end' => $note->getEnd(),
            'allDay' => $note->getAllDay(),
            'mere_id' => $note->getMere_id(),
            'user_id' => $note->getUser_id(),
            'creat_id' => $note->getCreat_id(),
            'creat_date' => $note->getCreat_date(),
            'modif_id' => $note->getModif_date(),
            'lieu' => $note->getLieu(),
            'detail' => $note->getDetail(),
            'pers_concern' => $note->getPers_concern(),
            'color' => $note->getColor(),
            'email' => $note->getEmail(),
            'contact_associe' => $note->getContact_associe(),
            'email_contact' => $note->getEmail_contact(),
            'partage' => $note->getPartage(),
            'disponibilite' => $note->getDisponibilite(),
            'rappel' => $note->getRappel(),
            'rappel_coef' => $note->getRappel_coef(),
            'periodicite' => $note->getPeriodicite(),
            'period_1' => $note->getPeriod_1(),
            'period_2' => $note->getPeriod_2(),
            'period_3' => $note->getPeriod_3()
        );	
        
        if ($note->getId()) {
            // The note has already been saved : update it
            $this->connection()->update('agenda_note', $noteData, array('note_id' => $note->getId()));
        } else {
            // The note has never been saved : insert it
            $this->insert($noteData);
            // Get the id of the newly created note and set it on the entity
            $id = $this->connection->lastInsertId();
            $note->setId($id);
        }
    }
    
    /**
     * Removes a note from the database.
     * 
     * @param integer $id The note id
     */
    public function insert($noteData) {
		
		$columns = $values = "";
		foreach ($noteData as $key => $val)
		{
			$columns .= $columns == "" ? $key : ", ".$key;
			$values	.= $values == "" ? ":".$key : ", :".$key;
		}
				
		$req = "INSERT agenda_note (".$columns.") values (".$values.")";
		$stmt = $this->connection->prepare($req);
                
                $data = array();
                foreach ($noteData as $key => $val){
                    $dataKey = ':'.$key;
                    $data[$dataKey] = $val;
                }
        $stmt->execute($data);
    }
    
    /**
     * Removes a note from the database.
     * 
     * @param integer $id The note id
     */
    public function delete($id) {
        // Delete the note
        $this->getDB()->delete('t_note', array('note_id' => $id));
    }

    /**
     * Creates a Note object based on a DB row.
     *
     * @param array $row The DB row containing Note data.
     * @return \Modea\Note object
     */
    protected function buildDomainObject($row) {
        $note = new Note();
        $note->setId($row['id']);
        $note->setTitre($row['title']);
        $note->setStart($row['start']);
        $note->setEnd($row['end']);
        $note->setMere_id($row['mere_id']);
        $note->setUser_id($row['user_id']);
        $note->setCreat_id($row['creat_id']);
        $note->setCreat_date($row['creat_date']);
        $note->setModif_id($row['modif_id']);
        $note->setModif_date($row['modif_date']);
        $note->setLieu($row['lieu']);
        $note->setDetail($row['detail']);
        $note->setPers_concern($row['pers_concern']);
        $note->setColor($row['color']);
        $note->setEmail($row['email']);
        $note->setContact_associe($row['contact_associe']);
        $note->setEmail_contact($row['email_contact']);
        $note->setPartage($row['partage']);
        $note->setDisponibilite($row['disponibilite']);
        $note->setRappel($row['rappel']);
        $note->setRappel_coef($row['rappel_coef']);
        $note->setPeriodicite($row['periodicite']);
        return $note;
    }


}