<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Utilisteur_Groupe
 *
 * @author fred
 */
class GroupAndUser extends Utilisateur_Groupe {
    
    private $mysql;
    
    public function __construct(  ) {
        $this->mysql= new MySQL();
    }
    
   /**
    * Récuperation de la liste des utilisateurs faisant parti du groupe
    *
    * @author fred
    */
    // 
    public function getUtilisateurByGroupe($id_groupe)
    {
        // Etablissement de la connexion à MySQL
        $Connexion=$this->mysql->getPDO();
        // Préparation de la requête
        $sql=$Connexion->prepare('select id_utilisateur from utilisateur_groupe where id_groupe=:id_groupe');
        try {
            // On envoi la requête
            $sql->execute(array('id_groupe'=>$id_groupe));
            $result=$sql->fetchAll();
            $donnees = array();
            for($i=0, $len=count($result); $i<$len; $i++){
                $donnees[$i]=$result[$i][0];
            }
            return $donnees;
        } catch( Exception $e ){
            return 'Erreur de requête : '.$e->getMessage();
        }
    }
}
