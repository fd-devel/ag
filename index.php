<?php
// autoload des classes.
function chargerClasse($classname)
{
  require '../../data/modeles/'.$classname.'.class.php'; 
}
spl_autoload_register('chargerClasse');


// Header (stronger - faster - better)
include "../header.php";


// ----------------------------------------------------------------------------
// FORMATAGE DES HEURES POUR L'AFFICHAGE
// ----------------------------------------------------------------------------
function afficheHeure($heure,$minute,$format="H:i") {
  return date($format,mktime($heure,($minute*60)%60,0,1,1,2000));
}
// ----------------------------------------------------------------------------



?>
<link href='./css/style.css' rel='stylesheet' />
<link href='../../../assets/plugins/fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='../../../assets/plugins/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<link href='../../assets/plugins/fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='../../assets/plugins/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />


</head>
<body>

	<div id='calendar' class='col-xs-12'></div>


<?php
// Footer (Harder - lower - ..)
include "../footer.php";
?>


<!-- Modal -->
<div id="createEventModal" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h3 id="myModalLabel1">Create Appointment</h3>
    </div>
    <div class="modal-body">
    <form id="createAppointmentForm" name="createAppointmentForm" class="form-horizontal" method="post">
        <div class="control-group">
		
	<!--  LIBELLE   -->
		<div class="form-group">
			<div class="col-xs-2">
				<label class="control-label col-xs-12 col-sm-2" for="NoteLibelle" >Libellé:</label>
			</div>
			<div class="col-xs-10">
				<input type="text" required name="NoteLibelle" id="NoteLibelle" class="form-control " data-provide="typeahead" data-items="4" data-source="[&quot;Value 1&quot;,&quot;Value 2&quot;,&quot;Value 3&quot;]">
