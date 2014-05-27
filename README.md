# ytdl-dash

youtube-dl wrapper for downloading dash movies. Requires php, youtube-dl and avconv (ffmpeg).

## Installing
Short:
* copy ytdl.php to /usr/local/bin/ytdl and make sure it has correct permissions (root:root, 755)
* Path is optional if you wondered

Slow:
* apt-get install php5-cli youtube-dl ffmpeg
* Download this
* sudo cp ytdl.php /usr/local/bin/ytdl
* sudo chmod 755 /usr/local/bin/ytdl
* sudo chown root:root /usr/local/bin/ytdl

## How does it even...?
* uses youtube-dl to get a list of formats
* uses youtube-dl to get the name of the file
* makes a temporary folder inside /tmp (random name)
* downloads video to /tmp/<name>/video.mp4
* downloads audio to /tmp/<name>/audio.mp4
* uses avconv to mux it to a file in your current directory
* deletes temporary folder
