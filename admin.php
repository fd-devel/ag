<?php
// autoload des classes
function chargerClasse($classname)
{
  require '../../data/modeles/'.$classname.'.class.php'; 
}
spl_autoload_register('chargerClasse');

define('DIR_DB_CONFIG', '../../data/db-config.inc.php');

require './src/DAO/DAO.php';
require './src/DAO/AgendaDAO.php';
require './src/DAO/CategorieDAO.php';
require './src/Domain/categorie.class.php'; 

// Message info (alert) sur l'ajout d'une radio
//   ne sert qu'a initialiser la variable info_alert en php et en js.
$info_alert = '<script>info_alert=0</script>';

// Traitement des paramétres 
if(isset($_POST['paramGenAgenda_Go']) && $_POST['paramGenAgenda_Go']==1 ) {

    $keys = array('debut_journee', 'fin_journee',
        'format_nom', 'planning', 'semaine_type', 'precision_planning',
        'duree_note', 'rappel_delai', 'rappel_type', 'rappel_email');
    $agendaData = array_fill_keys($keys, '');
    
    
        // Si aucune option cochée => toutes les options sont affectées
    $ttesOptions = (!isset($_POST['selJournee'])  &&
       !isset($_POST['selFormatNom']) &&
        !isset($_POST['selPlanning']) &&
        !isset($_POST['selSemaineType']) &&
        !isset($_POST['selPrecisAffich']) &&
        !isset($_POST['selDureeNote']) &&
        !isset($_POST['selRappel']) &&
        !isset($_POST['selAccesProfils']) &&
        !isset($_POST['selAccesAgenda']) &&
        !isset($_POST['selAccesNotes']) 
        ) ? true : false;
  

    if(isset($_POST['selJournee']) || $ttesOptions){
        $agendaData['debut_journee'] = $_POST['NoteStartTime'];
        $agendaData['fin_journee'] = $_POST['NoteEndTime'];
    }
    if(isset($_POST['selFormatNom']) || $ttesOptions){
        $agendaData['format_nom'] = $_POST['aff_noms'];
    }
    if(isset($_POST['selPlanning']) || $ttesOptions){
        $agendaData['planning'] = $_POST['agenda_def'];
    }
    if(isset($_POST['selSemaineType']) || $ttesOptions){
        $agendaData['semaine_type'] = "";
        for($i=1; $i<=7;$i++){
            $agendaData['semaine_type'] .= isset($_POST['sem_typ_'.$i]) ? 1 : 0;
        }
    }
    if(isset($_POST['selPrecisAffich']) || $ttesOptions){
        $agendaData['precision_planning'] = $_POST['precis_aff'];
    }
    if(isset($_POST['selDureeNote']) ||$ttesOptions){
        $agendaData['duree_note'] = $_POST['duree_note'];
    }
    if(isset($_POST['selRappel']) || $ttesOptions){
        if(isset($_POST['optionRappel']) && $_POST['optionRappel']==1){
            $agendaData['rappel_delai'] = $_POST['tpsRappel'];
            $agendaData['rappel_type'] = $_POST['rappel_type'];
            $agendaData['rappel_email'] = (array_key_exists('rappel_email', $_POST)) ? 1 : 0 ;
        }else{
            $agendaData['rappel_delai'] = 0;
            $agendaData['rappel_type'] = 1;
            $agendaData['rappel_email'] = 0 ;
        }

    }
    /*        if($_POST['selAccesProfils']){
        $agendaData[''] = $_POST[''];
    }
    if($_POST['selAccesAgenda']){
        $agendaData[''] = $_POST[''];
    }
    if($_POST['selAccesNotes']){
        $agendaData[''] = $_POST[''];
    }
    * 
    */
    require 'src/Domain/agenda.class.php';
//    require 'src/DAO/DAO.php';
//    require 'src/DAO/AgendaDAO.php';
    $yep = new Modea\DAO\AgendaDAO();
    
    $selUsers = false;
    if( (isset($_POST['GrpSel']) && !empty($_POST['GrpSel'])) || (isset($_POST['UsersSel']) && !empty($_POST['UsersSel']))){
        $selUsers = true;
        $UsersToModif = array();
        require './src/Utilisateur_Groupe.extends.class.php';
        if(isset($_POST['GrpSel']) && !empty($_POST['GrpSel'])){
            $groupesSelect = explode('+',$_POST['GrpSel']);
            $unGroupe = new GroupAndUser();
            foreach ($groupesSelect as $Gs) {
//                $unGroupe = new Utilisateur_Groupe();
                $laListe = $unGroupe->getUtilisateurByGroupe($Gs);
                foreach ($laListe as $U_s => $I_d) {
                    array_push($UsersToModif, $I_d);
                }
            }
        }
        if(isset($_POST['UsersSel']) && !empty($_POST['UsersSel'])){
            $usersSelect = explode('+',$_POST['UsersSel']);
            foreach ($usersSelect as $Us) {
                array_push($UsersToModif, $Us);
            }
        }
        array_unique($UsersToModif);
    }
    
    if(!$selUsers){     // Si aucun utilisateur choisit => paramétrage par defaut 
        $yep->saveParam($agendaData);
    }  else {
        foreach ($UsersToModif as $user) {
            $agendaData['Id_user'] = $user;
            $yap = new Modea\Domain\Agenda($agendaData);
            $yep->save($yap);
        }
    }
        
	$erreur ="1";
        // Message de confirmation 
        $info_alert ='<script>info_alert='.$erreur.'</script>';
	
}

