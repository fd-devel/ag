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


</head>
<body>

	<div id='calendar' class='col-xs-12'></div>


<?php
// Footer (Harder - lower - ..)
include "../footer.php";
?>
<script src='../../../assets/plugins/fullcalendar/lib/moment.min.js'></script>
<script src='../../../assets/plugins/fullcalendar/fullcalendar.min.js'></script>
<script src='../../../assets/plugins/fullcalendar/lang/fr.js'></script>
<script>

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
				starttime=moment(start).format('YYYY-MM-DD HH:mm:ss'); 
				endtime=moment(end).format('YYYY-MM-DD HH:mm:ss');
				A_date=moment(start).format('DD/MM/YYYY');

				  var mywhen = starttime + ' - ' + endtime;
				  $('#createEventModal #apptStartTime').val(start);
				  $('#createEventModal #apptEndTime').val(end);
				  $('#createEventModal #A_date').val(A_date);
				  $('#createEventModal #apptAllDay').val(allDay);
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
				url: 'events.php',
				error: function() {
					$('#script-warning').show();
				}
					
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

	function doSubmit(){
		$("#createEventModal").modal('hide');
/*		console.log($('#apptStartTime').val());   PEUT ETRE SUPPRIMER
		console.log($('#apptEndTime').val());
		console.log($('#apptAllDay').val());
//		alert("form submitted");
	*/	
		var title = $('#patientName').val();
		var start= new Date($('#apptStartTime').val());
		var end= new Date($('#apptEndTime').val());
		var allDay= ($('#apptAllDay').val() == "true");
		starttime=moment(start).format('YYYY-MM-DD HH:mm:ss'); 
		endtime=moment(end).format('YYYY-MM-DD HH:mm:ss');
	//					alert(starttime);
		
		$.ajax({
			url: 'add_events.php',
			data: 'title='+ title+'&start='+ starttime +'&end='+ endtime ,
			type: "POST",
			success: function(json) {
		//	alert('OK');
			}
		});
			
		$("#calendar").fullCalendar('renderEvent',
			{
				title: $('#patientName').val(),
				start: new Date($('#apptStartTime').val()),
				end: new Date($('#apptEndTime').val()),
				allDay: ($('#apptAllDay').val() == "true"),
			},
			true);
	}
	
    $(".datepicker").datepicker({
        format: "dd/mm/yyyy",
        weekStart: 1,
        autoclose: true
    });

		
	});

	
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
</script>

