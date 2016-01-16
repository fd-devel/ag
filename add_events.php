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

$Tous_les_X_jours = $Toutes_les_X_semaines = 1; // a incrementer en fonction
$jours_Ouvrables = $jours_dans_semaine = $jour_du_mois = $Tous_les_X_mois = 0;

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
            $Tous_les_X_jours = 1 ;
            $jours_Ouvrables = 1;
        }
        $noteData['periode1'] = $note['J_optionsRepetitionJour'];
        break;
    case 3: // Hebdomadaire
        $noteData['periode1'] = (floor($note['S_repetionSemaine'])>0) ? floor($note['S_repetionSemaine']) : 1 ;
            $Tous_les_X_jours = 1 ;
            $Toutes_les_X_semaines = $noteData['periode1'] ;
        // Creation d'un tableau des jours de la semaine au format "N" PHP 1 (pour Lundi) à 7 (pour Dimanche)
        $SemaineType = array();
        //Stockage de la semaine type au format PHP qui est utilisee pour creer la note
        $noteData['periode2'] = "";

        for ($i=1;$i<=7;$i++) {
            if($note['S_sem_'.$i] == 1){
                $noteData['periode2'] .= $i;    //  pour la sauvegarde
                array_push($SemaineType, $i);   //  pour la creation
//                $dernier_jour[$i]=0;            //  pour la creation
            }
        }
        $jours_dans_semaine = count($SemaineType);
        break;
    case 4: // Mensuelle
            $noteData['periode1'] = $note['M_repetionMois'];
            $Tous_les_X_mois = $noteData['periode1'];
        if( $note['M_optionsRepetitionMois'] == 1){
            $noteData['periode2'] = $note['M_jourDuMois'];
            $jour_du_mois = $noteData['periode2'];
        } else {
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
        $lejour = 0;
        $dernier_jour = array(0,0,0,0,0,0,0);
    for($i=0; $i<$nbOccurence; $i++){
        $do = true;
        $hD_time = mktime($Start_Note[0], $Start_Note[1], $Start_Note[2], $Date_Note[1], $Date_Note[0]+$Occurence , $Date_Note[2]);
        $hF_time = mktime($End_Note[0], $End_Note[1], $End_Note[2], $Date_Note[1], $Date_Note[0]+$Occurence , $Date_Note[2]);
        if($hD_time>$hF_time) $hF_time=$hD_time;

        if ($dateMax){
            if($hD_time>$dateMax)                break;
        }

        if($jours_Ouvrables){
            if (date("N",$hD_time) >5 ) {
                $do = false;
                $i--;
            }
        }

        if ($jours_dans_semaine) {
            $jour_choisi = false;
            $lejour = (int)date("N", $hD_time);

            foreach ($SemaineType as $value) {
                if( $lejour == $value){
                    $jour_choisi = true;
                    break;
                }
            }
            if($jour_choisi){
                $jour_choisi = false;
                $ecart = (int)$Toutes_les_X_semaines*7*24*60*60;
                if($dernier_jour[$lejour] === 0){
                    $jour_choisi = true;
                    $dernier_jour[$lejour] = $hD_time;
                }
                elseif (($hD_time - $dernier_jour[$lejour]) >= $ecart ) {
                    $jour_choisi = true;
                    $dernier_jour[$lejour] = $hD_time;
                }
                else {
                    $jour_choisi = false;
                }
            }

            if(!$jour_choisi){
                $i--;
                $do = false;
            }

        }

        if($Tous_les_X_mois){
            if($jour_du_mois){
                $hD_time = mktime($Start_Note[0], $Start_Note[1], $Start_Note[2], $Date_Note[1]+$Occurence, $jour_du_mois , $Date_Note[2]);
                $hF_time = mktime($End_Note[0], $End_Note[1], $End_Note[2], $Date_Note[1]+$Occurence, $jour_du_mois , $Date_Note[2]);
            }
            else {
                $cardinal = $noteData['periode2'];      // 1er - 2e -
                $jour_choisi = $noteData['periode3'];   // Mercredi => 3

                //  1er jour du mois timestamp
                $premierJour_time = mktime($Start_Note[0], $Start_Note[1], $Start_Note[2], $Date_Note[1]+$Occurence, 1 , $Date_Note[2]);
                $dernerJour_time = mktime($Start_Note[0], $Start_Note[1], $Start_Note[2], $Date_Note[1]+$Occurence+1, 1 , $Date_Note[2]);
                
                //  1er jour du mois est le Xe jour de semaine (lundi = 1)
                $premierJour = date("N", $premierJour_time);        // 5 
                $nb_jours_du_mois = date("t", $premierJour_time);    // 31
                $dernierJour = date("N", $dernerJour_time) - 1;

                if($cardinal<=4){
                    // Si jour choisi = 7 (dimanche) et premier jour du mois = 2 (mardi)
                    // date du jour = 7-2=5 => le 5 du mois est un dimanche
                    $ecart = $jour_choisi - $premierJour ;
                    if ($ecart<0) $ecart += 7;
                
                    $ecart = $ecart + 7*($cardinal-1);
                    
                    $hD_time = mktime($Start_Note[0], $Start_Note[1], $Start_Note[2], $Date_Note[1]+$Occurence, 1+$ecart , $Date_Note[2]);
                    $hF_time = mktime($End_Note[0], $End_Note[1], $End_Note[2], $Date_Note[1]+$Occurence, 1+$ecart , $Date_Note[2]);
            
                }
                else {
                    $ecart = $dernierJour - $jour_choisi ;
                    if ($ecart<0) $ecart += 7;
                    $hD_time = mktime($Start_Note[0], $Start_Note[1], $Start_Note[2], $Date_Note[1]+$Occurence, $nb_jours_du_mois-$ecart , $Date_Note[2]);
                    $hF_time = mktime($End_Note[0], $End_Note[1], $End_Note[2], $Date_Note[1]+$Occurence, $nb_jours_du_mois-$ecart , $Date_Note[2]);
                }
            }
        }

        $date_start = date("Y-m-d H:i:s",$hD_time);
        $date_end = date("Y-m-d H:i:s",$hF_time);

        if ($do) {

            $yap = new Modea\Domain\Note($noteData);
            $yap->setStart($date_start);
            $yap->setEnd($date_end);
            $yap->setUser_id($participant);
            if ($id_mere) $yap->setMere_id ($id_mere);
            $yep->save($yap);
            if(!$id_mere) $id_mere = $yap->getId ();
        }

            $Occurence += $Tous_les_X_jours;

    }
    }



echo 'Success';
}
?>