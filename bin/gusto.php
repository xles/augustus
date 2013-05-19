#!/usr/bin/php
<?php
/**
 * Augustus, a static page generator
 */
namespace Augustus;
$version = "Augustus 0.0.1\n";
date_default_timezone_set('UTC');

if ($argc == 1) 
	exit("Ambiguous command, see `gusto help` for more info.\n");

include('./src/augustus.php');

$gusto = new Augustus();

if (in_array('--help', $argv))
	print_help();
if (in_array('--version', $argv))
	exit($version);

if ($argc == 2) {
	switch ($argv[1]) {
		case '-h':
		case 'help':
			print_help();
			break;
	}
}

if ($argv[1][0] == '-') {
	$options = array_slice(str_split($argv[1]), 1);
	$method = implode('_',array_slice($argv, 2, 2));
	$args = array_slice($argv, 4);
} else {
	$method = implode('_',array_slice($argv, 1, 2));
	$args = array_slice($argv, 3);
}

if (method_exists($gusto, $method)) {
	$gusto->$method($args);
} else {
	if ($argv[1][0] == '-')
		$cmd = implode(' ',array_slice($argv, 2));
	else
		$cmd = implode(' ',array_slice($argv, 1));

	exit("Unknown command '{$cmd}'.\n");
}


/**
 * Prints help and usage to the termianl
 */
function print_help()
{
	$help = 
'Augustus is a static page generator and blog engine, written in php 5.4

Usage: gusto [options] <command> [<args>].

Available commands:
   add    Adds new entry to 
   rm     Remove an entry from
   edit   Alters an entry in
   list   Lists entries in
   help   Prints this help file.

Examples:
   gusto add post
';
	exit($help);
}
