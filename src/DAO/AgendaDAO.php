<?php

namespace Modea\DAO;

use Modea\Domain\Agenda as Agenda;

class AgendaDAO extends DAO
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
	
    public function findParam()
    {
        $stmt = $this->connection->prepare('
            SHOW FIELDS FROM agenda_user
        ');
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        
        return $stmt->fetchAll();
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
     * $param \Webnotes\Domain\note $agenda The note to save
     */

    public function saveParam($agenda) {
        $sql  = "ALTER TABLE agenda_user";
        if($agenda['debut_journee']){
            $sql .= " CHANGE debut_journee debut_journee float(10,2) NOT NULL DEFAULT '".$agenda['debut_journee']."',";
        }
        if($agenda['fin_journee']){
            $sql .= " CHANGE fin_journee fin_journee float(10,2) NOT NULL DEFAULT '".$agenda['fin_journee']."',";
        }
        if($agenda['format_nom']){
            $sql .= " CHANGE format_nom format_nom ENUM('0','1') NOT NULL DEFAULT '".$agenda['format_nom']."',";
        }
        if($agenda['planning']){
            $sql .= " CHANGE planning planning tinyint(3) unsigned DEFAULT '".$agenda['planning']."',";
        }
        if($agenda['semaine_type']){
            $sql .= " CHANGE semaine_type semaine_type varchar(7) NOT NULL DEFAULT '".$agenda['semaine_type']."',";
        }
        if($agenda['precision_planning']){
            $sql .= "CHANGE precision_planning precision_planning enum('1','2') NOT NULL DEFAULT '".$agenda['precision_planning']."',";
        }
        if($agenda['duree_note']){
            $sql .= " CHANGE util_duree_note util_duree_note enum('1','2','3','4') NOT NULL DEFAULT '".$agenda['duree_note']."',";
        }
        if($agenda['rappel_delai']){
            $sql .= " CHANGE util_rappel_delai util_rappel_delai tinyint(3) unsigned NOT NULL DEFAULT '".$agenda['rappel_delais']."',";
        }
        if($agenda['rappel_type']){
            $sql .= " CHANGE util_rappel_type util_rappel_type smallint(5) unsigned NOT NULL DEFAULT '".$agenda['rappel_type']."',";
        }
        if($agenda['rappel_email']){
            $sql .= " CHANGE util_rappel_email util_rappel_email tinyint(3) unsigned NOT NULL DEFAULT '".$agenda['rappel_email']."',";
        }
        
        $sql = substr($sql, 0, -1);
/*        if(array_key_exists('', $agenda)){
            
        }
        if(array_key_exists('', $agenda)){
            
        }
 * 
 */
        $stm = $this->connection->exec($sql);
 //       $stm->execute();
        
 /*       
        if ($agenda->getId()) {
            // The note has already been saved : update it
            $this->connection()->update('agenda_note', $agendaData, array('note_id' => $agenda->getId()));
        } else {
            // The note has never been saved : insert it
            $this->insert($agendaData);
            // Get the id of the newly created note and set it on the entity
            $id = $this->connection->lastInsertId();
            $agenda->setId($id);
        }
  * 
  */
    }
    
    /**
     * Saves a note into the database.
     * 
     * $param \Webnotes\Domain\note $agenda The note to save
     */

    public function save(Agenda $agenda) {
        $agendaData = array(
            'title' => $agenda->getTitle(),
            'start' => $agenda->getStart(),
            'end' => $agenda->getEnd(),
            'allDay' => $agenda->getAllDay(),
            'mere_id' => $agenda->getMere_id(),
            'user_id' => $agenda->getUser_id(),
            'creat_id' => $agenda->getCreat_id(),
            'creat_date' => $agenda->getCreat_date(),
            'modif_id' => $agenda->getModif_date(),
            'lieu' => $agenda->getLieu(),
            'detail' => $agenda->getDetail(),
            'pers_concern' => $agenda->getPers_concern(),
            'color' => $agenda->getColor(),
            'email' => $agenda->getEmail(),
            'contact_associe' => $agenda->getContact_associe(),
            'email_contact' => $agenda->getEmail_contact(),
            'partage' => $agenda->getPartage(),
            'disponibilite' => $agenda->getDisponibilite(),
            'rappel' => $agenda->getRappel(),
            'rappel_coef' => $agenda->getRappel_coef(),
            'periodicite' => $agenda->getPeriodicite(),
            'period_1' => $agenda->getPeriod_1(),
            'period_2' => $agenda->getPeriod_2(),
            'period_3' => $agenda->getPeriod_3()
        );	
        
        if ($agenda->getId()) {
            // The note has already been saved : update it
            $this->connection()->update('agenda_note', $agendaData, array('note_id' => $agenda->getId()));
        } else {
            // The note has never been saved : insert it
            $this->insert($agendaData);
            // Get the id of the newly created note and set it on the entity
            $id = $this->connection->lastInsertId();
            $agenda->setId($id);
        }
    }
    
    /**
     * Removes a note from the database.
     * 
     * @param integer $id The note id
     */
    public function insert($agendaData) {
		
		$columns = $values = "";
		foreach ($agendaData as $key => $val)
		{
			$columns .= $columns == "" ? $key : ", ".$key;
			$values	.= $values == "" ? ":".$key : ", :".$key;
		}
				
		$req = "INSERT agenda_note (".$columns.") values (".$values.")";
		$stmt = $this->connection->prepare($req);
                
                $data = array();
                foreach ($agendaData as $key => $val){
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
        $agenda = new Note();
        $agenda->setId($row['id']);
        $agenda->setTitre($row['title']);
        $agenda->setStart($row['start']);
        $agenda->setEnd($row['end']);
        $agenda->setMere_id($row['mere_id']);
        $agenda->setUser_id($row['user_id']);
        $agenda->setCreat_id($row['creat_id']);
        $agenda->setCreat_date($row['creat_date']);
        $agenda->setModif_id($row['modif_id']);
        $agenda->setModif_date($row['modif_date']);
        $agenda->setLieu($row['lieu']);
        $agenda->setDetail($row['detail']);
        $agenda->setPers_concern($row['pers_concern']);
        $agenda->setColor($row['color']);
        $agenda->setEmail($row['email']);
        $agenda->setContact_associe($row['contact_associe']);
        $agenda->setEmail_contact($row['email_contact']);
        $agenda->setPartage($row['partage']);
        $agenda->setDisponibilite($row['disponibilite']);
        $agenda->setRappel($row['rappel']);
        $agenda->setRappel_coef($row['rappel_coef']);
        $agenda->setPeriodicite($row['periodicite']);
        return $agenda;
    }


}