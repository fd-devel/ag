<?php

namespace Modea\Domain;

class Agenda
{
    private $id_user;                   //
    private $debut_journee;             //
    private $fin_journee;               //
    private $format_nom;                //
    private $planning;                  //
    private $semaine_type;              // date de la création de la note       : integer
    private $precision_planning;          // Id de la note mère                   : integer
    private $duree_note;                // Id du dernier utilisateur qui a modifié la note  : integer
    private $rappel_delai;              // date de la derniére modification     : integer
    private $rappel_type;               // libellé du rappel_type									: string
    private $rappel_email;              // texte du détail de la note           : string
    private $partage_planning;          // Id de la note mère                   : integer
    private $autorise_affect;           // Id de l'utilisateur concerné         : integer
    private $alert_affect;              // Id de l'utilisateur qui a créé la note   : integer		:
    private $couleur;                     // color											:




    public function __construct(array $donnees = Null )
    {		
        if($donnees) $this->hydrate($donnees);
    }

   /* 
    *   Contruit un objet avec toutes ses methodes
    */
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

    public function getSemaine_type() {
        return $this->semaine_type;
    }
    public function setSemaine_type($val) {
        $this->semaine_type = $val;
    }

    public function getPrecision_planning() {
        return $this->precision_planning;
    }
    public function setPrecision_planning($val) {
        $this->precision_planning = $val;
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

    public function getCouleur() {
        return $this->couleur;
    }
    public function setCouleur($val) {
        $this->couleur = $val;
    }
}