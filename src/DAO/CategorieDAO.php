<?php

namespace Modea\DAO;

use Modea\Domain\Categorie as Categorie;

class CategorieDAO extends DAO
{
    private $connection; // PDO instance
    
    public function __construct(array $donnees = Null )
    {
        $this->setDb();
    }
	
	
    public function setDb(\PDO $connection = null)
    {
        $db_config = '../../data/db-config.inc.php';
        if(!file_exists($db_config)) $db_config = '../'.$db_config;
        require $db_config;

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
            SELECT * FROM agenda_color
        ');
        $stmt->execute();
 //       $stmt->setFetchMode(\PDO::FETCH_CLASS, 'Categorie');
        $result = $stmt->fetchAll();

        return $result;
    }
	
    public function findParam()
    {
        $stmt = $this->connection->prepare('
            SHOW FIELDS FROM agenda_color
        ');
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Saves a note into the database.
     * 
     * $param \Webnotes\Domain\note $agenda The note to save
     */
    public function save(Categorie $categorie) {	
        $categorieData = array();
        $fields = $this->findParam();
        foreach ($fields as $field){
            $method = 'get'.ucfirst($field['Field']);
            if(method_exists($categorie, $method)){
                $categorieData[$field['Field']] = (empty($categorie->$method()) ) ? $field['Default'] : $categorie->$method() ;
            }
        }
        if ($categorie->getId()) {
            // The note has already been saved : update it
            $this->update( $categorieData );
        } else {
            // The note has never been saved : insert it
            $this->insert($categorieData);
        }
    }
    
    /**
     * Removes a note from the database.
     * 
     * @param integer $id The note id
     */
    public function insert($categorieData) {
		
        $columns = $values = "";
        $data = array();
        foreach ($categorieData as $key => $val)
        {
            $columns .= $columns == "" ? $key : ", ".$key;
            $values	.= $values == "" ? ":".$key : ", :".$key;
            $dataKey = ':'.$key;
            $data[$dataKey] = $val;
        }

        $req = "INSERT agenda_color (".$columns.") values (".$values.")";
        $stmt = $this->connection->prepare($req);

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

        $req = "UPDATE agenda_color SET ".$columns." WHERE id=:id";
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
        
        $req = "DELETE FROM agenda_color WHERE id=:id";
        $stmt = $this->connection->prepare($req);
        $stmt->execute( array('id' => $id));
    }

    /**
     * Creates a Note object based on a DB row.
     *
     * @param array $row The DB row containing Note data.
     * @return \Modea\Note object
     */
    protected function buildDomainObject($row) {
                        
        $val['id'] = $row['id'] ;    
        $val['nom'] = $row['nom'] ;
        $val['categorie']  = $row['categorie'] ;
        $val['id_groupe'] = $row['id_groupe'] ;
        $val['id_user'] = $row['id_user'] ;
        $val['id_espace'] = $row['id_espace'] ;
//        
        return $val;
    }
}