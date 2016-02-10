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
                
                    starttimeH=moment(event.start).format('H'); 
                    starttimeM=moment(event.start).format('mm'); 
                    endtimeH=moment(event.end).format('H');
                    endtimeM=moment(event.end).format('mm');
                    
                $(element).tooltip({
                    html:true,container:'body',
                    title: '<div style="background-color:'+event.color+';color:#000;" ><div class="agd-tip-title">'
                            +event.title+
                            '<div class="agd-tip-hour">'
                            +starttimeH+'.'+starttimeM+' - '+endtimeH+'.'+endtimeM+
                            '</div><div class="agd-tip-hour">'
                            +event.lieu+
                            '</div></div><div class="agd-tip-content">'
                            + event.detail+
                            '</div></div>',
                    delay:{ "show": 500}
                    
                });             
            },
            eventClick: function(event) {
                $this = $(this);
                $this.popover({
                    html:true,container:'body',
                    title: '<div class="text-center"><a  class="btn btn-warning" data-toggle="tooltip" id="pop-'+event.id+'" onclick="javascript:action_event(\'delete\','+event+' );" ><i class="fa fa-trash-o"></i></a>'
                            +'<a  class="btn btn-info" data-toggle="tooltip" onclick="javascript:action_event(\'update\',\'event\' );" ><i class="fa fa-pencil-square-o "></i></a></div>' , 
                    content:event.title,
                    placement:'bottom'
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



    });
    /*  FIN INITIALISE CALENDAR  */

    </script>
    
    
    <script>
    
    function action_event(action, event){
        $this = $(this); $this.popover('hide');
        debugger;
        var x = document.getElementById("pop-"+event.id).parentNode.parentNode.parentNode.id;

        $('#'+x).popover('hide');
         $.ajax({
            type: "POST",
            url: "update_events.php",
            data: {
                do: "search",
                note_id: id_event,
                user: '<?php echo $_SESSION['id_user']; ?>'
            },
            success: function(response) {
                var result = $.trim(response);
                var msgHeader = "", msgBody = "", MsgInput="";
                if(action === 'delete'){
                    if (result === 'Mere') {
                        msgHeader = "1 - Suppression d'une occurence ";
                        msgBody = " <string>rappel :</strong><br />- Toutes les occurences découlant de cette note seront également effacées<br /> - Pour supprimer juste une occurence, cliquez sur l'image correspondante à droite de la note dans les planning<br /><br />Voulez vous supprimer cette note ?";
                        document.getElementById('ModalMsgHeader').innerHTML = msgHeader;
                        document.getElementById('ModalMsgBody').innerHTML = msgBody;
                    }
                    else if (result === 'notMere') {
                        msgHeader = "2 - Suppression d'une note";
                        msgBody = " Vous allez supprimer cette note.<br /> Voulez vous supprimer cette occurence ?";
                        document.getElementById('ModalMsgHeader').innerHTML = msgHeader;
                        document.getElementById('ModalMsgBody').innerHTML = msgBody;
                    }
                    else{
                    }
                }else{      // Edit
                    if (result === 'Mere') {
                        msgHeader = "3 - Modification d'une occurence";
                        msgBody = " - Toutes les occurences découlant de cette note seront également modifiées<br /><br />"
                                +"<label class='checkbox-inline'><input type='checkbox' value='1' id='chbx1' >"
                                +"- Pour modifier seulement cette occurence, cochez la case</label><br /><br />"
                                +"Voulez vous modifier cette note ?";
  
                        document.getElementById('ModalMsgHeader').innerHTML = msgHeader;
                        document.getElementById('ModalMsgBody').innerHTML = msgBody;
  
                        action_event_do(action, id_event, 4);
                    }
                    else if (result === 'notMere') {
debugger;                        
                document.getElementById('NoteStartTime').selectedIndex=starttimeIndex;
                document.getElementById('NoteEndTime').selectedIndex=endtimeIndex;
                document.getElementById('NoteDureeTime').selectedIndex=(endtimeIndex-starttimeIndex);
                $('#createEventModal #A_date').val(A_date);
                $('#createEventModal #NoteAllDay').val(allDay);
                $('#createEventModal').modal('show');

                    }
                    else{
                    }
                    
                }
                $('#confirnModal').modal('show');


            },
            error : function(event, jqxhr, settings, thrownError){
                alert(settings.url);
            }
        });
//        $("#calendar").fullCalendar('rtyefetchEvents');
    }
    
    function action_event_do(action, id_event, param){
        if(action === "delete" || action === "update"){
         $.ajax({
            type: "POST",
            url: action+"_events.php",
            data: {
                note_id: id_event,
                param: param,
                user: '<?php echo $_SESSION['id_user']; ?>'
            },
            success: function(response) {
                var result = $.trim(response);
                if (result === 'Success') {
                    alert('Note créée!');
                  //  $("#calendar").fullCalendar('refetchEvents');
                }
            },
            error : function(event, jqxhr, settings, thrownError){
                alert(settings.url);
            }
        });
        }
        }
    </script>
    
<!-- Modal -->
<div class="modal fade" id="confirnModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="ModalMsgHeader">Modal title</h4>
      </div>
      <div class="modal-body">
          <p id="ModalMsgBody"></p>
          <p id="MsgInput" class="col-xs-2 pull-right"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
</body>
</html>