// Traitement des catégories
if(isset($_POST['yep']) 
        && (isset($_POST['text']) && !empty($_POST['text'] )) 
        && (isset($_POST['cp1']) && !empty($_POST['cp1'])) ){
    
    $add_cat['nom'] = $_POST['text'];
    $add_cat['couleur'] = $_POST['cp1'];
    $dao = new Modea\DAO\CategorieDAO();
    $obj = new Modea\Domain\Categorie($add_cat);
    $dao->save($obj);
}


//  Hearder
require "../header.php";

include 'admin/text_popover.php';

$yap = new Modea\DAO\AgendaDAO();
$params=$yap->findParam();

// Format d'affichage des noms
$checked_nom1 = ($params[3]['Default'] == 0) ? "checked" : "";
$checked_nom2 = ($params[3]['Default'] == 0) ? "" : "checked";

// Planning par default
$selected_planning1 = ($params[4]['Default'] == 1) ? "selected" : "";
$selected_planning2 = ($params[4]['Default'] == 2) ? "selected" : "";
$selected_planning3 = ($params[4]['Default'] == 3) ? "selected" : "";

// Partage planning

// Semaine type
list($sem_1, $sem_2, $sem_3, $sem_4, $sem_5, $sem_6, $sem_7) = str_split($params[5]['Default']);
$sem_typ1 = $sem_1 == 0 ? "" : "checked";
$sem_typ2 = $sem_2 == 0 ? "" : "checked";
$sem_typ3 = $sem_3 == 0 ? "" : "checked";
$sem_typ4 = $sem_4 == 0 ? "" : "checked";
$sem_typ5 = $sem_5 == 0 ? "" : "checked";
$sem_typ6 = $sem_6 == 0 ? "" : "checked";
$sem_typ7 = $sem_7 == 0 ? "" : "checked";

// Precision d'affichage
$precis1 = ($params[6]['Default'] == 1) ? "selected" : "";
$precis2 = ($params[6]['Default'] == 1) ? "" : "selected";

// Duree par efaut d'une note
$duree1 = ($params[7]['Default'] == 1) ? "selected" : "";
$duree2 = ($params[7]['Default'] == 2) ? "selected" : "";
$duree3 = ($params[7]['Default'] == 3) ? "selected" : "";
$duree4 = ($params[7]['Default'] == 4) ? "selected" : "";

