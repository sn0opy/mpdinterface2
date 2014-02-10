$(document).ready(function() {
	// controlls
	$('#playback').click(function() {
		if($('#audioplayer').paused)
			$('#audioplayer').play();
		else
			$('#audioplayer').pause();
	});
	
	$('.controls a').click(function(e) {	
		ctrlType = $(this).attr('href');
		$.get(ctrlType);		
		e.preventDefault();
	});

	// check mpd status periodically
	setInterval(function() {
		$.getJSON(BASE + '/status', function(data) {
			console.log(data);	
			
			if(data.state == 'play') {
				$('#playpause i').attr('class', 'icon-pause');
				$('#playpause').attr('href', '/control/pause');
			}
						
			if(data.state == 'pause') {
				$('#playpause i').attr('class', 'icon-play');
				$('#playpause').attr('href', '/control/play');
			}
			
			if(data.state == 'stop')
				$('#npHead').html('');
			else
				$('#npHead').html(data.currentTrack.artist + ' - ' + data.currentTrack.title);
		});
	}, 4000);
});
