<?php

namespace Modea\Agenda;

class Note
{
    private $id;						//
    private $mere_id;					// Id de la note m�re : 							: integer
    private $user_id;					// Id de l'utilisateur concern�						: integer
    private $creat_id;					// Id de l'utilisateur qui a cr�� la note			: integer
    private $creat_date;					// date de la cr�ation de la note					: integer
    private $modif_id;					// Id du dernier utilisateur qui a modifi� la note	: integer
    private $modif_date;					// date de la derni�re modification					: integer
	private $lieu;						// libell� du lieu									: string
	private $detail;					// texte du d�tail de la note						: string
	private $date_note;					// date 											: date
	private $h_debut;					// heure d�but										: float
	private $h_fin;						// heure fin										: float
	private $h_duree;					// 
	private $pers_concernee;			// nombre de personnes concern�es					:
	private $couleur;					// couleur											:
	private $email;						// rappel par email			o/n						: bool
	private $contact_associe;			// Id contact										: integer
	private $email_contact;				// envoi email au contact associ�	o/n				: bool
	private $partage;					// Note publique ou note priv�e						: integer
	private $disponibilite;				// disponible ou pas pour un autre rdv				: integer
	private $rappel;					// active rappel									: integer
	private $rappel_coef;				// coefficient 1=min 60=heure 1440=jour				: integer
	private $periodicite;				// 
	
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }
	
    public function getMere_id() {
        return $this->id;
    }

    public function setMere_id($mere_id) {
        $this->mere_id = $mere_id;
    }
	
    public function getUser_id() {
        return $this->user_id;
    }

    public function setUser_id($user_id) {
        $this->user_id = $user_id;
    }
	
    public function getCreat_id() {
        return $this->creat_id;
    }

    public function setCreat_id($creat_id) {
        $this->creat_id = $creat_id;
    }
	
    public function getCreat_date() {
        return $this->creat_date;
    }

    public function setCreat_date($creat_date) {
        $this->creat_date = $creat_date;
    }
	
    public function getModif_id() {
        return $this->modif_id;
    }

    public function setModif_id($modif_id) {
        $this->modif_id = $modif_id;
    }
	
    public function getModif_date() {
        return $this->modif_date;
    }

    public function setModif_date($modif_date) {
        $this->modif_date = $modif_date;
    }

    public function getLieu() {
        return $this->lieu;
    }

    public function setLieu($lieu) {
        $this->lieu = $lieu;
    }

    public function getDetail() {
        return $this->detail;
    }

    public function setDetail($detail) {
        $this->detail = $detail;
    }

    public function getDate_note() {
        return $this->date_note;
    }

    public function setDate_note($date_note) {
        $this->date_note = $date_note;
    }

    public function getH_debut() {
        return $this->h_bedut;
    }

    public function setH_debut($h_bedut) {
        $this->h_bedut = $h_bedut;
    }

    public function getH_fin() {
        return $this->h_fin;
    }

    public function setH_fin($h_fin) {
        $this->h_fin = $h_fin;
    }

    public function getPers_concernee() {
        return $this->pers_concernee;
    }

    public function setPers_concernee($pers_concernee) {
        $this->pers_concernee = $pers_concernee;
    }

    public function getCouleur() {
        return $this->couleur;
    }

    public function setCouleur($couleur) {
        $this->couleur = $couleur;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getContact_associe() {
        return $this->contact_associe;
    }

    public function setContact_associe($contact_associe) {
        $this->contact_associe = $contact_associe;
    }

    public function getEmail_contact() {
        return $this->email_contact;
    }

    public function setEmail_contact($email_contact) {
        $this->email_contact = $email_contact;
    }

    public function getPartage() {
        return $this->partage;
    }

    public function setPartage($partage) {
        $this->partage = $partage;
    }

    public function getDisponibilite() {
        return $this->disponibilite;
    }

    public function setDisponibilite($disponibilite) {
        $this->disponibilite = $disponibilite;
    }

    public function getRappel() {
        return $this->rappel;
    }

    public function setRappel($rappel) {
        $this->rappel = $rappel;
    }

    public function getRappel_coef() {
        return $this->rappel_coef;
    }

    public function setRappel_coef($rappel_coef) {
        $this->rappel_coef = $rappel_coef;
    }

    public function getPeriodicite() {
        return $this->periodicite;
    }

    public function setPeriodicite($periodicite) {
        $this->periodicite = $periodicite;
    }

}