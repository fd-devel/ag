<?php

// autoload des classes.
function chargerClasse($classname) {
    require '../../data/modeles/' . $classname . '.class.php';
}

spl_autoload_register('chargerClasse');


// Header (stronger - faster - better)
include "../header.php";

// ----------------------------------------------------------------------------
// FORMATAGE DES HEURES POUR L'AFFICHAGE
// ----------------------------------------------------------------------------
function afficheHeure($heure, $minute, $format = "H:i") {
    return date($format, mktime($heure, ($minute * 60) % 60, 0, 1, 1, 2000));
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

    <div id='calendar' ></div>


<?php
    include 'modale_add.php';
// Footer (Harder - lower - ..)
include "../footer.php";
?>


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
            weekNumbers: true,
//			weekNumberCalculation : 'local',
            /* Pour affichage jour */
            allDaySlot: true,
            scrollTime: '08:00:00',
            minTime: '07:00:00',
            maxTime: '19:00:00',
            businessHours: {
                start: '10:00', // a start time (10am in this example)
                end: '18:00', // an end time (6pm in this example)

//				dow: [ 1, 2, 3, 4 ]
                // days of week. an array of zero-based day of week integers (0=Sunday)
                // (Monday-Thursday in this example)
            },
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
            eventRender: function(event, element) {
                $(element).tooltip({title: event.lieu});             
            },
            eventClick: function(event, element) {
                $(element).popover({title: event.title, content:event.lieu});
/*
                alert('Event: ' + calEvent.title);
                alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
                alert('View: ' + view.name);

                // change the border color just for fun
                $(this).css('border-color', 'red');
*/
            },
            eventDrop: function(event, delta) {
                start = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
                end = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss");
                $.ajax({
                    url: 'update_events.php',
                    data: 'title=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id,
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
                    data: 'title=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id,
                    type: "POST",
                    success: function(json) {
                        alert("OK");
                    }
                });

            },
            eventLimit: true, // allow "more" link when too many events
            eventOverlap: function(stillEvent, movingEvent) {
                return stillEvent.allDay && movingEvent.allDay;
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
                month: {// name of view
                    //	titleFormat: 'DD, MM, YYYY'
                    // other view-specific options here
                }
            },
            slotDuration : '00:15:00',
            //	defaultDate: '2014-11-12',
            selectable: true,
            selectHelper: true,
            select: function(start, end, allDay) {
                var view = $('#calendar').fullCalendar('getView');
                //  alert("The view's title is " + view.name);
                var starttimeIndex = 16;
                var endtimeIndex = 18;
                if (view.name === 'agendaDay') {
                    starttimeH = moment(start).format('H');
                    starttimeM = moment(start).format('mm');
                    starttimeIndex = parseInt((starttimeH + '.' + starttimeM / 60 * 100) * 100) / 100 * 2;

                    endtimeH = moment(end).format('H');
                    endtimeM = moment(end).format('mm');
                    endtimeIndex = parseInt((endtimeH + '.' + endtimeM / 60 * 100) * 100) / 100 * 2;
                }
                //	Calcul du numéro de l'index de NoteDureeTime
                dureetimeIndex = ((endtimeIndex - starttimeIndex));
                //  Date
                A_date = moment(start).format('DD/MM/YYYY');

                var mywhen = starttimeIndex + ' - ' + endtimeIndex;
                document.getElementById('NoteStartTime').selectedIndex = starttimeIndex;
                document.getElementById('NoteEndTime').selectedIndex = endtimeIndex;
                document.getElementById('NoteDureeTime').selectedIndex = dureetimeIndex;
                $('#createEventModal #A_date').val(A_date);
                $('#createEventModal #NoteAllDay').val(allDay);
                $('#createEventModal #when').text(mywhen);
                $('#createEventModal').modal('show');
            },
            editable: true
        });

        // function for add event
        $('#submitButton').on('click', function(e) {
            // We don't want this to act as a link so cancel the link action
            e.preventDefault();

            doSubmit();
        });



    });
    /*  FIN INITIALISE CALENDAR  */

    function doSubmit() {

        if (!saisieOK()) {
            return;
        }

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
        if (!chk_date_format($('#A_date').val())) {
            window.alert("La date n'est pas valide ou pas au format jj/mm/aaaa");
            $('#A_date').focus();
            return (false);
        }
//      if (!end_sup_start($('#NoteStartTime').val(), $('#NoteEndTime').val()) ){
        if ($('#NoteStartTime').val() > $('#NoteEndTime').val()) {
            window.alert("L'heure de fin doit être postérieure ou égale à l'heure de début");
            $('#NoteStartTime').focus();
            return (false);
        }
        if (!check_select('zlParticipant')) {
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
            case '2' :
                periodiciteVisible = document.getElementById('detailJour');
                break;
            case '3' :
                periodiciteVisible = document.getElementById('detailSemaine');
                break;
            case '4' :
                periodiciteVisible = document.getElementById('detailMois');
                break;
            case '5' :
                periodiciteVisible = document.getElementById('detailAnnee');
                break;
            default :
                idDiv = '1';
                periodiciteVisible = null;
                break;
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
        if (idxHeureFin >= document.createAppointmentForm.NoteEndTime.options.length) {
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
        document.createAppointmentForm.NoteDureeTime.selectedIndex = Math.min(idxHeureDuree, document.createAppointmentForm.NoteDureeTime.length - 1);
    }

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


////	FONCTIONS UTILISEES POUR LA VALIDATION DoSubmit()

// Balises radio : retourne la valeur 'value' du radio checked
    function check(name) {
        var inputs = document.getElementsByName(name);
        var inputsLength = inputs.length;

        for (var i = 0; i < inputsLength; i++) {
            if (inputs[i].type == 'radio' && inputs[i].checked) {
                return inputs[i].value;
                break;
            }
        }
    }
// Balises checkbox : retourne la valeur true ou false en fonction de checked
    function check_box(Id) {
        var inputs = document.getElementById(Id);
        var checked = 0;
        if (inputs.checked) {
            checked = 1;
        }
        return checked;
    }

    // Balises select multiple:
    // retour: string des options selected séparées par ##
    function check_select(id) {
        var inputs = document.getElementById(id);
        var selecteds = "";
        if (inputs.nodeName == 'SELECT' && inputs.options.length >= 1) {
            for (var i = 0, iLen = inputs.options.length; i < iLen; i++) {
                selecteds = selecteds + '##' + inputs.options.item(i).value;
                ;
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
