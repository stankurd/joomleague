<?php
if ($argc != 3) {
	print "Usage: $argv[0] <manifest_filename> <version_part>\n";
	print "where: version_part is MAJOR, MINOR, BUILD or REVISION";
	die ();
}

$manifest = file_get_contents ( $argv [1] );
preg_match ( '/.*<version>(\d+).(\d+).(\d+).([\da-fA-F]+)<\/version>.*/', $manifest, $matches );
if (count ( $matches ) == 5) {
	if ($argv [2] == "MAJOR") {
		print $matches [1];
	} else if ($argv [2] == "MINOR") {
		print $matches [2];
	} else if ($argv [2] == "BUILD") {
		print $matches [3];
	} else if ($argv [2] == "REVISION") {
		print $matches [4];
	} else {
		die ( "Unknown version_part" );
	}
} else {
	die ( "Version information could not be located" );
} 