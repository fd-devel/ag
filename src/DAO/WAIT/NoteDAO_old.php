<?php
/*
namespace Modea\DAO;

use Modea\Agenda;
*/
class NoteDAO extends DAO
{
    /**
     * Return a list of all notes, sorted by date (most recent first).
     *
     * @return array A list of all notes.
     */
    public function find() {
		
 // exécution de la requête
 $resultat = $bdd->query($requete) or die(print_r($bdd->errorInfo()));
 
 // envoi du résultat au success
 echo json_encode($resultat->fetchAll(PDO::FETCH_ASSOC));
 
        // Etablissement de la connexion à MySQL
        $mysql=new MySQL();
        $Connexion=$mysql->getPDO();
        // Préparation de la requête
        $sql=$Connexion->prepare("SELECT * FROM agenda_note ORDER BY id");
        try {
            // On envoi la requête
            $sql->execute();

			// Convert query result to an array of domain objects
			$entities = array();
			foreach ($result as $row) {
				$id = $row['user_id'];
				$entities[$id] = $this->buildDomainObject($row);
			}
			return $entities;
                

        } 
		catch( \Exception $e ){
            $Log=new \Log(array(
                "traitement"=>"User->getUserDB", 
                "erreur"=>$e->getMessage(),
                "requete"=>"select * from user where id=".$id
            ));
            $Log->Save();
            return 'Erreur de requête : '.$e->getMessage();
        }
		// requête qui récupère les événements
		$requete = "SELECT * FROM agenda_note ORDER BY id";
        $result = $this->getDb()->fetchAll($requete);

        // Convert query result to an array of domain objects
        $notes = array();
        foreach ($result as $row) {
            $noteId = $row['art_id'];
            $notes[$noteId] = $this->buildDomainObject($row);
        }
        return $notes;
    }
	
	/**
     * Returns a Note matching the supplied id.
     *
     * @param integer $id
     *
     * @return \Modea\Note | throws an exception if no matching note is found
     */
    public function find($id) {
        $sql = "select * from px_note where note_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No note matching id " . $id);
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
        $note->setLibelle($row['libelle']);
        $note->setStart($row['start']);
        $note->setEnd($row['end']);
        $note->setMere_id($row['mere_id']);
        $note->setCreat_id($row['creat_id']);
        $note->setCreat_date($row['creat_date']);
        $note->setModif_id($row['modif_id']);
        $note->setModif_date($row['modif_date']);
        $note->setLieu($row['lieu']);
        $note->setDetail($row['detail']);
        $note->setPers_concern($row['pers_concern']);
        $note->setCouleur($row['couleur']);
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

    /**
     * Saves an note into the database.
     *
     * @param \MicroCMS\Domain\Article $note The note to save
     */
    public function save(Article $note) {
        $noteData = array(
            'art_title' => $note->getTitle(),
            'art_content' => $note->getContent(),
            );

        if ($note->getId()) {
            // The note has already been saved : update it
            $this->getDb()->update('t_note', $noteData, array('art_id' => $note->getId()));
        } else {
            // The note has never been saved : insert it
            $this->getDb()->insert('t_note', $noteData);
            // Get the id of the newly created note and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $note->setId($id);
        }
    }

    /**
     * Removes an note from the database.
     *
     * @param integer $id The note id.
     */
    public function delete($id) {
        // Delete the note
        $this->getDb()->delete('t_note', array('art_id' => $id));
    }
}