// Rappel
$rappel1 = ($params[8]['Default'] == 0) ? "checked" : "";
$rappel2 = ($params[8]['Default'] > 0) ? "checked" : "";

$rappelTps = 5;
if($params[8]['Default'] > 0) $rappelTps = $params[8]['Default'];


$rappelTyp1 = ($params[9]['Default'] == 1) ? "selected" : "";
$rappelTyp2 = ($params[9]['Default'] == 60) ? "selected" : "";
$rappelTyp3 = ($params[9]['Default'] == 1440) ? "selected" : "";

$rappelMail = ($params[10]['Default'] == 1) ? "checked" : "";

// Info alerte de l'ajout d'une nouvelle radio
echo $info_alert;

// ----------------------------------------------------------------------------
// FORMATAGE DES HEURES POUR L'AFFICHAGE
// ----------------------------------------------------------------------------
function afficheHeure($heure, $minute, $format = "H:i") {
    return date($format, mktime($heure, ($minute * 60) % 60, 0, 1, 1, 2000));
}

// ----------------------------------------------------------------------------
?>

<link href="css/style.css" rel="stylesheet">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.0/css/bootstrap-toggle.min.css" rel="stylesheet">


<div class="container-fluid">
    <!-- HEADER -->
    <div class="page_titre bg-danger">
        Administration Agenda 
    </div>
    <ol class="breadcrumb danger square rsaquo sm">
        <li class="active"><i class="fa fa-home"></i></li>
        <li class="active">ADMINISTRATION</li>        
        <li class="active"><a href="../admin/modules.php">Modules</a></li>
        <li class="active">Agenda</li>
    </ol>
    <!-- FIN HEADER -->

   
   <!-- Contenu de la page -->
    <div class="row" style="margin-left:-30px;margin-right:-30px;margin-top:-20px;">
        <div class="col-md-12">
            <div class="the-box no-border">
                <h2>Paramétrages</h2>
            </div>
        </div>
    </div>
	
	<!-- LISTE -->
    <div class="panel panel-primary">
        <div class="panel-heading " style="padding:10px 15px;">
            <div class="row">
                <div class="col-xs-7">
                    <h3 class="panel-title">
                        <i class="fa fa-info" style="margin-right:10px"></i>
                        Paramètres par défaut des utilisateurs
                    </h3>
                </div>
                <div class="col-xs-3 col-sm-1 pull-right ">
                    <button class="btn btn-warning btn-minimize">
                        <a class="" href="#"><i class="fa fa-chevron-up"></i></a>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="panel-body" id="body-liste_radios">
            <form action="admin.php" id="paramGenAgenda" name="paramGenAgenda" method="POST">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Paramètres</th>
                        <th>Valeurs</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="pad-15">
                                <input type="checkbox" value="1" name="selJournee" >
                            </div>
                        </td>
                        <td>
                            <div class='alert alert-info alert-bold-border'>
                                <h4>Journée type</h4>
                            </div>
                        </td>
                        <td>
                            <div class="pad-15">
                                Débute à
                                <select class="form-control" id="NoteStartTime" name="NoteStartTime" style="display:inline; width:auto;">
       <?php
for ($i = 0; $i <= 23.5; $i = $i + 0.5) {
    //		$selected = ($i == $rsProfil['util_debut_journee']) ? " selected" : "";
    $selected = ($i == $params[1]['Default']) ? " selected" : "";
    echo '<option value="' . $i . '" ' . $selected . '>' . afficheHeure($i, $i) . '</option>';
}
?>
                                </select>
                            
                                Se termine à
                                <select class="form-control" id="NoteEndTime" name="NoteEndTime" style="display:inline; width:auto;">
        <?php
