#!/usr/bin/php
<?php
if ($argc == 1) 
	exit("Ambiguous command, see help for more info.\n");

switch ($argv[1]) {
	case 'help':
	case 'new':
	case 'remove':
	case 'edit':
	default:
		exit("Unknown command '{$argv[1]}'.\n");
		break;
}

function help()
{

}

function _new()
{
	switch ($argv[2]) {
		case 'post':
		case 'category':
		case 'tag':
		default:
			break;
	}
}

function _remove()
{
	switch ($argv[2]) {
		case 'post':
		case 'category':
		case 'tag':
		default:
			break;
	}
}

function _edit()
{
	switch ($argv[2]) {
		case 'post':
		case 'category':
		case 'tag':
		default:
			break;
	}
}
