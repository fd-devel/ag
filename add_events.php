<?php
/*
 *  Entrée : string ex 31/12/2016
 *  Retourne : array [mois, jour, année]
 */
function good_Date($jour){
	if(!preg_match('#^([0-3]{1})([0-9]{1})\/([0-1]{1})([0-9]{1})\/[2]([0-9]{3})$#', $jour)) throw new \Exception("No date matching preg_match");
	$jour_f = explode('/', $jour);
	if(!checkdate($jour_f[1],$jour_f[0],$jour_f[2])) throw new \Exception("No date matching checkdate");
	return $jour_f;
} 
/*
 * Entrée : string   ex 8.5  -  12  
 * retourne : string H,m,i
 */
function good_hour($heure){
	if(!preg_match('#^(((0|1)?[0-9])|(2[0-3]))([.025]{1,3})?$#', $heure)) throw new \Exception("No heure matching preg_match");
	$heure_f = explode('.',$heure);
	$heure_f[1] = (array_key_exists(1, $heure_f)) ? $heure_f[1]*60/10 : 0 ; // Conversion 
        $heure_f[2] = 0;
	return $heure_f;
}


    
$id_user=$_POST['user'];

if(!$_POST['note']){
    echo 'Error';
}
else{

require './src/DAO/DAO.php';
require './src/DAO/NoteDAO.php';
require './src/Domain/note.class.php';

$note = json_decode($_POST['note'], true);
$noteData=array(); // -- > pour objet

// Vérification de la date initiale 
$Date_Note = good_Date($note['A_date']);
$Start_Note = good_hour($note['start']);
$End_Note = good_hour($note['end']);

$noteData['title']      = $note['title'];
$noteData['lieu']       = !$note['lieu'] == "" ? $note['lieu'] : "";
$noteData['detail']     = !$note['detail'] == "" ? $note['detail'] : "";
$noteData['allDay']     = ($note['allDay'] != 1) ? 0 : 1;
//$noteData['user_id']     = $id_user;
$noteData['creat_id']     = $id_user;
$noteData['creat_date']     = date("Y-m-d H:i:s");  
//$noteData['user_id']     = $id_user;             $dateCreation = gmdate("Y-m-d H:i:s", time());
$noteData['partage']    = ($note['partage'] != 1) ? 0 : 1;
$noteData['dispo']      = ($note['dispo'] != 1) ? 0 : 1;
$noteData['color']      = !$note['color'] == "" ? $note['color'] : '#D925AC' ; //$note['color'];

/*
 * QUI = Participants
 */
$participants = explode('##', $note['participants']);

/*
 * QUAND  = Quelles dates
 */

$Tous_les_X_jours = $Toutes_les_X_semaines = $Tous_les_X_mois = 1; // a incrementer en fonction

$noteData['periodicite'] = $note['periodicite'];
switch ($note['periodicite']){
    case 2: // Quotidienne
        if($note['J_optionsRepetitionJour'] == 1){
            // Toues XX jours
            $noteData['periode2'] = (floor($note['J_repetitionJours'])>0) ? floor($note['J_repetitionJours']) : 1;
            $Tous_les_X_jours = $noteData['periode2'] ;
        }else{
            // Tous les jours ouvrables
            $noteData['periode2'] = 2;
            $Tous_les_X_jours = [1,1,1,1,1,0,0] ;
        }
        $noteData['periode1'] = $note['J_optionsRepetitionJour'];
        break;
    case 3: // Hebdomadaire
        $noteData['periode1'] = (floor($note['S_repetionSemaine'])>0) ? floor($note['S_repetionSemaine']) : 1 ;
        $Toutes_les_X_semaines = [$noteData['periode1']];
        // Creation d'un tableau des jours de la semaine au format PHP ie. du Dimanche(0) au Samedi(6)
        $aSemaineType = array();
        //Stockage de la semaine type au format PHP qui est utilisee pour creer la note
        $noteData['periode2'] = "";
        for ($i=0;$i<7;$i++) {
            $aSemaineType[$i] = (!$i) ? $note['S_sem_0'] + 0 : $note['S_sem_'.$i] + 0;
            $noteData['periode2'] .= $aSemaineType[$i];
            array_push($Tous_les_X_jours, $aSemaineType[$i]);
        }
        break;
    case 4: // Mensuelle
        if( $note['M_optionsRepetitionMois'] == 1){
            $noteData['periode1'] = 1;
            $noteData['periode2'] = $note['M_jourDuMois'];
        } else {
            $noteData['periode1'] = 2;
            $noteData['periode2'] = $note['M_moisCardinalite'];
            $noteData['periode3'] = $note['M_moisCardinaliteJour'];
        }
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
		$nbOccurence = 99;
		list($P1,$P2,$P3) = explode("/",$note['RepetitionDateFin']);
		if (!checkdate($P2,$P1,$P3))
                    $P1 = date("t", mktime(12,0,0,$P2,1,$P3));
		$dateMax = mktime(23,59,0,$P2,$P1,$P3);
    } elseif ($note['periodicite'] > 1) {
		$note['Occurences'] += 0;
		$note['RepetitionOccurence'] = 1;
		$nbOccurence = min($note['Occurences'],99);
		$dateMax = 0;
    } else {
      $note['RepetitionOccurence'] = 1;
      $nbOccurence = 1;
      $dateMax = 0;
    }
    
    if ($note['Rappel'] != 2) {
      $noteData['Rappel'] = 0;
      $noteData['rappel_coef'] = 1;
      $noteData['email'] = 0;
      $noteData['contact_associe'] = 0;
    } else {
      if ($note['email'] != 1) {
        $noteData['email'] = 0;
      }
      if ($note['contact_associe'] != 1) {
        $noteData['contact_associe'] = 0;
      }
    }
    $noteData['partage'] = ($note['partage'] != 1) ? 0 : 1;
//    $noteData['dispo'] = ($note['dispo'] != 1) ? 0 : 1;

    
    
/*
 * Construction objet et enregistrement
 */
    
    $id_mere = 0;
    $yep = new Modea\DAO\NoteDAO();
    foreach ($participants as $participant) {
    $Occurence = 0;
    for($i=0; $i<$nbOccurence; $i++){
        $hD_time = mktime($Start_Note[0], $Start_Note[1], $Start_Note[2], $Date_Note[1], $Date_Note[0]+$Occurence , $Date_Note[2]);
        $hF_time = mktime($End_Note[0], $End_Note[1], $End_Note[2], $Date_Note[1], $Date_Note[0]+$Occurence , $Date_Note[2]);
        
        if ($dateMax){
            if($hD_time>$dateMax)                break;
        }
        $date_start = date("Y-m-d H:i:s",$hD_time);
        $date_end = date("Y-m-d H:i:s",$hF_time);
        
        $yap = new Modea\Domain\Note($noteData);
        $yap->setStart($date_start);
        $yap->setEnd($date_end);
        $yap->setUser_id($participant);
        if ($id_mere) $yap->setMere_id ($id_mere);
        $yep->save($yap);
        if(!$id_mere) $id_mere = $yap->getId ();
        
        $Occurence += $Tous_les_X_jours;      
        
    }
    }
        


echo 'Success';
}
?>