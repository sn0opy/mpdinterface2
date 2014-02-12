<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>mpdinterface</title>
	<link rel="stylesheet" href="{{@BASE}}/assets/css/gumby.css" type="text/css" />
	<link rel="stylesheet" href="{{@BASE}}/assets/css/style.css" type="text/css" />
</head>
<body>
	<check if="{{isset(@mpd_httpd_host) && isset(@mpd_httpd_port) && isset(@streamType)}}">
	<audio id="audioplayer">
		<source src="http://{{@mpd_httpd_host}}:{{@mpd_httpd_port}}" type="{{@streamType}}" />
	</audio>
	</check>
	<nav class="navbar">
		<div class="row">
			<span class="nowplaying" id="npHead"></span>
			<nav class="pull_right">
				<ul class="controls">
					<li><a href="{{@BASE}}/control/previous"><i class="icon-fast-backward"></i></a></li>
					<li><a href="{{@BASE}}/control/play" id="playpause"><i class="icon-play"></i></a></li>
					<li><a href="{{@BASE}}/control/stop"><i class="icon-stop"></i></a></li>
					<li><a href="{{@BASE}}/control/next"><i class="icon-fast-forward"></i></a></li>
					<check if="{{isset(@mpd_httpd_host) && isset(@mpd_httpd_port) && isset(@streamType)}}">
						<li><a href="#" class="playback" id="playback"><span id="pbstatus" title="Start audio stream in browser"><i class="icon-note"></i><span id="pbstatustxt">off</span></span></a></li>
					</check>
				</ul>
			</nav>
		</div>
	</nav>
	<div class="row">
	<table class="playlist">
		<tbody>
			<repeat group="{{@playlist}}" value="{{@row}}">
				<tr{{(@currentTrack.id == @row.Id)?' class="nowplaying"':''}}>
					<td>{{isset(@row.Artist) ? @row.Artist : 'n/a'}}</td>
					<td class="controls"><a href="{{@BASE}}/control/playback/{{@row.Pos}}">{{isset(@row.Title) ? @row.Title : isset(@row.Name) ? @row.Name : @row.file}}</a></td>
					<td>{{isset(@row.Album) ? @row.Album : 'n/a'}}</td>
					<td>{{isset(@row.Time) ? gmdate("i:s", @row.Time) : 'n/a'}}</td>
				</tr>
			</repeat>
		</tbody>
	</table>
	</div>
	<script src="{{@BASE}}/assets/js/jquery.js"></script>
	<script>
		BASE = "{{@BASE}}";
	</script>
	<script src="{{@BASE}}/assets/js/mpdinterface.js"></script>
</body>
</html>
