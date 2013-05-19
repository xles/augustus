/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.skin = 'BootstrapCK-Skin';
	//config.toolbar = 'Basic';

/* Main post toolbar
	config.toolbar =
		[
			['Source','-','Undo','Redo'],
			['Link', 'Unlink', 'Image'],
			['NumberedList','BulletedList','-','Blockquote'],
			'/',
			['Format','RemoveFormat'],
			['Bold', 'Italic','Underline','StrikeThrough','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Subscript','Superscript']
		];
*/

/* Comment toolbar */
	config.extraPlugins = 'bbcode';
	config.toolbar =
		[
			['Source'],
			['Bold', 'Italic','Underline','StrikeThrough','RemoveFormat'],
			['Link', 'Unlink','Blockquote'],
		];

};

