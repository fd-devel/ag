<?php
if($_POST['note']){
$note = json_decode($_POST['note'], true);

$title 		= htmlspecialchars($note['libelle']);
$start		= $note['starttime'];
$end		= $note['endtime'];
$lieu		= htmlspecialchars($note['lieu']);
$detail 	= htmlspecialchars($note['detail']);
$date_note	= $note['note_date'];
$journee 	= $note['journee'];
$Hdebut		= $note['Hdebut'];
$Hfin 		= $note['Hfin'];
$participants = $note['participants'];
$partage 	= $note['partage'];
$dispo	 	= $note['dispo'];
$couleur 	= $note['couleur'];
$periodicite = $note['periodicite'];
$J_repetitionJours = $note['J_repetitionJours'];
$J_optionsRepetitionJour = $note['J_optionsRepetitionJour'];
$J_repetionJours = $note['J_repetionJours'];
$S_repetionSemaine = $note['S_repetionSemaine'];
$S_sem_lundi = $note['S_sem_lundi'];
$S_sem_mardi = $note['S_sem_mardi'];
$S_sem_mercredi = $note['S_sem_mercredi'];
$S_sem_jeudi = $note['S_sem_jeudi'];
$S_sem_vendredi = $note['S_sem_vendredi'];
$S_sem_samedi = $note['S_sem_samedi'];
$S_sem_dimanche = $note['S_sem_dimanche'];
$M_repetionMois = $note['M_repetionMois'];
$M_optionsRepetitionMois = $note['M_optionsRepetitionMois'];
$M_jourDuMois = $note['M_jourDuMois'];
$M_moisCardinalite = $note['M_moisCardinalite'];
$M_moisCardinaliteJour = $note['M_moisCardinaliteJour'];
$A_optionsRepetitionAn = $note['A_optionsRepetitionAn'];
$A_jourDuMois = $note['A_jourDuMois'];
$A_Mois = $note['A_Mois'];
$A_anCardinalite = $note['A_anCardinalite'];
$A_anCardinaliteJour = $note['A_anCardinaliteJour'];
$A_anCardinaliteMois = $note['A_anCardinaliteMois'];
$RepetitionOccurence = $note['RepetitionOccurence'];
$Occurences = $note['Occurences'];
$RepetitionDateFin = $note['RepetitionDateFin'];


// connexion à la base de données
 try {
 $bdd = new PDO('mysql:host=localhost;dbname=aaaa', 'root', '');
 } catch(Exception $e) {
 exit('Impossible de se connecter à la base de données.');
 }
 
$sql = "INSERT INTO agenda_note (title, start, end) VALUES (:title, :start, :end)";
$q = $bdd->prepare($sql);
$q->execute(array(':title'=>$title, ':start'=>$start, ':end'=>$end));

}
?>