<!--					<input type="hidden" id="Note StartTime"/>
					<input type="hidden" id="Note EndTime"/>
					<input type="hidden" id="NoteAllDay" />       -->
			</div>
        </div>
		
	<!--  LIEU   -->
		<div class="form-group">
			<div class="col-xs-2">
				<label class="control-label col-xs-12 col-sm-2" for="lieu" >Lieu:</label>
			</div>
			<div class="col-xs-10">
				<input type="text" name="lieu" id="lieu" class="form-control "  data-provide="typeahead" data-items="4" data-source="">
			</div>
        </div>
		
	<!--  DÉTAIL   -->		
		<div class="form-group">
			<div class="col-xs-2">
				<label for="detail" class="control-label col-xs-12 col-sm-2">Détail</label>
			</div>
			<div class="col-xs-10">
				<textarea id="detail" name="detail" class="form-control" rows="3"></textarea>
			</div>
		</div>
		
	<!--  DATE  -->
		<div class="form-group">
			<div class="col-xs-2">
				<label for="A_date" class="control-label col-xs-12 col-sm-2">Date</label>
			</div>
			<div class="col-xs-10">
				<div class="col-xs-3">
					<input type="text" class="form-control datepicker" id="A_date">
				</div>
				<div class="checkbox col-xs-7">
					<label >
						<input type="checkbox" class="checkbox" id="allDay" onclick="affPlageHoraire(this.checked)">
						Note couvrant toute la journée
					</label>
				</div>
			</div>
		</div>
		
	<!--  HORAIRES   -->
		<div class="form-group input-group-sm " >
			<div class="col-xs-2">
				<label for="A_date" class="control-label col-xs-12 col-sm-2">Horaires</label>
			</div>
			<div class="col-xs-10" id="plageHoraireFull" style="display:none;">
				Toute la journée
			</div>
			<div class="col-xs-10" id="plageHoraire">
				Débute à
				<select class="form-control" id="NoteStartTime" name="NoteStartTime" onchange="ajustHeureFin();" style="display:inline; width:auto;">
					<?php
						for ($i=0; $i<23.5;$i=$i+0.5) {
					//		$selected = ($i == $rsProfil['util_debut_journee']) ? " selected" : "";
							$selected = ($i == 8) ? " selected" : "";
							echo '<option value="'.$i.'" '.$selected.'>'.afficheHeure($i,$i).'</option>';
						}
					?>
				</select>
				Durée
				<select class="form-control" id="NoteDureeTime" name="NoteDureeTime" onchange="ajustHeureFin();" style="display:inline; width:auto;">
					<?php
						for ($i=0; $i<23.5;$i=$i+0.5) {
					//		$selected = ($i == $rsProfil['util_debut_journee']) ? " selected" : "";
							$selected = ($i == 1) ? " selected" : "";
							echo '<option value="'.$i.'" '.$selected.'>'.afficheHeure($i,$i).'</option>';
						}
					?>
				</select>
				Termine à
				<select class="form-control" id="NoteEndTime" name="NoteEndTime" style="display:inline; width:auto;">
					<?php
						for ($i=0; $i<23.5;$i=$i+0.5) {
					//		$selected = ($i == $rsProfil['util_debut_journee']) ? " selected" : "";
							$selected = ($i == 9) ? " selected" : "";
							echo '<option value="'.$i.'" '.$selected.'>'.afficheHeure($i,$i).'</option>';
						}
					?>
				</select>
			</div>
		</div>
		
		<hr>
		
	<!--  PERSONNES CONCERNÉES   -->		
		<div class="form-group input-group-sm ">
			<div class="col-xs-2">
				<label for="A_date" class="control-label col-xs-12 col-sm-2">Personnes concernées</label>
			</div>
			<div class="col-xs-10">
				<div class="col-xs-5"><small>Personne disponibles</small>
				</div>
				<div class="col-xs-2">
				</div>
				<div class="col-xs-5"><small>Personnes sélectionnées</small>
				</div>
				
				<div class="col-xs-5">
					<select class="form-control"  multiple="" size="8" id="zlUtilisateur" name="zlUtilisateur">
						<option value="51">Superman</option>
						<option value="514">Batman</option>
						<option value="551">Wolverine</option>
						<option value="5">Canard</option>
						<option value="1">coincoin</option>
					</select>
				</div>
				<div class="col-xs-2">
					<table class="text-center">
						<tr>
							<td>
								<button type="button" class="btn btn-info" id="btSelect" onclick="javascript: selectUtil(document.createAppointmentForm.zlUtilisateur, document.createAppointmentForm.zlParticipant);" title="Ajouter la sélection" name="btSelect"> > </button>
							</td>
						</tr>
						<tr>
							<td>
								<button type="button" class="btn btn-info" id="btSelect" onclick="javascript: selectAll(document.createAppointmentForm.zlUtilisateur, document.createAppointmentForm.zlParticipant);" title="Ajouter Tous" name="btSelect">> ></button>
							</td>
						</tr>
						<tr>
							<td>
								<button type="button" class="btn btn-info" id="btDeselect" onclick="javascript: selectUtil(document.createAppointmentForm.zlParticipant, document.createAppointmentForm.zlUtilisateur);" title="Enlever la sélection" name="btDeselect">. < -
								</button>
							</td>
						</tr>
						<tr>
							<td>
								<button type="button" class="btn btn-info" id="btDeselect" onclick="javascript: selectAll(document.createAppointmentForm.zlParticipant, document.createAppointmentForm.zlUtilisateur);" title="Enlever Tous" name="btDeselect">< <</button>
							</td>
						</tr>
					</table>
				</div>
				<div class="col-xs-5">
					<select class="form-control"  multiple="" size="8" id="zlParticipant" name="zlParticipant">
						
					</select>
				</div>
			</div>
		
		</div>
		
		<hr>
		
	<!--  PARTAGE  -->
		<div class="form-group">
			<div class="col-xs-2">
				<label class="control-label col-xs-12 col-sm-2">Partage</label>
			</div>
			<div class="radio col-xs-10">
			  <label>
				<input type="radio" name="optionsPartage" id="optionsPartage1" value="1" checked>
				 Note publique (note détaillée dans le partage de planning)
			  </label>
			  <label>
				<input type="radio" name="optionsPartage" id="optionsPartage2" value="0">
				Note privée (mention "Occupé" dans le partage de planning)
			  </label>
			</div>
		</div>
		
		<hr>
		
	<!--  DISPONIBILITE  -->
		<div class="form-group">
			<div class="col-xs-2">
				<label for="A_date" class="control-label col-xs-12 col-sm-2">Disponibilité</label>
			</div>
			<div class="radio col-xs-10">
			  <label>
				<input type="radio" name="optionsDispo" id="optionsDispo1" value="1" checked>
				 Occupé(e) (considérer comme non disponible dans le module des disponibilités)
			  </label>
			  <label>
				<input type="radio" name="optionsDispo" id="optionsDispo2" value="0">
				 Libre (considérer comme libre dans le module des disponibilités)
			  </label>
			</div>
		</div>
		
		<hr>
		
	<!--  COULEUR  -->
		<div class="form-group">
			<div class="col-xs-2">
				<label for="detail" class="control-label col-xs-12 col-sm-2">Couleur</label>
			</div>
			<div class="col-xs-10">
				<div class=" col-sm-5">
					<select class="form-control" name="couleur" id="couleur">
						<option value="1">bleu</option>
						<option value="2">Vert</option>
                                                <option value="3">Jaune</option>
                                                <option value="4">Rose</option>
                                                <option value="5">Rouge</option>
					</select>
				</div>
				<div class="col-xs-offset-1 col-xs-4">
					<input type="text" class="" value="Apparence" disabled>
				</div>
			</div>
		</div>
		
		<hr>
		
	<!--  PÉRIODICITÉ  -->
		<div class="form-group">
			<div class="col-xs-2">
				<label for="periodicite" class="control-label col-xs-12 col-sm-2">Périodicité</label>
			</div>
			<div class="col-xs-10">
				<div>
					Répétition :
					<select class="form-control" id="optionPeriodicite" name="optionPeriodicite" onchange="javascript: affPeriodicite(this.value);" style="display:inline; width:auto;"> 
						<option selected="" value="1">Aucune</option>
						<option value="2">Quotidienne</option>
						<option value="3">Hebdomadaire</option>
						<option value="4">Mensuelle</option>
						<option value="5">Annuelle</option>
					</select>
				</div>
				<div>
			
					<div id="detailJour" class="col-xs-12" style="background-color:#E966D5; display:none;" >
						<div class="radio col-xs-12 col-sm-offset-1 col-sm-11">
							<div class="col-xs-1">
								<input type="radio" name="optionsRepetitionJour" id="optionsRepetitionJour1" value="1" checked>
							</div>
							<div class="input-group col-xs-6">
								<div class="input-group-addon">Tous les</div>
									<input type="text" class="form-control" name="repetitionJours" id="repetitionJours" value="1" onfocus="document.getElementById('optionsRepetitionJour1').checked='true'">
								<div class="input-group-addon">jour(s)</div>
							</div>
						</div>
						<div class="radio col-xs-12 col-sm-offset-1 col-sm-11">
							<div class="col-xs-1">
								<input type="radio" name="optionsRepetitionJour" id="optionsRepetitionJour2" value="2">
							</div>
							<div class="col-xs-11" onclick="document.getElementById('optionsRepetitionJour2').checked='true'">
								Tous les jours ouvrables (lundi à vendredi)
							</div>
						</div>
					</div>
					
					<div id="detailSemaine" class="col-xs-12" style="background-color:#B6ED66; display:none;">
						<div class="input-group col-xs-offset-1 col-xs-6">
							<div class="input-group-addon">Toutes les</div>
								<input type="text" class="form-control" name="repetionSemaine" id="repetionSemaine" value="1">
							<div class="input-group-addon">semaine(s)</div>
						</div>
						<div class=" col-xs-12 col-sm-offset-1 col-sm-11">
						Les &nbsp;&nbsp;
							<label for="lundi"><input type="checkbox" id="sem_lundi" class="" checked="" tabindex="1" value="1" name="bt1">&nbsp;Lun</label>
							<label for="mardi"><input type="checkbox" id="sem_mardi" class="" checked="" tabindex="2" value="2" name="bt2">&nbsp;Mar</label>
							<label for="mercredi"><input type="checkbox" id="sem_mercredi" class="" checked="" tabindex="3" value="3" name="bt3">&nbsp;Mer</label>
							<label for="jeudi"><input type="checkbox" id="sem_jeudi" class="" checked="" tabindex="4" value="4" name="bt4">&nbsp;Jeu</label>
							<label for="vendredi"><input type="checkbox" id="sem_vendredi" class="" checked="" tabindex="5" value="5" name="bt5">&nbsp;Ven</label>
							<label for="samedi"><input type="checkbox" id="sem_samedi" class="" tabindex="6" value="6" name="bt6">&nbsp;Sam</label>
							<label for="dimanche"><input type="checkbox" id="sem_dimanche" class="" tabindex="7" value="7" name="bt7">&nbsp;Dim</label>
						</div>
					</div>
					
					<div id="detailMois" class="col-xs-12" style="background-color:#D7FFFB; display:none;">
						<div class="input-group col-xs-offset-1 col-xs-6">
								<div class="input-group-addon">Tous les</div>
									<input type="text" class="form-control" name="repetionMois" id="repetionMois" value="1">
								<div class="input-group-addon">mois</div>
						</div>
						<div class=" col-xs-12 col-sm-offset-1 col-sm-11">
							<label for="optionsRepetitionMois1">
								<input type="radio" name="optionsRepetitionMois" id="optionsRepetitionMois1" value="1" checked>
								Le 
								<select class="form-control" id="M_jourDuMois" name="M_jourDuMois" style="display:inline; width:auto;" onfocus="document.getElementById('optionsRepetitionMois1').checked='true'">
									<option selected="" value="1">1</option>
									<?php
									for($i=2; $i<=31; $i++){
										echo '<option value="'.$i.'">'.$i.'</option>';
									}
									?>
								</select>
								de chaque mois
							</label>
						</div>
						<div class=" input-group-sm radio col-xs-12 col-sm-offset-1 col-sm-11 ">
							<label for="optionsRepetitionMois2">
								<input type="radio" name="optionsRepetitionMois" id="optionsRepetitionMois2" value="2">
								Le 
								<select class="form-control" id="moisCardinalite" name="moisCardinalite" style="display:inline; width:auto;" onfocus="document.getElementById('optionsRepetitionMois2').checked='true'">
									<option selected="" value="1">1er</option>
									<option value="2">2 ième</option>
									<option value="3">3 ième</option>
									<option value="4">4 ième</option>
									<option value="5">Dernier</option>
								</select>
								<select class="form-control" id="moisCardinaliteJour" name="moisCardinaliteJour" style="display:inline; width:auto;" onfocus="document.getElementById('optionsRepetitionMois2').checked='true'">
									<option selected="" value="1">Lundi</option>
									<option value="2">Mardi</option>
									<option value="3">Mercredi</option>
									<option value="4">Jeudi</option>
									<option value="5">Vendredi</option>
									<option value="6">Samedi</option>
									<option value="7">Dimanche</option>
								</select>
								du mois
							</label>
						</div>
					</div>
						
					<div id="detailAnnee" class="col-xs-12" style="background-color:#F75E5E; display:none;">
						<div class=" input-group-sm col-xs-12 col-sm-offset-1 col-sm-11">
							<label for="optionsRepetitionAn1">
								<input type="radio" name="optionsRepetitionAn" id="optionsRepetitionAn1" value="1" checked>
								Tous les
								<select class="form-control" id="A_jourDuMois" name="A_jourDuMois" style="display:inline; width:auto;" onfocus="document.getElementById('optionsRepetitionAn1').checked='true'">
									<option selected="" value="1">1</option>
									<?php
									for($i=2; $i<=31; $i++){
										echo '<option value="'.$i.'">'.$i.'</option>';
									}
									?>
								</select>
								<select class="form-control" id="A_Mois" name="A_Mois" style="display:inline; width:auto;" onfocus="document.getElementById('optionsRepetitionAn1').checked='true'">
									<option selected="" value="1">janvier</option>
									<option value="2">Février</option>
									<option value="3">Mars</option>
									<option value="4">Avril</option>
									<option value="5">Mai</option>
									<option value="6">Juin</option>
									<option value="7">Juillet</option>
									<option value="8">Août</option>
									<option value="9">Septembre</option>
									<option value="10">Octobre</option>
									<option value="11">Novembre</option>
									<option value="12">Décembre</option>
								</select>
							</label>
						</div>
						<div class=" input-group-sm radio col-xs-12 col-sm-offset-1 col-sm-11">
							<label for="optionsRepetitionAn2">
								<input type="radio" name="optionsRepetitionAn" id="optionsRepetitionAn2" value="2">
								Le 
								<select class="form-control" id="anCardinalite" name="anCardinalite" style="display:inline; width:auto;" onfocus="document.getElementById('optionsRepetitionAn2').checked='true'">
									<option selected="" value="1">1er</option>
									<option value="2">2 ième</option>
									<option value="3">3 ième</option>
									<option value="4">4 ième</option>
									<option value="5">Dernier</option>
								</select>
								<select class="form-control" id="anCardinaliteJour" name="anCardinaliteJour" style="display:inline; width:auto;" onfocus="document.getElementById('optionsRepetitionAn2').checked='true'">
									<option selected="" value="1">Lundi</option>
									<option value="2">Mardi</option>
									<option value="3">Mercredi</option>
									<option value="4">Jeudi</option>
									<option value="5">Vendredi</option>
									<option value="6">Samedi</option>
									<option value="7">Dimanche</option>
								</select>
								de
								<select class="form-control" id="anCardinaliteMois" name="anCardinaliteMois" style="display:inline; width:auto;" onfocus="document.getElementById('optionsRepetitionAn2').checked='true'">
									<option selected="" value="1">janvier</option>
									<option value="2">Février</option>
									<option value="3">Mars</option>
									<option value="4">Avril</option>
									<option value="5">Mai</option>
									<option value="6">Juin</option>
									<option value="7">Juillet</option>
									<option value="8">Août</option>
									<option value="9">Septembre</option>
									<option value="10">Octobre</option>
									<option value="11">Novembre</option>
									<option value="12">Décembre</option>
								</select>
							</label>
						</div>
					</div>
					<div id="plagePeriodicite" class="col-xs-12" style="background-color:#F75E5E; display:none;">
						<div class="col-xs-12 col-sm-offset-1 col-sm-11" id="plagePeriodicite">
							Définir la date de fin :
						</div>
						<div class="radio col-xs-12 col-sm-offset-1 col-sm-11">
							<label for="RepetitionOccurence1">
								<input type="radio" id="RepetitionOccurence1" name="RepetitionOccurence" value="1" checked>
								Fin après 
								<input type="text" id="Occurence" name="Occurence" class="" value="10" >
								occurence (s)
							</label>
						</div>
						<div class="radio col-xs-12 col-sm-offset-1 col-sm-11">
							<label for="RepetitionOccurence2">
								<input type="radio" id="RepetitionOccurence2" name="RepetitionOccurence" value="2" >
									Fin le 
								<input type="text" class="datepicker" id="RepetitionDateFin" name="RepetitionDateFin" placeholder="Date" style="width:100px;" onfocus="document.getElementById('RepetitionOccurence2').checked='true'">
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>

		
		<hr>
		
	<!--  RAPPEL  -->
		<div class="form-group">
			<div class="col-xs-2">
				<label for="detail" class="control-label col-xs-12 col-sm-2">Rappel</label>
			</div>
			<div class="radio col-xs-10">
				<div>
					<label style="padding:0;"><input type="radio" name="optionsRappel" id="optionsRappel1" value="1" checked>
					Pas de rappel</label>
				</div>
				<div >
					<label style="padding:0;">
					<input type="radio" name="optionsRappel" id="optionsRappel2" value="2" >
					rappel
						<select class="form-control" id="tpsRappel" name="tpsRappel" style="display:inline; width:auto;" onfocus="document.getElementById('optionsRappel2').checked='true'">
						<?php 
							for($i=1; $i<59; $i++){
								$selected = ($i == 5) ? 'selected=""' : '' ;
								echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
							}
						?>
						</select>
						<select class="form-control" id="rappel_coef" name="rappel_coef" style="display:inline; width:auto;" onfocus="document.getElementById('optionsRappel2').checked='true'">
							<option value="1">minute(s)</option>
							<option value="60">heure(s)</option>
							<option value="1440">jour(s)</option>
						</select>
					avant.
					</label>
				</div>
				<div>
				<label class="checkbox-inline">
					Copie par mail
				</label>
				<label class="checkbox-inline">
					<input type="checkbox" id="ckEmail" value="option1"> Personne(s) concernée(s)
				</label>
				<label class="checkbox-inline">
					<input type="checkbox" id="ckEmailContact" value="option2"> Contact associé
				</label>
				</div>
			</div>
		</div>
		
		<hr>
		
	<!--  WHEN  -->
        <div class="control-group">
			<div class="col-xs-2">
				<label class="control-label" for="when">When:</label>
			</div>
            <div class="controls controls-row col-xs-10" id="when" style="margin-top:5px;">
            </div>
        </div>
    </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button type="submit" class="btn btn-primary" id="submitButton">Save</button>
    </div>
        </div>
    </div>
