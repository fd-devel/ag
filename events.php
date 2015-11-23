<?php
$id_user=$_GET['user'];

require './src/DAO/DAO.php';
require './src/DAO/NoteDAO.php';
require './src/Domain/note.class.php';

$yap = new Modea\DAO\NoteDAO();
$resultat=$yap->findByUser($id_user);

 
 // envoi du résultat au success
//$a=$resultat->serialize();
$yp =json_encode($resultat);
 echo $yp;
/*

// liste des événements
 $json = array();
 // requête qui récupère les événements
 $requete = "SELECT * FROM agenda_note ORDER BY id";
 

 // connexion à la base de données
 try {
 $bdd = new PDO('mysql:host=localhost;dbname=aaaa', 'root', '');
 } catch(Exception $e) {
 exit('Impossible de se connecter à la base de données.');
 }
 // exécution de la requête
 $resultat = $bdd->query($requete) or die(print_r($bdd->errorInfo()));
 
 // envoi du résultat au success
 echo json_encode($resultat->fetchAll(PDO::FETCH_ASSOC));
 */
?>