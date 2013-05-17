#!/usr/bin/php
<?php
if($argc == 1) 
	exit("Ambiguous command, see help for more info.\n");

switch($argv[1]) {
	case 'help':
	case 'new':
	case 'remove':
	case 'edit':
	default:
		exit("Unknown command '{$argv[1]}'.\n");
		break;
}
