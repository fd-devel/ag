<?php

namespace Modea\Domain;

class Categorie
{
    private $id;                   //
    private $nom;             //
    private $couleur;               //
    private $id_groupe;                //
    private $id_user;                  //
    private $id_espace;              // date de la création de la note       : integer



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
    public function getId() {
        return $this->id;
    }
    public function setId($val) {
        $this->id = $val;
    }
    public function setNom($val) {
        $this->nom = $val;
    }
    public function getNom() {
        /* @var $nom type */
        return $this->nom;
    }
    public function getCouleur() {
        return $this->couleur;
    }
    public function setCouleur($val) {
        $this->couleur = $val;
    }
    public function getId_groupe() {
        return $this->id_groupe;
    }
    public function setId_groupe($val) {
        $this->id_groupe = $val;
    }

    public function getId_user() {
        return $this->id_user;
    }
    public function setId_user($val) {
        $this->id_user = $val;
    }

    public function getId_espace() {
        return $this->id_espace;
    }
    public function setId_espace($val) {
        $this->id_espace = $val;
    }
}