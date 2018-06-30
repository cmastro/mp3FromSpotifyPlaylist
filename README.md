# mp3FromSpotifyPlaylist
Download MP3 songs from a Spotify Playlist without logging with your account.

This script parce your Spotify Playlist, and search the name song and artist song on Youtube. Then you can download the MP3 from the Youtube link.


# How to Use it

	Copy the the Embed Link from a Spotify Playlist on the "$spotifyPlaylistEmbedCode" var.
	Get your list song from the playlist.
		THIS WILL TAKE A LOT OF TIME DEPENDING OF THE AMOUNT OF SONGS (15 Seconds for song)
		http://localhost/mp3FromSpotifyPlaylist/mp3FromSpotifyPlaylist.php?action=getjson
	Downlad your songs.
		http://localhost/mp3FromSpotifyPlaylist/mp3FromSpotifyPlaylist.php?action=getlink

# How to Use it (Extended)

	1) Open Spotify Desktop Player
	2) Right-click on the Playlist you want to get all songs
	3) Click on "Share"
	4) Select "Copy Embed Code"
	5) Open the "mp3FromSpotifyPlaylist.php" file for edit
	6) Paste the Embed Link Copied on "$spotifyPlaylistEmbedCode" var
		Example:
			$spotifyPlaylistEmbedCode = '<iframe src="https://open.spotify.com/embed/user/cmastro84/playlist/48qPTKyaN89IOScJk8jKOd" width="300" height="380" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe>';
	7) Open your web browser

	8) Now you have two option:

		Op1) Get your list songs from the playlist:
			THIS WILL TAKE A LOT OF TIME DEPENDING OF THE AMOUNT OF SONGS (15 Seconds for song)
			http://localhost/mp3FromSpotifyPlaylist/mp3FromSpotifyPlaylist.php?action=getjson

		Op2) Downlad your songs:
			http://localhost/mp3FromSpotifyPlaylist/mp3FromSpotifyPlaylist.php?action=getlink



# Notes:
	You must get your list songs first at all (8 - Op1). This will put information on the "spotifySongs.json" file.
	It takes 15 seconds to get each song, because youtube ban your IP if you get continues request.


# Donation
If this project help you, you can give me a cup of coffee :)

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.me/cmastro84)


# Thanks!

	1) simple_html_dom
		Redistributions of files must retain the above copyright notice.
		@author S.C. Chen <me578022@gmail.com>
		@author John Schlick
		@author Rus Carroll
		@version 1.5 ($Rev: 196 $)
		@package PlaceLocalInclude
		@subpackage simple_html_dom
	2) https://y-api.org