for ($i = 0; $i <= 23.5; $i = $i + 0.5) {
//		$selected = ($i == $rsProfil['util_debut_journee']) ? " selected" : "";
     $selected = ($i == $params[2]['Default']) ? " selected" : "";
     echo '<option value="' . $i . '" ' . $selected . '>' . afficheHeure($i, $i) . '</option>';
}
?>
                                </select>
                            </div>
                        </td>
                        <td >
                            <!-- ( Info ) popover  -->
                            <div class="pad-15">
                                <button id="pop1" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $pop1_titre; ?>" data-content="<?php echo $pop1_coprs; ?>" >
                                    <i class="fa fa-info-circle"></i>
                                </button>
                            </div>
                            <!-- ( Info ) popover  FIN -->
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pad-15">
                                <input type="checkbox" value="1" name="selFormatNom">
                            </div>
                        </td>
                        <td>
                            <div class="alert alert-info alert-bold-border">
                                <h4>Affichage des noms</h4>
                            </div>
                        </td>
                        <td>
                            <div class="pad-15">
                                <label for="aff_noms_1"><input type="radio" name="aff_noms" id="aff_noms_1" value="0" <?php echo $checked_nom1; ?>>Nom prénom</label>
                                <label for="aff_noms_2"><input type="radio" name="aff_noms" id="aff_noms_2" value="1" <?php echo $checked_nom2; ?>>Prénom Nom</label>
                            </div>
                        </td>
                        <td >
                            <!-- ( Info ) popover  -->
                            <div class="pad-15">
                                <button id="pop2" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $pop2_titre; ?>" data-content="<?php echo $pop2_coprs; ?>" >
                                    <i class="fa fa-info-circle"></i>
                                </button>
                            </div>
                            <!-- ( Info ) popover  FIN -->
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pad-15">
                                <input type="checkbox" value="1" name="selPlanning">
                            </div>
                        </td>
                        <td>
                            <div class="alert alert-info alert-bold-border">
                                <h4>Agenda par défaut</h4>
                            </div>
                        </td>
                        <td>
                            <div class="pad-15">
                                <select class="form-control" id="agenda_def" name="agenda_def" style="display:inline; width:auto;" >
                                    <option <?php echo $selected_planning1; ?> value="1">Quotidien</option>
                                    <option <?php echo $selected_planning2; ?> value="2">Hebdomadaire</option>
                                    <option <?php echo $selected_planning3; ?> value="3">Mensuel</option>
                                </select>
                            </div>
                        </td>
                        <td >
                            <!-- ( Info ) popover  -->
                            <div class="pad-15">
                                <button id="pop3" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $pop3_titre; ?>" data-content="<?php echo $pop3_coprs; ?>" >
                                    <i class="fa fa-info-circle"></i>
                                </button>
                            </div>
                            <!-- ( Info ) popover  FIN -->
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pad-15">
                                <input type="checkbox" value="1" name="selSemaineType">
                            </div>
                        </td>
                        <td>
                            <div class="alert alert-info alert-bold-border">
                                <h4>Semaine type</h4>
                            </div>
                        </td>
                        <td>
                            <div class="pad-15">
