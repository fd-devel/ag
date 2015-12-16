<?php
// autoload des classes
function chargerClasse($classname)
{
  require '../../data/modeles/'.$classname.'.class.php'; 
}
spl_autoload_register('chargerClasse');


require './src/DAO/DAO.php';
require './src/DAO/AgendaDAO.php';

// Message info (alert) sur l'ajout d'une radio
//   ne sert qu'a initialiser la variable info_alert en php et en js.
$info_alert = '<script>info_alert=0</script>';

// Traitement de l'ajout
if(isset($_POST['paramsGenAgenda_Go']) ) {
    
    $keys = array('debut_journee', 'fin_journee',
        'format_nom', 'planning', 'semaine_type', 'precision_planning',
        'duree_note', 'rappel_delai', 'rappel_type', 'rappel_email');
    $agendaData = array_fill_keys($keys, '');
    
    
        // Si aucune option cochée => toutes les options sont affectées
    $ttesOptions = (isset($_POST['selJournee'])  &&
       isset($_POST['selFormatNom']) &&
        isset($_POST['selPlanning']) &&
        isset($_POST['selSemaineType']) &&
        isset($_POST['selPrecisAffich']) &&
        isset($_POST['selDureeNote']) &&
        isset($_POST['selRappel']) &&
        isset($_POST['selAccesProfils']) &&
        isset($_POST['selAccesAgenda']) &&
        isset($_POST['selAccesNotes']) 
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
        if($_POST['optionRappel']==2){
            $agendaData['rappel_delai'] = $_POST['tpsRappel'];
            $agendaData['rappel_type'] = $_POST['rappel_coef'];
            $agendaData['rappel_email'] = (array_key_exists('option_mail', $_POST)) ? 1 : 0 ;
        }else{
            $agendaData['rappel_delais'] = 0;
            $agendaData['rappel_type'] = 0;
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
    if(!$selUsers){     // Si aucun utilisateur choisit => paramétrage par defaut 
          
        $yep->saveParam($agendaData);
    
  
    }  else {
        
    $yap = new Modea\Domain\Agenda($agendaData);
        $yep->save($yap);
    }
        
	$erreur ="";
        // Message de confirmation 
        $info_alert ='<script>info_alert='.$erreur.'</script>';
	
}


// Textes des Popovers
//
$journTp_pop_titre = "<div class=' panel-heading bg-success'> Journée type :</div>";
$journTp_pop_corps = "Horaires début et fin de la journée de base.";

//  Hearder
require "../header.php";


$yap = new Modea\DAO\AgendaDAO();
$params=$yap->findParam();

// Format d'affichage des noms
$checked_nom1 = ($params[3]['Default'] == 0) ? "checked" : "";
$checked_nom2 = ($params[3]['Default'] == 0) ? "" : "checked";

// Planning par default
$selected_planning1 = ($params[4]['Default'] == 0) ? "selected" : "";
$selected_planning2 = ($params[4]['Default'] == 1) ? "selected" : "";
$selected_planning3 = ($params[4]['Default'] == 2) ? "selected" : "";

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
                                <button id="eee" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $journTp_pop_titre; ?>" data-content="<?php echo $journTp_pop_corps; ?>" >
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
                                <label for="aff_noms_1"><input type="radio" name="aff_noms" id="aff_noms_1" value="1" <?php echo $checked_nom1; ?>>Nom prénom</label>
                                <label for="aff_noms_2"><input type="radio" name="aff_noms" id="aff_noms_2" value="2" <?php echo $checked_nom2; ?>>Prénom Nom</label>
                            </div>
                        </td>
                        <td >
                            <!-- ( Info ) popover  -->
                            <div class="pad-15">
                                <button id="eee" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $journTp_pop_titre; ?>" data-content="<?php echo $journTp_pop_corps; ?>" >
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
                                <button id="eee" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $journTp_pop_titre; ?>" data-content="<?php echo $journTp_pop_corps; ?>" >
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
                                <label for="sem_typ_1"><input type="checkbox" name="sem_typ_1" id="sem_typ_1" value="1" <?php echo $sem_typ1; ?>> Lun </label>
                                <label for="sem_typ_2"><input type="checkbox" name="sem_typ_2" id="sem_typ_2" value="2" <?php echo $sem_typ2; ?>> Mar </label>
                                <label for="sem_typ_3"><input type="checkbox" name="sem_typ_3" id="sem_typ_3" value="3" <?php echo $sem_typ3; ?>> Mer </label>
                                <label for="sem_typ_4"><input type="checkbox" name="sem_typ_4" id="sem_typ_4" value="4" <?php echo $sem_typ4; ?>> Jeu </label>
                                <label for="sem_typ_5"><input type="checkbox" name="sem_typ_5" id="sem_typ_5" value="5" <?php echo $sem_typ5; ?>> Ven </label>
                                <label for="sem_typ_6"><input type="checkbox" name="sem_typ_6" id="sem_typ_6" value="6" <?php echo $sem_typ6; ?>> Sam </label>
                                <label for="sem_typ_7"><input type="checkbox" name="sem_typ_7" id="sem_typ_7" value="7" <?php echo $sem_typ7; ?>> Dim </label>
                            </div>
                        </td>
                        <td >
                            <!-- ( Info ) popover  -->
                            <div class="pad-15">
                                <button id="eee" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $journTp_pop_titre; ?>" data-content="<?php echo $journTp_pop_corps; ?>" >
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
                                <select class="form-control" id="agenda_def" name="precis_aff" style="width:auto;" >
                                    <option selected="" value="1">30 minutes</option>
                                    <option value="2">15 minutes</option>
                                </select>
                            </div>
                        </td>
                        <td >
                            <!-- ( Info ) popover  -->
                            <div class="pad-15">
                                <button id="eee" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $journTp_pop_titre; ?>" data-content="<?php echo $journTp_pop_corps; ?>" >
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
                                    <option selected="" value="1">15 minutes</option>
                                    <option value="2">30 minutes</option>
                                    <option value="2">45 minutes</option>
                                    <option value="2">1 heure</option>
                                </select>
                            </div>
                        </td>
                        <td >
                            <!-- ( Info ) popover  -->
                            <div class="pad-15">
                                <button id="eee" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $journTp_pop_titre; ?>" data-content="<?php echo $journTp_pop_corps; ?>" >
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
                                <label style="padding:0;"><input type="radio" name="optionsRappel" id="optionsRappel1" value="1" checked>
                                Pas de rappel
                                </label>
                            </div>
                            <div>
                                <label style="padding:0;">
                                <input type="radio" name="optionsRappel" id="optionsRappel2" value="2" >
                                rappel
                                <select class="form-control" id="tpsRappel" name="tpsRappel" style="display:inline; width:auto;" onfocus="document.getElementById('optionsRappel2').checked = 'true'">
   <?php
for ($i = 1; $i <= 59; $i++) {
$selected = ($i == 5) ? 'selected=""' : '';
echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
}
?>
                                </select>
                                <select class="form-control" id="rappel_coef" name="rappel_coef" style="display:inline; width:auto;" onfocus="document.getElementById('optionsRappel2').checked = 'true'">
                                    <option value="1">minute(s)</option>
                                    <option value="60">heure(s)</option>
                                    <option value="1440">jour(s)</option>
                                </select>
                                    avant.
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="ckEmail" name="option_mail" value="1">Copie par mail
                                </label>
                            </div>
                            </div>
                            
                        </td>
                        <td >
                            <!-- ( Info ) popover  -->
                            <div class="pad-15">
                                <button id="eee" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $journTp_pop_titre; ?>" data-content="<?php echo $journTp_pop_corps; ?>" >
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
                                <button id="eee" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $journTp_pop_titre; ?>" data-content="<?php echo $journTp_pop_corps; ?>" >
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
                                <button id="eee" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $journTp_pop_titre; ?>" data-content="<?php echo $journTp_pop_corps; ?>" >
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
                                <button id="eee" type="button" class="btn btn-default" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="left" title="<?php echo $journTp_pop_titre; ?>" data-content="<?php echo $journTp_pop_corps; ?>" >
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
        <input type="hidden" name="paramsGenAgenda_Go" value="1">
            </form>
        </div>
    </div>
        <!-- Fin LISTE -->
	
	<!-- AJOUT -->
    <div class="panel panel-warning">
        <div class="panel-heading" style="padding:10px 15px;">
            <h3 class="panel-title">
                <i class="fa fa-info" style="margin-right:10px"></i>
                Ajouter une nouvelle station de radio ?
            </h3>
        </div>
        
        <div class="panel-body">
            <form name="Nvelle_radio" action="admin.php" method="post" enctype="multipart/form-data">
                <fieldset>
                    <legend>Nouvelle radio</legend>
                    <div class="row input-group " style="display: block;">
                        <div class="col-md-offset-1 col-md-3 col-xs-12">
                            <label class="control-label" for="appons1">Nom </label>
                            <input class="form-control focused" id="appons1" name="nom" type="text" placeholder="Nom de la radio">
                        </div>
                        <div class="col-md-offset-1 col-md-7 col-xs-12">
                            <label class="control-label" for="appons2">Adresse url</label>
                            <input class="form-control " id="appons2" name="url" type="text" placeholder="Url complète ex : http://radio.com/stream">
                        </div>
                        <div class="col-md-offset-1 col-md-4 col-xs-12">
                            <label class="control-label" for="appons3">Logo </label>
                            <input type="hidden" name="MAX_FILE_SIZE" value="500000" />
                            <input id="appons3" class="form-control" type="text" placeholder="Image...(max:500Ko)" readonly="">
                            <span class="input-group-btn">
                                <span class="btn btn-default btn-file">Parcourir…<input type="file" name="logo_file" id="imgInp"></span>
                            </span>
                        </div>
                        <div class="col-md-offset-2 col-md-5 col-xs-12" >
                            <label class="control-label" for="appons4" style="display: block;">Aperçu </label>
                            <img class="img-thumbnail" id="appons4" alt="Aperçu Image" src="../../assets/img/img-file/img.png" Style="width:100px;height:100px;text-align:center;">
                            
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Oui c'est bon, vas'y</button>
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
// Modale ==> Validation
include "admin/modal_submit.php";
?>


<!-- Javascript -->
<script src="modeles/admin.js" ></script>
<script>
    
    /* Fonction de manupulation des listes dans les balises <selec t> */
    function genereListe(_liste, _tabTexte, _tabValue, _tailleTab) {
        for (var i = 0; i < _tailleTab; i++)
            _liste.options[i] = new Option(_tabTexte[i], _tabValue[i]);
    }

    function bubbleSort(_tabText, _tabValue, _tailleTab) {
        var i, s;

        do {
            s = 0;
            for (i = 1; i < _tailleTab; i++)
                if (_tabText[i - 1] > _tabText[i]) {
                    y = _tabText[i - 1];
                    _tabText[i - 1] = _tabText[i];
                    _tabText[i] = y;
                    y = _tabValue[i - 1];
                    _tabValue[i - 1] = _tabValue[i];
                    _tabValue[i] = y;
                    s = 1;
                }
        } while (s);
    }

    function videListe(_liste) {
        var cpt = _liste.options.length;

        for (var i = 0; i < cpt; i++) {
            _liste.options[0] = null;
        }
    }

    function selectUtil(_listeSource, _listeDest) {
        var i, j;
        var ok = false;
        var tabDestTexte = new Array();
        var tabDestValue = new Array();
        var tailleTabDest = 0;

        for (i = 0; i < _listeDest.options.length; i++) {
            tabDestTexte[tailleTabDest] = _listeDest.options[i].text;
            tabDestValue[tailleTabDest++] = _listeDest.options[i].value;
        }

        for (j = _listeSource.options.length - 1; j >= 0; j--) {
            if (_listeSource.options[j].selected) {
                ok = true;
                tabDestTexte[tailleTabDest] = _listeSource.options[j].text;
                tabDestValue[tailleTabDest++] = _listeSource.options[j].value;
                _listeSource.options[j] = null;
            }
        }

        if (ok) {
            //Trie du tableau
            bubbleSort(tabDestTexte, tabDestValue, tailleTabDest);
            //Vide la liste destination
            videListe(_listeDest);
            //Recree la liste
            genereListe(_listeDest, tabDestTexte, tabDestValue, tailleTabDest);
        }
    }

    //Fonction pour selectionner tous les utilisateurs d'une liste source et les transferer dans une liste destination
    function selectAll(_listeSource, _listeDest) {
        for (var i = 0; i < _listeSource.options.length; i++) {
            _listeSource.options[i].selected = true;
        }
        selectUtil(_listeSource, _listeDest);
    }

    function recupSelection(_liste, _champ) {
        _champ.value = "";
        for (var i = 0; i < _liste.options.length; i++) {
            _champ.value += ((i) ? "+" : "") + _liste.options[i].value;
        }
    }
</script>