</div>

<script src="../../assets/plugins/chosen/chosen.jquery.min.js"></script>
<script src='../../../assets/plugins/fullcalendar/lib/moment.min.js'></script>
<script src='../../../assets/plugins/fullcalendar/fullcalendar.min.js'></script>
<script src='../../../assets/plugins/fullcalendar/lang/fr.js'></script>
<script src='../../assets/plugins/fullcalendar/lib/moment.min.js'></script>
<script src='../../assets/plugins/fullcalendar/fullcalendar.min.js'></script>
<script src='../../assets/plugins/fullcalendar/lang/fr.js'></script>
<script src='./js/jquery.json-2.4.js'></script>
<script src='./js/check.js'></script>
<script>
/* INITIALISE CALENDAR */
	$(document).ready(function() {
		
		$('#calendar').fullCalendar({
			customButtons: {
				myCustomButton: {
					text: 'custom!',
					click: function() {
						alert('clicked the custom button!');
					}
				}
			},
			header: {
				left: 'prev,next today myCustomButton',
				center: 'title',
				right: 'month,agendaWeek,agendaDay',
				lang: 'fr'
			},
			/* Pour affichage mois */
			weekNumbers : true,
//			weekNumberCalculation : 'local',
			/* Pour affichage jour */
			allDaySlot : false,
			scrollTime : '08:00:00',
			minTime : '07:00:00',
			maxTime : '19:00:00',
			businessHours: {
				start: '10:00', // a start time (10am in this example)
				end: '18:00', // an end time (6pm in this example)

//				dow: [ 1, 2, 3, 4 ]
				// days of week. an array of zero-based day of week integers (0=Sunday)
				// (Monday-Thursday in this example)
			},
			dayClick: function(date, jsEvent, view) {

/*				alert('Clicked on: ' + date.format());
				alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
				alert('Current view: ' + view.name);
				// change the day's background color just for fun
				$(this).css('background-color', 'red');
*/				
		//		$('#myModal').modal('show');

			},
			eventClick: function(calEvent, jsEvent, view) {

				alert('Event: ' + calEvent.title);
				alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
				alert('View: ' + view.name);

				// change the border color just for fun
				$(this).css('border-color', 'red');

			},
//			height : 'auto',
			views: {
				month: { // name of view
					titleFormat: 'DD, MM, YYYY'
				// other view-specific options here
				}
			},
		//	defaultDate: '2014-11-12',
			selectable: true,
			selectHelper: true,
			select: function(start, end, allDay) {
                            var view = $('#calendar').fullCalendar('getView');
                          //  alert("The view's title is " + view.name);
                            var starttimeIndex = 16;
                            var endtimeIndex = 18;
                          if(view.name==='agendaDay'){
								starttimeH=moment(start).format('H'); 
								starttimeM=moment(start).format('mm'); 
                                starttimeIndex=parseInt((starttimeH + '.' + starttimeM/60*100)*100)/100*2;
                            
								endtimeH=moment(end).format('H');
								endtimeM=moment(end).format('mm');
                                endtimeIndex=parseInt((endtimeH + '.' + endtimeM/60*100)*100)/100*2;
                            }
                            //	Calcul du numéro de l'index de NoteDureeTime
                                dureetimeIndex=((endtimeIndex-starttimeIndex));
                            //  Date
            			A_date=moment(start).format('DD/MM/YYYY');

				  var mywhen = starttimeIndex + ' - ' + endtimeIndex;
				  document.getElementById('NoteStartTime').selectedIndex=starttimeIndex;
				  document.getElementById('NoteEndTime').selectedIndex=endtimeIndex;
				  document.getElementById('NoteDureeTime').selectedIndex=dureetimeIndex;
				  $('#createEventModal #A_date').val(A_date);
				  $('#createEventModal #NoteAllDay').val(allDay);
				  $('#createEventModal #when').text(mywhen);
				  $('#createEventModal').modal('show');
			   },
			editable: true,
			eventDrop: function(event, delta) {
				 start = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
				 end = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss");
				 $.ajax({
					url: 'update_events.php',
					data: 'title='+ event.title+'&start='+ start +'&end='+ end +'&id='+ event.id ,
					type: "POST",
					success: function(json) {
						alert("OK");
					}
				 });
				},
			eventResize: function(event) {
				 start = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
				 end = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss");
				 $.ajax({
					url: 'update_events.php',
					data: 'title='+ event.title+'&start='+ start +'&end='+ end +'&id='+ event.id ,
					type: "POST",
					success: function(json) {
						alert("OK");
					}
				 });
 
			},
			eventLimit: true, // allow "more" link when too many events
			events: {
				url: 'events.php?user=<?php echo $_SESSION['id_user']; ?>',
				type: 'GET',
				data: {
					custom_param1: 'something',
					custom_param2: 'somethingelse'
				},
				error: function() {
					$('#script-warning').show();
				},
	//			color: 'yellow',   // a non-ajax option
	//			textColor: 'black' // a non-ajax option
					
			},
			
    eventOverlap: function(stillEvent, movingEvent) {
        return stillEvent.allDay && movingEvent.allDay;
	}
		});

		// function for add event
	$('#submitButton').on('click', function(e){
    // We don't want this to act as a link so cancel the link action
    e.preventDefault();

    doSubmit();
	});


		
	});
