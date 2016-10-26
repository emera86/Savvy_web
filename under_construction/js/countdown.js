(function($) {
	$.fn.countdown = function(options, callback) {
		element = $(this);
		var settings = { 'date': null, 'format': null };
		if(options) {
			$.extend(settings, options);
		}
		function countdown() {		
			countToDate = Date.parse(settings['date']) / 1000;
			currentDate = Math.floor($.now() / 1000);
			
			if(countToDate <= currentDate) {
				callback.call(this);
				clearInterval(interval);
			}
			
			seconds = countToDate - currentDate;
			
			days = Math.floor(seconds / (60 * 60 * 24));
			seconds -= days * 60 * 60 * 24;
			
			hours = Math.floor(seconds / (60 * 60));
			seconds -= hours * 60 * 60;
			
			minutes = Math.floor(seconds / 60);
			seconds -= minutes * 60;
			
			if (days == 1) { element.find(".time_Days").text("Days"); } else { element.find(".time_Days").text("Days"); }
			if (hours == 1) { element.find(".time_Hours").text("Hours"); } else { element.find(".time_Hours").text("Hours"); }
			if (minutes == 1) { element.find(".time_Minutes").text("Minutes"); } else { element.find(".time_Minutes").text("Minutes"); }
			if (seconds == 1) { element.find(".time_Seconds").text("Seconds"); } else { element.find(".time_Seconds").text("Seconds"); }

			if(settings['format'] == "on") {
				days = (String(days).length >= 2) ? days : "0" + days;
				hours = (String(hours).length >= 2) ? hours : "0" + hours;
				minutes = (String(minutes).length >= 2) ? minutes : "0" + minutes;
				seconds = (String(seconds).length >= 2) ? seconds : "0" + seconds;
			}

			if(!isNaN(countToDate)) {
				element.find(".days").text(days);
				element.find(".hours").text(hours);
				element.find(".minutes").text(minutes);
				element.find(".seconds").text(seconds);
			} else { 
				alert("Date is invalid!");
				clearInterval(interval); 
			}
		}
		

		countdown();
		interval = setInterval(countdown,1000);
		
	}
}) (jQuery);