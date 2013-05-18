#!/usr/bin/php
<?php
namespace Augustus;
if ($argc == 1) 
	exit("Ambiguous command, see help for more info.\n");

include('./src/augustus.php');

switch ($argv[1]) {
	case 'help':
		print_help();
		break;
	case 'new':
		_new($argv);
		break;
	case 'remove':
	case 'edit':
	default:
		exit("Unknown command '{$argv[1]}'.\n");
		break;
}



function _new($argv)
{
	switch ($argv[2]) {
		case 'post':
			new_post();
			break;
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
function new_post()
{
	echo "Creating a new post\nTitle: ";
	$title = trim(fgets(STDIN));

	echo "Publish date [".date('Ymd')."]: ";
	$date = trim(fgets(STDIN));
	if(empty($date))
		$date = date('Ymd');

	echo "Category [Uncategorized]: ";
	$category = trim(fgets(STDIN));
	if(empty($category))
		$category = 'Uncategorized';

	echo "Tags (separate by commas): ";
	$tags = array_map('trim',(explode(',', fgets(STDIN))));

	$json = ['title'    => $title,
		 'category' => $category,
		 'tags'     => $tags,
		 'pubdate'  => $date];

	$gusto = new Augustus();

	$filename = $gusto->new_post($json);
	shell_exec('subl -w ./'.$filename);
	exit("Blog post saved as $filename.");
}

/**
 * Prints help and usage to the termianl
 */
function print_help()
{
	$help = 
'Augustus is a static page generator and blog engine, written in php 5.4

Usage:  gusto [options] [command [subcommand [...]]].

Available commands:
   add
   remove
   edit
   help';
	exit($help);
}