<?php
$JoursCourts = array('','Lun','Mar','Mer','Jeu','Ven','Sam','Dim');
for ($i=1; $i<=7; $i++){
    $sem='sem_typ'.$i;
    echo '<label for="sem_typ_'.$i.'"><input type="checkbox" name="sem_typ_'.$i.'" id="sem_typ_'.$i.'" value="'.$i.'" '.$$sem.'> '.$JoursCourts[$i].' </label>';
}
?>
                                
                            </div>
                        </td>
                        <td >
                            <!-- ( Info ) popover  -->
                            <div class="pad-15">
                                <button id="pop4" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $pop4_titre; ?>" data-content="<?php echo $pop4_coprs; ?>" >
                                    <i class="fa fa-info-circle"></i>
                                </button>
                            </div>
                            <!-- ( Info ) popover  FIN -->
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pad-15">
                                <input type="checkbox" value="1" name="selPrecisAffich">
                            </div>
                        </td>
                        <td>
                            <div class="alert alert-info alert-bold-border">
                                <h4>Précision d'affichage</h4>
                            </div>
                        </td>
                        <td>
                            <div class="pad-15">
                                <select class="form-control" id="precis_aff" name="precis_aff" style="width:auto;" >
                                    <option <?php echo $precis1; ?> value="1">30 minutes</option>
                                    <option <?php echo $precis2; ?> value="2">15 minutes</option>
                                </select>
                            </div>
                        </td>
                        <td >
                            <!-- ( Info ) popover  -->
                            <div class="pad-15">
                                <button id="pop5" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $pop5_titre; ?>" data-content="<?php echo $pop5_coprs; ?>" >
                                    <i class="fa fa-info-circle"></i>
                                </button>
                            </div>
                            <!-- ( Info ) popover  FIN -->
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pad-15">
                                <input type="checkbox" value="1" name="selDureeNote">
                            </div>
                        </td>
                        <td>
                            <div class="alert alert-info alert-bold-border">
                                <h4>Durée d'une note</h4>
                            </div>
                        </td>
                        <td>
                            <div class="pad-15">
                                <select class="form-control" id="duree_note" name="duree_note" style="width:auto;" >
<?php
$dureeTxt = array('','15 minutes','30 minutes','45 minutes','1 heure');
for ($i=1; $i<=4; $i++){
    $duree='duree'.$i;
    echo '<option '.$$duree.' value="'.$i.'"> '.$dureeTxt[$i].' </option>';
}
?>
                                </select>
                            </div>
                        </td>
                        <td >
                            <!-- ( Info ) popover  -->
                            <div class="pad-15">
                                <button id="pop6" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $pop6_titre; ?>" data-content="<?php echo $pop6_coprs; ?>" >
                                    <i class="fa fa-info-circle"></i>
                                </button>
                            </div>
                            <!-- ( Info ) popover  FIN -->
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pad-15">
                                <input type="checkbox" value="1" name="selRappel">
                            </div>
                        </td>
                        <td>
                            <div class="alert alert-info alert-bold-border">
                                <h4>Rappel par défault </br> à la création d'une note</h4>
                            </div>
                        </td>
                        <td>
                            <div class="pad-15">
                            <div >
                                <label style="padding:0;">
                                    <input type="radio" name="optionRappel" id="optionRappel1" value="0" <?php echo $rappel1; ?>>
                                    Pas de rappel
                                </label>
                            </div>
                            <div>
                                <label style="padding:0;">
                                    <input type="radio" name="optionRappel" id="optionRappel2" value="1" <?php echo $rappel2; ?> >
                                    rappel
                                <select class="form-control" id="tpsRappel" name="tpsRappel" style="display:inline; width:auto;" onfocus="document.getElementById('optionRappel2').checked = 'true'">
   <?php
