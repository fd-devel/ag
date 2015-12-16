<?php

namespace Modea\Domain;

class Agenda
{
    private $id_user;                   //
    private $debut_journee;             //
    private $fin_journee;               //
    private $format_nom;                //
    private $planning;                  //
    private $partage_planning;          // Id de la note mère                   : integer
    private $autorise_affect;           // Id de l'utilisateur concerné         : integer
    private $alert_affect;              // Id de l'utilisateur qui a créé la note   : integer
    private $semaine_type;              // date de la création de la note       : integer
    private $duree_note;                // Id du dernier utilisateur qui a modifié la note  : integer
    private $rappel_delai;              // date de la derniére modification     : integer
    private $rappel_type;               // libellé du rappel_type									: string
    private $rappel_email;              // texte du détail de la note           : string
    private $pers_concern;              // nombre de personnes concernées					:
    private $color;                     // color											:
    private $email;                     // rappel par email   : o/n             : bool
    private $contact_associe;           // Id contact                           : integer
    private $email_contact;             // envoi email au contact associé : o/n	:  bool
    private $partage;                   // Note publique ou note privée         : integer
    private $disponibilite;             // disponible ou pas pour un autre rdv  : integer
    private $rappel;                    // active rappel                        : integer
    private $rappel_coef;               // coefficient 1=min 60=heure 1440=jour : integer
    private $periodicite;               // 
    private $period_1;                  // 
    private $period_2;                  // 
    private $period_3;                  // 



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
    public function getId_user() {
        return $this->id_user;
    }
    public function setId_user($val) {
        $this->id_user = $val;
    }
    public function setDebut_journee($val) {
        $this->debut_journee = $val;
    }
    public function getDebut_journee() {
        /* @var $debut_journee type */
        return $this->debut_journee;
    }
    public function getFin_journee() {
        return $this->fin_journee;
    }
    public function setFin_journee($val) {
        $this->fin_journee = $val;
    }
    public function getFormat_nom() {
        return $this->format_nom;
    }
    public function setFormat_nom($val) {
        $this->format_nom = $val;
    }

    public function getPlanning() {
        return $this->planning;
    }
    public function setPlanning($val) {
        $this->planning = $val;
    }

    public function getPartage_planning() {
        return $this->partage_planning;
    }
    public function setPartage_planning($val) {
        $this->partage_planning = $val;
    }

    public function getAutorise_affect() {
        return $this->autorise_affect;
    }
    public function setAutorise_affect($val) {
        $this->autorise_affect = $val;
    }

    public function getAlert_affect() {
        return $this->alert_affect;
    }
    public function setAlert_affect($val) {
        $this->alert_affect = $val;
    }

    public function getSemaine_type() {
        return $this->semaine_type;
    }
    public function setSemaine_type($val) {
        $this->semaine_type = $val;
    }

    public function getDuree_note() {
        return $this->duree_note;
    }
    public function setDuree_note($val) {
        $this->duree_note = $val;
    }

    public function getRappel_delai() {
        return $this->rappel_delai;
    }
    public function setRappel_delai($val) {
        $this->rappel_delai = $val;
    }

    public function getRappel_type() {
        return $this->rappel_type;
    }
    public function setRappel_type($val) {
        $this->rappel_type = $val;
    }

    public function getRappel_email() {
        return $this->rappel_email;
    }
    public function setRappel_email($val) {
        $this->rappel_email = $val;
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