<div class="box box-primary">
  <div class="box-body no-padding">
    <!-- THE CALENDAR -->
    <div id="calendar"></div>
  </div>
</div>

<style>
.fc-disabled {
    background-color: #F0F0F0 !important;
    color: #000;
    cursor: default;
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 10px;
}
</style>

<script language="javascript">
$(function () {
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        buttonText: {
            today: 'today',
            month: 'month',
            week: 'week',
            day: 'day'
        },
        events: function(start, end, timezone, callback) {
            $.ajax({
                url: '<?php echo WEB_ROOT; ?>api/process.php?cmd=calview',
                dataType: 'json',
                type: 'POST',
                data: {
                    start: start.format(),
                    end: end.format()
                },
                success: function(response) {
                    if (response.error) {
                        console.error("Error: " + response.error);
                        alert("Error loading events: " + response.error);
                        return;
                    }
                    callback(response);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    alert("Failed to load calendar data");
                }
            });
        },
        editable: false,
        droppable: false,
        // Improve the event rendering and appearance
        eventRender: function(event, element, view) {
            // Add tooltip with more details
            var statusText = '';
            switch(event.backgroundColor) {
                case '#00cc00': statusText = 'Approved'; break;
                case '#007bff': statusText = 'Success'; break;
                case '#ff0000': statusText = 'Denied'; break;
                case '#AA38A6': statusText = 'Outsource'; break;
                default: statusText = 'Waiting'; break;
            }
            
            
            // Add a small icon to indicate status
            var statusIcon = $('<span>').addClass('status-dot').css({
                'display': 'inline-block',
                'width': '8px',
                'height': '8px',
                'border-radius': '50%',
                'margin-right': '4px',
                'background-color': event.backgroundColor
            });
            
            // For agendaView, format differently
            if (view.name === 'month') {
                element.find('.fc-time').remove(); // Remove time in month view to save space
                var eventTime = event.start ? event.start.format('HH:mm') + ' ' : '';
element.find('.fc-content').html(statusIcon).append('<span style="margin-left: 5px;">' + eventTime + event.title + '</span>');

            } 
            
            // If there are multiple events in the same day in month view
            if (view.name === 'month') {
                var eventsInSameDay = $('#calendar').fullCalendar('clientEvents', function(otherEvent) {
                    return otherEvent !== event && moment(otherEvent.start).isSame(event.start, 'day');
                });
                
                // If there are many events on the same day, make them more compact
                if (eventsInSameDay.length > 2) {
                    element.css({
                        'margin-bottom': '1px',
                        'padding-top': '1px',
                        'padding-bottom': '1px',
                        'line-height': '1.1'
                    });
                }
            }
        },
        // Add limit to number of events shown per day
        eventLimit: true, // When there are too many events, show a "+X more" link
        eventLimitText: "more ", // Customize the text in Thai
        eventLimitClick: "popover", // Show a popover with all events when clicked
        views: {
            month: {
                eventLimit: 4 // Adjust this number based on your needs
            }
        },
                    // In calendar.php, modify the eventClick function:
            eventClick: function(calEvent, jsEvent, view) {
                var loggedInUser = '<?php echo $_SESSION['calendar_fd_user']['name']; ?>';
                var userType = '<?php echo $_SESSION['calendar_fd_user']['type']; ?>';

                // Allow admin, owner, and employee to view all bookings
                // Regular users can only view their own bookings
                if (
    userType === 'admin' ||
    userType === 'owner' ||
    userType === 'employee' ||
    userType === 'driver' || // ✅ เพิ่มบรรทัดนี้เข้าไป
    calEvent.title === loggedInUser
) {
    window.location.href = calEvent.url;
} else {
    alert("You can only view your own bookings.");
    return false;
}

            },
        dayRender: function(date, cell) {
            $(cell).css('opacity', 1);
        },
        eventAfterRender: function(ev, element, view) {
            if(ev.block == true) {
                var start = ev.start.format();
                $("td.fc-day[data-date='"+ start +"']").addClass('fc-disabled');
            }
        },
        // Add additional options for better appearance
        timeFormat: 'H:mm', // 24-hour format
        displayEventTime: false, // Don't show event time in month view
        eventColor: '#f5f5f5', // Default light background
        eventTextColor: '#ffffff', // Dark text for contrast
        height: '500', // Adjust height automatically
        contentHeight: '400' // Content height auto
    });

    // เพิ่มสีแสดงสถานะของ Event
    setTimeout(function(){
        $(".fc-left").append(`
            <div style="display: inline-block; margin-left: 15px;">
                <span style="display: inline-block; width: 12px; height: 12px; background-color: #28a745; border-radius: 50%; margin-right: 5px;"></span> Approve
                <span style="display: inline-block; width: 12px; height: 12px; background-color: #007bff; border-radius: 50%; margin-left: 15px; margin-right: 5px;"></span> Success
                <span style="display: inline-block; width: 12px; height: 12px; background-color: #fd7e14; border-radius: 50%; margin-left: 15px; margin-right: 5px;"></span> Waiting
                <span style="display: inline-block; width: 12px; height: 12px; background-color: #dc3545; border-radius: 50%; margin-left: 15px; margin-right: 5px;"></span> Denied
                <span style="display: inline-block; width: 12px; height: 12px; background-color: #AA38A6; border-radius: 50%; margin-left: 15px; margin-right: 5px;"></span> Outsource
            </div>
        `);
    }, 0);
});
</script>