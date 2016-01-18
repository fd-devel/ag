<?php
// autoload des classes.
function chargerClasse($classname) {
    require '../../data/modeles/' . $classname . '.class.php';
}
spl_autoload_register('chargerClasse');

if(isset($_POST['createAppointmentForm'])){
    include 'add_events.php';
}
// Header (stronger - faster - better)
include "../header.php";

require './src/DAO/DAO.php';
require './src/DAO/AgendaDAO.php';

    include './inc/format_params.php';
    

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
// Footer (Harder - lower - ..)
include "../footer.php";
    include 'modale_add.php';
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
                    id: 'consultusers',
                    click: function(event, jsEvent, view) {
                        $this = $(this);
                        $this.popover({html:true,title:event.title,placement:'top',container:'body'}).popover('show');
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
            defaultView : '<?php echo $userParams['planning']; ?>' ,
            /* Pour affichage mois */
            weekNumbers: true,
//			weekNumberCalculation : 'local',
            /* Pour affichage jour */
            allDaySlot: true,
            scrollTime: '08:00:00',
            minTime: '07:00:00',
            maxTime: '19:00:00',
            hiddenDays: <?php echo $userParams['semaine_type']; ?>,
            businessHours: {
                start: '<?php echo $userParams['debut_journee']; ?>' , //'07:00', // a start time (10am in this example)
                end: '<?php echo $userParams['fin_journee']; ?>' ,// '20:00', // an end time (6pm in this example)

                // [ 1, 2, 3, 4 ] days of week. an array of zero-based day of week integers (0=Sunday)
                dow: [ 1, 2, 3, 4 ]
            },
            events: {
                url: 'events.php',
                type: 'POST',
                data: {
                    user: '<?php echo $consultUser; ?>',
                    custom_param2: 'somethingelse'
                }
                // color: 'yellow',   // a non-ajax option
                // textColor: 'black' // a non-ajax option
            },
            eventRender: function(event, element) {
                $(element).tooltip({title: event.lieu});             
            },
            eventClick: function(event, jsEvent, view) {
                $this = $(this);
                $this.popover({
                    html:true,container:'body',
                    title: '<div class=" panel-heading bg-success">'+event.title+' </div>' , 
                    content:event.lieu,
                    placement:'top'
                    }).popover('show');
//                $(element).popover({title: event.title});
//
//                alert('Event: ' + event.title);
//                alert('Coordinates: ' + event.lieu);
//                alert('View: ' + view.name);

                // change the border color just for fun
                $(this).css('border-color', 'red');

            },
//            height : 'auto',
            views: {
                month: { 
                    height : '500px'
//           name of view
            //	titleFormat: 'DD, MM, YYYY'
            // other view-specific options here
                },
                AgendaWeek:{
                    height : '500px'
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

                document.getElementById('NoteStartTime').selectedIndex=starttimeIndex;
                document.getElementById('NoteEndTime').selectedIndex=endtimeIndex;
                document.getElementById('NoteDureeTime').selectedIndex=(endtimeIndex-starttimeIndex);
                $('#createEventModal #A_date').val(A_date);
                $('#createEventModal #NoteAllDay').val(allDay);
                $('#createEventModal').modal('show');
            },
            editable: true,
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
            eventLimit: true, // nombre limite de notes : 0-9 , true , false
            eventLimitText: 'de plus',
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
            slotDuration : '<?php echo $precision_planning; ?>', // '00:15:00',
            //	defaultDate: '2014-11-12',
        });

        // function for add event
        $('#submitButton').on('click', function(e) {
            // We don't want this to act as a link so cancel the link action
            e.preventDefault();

            doSubmit();
        });



    });
    /*  FIN INITIALISE CALENDAR  */

    </script>
</body>
</html>