<!-- Modal -->
<div id="createEventModal" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-md" >
        <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h3 id="myModalLabel1">Create Appointment</h3>
    </div>
    <div class="modal-body">
    <form id="createAppointmentForm" name="createAppointmentForm" class="form-horizontal">
        <div class="control-group">
		
	<!--  LIBELLE   -->
		<div class="form-group">
			<div class="col-xs-2">
				<label class="control-label col-xs-12 col-sm-2" for="patientName" >Libellé:</label>
			</div>
			<div class="col-xs-10">
				<input type="text" required name="patientName" id="patientName" class="form-control " data-provide="typeahead" data-items="4" data-source="[&quot;Value 1&quot;,&quot;Value 2&quot;,&quot;Value 3&quot;]">
					<input type="hidden" id="apptStartTime"/>
					<input type="hidden" id="apptEndTime"/>
					<input type="hidden" id="apptAllDay" />
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
						<input type="checkbox" class="checkbox" id="A_day">
						Note couvrant toute la journée
					</label>
				</div>
			</div>
		</div>
		
	<!--  HORAIRES   -->
		<div class="form-group input-group-sm ">
			<div class="col-xs-2">
				<label for="A_date" class="control-label col-xs-12 col-sm-2">Horaires</label>
			</div>
			<div class="col-xs-10">
				Débute à
				<select class="form-control" style="display:inline; width:auto;">
					<?php
						for ($i=0; $i<23.5;$i=$i+0.5) {
					//		$selected = ($i == $rsProfil['util_debut_journee']) ? " selected" : "";
							$selected = ($i == 8) ? " selected" : "";
							echo '<option value="'.$i.'" '.$selected.'>'.afficheHeure($i,$i).'</option>';
						}
					?>
				</select>
				Durée
				<select class="form-control" style="display:inline; width:auto;">
					<?php
						for ($i=0; $i<23.5;$i=$i+0.5) {
					//		$selected = ($i == $rsProfil['util_debut_journee']) ? " selected" : "";
							$selected = ($i == 8) ? " selected" : "";
							echo '<option value="'.$i.'" '.$selected.'>'.afficheHeure($i,$i).'</option>\n';
						}
					?>
				</select>
				Termine à
				<select class="form-control" id="optionPeriodicite" name="optionPeriodicite" style="display:inline; width:auto;">
					<?php
						for ($i=0; $i<23.5;$i=$i+0.5) {
					//		$selected = ($i == $rsProfil['util_debut_journee']) ? " selected" : "";
							$selected = ($i == 8) ? " selected" : "";
							echo '<option value="'.$i.'" '.$selected.'>'.afficheHeure($i,$i).'</option>\n';
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
		
	<!--  DATE  -->
		<div class="form-group">
			<div class="col-xs-2">
				<label for="A_date" class="control-label col-xs-12 col-sm-2">Partage</label>
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
		
	<!--  DATE  -->
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
			<div class="radio col-xs-10">
				<div class=" col-sm-5">
					<select class="form-control">
						<option>1</option>
						<option>2</option>
						<option>3</option>
						<option>4</option>
						<option>5</option>
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
			
					<div id="detailJour" class="COL-XS-12" style="background-color:#E966D5; display:none;" >
						<div class="radio col-xs-12 col-sm-offset-1 col-sm-11">
							<div class="col-xs-1">
								<input type="radio" name="optionsRepetitionJour" id="optionsRepetitionJour1" value="1" checked>
							</div>
							<div class="input-group col-xs-6">
								<div class="input-group-addon">Tous les</div>
									<input type="text" class="form-control" name="repetionJours" id="repetionJours" placeholder="1">
								<div class="input-group-addon">jour(s)</div>
							</div>
						</div>
						<div class="radio col-xs-12 col-sm-offset-1 col-sm-11">
							<div class="col-xs-1">
								<input type="radio" name="optionsRepetitionJour" id="optionsRepetitionJour2" value="2">
							</div>
							<div class="col-xs-11">
								Tous les jours ouvrables (lundi à vendredi)
							</div>
						</div>
					</div>
					
					<div id="detailSemaine" class="COL-XS-12" style="background-color:#B6ED66; display:none;">
						<div class="input-group col-xs-12 col-sm-offset-1 col-sm-11">
								<div class="input-group-addon">Toutes les</div>
									<input type="text" class="form-control" name="repetionJours" id="repetionJours" value="1">
								<div class="input-group-addon">semaine(s)</div>
						</div>
						<div class=" col-xs-12 col-sm-offset-1 col-sm-11">
						Les
							<label for="lundi"><input type="checkbox" id="lundi" class="" checked="" tabindex="" value="1" name="bt1">&nbsp;Lun</label>
							<label for="mardi"><input type="checkbox" id="mardi" class="" checked="" tabindex="1" value="1" name="bt2">&nbsp;Mar</label>
							<label for="mercredi"><input type="checkbox" id="mercredi" class="" checked="" tabindex="2" value="1" name="bt3">&nbsp;Mer</label>
							<label for="jeudi"><input type="checkbox" id="jeudi" class="" checked="" tabindex="3" value="1" name="bt4">&nbsp;Jeu</label>
							<label for="vendredi"><input type="checkbox" id="vendredi" class="" checked="" tabindex="4" value="1" name="bt5">&nbsp;Ven</label>
							<label for="samedi"><input type="checkbox" id="samedi" class="" tabindex="5" value="1" name="bt6">&nbsp;Sam</label>
							<label for="dimanche"><input type="checkbox" id="dimanche" class="" tabindex="6" value="1" name="bt7">&nbsp;Dim</label>
						</div>
					</div>
					
					<div id="detailMois" class="COL-XS-12" style="background-color:#D7FFFB; display:none;">
						<div class="input-group col-xs-12 col-sm-offset-1 col-sm-11">
								<div class="input-group-addon">Tous les</div>
									<input type="text" class="form-control" name="repetionMois" id="repetionJours" value="1">
								<div class="input-group-addon">mois(s)</div>
						</div>
						<div class=" col-xs-12 col-sm-offset-1 col-sm-11">
								<label for="optionsRepetition1"></label>
									<input type="radio" name="optionsRepetitionMois" id="optionsRepetitionMois1" value="1" checked>
									Le 
									<select class="form-control" id="optionPeriodicite" name="optionPeriodicite" style="display:inline; width:auto;">
										<option selected="" value="1">1</option>
										<?php
										for($i=2; $i<=31; $i++){
											echo '<option value="'.$i.'">'.$i.'</option>';
										}
										?>
									</select>
								de chaque mois
						</div>
						<div class="radio col-xs-12 col-sm-offset-1 col-sm-11">
							<label for="optionsRepetition2"></label>
								<input type="radio" name="optionsRepetitionMois" id="optionsRepetitionMois2" value="2">
								Le 
								<select class="form-control" style="display:inline; width:auto;">
									<option selected="" value="1">Premier</option>
									<option value="2">Deuxième</option>
									<option value="3">Troisième</option>
									<option value="4">Quatrième</option>
									<option value="5">Dernier</option>
								</select>
								<select class="form-control" style="display:inline; width:auto;">
									<option selected="" value="1">Lundi</option>
									<option value="2">Mardi</option>
									<option value="3">Mercredi</option>
									<option value="4">Jeudi</option>
									<option value="5">Vendredi</option>
									<option value="6">Samedi</option>
									<option value="7">Dimanche</option>
								</select>
								du mois
						</div>
						<div class="col-xs-12 col-sm-offset-1 col-sm-11">
							Définir la date de fin :
						</div>
						<div class="radio col-xs-12 col-sm-offset-1 col-sm-11">
							<label for="optionsRepetitionMois1"></label>
								<input type="radio" name="optionsRepetitionMois_Fin" id="optionsRepetitionMois1" value="1" checked>
								Fin après 
								<input type="text" class="" value="10" >
								occurence (s)
						</div>
						<div class="radio col-xs-12 col-sm-offset-1 col-sm-11">
							<label for="optionsRepetitionMois1"></label>
								<input type="radio" name="optionsRepetitionMois_Fin" id="optionsRepetitionMois1" value="1" >
									Fin le 
								<input type="text" class="datepicker" id="FinPeriode_date" placeholder="Date" style="width:100px;">
						</div>
					</div>
						
					<div id="detailAnnee" class="COL-XS-12" style="background-color:#F75E5E; display:none;">
						<div class=" input-group-sm col-xs-12 col-sm-offset-1 col-sm-11">
								<label for="optionsRepetitionAn1"></label>
									<input type="radio" name="optionsRepetitionAn" id="optionsRepetitionAn1" value="1" checked>
									Tous les
									<select class="form-control" id="optionPeriodicite" name="optionPeriodicite" style="display:inline; width:auto;">
										<option selected="" value="1">1</option>
										<?php
										for($i=2; $i<=31; $i++){
											echo '<option value="'.$i.'">'.$i.'</option>';
										}
										?>
									</select>
									<select class="form-control" id="optionPeriodicite" name="optionPeriodicite" style="display:inline; width:auto;">
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
						</div>
						<div class=" input-group-sm radio col-xs-12 col-sm-offset-1 col-sm-11">
							<label for="optionsRepetitionAn2"></label>
								<input type="radio" name="optionsRepetitionAn" id="optionsRepetitionAn2" value="2">
								Le 
								<select class="form-control" style="display:inline; width:auto;">
									<option selected="" value="1">Premier</option>
									<option value="2">Deuxième</option>
									<option value="3">Troisième</option>
									<option value="4">Quatrième</option>
									<option value="5">Dernier</option>
								</select>
								<select class="form-control" style="display:inline; width:auto;">
									<option selected="" value="1">Lundi</option>
									<option value="2">Mardi</option>
									<option value="3">Mercredi</option>
									<option value="4">Jeudi</option>
									<option value="5">Vendredi</option>
									<option value="6">Samedi</option>
									<option value="7">Dimanche</option>
								</select>
								de
								<select class="form-control" id="optionPeriodicite" name="optionPeriodicite" style="display:inline; width:auto;">
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
						</div>
						<div class="col-xs-12 col-sm-offset-1 col-sm-11">
							Définir la date de fin :
						</div>
						<div class="radio col-xs-12 col-sm-offset-1 col-sm-11">
							<label for="optionsRepetitionAn1"></label>
								<input type="radio" name="optionsRepetitionAn_Fin" id="optionsRepetitionAn1" value="1" checked>
								Fin après 
								<input type="text" class="" value="10" >
								occurence (s)
						</div>
						<div class="radio col-xs-12 col-sm-offset-1 col-sm-11">
							<label for="optionsRepetitionAn2"></label>
								<input type="radio" name="optionsRepetitionAn_Fin" id="optionsRepetitionAn2" value="1" >
									Fin le 
								<input type="text" class="datepicker" id="FinPeriode_date" placeholder="Date" style="width:100px;">
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
					<input type="radio" name="optionsRappel" id="optionsRappel1" value="0" checked>
					Pas de rappel
				</div>
				<div >
					<input type="radio" name="optionsRappel" id="optionsRappel2" value="1" >
				 rappel

				
					
						<select class="form-control" style="display:inline; width:auto;">
							<option>1</option>
							<option>2</option>
							<option>3</option>
							<option>4</option>
							<option>5</option>
						</select>
						<select class="form-control" style="display:inline; width:auto;">
							<option>minute(s)</option>
							<option>heure(s)</option>
							<option>jour(s)</option>
						</select>
					
					avant.
				</div>
				<div>
				<label class="checkbox-inline">
					Copie par mail
				</label>
				<label class="checkbox-inline">
					<input type="checkbox" id="inlineCheckbox1" value="option1"> Personne(s) concernée(s)
				</label>
				<label class="checkbox-inline">
					<input type="checkbox" id="inlineCheckbox2" value="option2"> Contact associé
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
<script type="text/javascript">
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

</script>
</body>
</html>
