<?php

function formatDate($heure, $jour){
	if(!preg_match('#^([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})$#', $jour)) echo 'error';
	if(!preg_match('#^([0-9]{1,2}).[05]?$#', $heure)) echo 'error';
	if (!preg_match('#/.#', $heure)) $heure .= ".00" ;
	list($heure_f[0], $heure_f[1]) = explode('.',$heure);
	$heure_f[1] = (!empty($heure_f[1])) ? $heure_f[1]*60 : 0 ;
	list($jour_f[0],$jour_f[1],$jour_f[2]) = explode('/', $jour);
	if(!checkdate($jour_f[1],$jour_f[0],$jour_f[2])) echo 'error';
	$date_f = $jour_f[2].'-'.$jour_f[1].'-'.$jour_f[0].' '.$heure_f[0].':'.$heure_f[1].':00';
	return $date_f;
}

$id_user=$_GET['user'];

if($_POST['note']){

require './src/DAO/DAO.php';
require './src/DAO/NoteDAO.php';
require './src/Domain/note.class.php';

$note = json_decode($_POST['note'], true);
$noteData=array();

$noteData['title']      = $note['title'];
$noteData['start']      = formatDate($note['start'], $note['A_date']);
$noteData['end']        = formatDate($note['end'], $note['A_date']);
$noteData['allDay']     = ($note['allDay'] != 1) ? 0 : 1;
$noteData['lieu']       = !$note['lieu'] == "" ? $note['lieu'] : "";
$noteData['detail']     = !$note['detail'] == "" ? $note['detail'] : "";
$noteData['partage']    = ($note['partage'] != 1) ? 0 : 1;
$noteData['dispo']      = ($note['dispo'] != 1) ? 0 : 1;
$noteData['color']      = '#D925AC' ; //$note['color'];

$noteData['periodicite'] = $note['periodicite'];
switch ($note['periodicite']){
    case 2: // Quotidienne
        if($note['J_optionsRepetitionJour'] == 1){
            $noteData['periode2'] = (floor($note['J_repetitionJours'])>0) ? floor($note['J_repetitionJours']) : 1;
        }else{
            $noteData['periode2'] = 2;
        }
        $noteData['periode1'] = $note['J_optionsRepetitionJour'];
        break;
    case 3: // Hebdomadaire
        $noteData['periode1'] = (floor($note['S_repetionSemaine'])>0) ? floor($note['S_repetionSemaine']) : 1 ;
        // Creation d'un tableau des jours de la semaine au format PHP ie. du Dimanche(0) au Samedi(6)
        $aSemaineType = array();
        //Stockage de la semaine type au format PHP qui est utilisee pour creer la note
        $noteData['periode2'] = "";
        for ($i=0;$i<7;$i++) {
            $aSemaineType[$i] = (!$i) ? $note['S_sem_0'] + 0 : $note['S_sem_'.$i] + 0;
            $noteData['periode2'] .= $aSemaineType[$i];
        }
        $noteData['periode2'] += 0; // On retransforme la chaine en entier pour enlever les 0 devants
        break;
    case 4: // Mensuelle
        if( $note['M_optionsRepetitionMois'] == 1){
            $noteData['periode2'] = $note['M_jourDuMois'];
        } else {
            $note['M_optionsRepetitionMois'] = 2;
            $noteData['periode2'] = $note['M_moisCardinalite'];
            $noteData['periode3'] = $note['M_moisCardinaliteJour'];
        }
        $noteData['periode1'] = $note['M_optionsRepetitionMois'];
        $noteData['periode4'] = (floor($note['M_repetionMois'])>0) ? floor($note['M_repetionMois']) : 1;
        break;
    case 5: // Annuelle
        if ($noteData['A_optionsRepetitionAn'] == 1) {
            $noteData['periode2'] = $note['A_jourDuMois']; 
            $noteData['periode3'] = $note['A_Mois'];
        } else {
            $noteData['A_optionsRepetitionAn'] = 2; 
            $noteData['periode2'] = $note['A_anCardinalite']; 
            $noteData['periode3'] = $note['A_anCardinaliteJour']; 
            $noteData['periode4'] = $note['A_anCardinaliteMois'];
        }
        $noteData['periode1'] = $noteData['A_optionsRepetitionAn'];
        break;
    default : $noteData['periodicite'] = 1;
}
    if ($note['RepetitionOccurence'] == 2 && $noteData['periodicite'] > 1) {
		$nbOccurence = 0;
		list($zlP1,$zlP2,$zlP3) = explode("/",$note['RepetitionDateFin']);
		if (!checkdate($zlP2,$zlP1,$zlP3))
                    $zlP1 = date("t", mktime(12,0,0,$zlP2,1,$zlP3));
		$dateMax = mktime(12,0,0,$zlP2,$zlP1,$zlP3);
    } elseif ($note['periodicite'] > 1) {
		$note['Occurences'] += 0;
		$note['RepetitionOccurence'] = 1;
		$nbOccurence = min($note['Occurences'],99);
		$dateMax = 0;
    } else {
      $note['RepetitionOccurence'] = 1;
      $nbOccurence = 10;
      $dateMax = 0;
    }
    
    if ($note['Rappel'] != 2) {
      $noteData['Rappel'] = 0;
      $noteData['rappel_coef'] = 1;
      $noteData['email'] = 0;
      $noteData['contact_associe'] = 0;
    } else {
      if ($noteData['email'] != 1) {
        $noteData['email'] = 0;
      }
      if ($noteData['contact_associe'] != 1) {
        $noteData['contact_associe'] = 0;
      }
    }
    $noteData['partage'] = ($note['partage'] != 1) ? 0 : 1;
//    $noteData['dispo'] = ($note['dispo'] != 1) ? 0 : 1;


        
$yap = new Modea\Domain\Note($noteData);
$yep = new Modea\DAO\NoteDAO();
$yep->save($yap);


/*

$title 		= htmlspecialchars($note['title']);
$start		= $note['start'];
$end		= $note['end'];
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
*/
}
?>