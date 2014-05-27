# ytdl-dash

youtube-dl wrapper for downloading dash movies. Requires php, youtube-dl and avconv (ffmpeg).

## Usage
* php ytdl.php <url>
* When installed: ytdl <url>

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
* makes two pipes in that directory, video & audio
* downloads video to /tmp/<name>/video
* downloads audio to /tmp/<name>/audio
* uses avconv to mux it to a file in your current directory
* Should be finished as soon as both downloads are done afaik
* deletes temporary folder

## Issues / Todo
* I'm thinking about downloading ALL the formats at once and muxing them to a single mkv
* This thing needs more command line options
* More error handling
* Get away from php, go to python instead
* autoupdater, like youtube-dl?

## Changelog
* Better temp name generation
* Download the highest video formats
* Better checking for already-existing files
