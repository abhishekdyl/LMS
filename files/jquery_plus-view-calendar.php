<?php
function plus_view_calendar(){
  global $wp;
  if ( !is_user_logged_in() || !current_user_can('plus_viewcalendar')) {
    return plus_view_noaccess();
  }
  $current_user = wp_get_current_user();
  $current_user = wp_get_current_user();
  $searchreq = new stdClass();
  $searchreq->id = plus_get_request_parameter("id", "");
  $html="";
  $html .=" <div id='calendar'></div>
  <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
  <script src='https://code.jquery.com/ui/1.12.1/jquery-ui.js'></script>
  <link rel='stylesheet' href='https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' />
  <script>

    function myFunction(dtime) {
        const d = new Date(dtime);
        var year = d.getFullYear();
        var monthint = String(d.getMonth()).padStart(2, '0');
        var mon = parseInt(monthint) + 1;
        var dat = String(d.getDate()).padStart(2, '0');
        var hr = String(d.getHours()).padStart(2, '0');
        var min = String(d.getMinutes()).padStart(2, '0');
        var eventdat = year+'-'+mon+'-'+dat+'T'+hr+':'+min;
        return eventdat;
    }

      $(document).ready(function() {
          $('#calendar').fullCalendar({
              header: {
                  left: 'prev,next today',
                  center: 'title',
                  right: 'month,agendaWeek,agendaDay,addEventButton'
              },
              events: '/api/calendarevents.php',
              defaultDate: moment().format('YYYY-MM-DD'),
              editable: false,
              eventLimit: true,
              customButtons: {
                addEventButton: {
                  text: 'Add Event',
                  click: function() {
                    console.log('clicked add event');
                    var titlelabel =  \$('<label>').text(' Start date: ');
                    var title =  \$('<input>').attr('type', 'datetime-local');
                    title.attr('placeholder', 'Start date');
                    title.attr('id', 'event_date');

                    var selectedOption;
                    var dialog = \$('<div>').append(
                        \$('<br><br>')
                    ).dialog({
                        modal: true,
                        buttons: {
                            'Add': function() {
                              selectedOption2 = $('#event_date').val();
                              var url = window.location.origin+'/add-event?startdate='+selectedOption2+'&returnto=calendar';
                              window.location.href = url ;
                            },
                            'Cancel': function() {
                                \$(this).dialog('close');
                            }
                        },
                        close: function() {
                            if (selectedOption) {
                                \$('#calendar').fullCalendar('renderEvent', {
                                    title: title.val() + ' - ' + selectedOption,
                                    start: moment(),
                                    allDay: true
                                });
                            }
                        }
                    });
                    dialog.prepend(title);
                    dialog.prepend(titlelabel);
                  }
                },
              },
              eventClick: function(calEvent, jsEvent, view) {
                var prevent = calEvent['fulldata'];
                console.log('aaaa',prevent);
                var sttime = myFunction((prevent.timestart*1000));
                var edtime = myFunction((prevent.timeend*1000));

                var dialog = \$('<div class= row event_list >').append(
                    \$('<h4>').addClass('col-sm-3').text(' School : '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'text').prop('disabled', true).attr('value',prevent.institution),
                    \$('<h4>').addClass('col-sm-3').text(' Teacher : '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'text').prop('disabled', true).attr('value',prevent.teacher),
                    \$('<h4>').addClass('col-sm-3').text(' Title : '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'text').prop('disabled', true).attr('value',prevent.name),
                    \$('<h4>').addClass('col-sm-3').text(' Description : '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'text').prop('disabled', true).attr('value',prevent.description),
                    \$('<h4>').addClass('col-sm-3').text(' Starttime : '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'datetime-local').prop('disabled', true).attr('value',sttime),
                    \$('<h4>').addClass('col-sm-3').text(' Endtime : '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'datetime-local').prop('disabled', true).attr('value',edtime),
                    
                ).dialog({
                    modal: true,
                    width: 800,
                    maxWidth: 900
                });

                console.log(`eventClick calEvent: `, calEvent)
                console.log(`eventClick jsEvent: `, jsEvent)
                console.log(`eventClick view: `, view)
              },
              dayClick: function(date, jsEvent, view) {
                  console.log('clicked add event');
                  const d = new Date(date['_d']);
                    var year = d.getFullYear();
                    var monthint = String(d.getMonth()).padStart(2, '0');
                    var mon = parseInt(monthint) + 1;
                    var dat = String(d.getDate()).padStart(2, '0');
                    var hr = String(d.getHours()).padStart(2, '0');
                    var min = String(d.getMinutes()).padStart(2, '0');
                  var eventdat = year+'-'+mon+'-'+dat+'T'+hr+':'+min;
                  var titlelabel =  \$('<label>').text(' Start date: ');
                  var title =  \$('<input>').attr('type', 'datetime-local');
                  title.attr('id', 'event_date');
                  title.attr('value', eventdat);

                  var dropdown = \$('<select>').append(
                      \$('<option>').text('Option 1'),
                      \$('<option>').text('Option 2'),
                      \$('<option>').text('Option 3')
                  );
                  var selectedOption;
                  var dialog = \$('<div>').append(
                      \$('<br><br>'),
                      \$('<label>').text(' Select an option: '),
                      dropdown
                  ).dialog({
                      modal: true,
                      buttons: {
                          'Add': function() {
                              selectedOption2 = $('#event_date').val();
                              var url = window.location.origin+'/add-event?startdate='+selectedOption2+'&returnto=calendar';
                              window.location.href = url ;
                          },
                          'Cancel': function() {
                              \$(this).dialog('close');
                          }
                      },
                      close: function() {
                          if (selectedOption) {
                              \$('#calendar').fullCalendar('renderEvent', {
                                  title: title.val() + ' - ' + selectedOption,
                                  start: moment(),
                                  allDay: true
                              });
                          }
                      }
                  });
                  dialog.prepend(title);
                  dialog.prepend(titlelabel);

                console.log(`dayClick date: `, date)
                console.log(`dayClick jsEvent: `, jsEvent)
                console.log(`dayClick view: `, view)
              }
          });
      });
  </script>";
  return $html;
}