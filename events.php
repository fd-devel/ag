<?php
$id_user=$_POST['user'];

require './src/DAO/DAO.php';
require './src/DAO/NoteDAO.php';
require './src/Domain/note.class.php';

$yap = new Modea\DAO\NoteDAO();
$resultat=$yap->findByUser($id_user);

 
 // envoi du résultat au success
//$a=$resultat->serialize();
$yp =json_encode($resultat);
 echo $yp;

?>