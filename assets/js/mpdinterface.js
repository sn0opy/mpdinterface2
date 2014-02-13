$(document).ready(function() {
	// controlls
	$('#playback').click(function() {
		if($('#audioplayer').paused)
			$('#audioplayer').play();
		else
			$('#audioplayer').pause();
	});
	
	$('.controls a').click(function(e) {
		e.preventDefault();
		ctrlType = $(this).attr('href');
		$.get(ctrlType);
	});

	// check mpd status periodically
	setInterval(function() {
		$.getJSON(BASE + '/status', function(data) {
			if(data.state == 'play') {
				$('#playpause i').attr('class', 'icon-pause');
				$('#playpause').attr('href', '/control/pause');
			}
						
			if(data.state == 'pause') {
				$('#playpause i').attr('class', 'icon-play');
				$('#playpause').attr('href', '/control/play');
			}

			if(data.state == 'stop') {
				$('#npHead').html('');
				$('#playpause i').attr('class', 'icon-play');
				$('#playpause').attr('href', '/control/play');
			} else if(data.state == undefined) {
				$('#npHead').html('MPD seems to be offline');
			} else {
				if(data.currentTrack.artist != '')
					$('#npHead').html(data.currentTrack.artist + ' - ' + data.currentTrack.title);
				else
					$('#npHead').html(data.currentTrack.title);
			}
		});
	}, 4000);
});