for ($i = 1; $i <= 59; $i++) {
$selected = ($i == $rappelTps) ? 'selected=""' : '';
echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
}
?>
                                </select>
                                <select class="form-control" id="rappel_type" name="rappel_type" style="display:inline; width:auto;" onfocus="document.getElementById('optionsRappel2').checked = 'true'">
                                    <option value="1" <?php echo $rappelTyp1; ?>>minute(s)</option>
                                    <option value="60" <?php echo $rappelTyp2; ?>>heure(s)</option>
                                    <option value="1440" <?php echo $rappelTyp3; ?>>jour(s)</option>
                                </select>
                                    avant.
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="rappel_email" name="rappel_email" value="1" <?php echo $rappelMail; ?>>Copie par mail
                                </label>
                            </div>
                            </div>
                            
                        </td>
                        <td >
                            <!-- ( Info ) popover  -->
                            <div class="pad-15">
                                <button id="pop7" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $pop7_titre; ?>" data-content="<?php echo $pop7_coprs; ?>" >
                                    <i class="fa fa-info-circle"></i>
                                </button>
                            </div>
                            <!-- ( Info ) popover  FIN -->
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pad-15">
                                <input type="checkbox" value="1" name="selAccesProfils">
                            </div>
                        </td>
                        <td>
                            <div class="alert alert-info alert-bold-border">
                                <h4>Accès aux profils</h4>
                            </div>
                        </td>
                        <td>
                        </td>
                        <td >
                            <!-- ( Info ) popover  -->
                            <div class="pad-15">
                                <button id="pop8" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $pop8_titre; ?>" data-content="<?php echo $pop8_coprs; ?>" >
                                    <i class="fa fa-info-circle"></i>
                                </button>
                            </div>
                            <!-- ( Info ) popover  FIN -->
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pad-15">
                                <input type="checkbox" value="1" name="selAccesAgenda">
                            </div>
                        </td>
                        <td>
                            <div class="alert alert-info alert-bold-border">
                                <h4>Accès aux agendas</h4>
                            </div>
                        </td>
                        <td>
                        </td>
                        <td >
                            <!-- ( Info ) popover  -->
                            <div class="pad-15">
                                <button id="pop9" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $pop9_titre; ?>" data-content="<?php echo $pop9_coprs; ?>" >
                                    <i class="fa fa-info-circle"></i>
                                </button>
                            </div>
                            <!-- ( Info ) popover  FIN -->
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pad-15">
                                <input type="checkbox" value="1" name="selAccesNotes">
                            </div>
                        </td>
                        <td>
                            <div class="alert alert-info alert-bold-border">
                                <h4>Accès aux notes</h4>
                            </div>
                        </td>
                        <td>
                        </td>
                        <td >
                            <!-- ( Info ) popover  -->
                            <div class="pad-15">
                                <button id="pop10" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $pop10_titre; ?>" data-content="<?php echo $pop10_coprs; ?>" >
                                    <i class="fa fa-info-circle"></i>
                                </button>
                            </div>
                            <!-- ( Info ) popover  FIN -->
                        </td>
                    </tr>
                </tbody>
            </table>
                
 <!-------------   GROUPES   -------------------------------->
    <div class="the-box no-border">
        <h4 class="small-title">APPLIQUER AUX GROUPES</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading" style="padding:10px 15px;">
                        <h3 class="panel-title">
                            <i class="fa fa-check" style="margin-right:10px"></i>
                            Groupes
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="list-group success">
                            <?php
$Liste_Groupe=array();
$Groupes = new Groupe();
$Liste_Groupe=$Groupes->getGroupeList();

if (empty($Liste_Groupe))
{
                            ?>
                            <a class="list-group-item mail-list" style="padding-left:15px;padding-right:0">
                                <div class="row">
                                    <span class="subject">Aucun groupe créé</span>
                                </div>
                            </a>
<?php
}
else
{
?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <select class="form-control"  multiple="" size="8" id="groupes" name="groupes">
<?php
foreach($Liste_Groupe as $groupe)
    {
    echo "<option value=".$groupe['id'].">".$groupe['nom'] ."</option>";  
    }
?>
                                        </select>
                                    </div>
                                </div>
                            
<?php

}

