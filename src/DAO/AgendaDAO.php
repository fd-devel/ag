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
        require DIR_DB_CONFIG;

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
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Agenda');
        $result = $stmt->fetchAll();

        return $result;
    }
	
    public function findByUser($id)
    {
        $stmt = $this->connection->prepare('
            SELECT id_user, debut_journee, fin_journee, format_nom, planning,
                semaine_type, precision_planning, duree_note, rappel_delai, rappel_type,
                rappel_email, partage_planning, autorise_affect, alert_affect, couleur 
            FROM agenda_user 
            WHERE id_user=:id_user
        ');
        $stmt->execute(array("id_user"=>$id));
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $tmp = $stmt->fetchAll();
        $result = $this->buildDomainObject($tmp[0]);
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
            $sql .= " CHANGE format_nom format_nom ENUM('0','1') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '".$agenda['format_nom']."',";
        }
        if($agenda['planning']){
            $sql .= " CHANGE planning planning enum('1','2','3') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '".$agenda['planning']."',";
        }
        if($agenda['semaine_type']){
            $sql .= " CHANGE semaine_type semaine_type varchar(7) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '".$agenda['semaine_type']."',";
        }
        if($agenda['precision_planning']){
            $sql .= "CHANGE precision_planning precision_planning enum('1','2') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '".$agenda['precision_planning']."',";
        }
        if($agenda['duree_note']){
            $sql .= " CHANGE duree_note duree_note enum('1','2','3','4') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '".$agenda['duree_note']."',";
        }
        if($agenda['rappel_delai'] || 1){
            $sql .= " CHANGE rappel_delai rappel_delai tinyint(3) unsigned NOT NULL DEFAULT '".$agenda['rappel_delai']."',";
        }
        if($agenda['rappel_type']){
            $sql .= " CHANGE rappel_type rappel_type smallint(5) unsigned NOT NULL DEFAULT '".$agenda['rappel_type']."',";
        }
        if($agenda['rappel_email'] || 1){
            $sql .= " CHANGE rappel_email rappel_email tinyint(3) unsigned NOT NULL DEFAULT '".$agenda['rappel_email']."',";
        }
        
        $sql = substr($sql, 0, -1);
/*        if(array_key_exists('', $agenda)){
            
        }
        if(array_key_exists('', $agenda)){
            
        }
 * 
 */
        $stm = $this->connection->exec($sql);

    }
    
    /**
     * Saves a note into the database.
     * 
     * $param \Webnotes\Domain\note $agenda The note to save
     */
    public function save(Agenda $agenda) {
        $agendaData = array();
        $fields = $this->findParam();
        foreach ($fields as $field){
            $method = 'get'.ucfirst($field['Field']);
            if(method_exists($agenda, $method)){
                $agendaData[$field['Field']] = (empty($agenda->$method()) ) ? $field['Default'] : $agenda->$method() ;
            }
        }	
        
        if ($this->findByUser($agenda->getId_user())) {
            // The note has already been saved : update it
            $this->update( $agendaData );
        } else {
            // The note has never been saved : insert it
            $this->insert($agendaData);

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
				
		$req = "INSERT agenda_user (".$columns.") values (".$values.")";
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
    public function update($agendaData) {
		
        $columns = $values = "";
        foreach ($agendaData as $key => $val)
        {
                $columns .= $columns == "" ? $key."=:".$key : ", ".$key."=:".$key;
             //   $values	.= $values == "" ? ":".$key : ", :".$key;
        }

        $req = "UPDATE agenda_user SET ".$columns." WHERE id_user=:id_user";
        $stmt = $this->connection->prepare($req);
                
        $stmt->execute($agendaData);
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
    
    
    

    protected function bonFormatHeure($heure){
        $tmp = explode('.', $heure);
        $tmp1 = $tmp[1] == 0 ? "00" : $tmp[1] * 60 / 100;
        $bonneHeure = $tmp[0].":". $tmp1 .":00";
        return $bonneHeure;
    }
    protected function bonFormatSemTyp($sem){
        // 0 = Dimanche
        $typ = "";
        $tmp = str_split($sem);
        $typ .= ($tmp[6] == 0) ? "0," : "" ;
        $typ .= ($tmp[0] == 0) ? "1," : "" ;
        $typ .= ($tmp[1] == 0) ? "2," : "" ;
        $typ .= ($tmp[2] == 0) ? "3," : "" ;
        $typ .= ($tmp[3] == 0) ? "4," : "" ;
        $typ .= ($tmp[4] == 0) ? "5," : "" ;
        $typ .= ($tmp[5] == 0) ? "6," : "" ;
        $typ = substr($typ, 0, -1);
        $typ = "[".$typ."]";
        return $typ;
    }
    protected function bonPlanning($val){
        switch ($val) {
            case 1: $plan = "agendaDay"; break;
            case 2: $plan = "agendaWeek"; break;
            case 3: $plan = "month"; break;
            default: $plan = "month"; break;
        }
        return $plan;
        
    }
    protected function bonPrecision($val){
        switch ($val) {
            case 1: $retour = "00:15:00"; break;
            case 2: $retour = "00:30:00"; break;
            case 3: $retour = "01:00:00"; break;
            default: $retour = "00:30:00"; break;
        }
        return $retour;
        
    }

    /**
     * Creates a Note object based on a DB row.
     *
     * @param array $row The DB row containing Note data.
     * @return \Modea\Note object
     */
    protected function buildDomainObject($row) {
                        
        $val['id_user'] = $row['id_user'] ;    
        $val['debut_journee'] = $this->bonFormatHeure($row['debut_journee']) ;                
        $val['fin_journee'] = $this->bonFormatHeure($row['fin_journee']) ;
        $val['format_nom'] = $row['format_nom'] ;
        $val['planning'] = $this->bonPlanning($row['planning']) ;
        $val['semaine_type'] = $this->bonFormatSemTyp($row['semaine_type']) ;
        $val['precision_planning'] = $this->bonPrecision($row['precision_planning']) ;
        $val['duree_note']  = $row['duree_note'] ;
        $val['rappel_delai'] = $row['rappel_delai'] ;
        $val['rappel_type'] = $row['rappel_type'] ;
        $val['rappel_email'] = $row['rappel_email'] ;
        $val['partage_planning'] = $row['partage_planning'] ;
        $val['autorise_affect'] = $row['autorise_affect'] ;
        $val['alert_affect'] = $row['alert_affect'] ;
        $val['couleur'] = $row['couleur'] ;
        
//        $keys = array('id_user', 'debut_journee', 'fin_journee', 'format_nom', 'planning',
//                'semaine_type', 'precision_planning', 'duree_note', 'rappel_delai', 'rappel_type',
//                'rappel_email', 'partage_planning', 'autorise_affect', 'alert_affect', 'couleur');
//        $agenda = array_fill_keys($keys, $val);
//        
        return $val;
    }

}