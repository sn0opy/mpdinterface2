$(document).ready(function() {
	$('#playback').click(function() {
		if($('#audioplayer').paused)
			$('#audioplayer').play();
		else
			$('#audioplayer').pause();
	});
	

	$('.controls a').click(function(e) {
		e.preventDefault();
		ctrlType = $(this).attr('href');
		updateData();
	});


	oldSong = -1;
	function updateData() {
		$.getJSON(BASE + '/status', function(data) {
			console.log(data);

			if(data.status.state == 'play') {
				$('#playpause i').attr('class', 'icon-pause');
				$('#playpause').attr('href', '/control/pause');
			}
						
			if(data.status.state == 'pause') {
				$('#playpause i').attr('class', 'icon-play');
				$('#playpause').attr('href', '/control/play');
			}

			if(data.status.state == 'stop' || data.status.state == undefined) {
				$('#npHead').html('MPD stopped or offline');
				$('#playpause i').attr('class', 'icon-play');
				$('#playpause').attr('href', '/control/play');
			} else {
				if(data.currentTrack.artist != undefined || data.currentTrack.artist != '')
					$('#npHead').html(data.currentTrack.title);
				else
					$('#npHead').html(data.currentTrack.artist + ' - ' + data.currentTrack.title);
				
				if(data.currentTrack.pos != oldSong)
					$('.playlist tr[data-songid="'+oldSong+'"]').removeClass('nowplaying');

				$('.playlist tr[data-songid="'+data.currentTrack.pos+'"]').addClass('nowplaying');
				oldSong = data.currentTrack.pos;
			}
		});
	}
	updateData();
	setInterval(updateData, 4000);
});