?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1">
                <div class="panel ">
                    <div class="panel-heading" style="padding:10px 15px;">
                        <h3 class="panel-title">
                            <i class="fa fa-ban" style="margin-right:10px"></i>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <table class="text-center">
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-success btn-lg" id="btSelect" onclick="javascript: selectUtil(document.paramGenAgenda.groupes, document.paramGenAgenda.groupesSelect);" title="Ajouter la sélection" name="btSelect">
                                        <i class="fa fa-chevron-right"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-success btn-lg" id="btSelect" onclick="javascript: selectAll(document.paramGenAgenda.groupes, document.paramGenAgenda.groupesSelect);" title="Ajouter Tous" name="btSelect">
                                        <i class="fa fa-arrow-right"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-success btn-lg" id="btDeselect" onclick="javascript: selectUtil(document.paramGenAgenda.groupesSelect, document.paramGenAgenda.groupes);" title="Enlever la sélection" name="btDeselect">
                                        <i class="fa fa-chevron-left"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-success btn-lg" id="btDeselect" onclick="javascript: selectAll(document.paramGenAgenda.groupesSelect, document.paramGenAgenda.groupes);" title="Enlever Tous" name="btDeselect">
                                        <i class="fa fa-arrow-left"></i>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="panel panel-success">
                    <div class="panel-heading" style="padding:10px 15px;">
                        <h3 class="panel-title">
                            <i class="fa fa-ban" style="margin-right:10px"></i>
                            Appliquer aux groupes
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="list-group success">
                            <div class="row">
                                <div class="col-md-12">
                                    <select class="form-control"  multiple="" size="8" id="groupesSelect" name="groupesSelect">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <!--------------------------------------------->
                 
 <!-------------   UTILISATEURS   -------------------------------->
    <div class="the-box no-border">
        <h4 class="small-title">APPLIQUER AUX UTILISATEURS</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading" style="padding:10px 15px;">
                        <h3 class="panel-title">
                            <i class="fa fa-check" style="margin-right:10px"></i>
                            Utilisateurs
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="list-group success">
                            <?php
$Liste_Utilisateurs=array();
$Utilisateurs = new User();
$Liste_Utilisateurs=$Utilisateurs->getUserList();

if (empty($Liste_Utilisateurs))
{
                            ?>
                            <a class="list-group-item mail-list" style="padding-left:15px;padding-right:0">
                                <div class="row">
                                    <span class="subject">Aucun utilisateur disponible</span>
                                </div>
                            </a>
<?php
}
else
{
?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <select class="form-control"  multiple="" size="8" id="utilisateurs" name="utilisateurs">
<?php
foreach($Liste_Utilisateurs as $utilisateur)
    {
    echo "<option value=".$utilisateur['id'].">".$utilisateur['nom'] ." ".$utilisateur['prenom']."</option>";  
    }
?>
                                        </select>
                                    </div>
                                </div>
                            
<?php

}

?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1">
                <div class="panel ">
                    <div class="panel-heading" style="padding:10px 15px;">
                        <h3 class="panel-title">
                            <i class="fa fa-ban" style="margin-right:10px"></i>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <table class="text-center">
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-success btn-lg" id="btSelect" onclick="javascript: selectUtil(document.paramGenAgenda.utilisateurs, document.paramGenAgenda.utilisateursSelect);" title="Ajouter la sélection" name="btSelect">
                                        <i class="fa fa-chevron-right"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-success btn-lg" id="btSelect" onclick="javascript: selectAll(document.paramGenAgenda.utilisateurs, document.paramGenAgenda.utilisateursSelect);" title="Ajouter Tous" name="btSelect">
                                        <i class="fa fa-arrow-right"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-success btn-lg" id="btDeselect" onclick="javascript: selectUtil(document.paramGenAgenda.utilisateursSelect, document.paramGenAgenda.utilisateurs);" title="Enlever la sélection" name="btDeselect">
                                        <i class="fa fa-chevron-left"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-success btn-lg" id="btDeselect" onclick="javascript: selectAll(document.paramGenAgenda.utilisateursSelect, document.paramGenAgenda.utilisateurs);" title="Enlever Tous" name="btDeselect">
                                        <i class="fa fa-arrow-left"></i>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="panel panel-success">
                    <div class="panel-heading" style="padding:10px 15px;">
                        <h3 class="panel-title">
                            <i class="fa fa-ban" style="margin-right:10px"></i>
                            Appliquer aux utilisateurs
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="list-group success">
                            <div class="row">
                                <div class="col-md-12">
                                    <select class="form-control"  multiple="" size="8" id="utilisateursSelect" name="utilisateursSelect">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <!--------------------------------------------->
        <button type="submit" class="btn btn-success">Pas celui-là --></button>
        <input type="button" name="btn" value="Valider" id="submitBtn" data-toggle="modal" data-target="#confirm-submit" class="btn btn-default" />
        <input type="hidden" name="paramGenAgenda_Go" id="paramGenAgenda_Go" value="0">
        <input type="hidden" name="GrpSel" id="GrpSel" value="0">
        <input type="hidden" name="UsersSel" id="UsersSel" value="0">
            </form>
        </div>
    </div>
        <!-- Fin LISTE -->
	
	<!-- CATEGORIES ET COULEURS -->
        
