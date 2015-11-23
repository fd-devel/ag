<?php

namespace Modea\Agenda;

class Couleur
{
    private $id;
    private $libelle;
    private $couleur;
    private $user_id;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getLibelle() {
        return $this->libelle;
    }

    public function setLibelle($libelle) {
        $this->libelle = $libelle;
    }

    public function getCouleur() {
        return $this->couleur;
    }

    public function setCouleur($couleur) {
        $this->couleur = $couleur;
    }
	
    public function getUser_id() {
        return $this->user_id;
    }

    public function setUser_id(User $user_id) {
        $this->user_id = $user_id;
    }
}