<?php

namespace Modea\Domain;

class Note
{
    private $id;						//
    private $title;						//
    private $start;						//
    private $end;						//
    private $allDay;					//
    private $mere_id;					// Id de la note mère : 							: integer
    private $user_id;					// Id de l'utilisateur concerné						: integer
    private $creat_id;					// Id de l'utilisateur qui a créé la note			: integer
    private $creat_date;					// date de la création de la note					: integer
    private $modif_id;					// Id du dernier utilisateur qui a modifié la note	: integer
    private $modif_date;					// date de la derniére modification					: integer
	private $lieu;						// libellé du lieu									: string
	private $detail;					// texte du détail de la note						: string
	private $pers_concern;			// nombre de personnes concernées					:
	private $color;					// color											:
	private $email;						// rappel par email			o/n						: bool
	private $contact_associe;			// Id contact										: integer
	private $email_contact;				// envoi email au contact associé	o/n				: bool
	private $partage;					// Note publique ou note privée						: integer
	private $disponibilite;				// disponible ou pas pour un autre rdv				: integer
	private $rappel;					// active rappel									: integer
	private $rappel_coef;				// coefficient 1=min 60=heure 1440=jour				: integer
	private $periodicite;				// 
	private $period_1;				// 
	private $period_2;				// 
	private $period_3;				// 
	
	
    
    public function __construct(array $donnees = Null )
	{		
		if($donnees) $this->hydrate($donnees);
    }
	
	public function hydrate(array $donnees)
	{
	  foreach ($donnees as $key => $value)
	  {
		// On récupère le nom du setter correspondant à l'attribut.
		$method = 'set'.ucfirst($key);
			
		// Si le setter correspondant existe.
		if (method_exists($this, $method))
		{
		  // On appelle le setter.
		  $this->$method($value);
		}
	  }
	}
    public function getId() {
        return $this->id;
    }
    public function setId($val) {
        $this->id = $val;
    }
    public function setTitle($val) {
        $this->title = $val;
    }
    public function getTitle() {
        /* @var $title type */
        return $this->title;
    }
    public function getStart() {
        return $this->start;
    }
    public function setStart($val) {
        $this->start = $val;
    }
    public function getEnd() {
        return $this->end;
    }
    public function setEnd($val) {
        $this->end = $val;
    }
	
    public function getAllDay() {
        return $this->allDay;
    }
    public function setAllDay($val) {
        $this->allDay = $val;
    }
	
    public function getMere_id() {
        return $this->mere_id;
    }
    public function setMere_id($val) {
        $this->mere_id = $val;
    }
	
    public function getUser_id() {
        return $this->user_id;
    }
    public function setUser_id($val) {
        $this->user_id = $val;
    }
	
    public function getCreat_id() {
        return $this->creat_id;
    }
    public function setCreat_id($val) {
        $this->creat_id = $val;
    }
	
    public function getCreat_date() {
        return $this->creat_date;
    }
    public function setCreat_date($val) {
        $this->creat_date = $val;
    }
	
    public function getModif_id() {
        return $this->modif_id;
    }
    public function setModif_id($val) {
        $this->modif_id = $val;
    }
	
    public function getModif_date() {
        return $this->modif_date;
    }
    public function setModif_date($val) {
        $this->modif_date = $val;
    }
	
    public function getLieu() {
        return $this->lieu;
    }
    public function setLieu($val) {
        $this->lieu = $val;
    }
	
    public function getDetail() {
        return $this->detail;
    }
    public function setDetail($val) {
        $this->detail = $val;
    }
	
    public function getPers_concern() {
        return $this->pers_concern;
    }
    public function setPers_concern($val) {
        $this->pers_concern = $val;
    }
	
    public function getColor() {
        return $this->color;
    }
    public function setColor($val) {
        $this->color = $val;
    }
	
    public function getEmail() {
        return $this->email;
    }
    public function setEmail($val) {
        $this->email = $val;
    }
	
    public function getContact_associe() {
        return $this->contact_associe;
    }
    public function setContact_associe($val) {
        $this->contact_associe = $val;
    }
	
    public function getEmail_contact() {
        return $this->email_contact;
    }
    public function setEmail_contact($val) {
        $this->email_contact = $val;
    }
	
    public function getPartage() {
        return $this->partage;
    }
    public function setPartage($val) {
        $this->partage = $val;
    }
	
    public function getDisponibilite() {
        return $this->disponibilite;
    }
    public function setDisponibilite($val) {
        $this->disponibilite = $val;
    }
	
    public function getRappel() {
        return $this->rappel;
    }
    public function setRappel($val) {
        $this->rappel = $val;
    }
	
    public function getRappel_coef() {
        return $this->rappel_coef;
    }
    public function setRappel_coef($val) {
        $this->rappel_coef = $val;
    }
	
    public function getPeriodicite() {
        return $this->periodicite;
    }
    public function setPeriodicite($val) {
        $this->periodicite = $val;
    }
	
    public function getPeriod_1() {
        return $this->period_1;
    }
    public function setPeriod_1($val) {
        $this->period_1 = $val;
    }
	
    public function getPeriod_2() {
        return $this->period_2;
    }
    public function setPeriod_2($val) {
        $this->period_2 = $val;
    }
	
    public function getPeriod_3() {
        return $this->period_3;
    }
    public function setPeriod_3($val) {
        $this->period_3 = $val;
    }
}