<script>
    document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialDate: Date.now(),
      locale: 'de',
      timeZone: 'UTC',
      nextDayThreshold: '09:00:00',
      navLinks: false, // can click day/week names to navigate views
      businessHours: false, // display business hours
      editable: true,
      selectable: true,
      eventOverlap: false,
      selectOverlap: false,
      weekNumbers: true,
      validRange : function(nowDate){
        return {
          start: nowDate
        }
      },
      eventSources: [
        {
          events: @json($events_json),
          id: 'db_events',
          color: 'magenta',
          editable: false,
          overlap: false,
        }
      ],
      select: function(info) {
        document.getElementById("start_date").value = info.startStr;
        document.getElementById("end_date").value = getDate(info.end, -1);
        var new_event = calendar.getEventById('new_event');
        if(new_event == null){
          calendar.addEvent({
            id: 'new_event',
            start: info.startStr,
            end: info.endStr,
            allDay: true
          });
        }
        else
        {
          new_event.setDates( info.startStr, info.endStr, {allDay: true});
        }
        calendar.unselect()
      },
      eventDrop: function (eventDropInfo ) {
        document.getElementById("start_date").value = getDate(eventDropInfo.event.start, 0);
        document.getElementById("end_date").value = getDate(eventDropInfo.event.end, -1);
      },
      eventResize: function (eventResizeInfo ) {
        document.getElementById("end_date").value = getDate(eventResizeInfo.event.end, -1);
      },
      eventChange: function (changeInfo ) {
        var db_eventSource = calendar.getEventSourceById('db_events')
        var db_events = db_eventSource.internalEventSource.meta;
        var new_event = calendar.getEventById('new_event');
        if(new_event != null){
          var start = getDate(changeInfo.event.start, 0);
          var end = getDate(changeInfo.event.end, -1);
          if(isOverlapping(start, end, db_events)){
            var resultDiv = document.getElementById("datewarn");
            resultDiv.innerHTML = "Die Buchung darf sich nicht mit bestehenden Buchungen überschneiden";
            resultDiv.style.color = "red";
            document.getElementById('start_date').value =getDate(changeInfo.oldEvent.start, 0);
            document.getElementById("end_date").value = getDate(changeInfo.oldEvent.end, -1);
            new_event.setDates( changeInfo.oldEvent.startStr, changeInfo.oldEvent.endStr, {allDay: true});
          }
        }
      },
    });

    
    var calendar_form = document.querySelector('#calendarform');   

    calendar_form.addEventListener('focusout', function () {
      var start_date = document.querySelector('#start_date'); 
      var end_date = document.querySelector('#end_date'); 
      var resultDiv = document.getElementById("datewarn");
      var new_event = calendar.getEventById('new_event');

      if(!isFutureDate(start_date.value)){
        resultDiv.innerHTML = "Das Buchungsdatum muss in der Zukunft liegen";
        resultDiv.style.color = "red";
        document.querySelector('#start_date').value = null;
        document.querySelector('#end_date').value = null;
        new_event.remove();
      } 
      else {
        resultDiv.innerHTML = "";
        resultDiv.style.color = "red";
        if (!!start_date.value && !!end_date.value){
          if(new_event == null){
            var db_eventSource = calendar.getEventSourceById('db_events')
            var db_events = db_eventSource.internalEventSource.meta;
            if(isOverlapping(start_date.value, end_date.value, db_events)){
              var resultDiv = document.getElementById("datewarn");
              resultDiv.innerHTML = "Die Buchung darf sich nicht mit bestehenden Buchungen überschneiden";
              resultDiv.style.color = "red";
              document.getElementById('start_date').value = null;
              document.getElementById("end_date").value = null;
            }
            else{
              calendar.addEvent({
                id: 'new_event',
                start: start_date.value,
                end: getDate(end_date.value, 1),
                allDay: true
              });
            }
            var new_event = calendar.getEventById('new_event');
          }
          else
          {
            new_event.setDates(start_date.value, getDate(end_date.value, 1),   {allDay: true});
          }
        }
      }
    });

    calendar.render();

    


  });
  </script>