<?php



    $couleur = "#8fdf82";
    $newCat = new Modea\DAO\CategorieDAO();
    $categories = $newCat->findAll();
?>
        <div class="panel panel-warning" style="margin-bottom: 80px">
        <div class="panel-heading" style="padding:10px 15px;">
            <h3 class="panel-title">
                <i class="fa fa-info" style="margin-right:10px"></i>
                Categories et couleurs des notes
            </h3>
        </div>
        
        <div class="panel-body">
            <fieldset>
                <div class="row">
<?php
if($categories){
    foreach ($categories as $categorie) {

        echo      ' <div class="col-xs-offset-1 col-xs-5 ">';
        echo      '     <input type="text" disabled="" value="'. $categorie['nom'] .' " style="background-color:' . $categorie['couleur'] .' ;  " />' ;
        echo      '     <a onclick="javascript:valider_suppression('. $categorie['id']. ' ,this);" data-toggle="tooltip" title="Supprimer" class="btn btn-warning" ><i class="fa fa-trash-o"></i></a>';
        echo      ' </div> ';          
    }
}
 else {
    echo '<div class="col-xs-offset-1 col-xs-10">';
    echo '<h4> Pas de catégorie.</h4>';
    echo '</div>';
}
?>
                </div>
            </fieldset>
            <form name="Nvelle_categorie" action="admin.php" method="post" >
                <fieldset>
                    <legend>Nouvelle categorie</legend>
                    <div class="row  " >
                        <div class="col-md-offset-1 col-md-4 col-xs-12">
                            <label class="control-label" for="appons1">categorie </label>
                            <input class="form-control focused" id="appons1" name="text" type="text" placeholder="Nom de la catégorie">
                        </div>
                        <div class="col-md-6 col-xs-12">
                            <label class="control-label" for="cp1" style="display: block;">Couleurs</label>
                            <input id="cp1" name="cp1" class="color pad-10 " value="<?php echo $couleur; ?>" placeholder="<?php echo $couleur; ?>" />
                        </div>
                        <div class="col-md-offset-2 col-md-7 col-xs-12" >
                            <label class="control-label" for="appons4" style="display: block;">Aperçu </label>
                            <input type="text" class="pad-10" id="appercu" disabled="" value="test" style="background-color: <?php echo $couleur; ?>;" />
                            
                            
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success"> Créer </button>
                    <input type="hidden" name="yep" value="1">
                </fieldset>
            </form>
        </div>
    </div>
        <!-- Fin AJOUT -->
</div>

    <!-- FIN Contenu de la page -->
</div>

<?php
// Chargement du footer
include "../footer.php";
?>
<!-- Javascript 
<script src="modeles/admin.js" ></script>-->
<script src="js/functions.js"></script>
<script src="js/admin.js"></script>

<!--??????????????????-->
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.0/js/bootstrap-toggle.min.js"></script>  
<!--?????????????????-->

<script src="js/jqColorPicker.min.js"></script>
<script type="text/javascript">

    var $colors = $('input.color').colorPicker({
    customBG: '#222',
    readOnly: true,
    init: function(elm, colors) { // colors is a different instance (not connected to colorPicker)
      elm.style.backgroundColor = elm.value;
      elm.style.color = colors.rgbaMixCustom.luminance > 0.22 ? '#222' : '#ddd';
    }
  }).each(function(idx, elm) {
		 $(elm).css({'background-color': this.value})
	});
</script>
<?php
// Modale ==> Validation
include "admin/modal_submit.php";
