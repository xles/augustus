<?php
include_once('html/head.html');
$file = 'list_posts.html';
if(isset($_GET['p']))
	$file = $_GET['p'].'.html';

echo get_file($file);
include_once('html/foot.html');

function get_file($file)
{
	if(!file_exists($file))
		return '404';

	$email = md5('xles@mirakulix.org');
	$gravatarurl = 'http://gravatar.com/avatar/'.$email.'?s=500';
	$replygrav = 'http://gravatar.com/avatar/'.$email.'?s=400';
	$photourl = 'http://placekitten.com/300/450/';

	ob_start();
	include_once($file);
	$buffer = ob_get_contents();
	ob_end_clean();

	ob_start();
	include_once('html/bacon.html');
	$bacon = ob_get_contents();
	ob_end_clean();


	$buffer = str_replace('{bacon}',$bacon,$buffer);

	return $buffer;
}