/*  FIN INITIALISE CALENDAR  */
	
	function doSubmit(){
		
		if(!saisieOK()) {return; }
		
		$("#createEventModal").modal('hide');
/*		console.log($('#NoteStartTime').val());   PEUT ETRE SUPPRIMER
		console.log($('#NoteEndTime').val());
		console.log($('#NoteAllDay').val());
//		alert("form submitted");
	*/	
		var post_data = {};
	
		post_data['title'] = $('#NoteLibelle').val();
		post_data['start'] = $('#NoteStartTime').val();
		post_data['end'] = $('#NoteEndTime').val();
		post_data['lieu'] = $('#lieu').val();
		post_data['detail'] = $('#detail').val();
		post_data['A_date'] = $('#A_date').val();
		post_data['allDay'] = check_box('allDay');								// chekbox
//		post_data['allDay'] = ($('#NoteAllDay').val()'] == "false");
		post_data['participants'] = check_select('zlParticipant');
		post_data['partage'] = check('optionsPartage');
                alert('partage : '+post_data['partage']);
		post_data['dispo'] = check('optionsDispo');
		post_data['color'] = $('#couleur').val();
		
		post_data['periodicite'] = $('#optionPeriodicite').val();				// select
		
		post_data['J_optionsRepetitionJour'] = check('optionsRepetitionJour');	// radio
		post_data['J_repetitionJours'] = $('#repetitionJours').val();				//
		
		post_data['S_repetionSemaine'] = $('#repetionSemaine').val();			//
		post_data['S_sem_1'] = $('#sem_lundi').val();						//
		post_data['S_sem_2'] = $('#sem_mardi').val();						//
		post_data['S_sem_3'] = $('#sem_mercredi').val();					//
		post_data['S_sem_4'] = $('#sem_jeudi').val();						//
		post_data['S_sem_5'] = $('#sem_vendredi').val();
		post_data['S_sem_6'] = $('#sem_samedi').val();
		post_data['S_sem_0'] = $('#sem_dimanche').val();
		
		post_data['M_repetionMois'] = $('#repetionMois').val();
		post_data['M_optionsRepetitionMois'] = check('optionsRepetitionMois');		// radio
		post_data['M_jourDuMois'] = $('#M_jourDuMois').val();					// select
		post_data['M_moisCardinalite'] = $('#moisCardinalite').val();			// select
		post_data['M_moisCardinaliteJour'] = $('#moisCardinaliteJour').val();	// select
		
		post_data['A_optionsRepetitionAn'] = check('optionsRepetitionAn');			// radio
		post_data['A_jourDuMois'] = $('#A_jourDuMois').val();					// select
		post_data['A_Mois'] = $('#A_Mois').val();								// select
		post_data['A_anCardinalite'] = $('#anCardinalite').val();
		post_data['A_anCardinaliteJour'] = $('#anCardinaliteJour').val();
		post_data['A_anCardinaliteMois'] = $('#anCardinaliteMois').val();
		
		post_data['RepetitionOccurence'] = check('RepetitionOccurence');
		post_data['Occurences'] = $('#Occurence').val();
		post_data['RepetitionDateFin'] = $('#RepetitionDateFin').val();
		
		post_data['Rappel'] = check('optionsRappel');
		post_data['tpsRappel'] = $('#tpsRappel').val();
		post_data['rappel_coef'] = $('#rappel_coef').val();
                
		post_data['ckEmail'] = $('#ckEmail').val();
		post_data['ckEmailContact'] = $('#ckEmailContact').val();
		
		var json = JSON.stringify(post_data);
//alert(json);
		$.ajax({
			type: "POST",
			url: "add_events.php?user=<?php echo $_SESSION['id_user']; ?>'",
	//		dataType: "json",
			data: {note: json},
			success: function(json) {
		//	alert('OK');
			}
		});
			
		$("#calendar").fullCalendar('renderEvent',
			{
				title: $('#NoteLibelle').val(),
				start: new Date($('#NoteStartTime').val()),
				end: new Date($('#NoteEndTime').val()),
				allDay: ($('#NoteAllDay').val() == "true"),
			},
			true);
	}
	
	/*   ---------------------------------------  */
    function saisieOK() {
      if (trim($('#NoteLibelle').val()) == "") {
        window.alert("Le libelle est obligatoire");
        $('#NoteLibelle').focus();
        return (false);
      }
      if ($('#A_date').val() == "") {
        window.alert("La date est obligatoire");
        $('#A_date').focus();
        return (false);
      }
      if (!chk_date_format($('#A_date').val()) ){
        window.alert("La date n'est pas valide ou pas au format jj/mm/aaaa");
        $('#A_date').focus();
        return (false);
      }
//      if (!end_sup_start($('#NoteStartTime').val(), $('#NoteEndTime').val()) ){
      if ($('#NoteStartTime').val()> $('#NoteEndTime').val()) {
        window.alert("L'heure de fin doit être postérieure ou égale à l'heure de début");
        $('#NoteStartTime').focus();
        return (false);
      }
      if (!check_select('zlParticipant')){
        window.alert("Vous devez sélectionner au moins un participant");
        $('#zlUtilisateur').focus();
        return (false);
      }
		
		return true;
	}
	
	
    $(".datepicker").datepicker({
        format: "dd/mm/yyyy",
        weekStart: 1,
        autoclose: true
    });
    // Permet de ne pas afficher l'horaire de la note lorsqu'elle couvre toute la journee
    function affPlageHoraire(_allDay) {
      var t1 = document.getElementById('plageHoraire');
      var t2 = document.getElementById('plageHoraireFull');
      if (!_allDay) {
        t2.style.display = "none";
        t1.style.display = "block";
      } else {
        t1.style.display = "none";
        t2.style.display = "block";
      }
    }
