<?php

namespace Modea\DAO;

use Modea\Agenda;

class NoteDAO extends DAO
{
    /**
     * Return a list of all notes, sorted by date (most recent first).
     *
     * @return array A list of all notes.
     */
    public function findInMonth($month) {
        $sql = "select * from px_note order by art_id ";
        $result = $this->getDb()->fetchAll($sql);

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
        $note->setId($row['']);
        $note->setMere_id($row['']);
        $note->setUser_id($row['']);
        $note->setCreat_id($row['']);
        $note->setCreat_date($row['']);
        $note->setModif_id($row['']);
        $note->setModif_date($row['']);
        $note->setLieu($row['']);
        $note->setDetail($row['']);
        $note->setDate_note($row['']);
        $note->setH_debut($row['']);
        $note->setH_fin($row['']);
        $note->setDuree($row['']);
        $note->setPers_concernee($row['']);
        $note->setCouleur($row['']);
        $note->setEmail($row['']);
        $note->setContact_associe($row['']);
        $note->setEmail_contact($row['']);
        $note->setPartage($row['']);
        $note->setDisponibilite($row['']);
        $note->setRappel($row['']);
        $note->setRappel_coef($row['']);
        $note->setPeriodicite($row['']);
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