// Permet de ne pas afficher les details de chaque choix de la periodicite
    function affPeriodicite(idDiv) {
    var periodiciteVisible;
      if (periodiciteVisible) {
        periodiciteVisible.style.display = "none";
      } else {
        document.getElementById('detailJour').style.display = "none";
        document.getElementById('detailSemaine').style.display = "none";
        document.getElementById('detailMois').style.display = "none";
        document.getElementById('detailAnnee').style.display = "none";
      }
      switch (idDiv) {
        case '2' : periodiciteVisible = document.getElementById('detailJour'); break;
        case '3' : periodiciteVisible = document.getElementById('detailSemaine'); break;
        case '4' : periodiciteVisible = document.getElementById('detailMois'); break;
        case '5' : periodiciteVisible = document.getElementById('detailAnnee'); break;
        default : idDiv = '1'; periodiciteVisible = null; break;
      }
      if (idDiv != '1') {
        periodiciteVisible.style.display = "block";
        // Affichage de la plage de periodicite uniquement lorsque la note n'est pas unique
        document.getElementById('plagePeriodicite').style.display = "block";
      } else {
        document.getElementById('plagePeriodicite').style.display = "none";
      }
    }

// Ajuste l'heure de fin en fonction de l'heure de debut et de la duree selectionnees
    function ajustHeureFin() {
      var idxHeureFin = document.createAppointmentForm.NoteStartTime.selectedIndex + document.createAppointmentForm.NoteDureeTime.selectedIndex;
      if (idxHeureFin>=document.createAppointmentForm.NoteEndTime.options.length) {
        document.createAppointmentForm.NoteEndTime.selectedIndex = document.createAppointmentForm.NoteEndTime.options.length - 1;
        ajustHeureDuree();
      } else {
        document.createAppointmentForm.NoteEndTime.selectedIndex = idxHeureFin;
      }
    }
// Ajuste la liste de choix de duree de la note en fonction de l'heure de fin selectionne
    function ajustHeureDuree() {
      var idxHeureDuree = document.createAppointmentForm.NoteEndTime.selectedIndex - document.createAppointmentForm.NoteStartTime.selectedIndex;
      if (idxHeureDuree < 0) {
        idxHeureDuree = 0;
        document.createAppointmentForm.NoteEndTime.selectedIndex = document.createAppointmentForm.NoteStartTime.selectedIndex;
      }
      document.createAppointmentForm.NoteDureeTime.selectedIndex = Math.min(idxHeureDuree,document.createAppointmentForm.NoteDureeTime.length-1);
    }
    
/* Fonction de manupulation des listes dans les balises <selec t> */

    function genereListe(_liste, _tabTexte, _tabValue, _tailleTab) {
      for (var i=0; i<_tailleTab; i++)
        _liste.options[i]=new Option(_tabTexte[i], _tabValue[i]);
    }

    function bubbleSort(_tabText, _tabValue,_tailleTab) {
      var i,s;

      do {
        s=0;
        for (i=1; i<_tailleTab; i++)
          if (_tabText[i-1] > _tabText[i]) {
            y = _tabText[i-1];
            _tabText[i-1] = _tabText[i];
            _tabText[i] = y;
            y = _tabValue[i-1];
            _tabValue[i-1] = _tabValue[i];
            _tabValue[i] = y;
            s = 1;
          }
      } while (s);
    }

    function videListe(_liste) {
      var cpt = _liste.options.length;

      for(var i=0; i<cpt; i++) {
        _liste.options[0] = null;
      }
    }

    function selectUtil(_listeSource, _listeDest) {
      var i,j;
      var ok = false;
      var tabDestTexte = new Array();
      var tabDestValue = new Array();
      var tailleTabDest = 0;

      for (i=0; i<_listeDest.options.length; i++) {
        tabDestTexte[tailleTabDest]   = _listeDest.options[i].text;
        tabDestValue[tailleTabDest++] = _listeDest.options[i].value;
      }

      for (j=_listeSource.options.length-1; j>=0; j--) {
        if (_listeSource.options[j].selected) {
          ok = true;
          tabDestTexte[tailleTabDest]   = _listeSource.options[j].text;
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
      for (var i=0; i<_listeSource.options.length; i++) {
        _listeSource.options[i].selected = true;
      }
      selectUtil(_listeSource, _listeDest);
    }

    function recupSelection(_liste, _champ) {
      _champ.value = "";
      for (var i=0; i<_liste.options.length; i++) {
        _champ.value += ((i) ? "+" : "") + _liste.options[i].value;
      }
    }

	
////	FONCTIONS UTILISEES POUR LA VALIDATION DoSubmit()

// Balises radio : retourne la valeur 'value' du radio checked
    function check(name) {
        var inputs = document.getElementsByTagName(name);
        var inputsLength = inputs.length;
                alert('partage : '+inputsLength);
    
        for (var i = 0 ; i < inputsLength ; i++) {
            if (inputs[i].type == 'radio' && inputs[i].checked) {
                return inputs[i].value;
				break;
            }
        }
    }
// Balises checkbox : retourne la valeur true ou false en fonction de checked
    function check_box(name) {
        var inputs = document.getElementsByTagName(name);
        var checked = false;
		if (inputs.type == 'checkbox' && inputs.checked) {
				checked	= true;
			}
		return checked;
	}
	
	// Balises select multiple: 
	// retour: string des options selected séparées par ##
	function check_select(id) {
		var inputs = document.getElementById(id);
		var selecteds = "" ;
		if (inputs.nodeName == 'SELECT' && inputs.options.length >= 1) {
		  for (var i=0, iLen=inputs.options.length; i<iLen; i++) {
					selecteds = selecteds + '##' + inputs.options.item(i).value;;
			}
		}
		return selecteds;
	}

    // Fonction trim javascript (suppression d'espaces avant et apres une chaine)
    function trim(chaine) {
      return chaine.replace(/^\s+/, "").replace(/\s+$/, "");
    }
 
</script>
</body>
</html>
