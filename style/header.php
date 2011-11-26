<?php

/**
 * dbox :: style/header.php
 *
 * This is the style header for all pages.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011 Gordon P. Hemsley
 * @license docs/LICENSE BSD License
 */

if( !defined( 'ROOT' ) )
{
	exit;
}

@require_once( ROOT . 'inc/inc.main.php' );

header( 'Content-Type: text/html; charset=UTF-8' );

if( !isset( $tab ) )
{
	$tab = FALSE;
}

$tab_home = $tab_records = $tab_dictionary = $tab_dev = '';

switch( $tab )
{
	case 'home':
		$tab_home = ' class="selected"';
	break;

	case 'records':
		$tab_records = ' class="selected"';
	break;

	case 'dictionary':
		$tab_dictionary = ' class="selected"';
	break;

	case 'dev':
		$tab_dev = ' class="selected"';
	break;
}

$breadcrumbs = implode( ' &rarr; ', $page_title );

rsort( $page_title );

$title = implode( ' :: ', $page_title ) . ' :: dbox';

?>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="UTF-8" />
	<title><?php print $title; ?></title>

	<link rel="icon" type="image/png" sizes="32x32" href="<?php print ROOT; ?>style/images/favicon.png" />

	<link rel="stylesheet" type="text/css" media="screen" href="<?php print ROOT; ?>style/screen.css" />
	<link rel="stylesheet" type="text/css" media="print" href="<?php print ROOT; ?>style/print.css" />
</head>
<body>

<header id="header">
	<div id="logo">
		<a href="<?php print ROOT; ?>index.php"><img src="<?php print ROOT; ?>style/images/favicon.png" alt="" width="32" height="32" /></a>
	</div>
	<div id="title">
		<h1>dbox</h1>
		<p style="margin: auto; /*font-variant: small-caps;*/ font-style: italic; font-family: cursive;">language documentation done right</p>
	</div>
	<div id="user-links">
<?php


if( $Sessions->is_logged_in() )
{
	print "\t\t" . $User->user_info['name'] . "\n";
	print "\t\t&middot;\n";
//	print "\t\t" . '<a href="' . ROOT . 'register.php">Edit Profile</a>' . "\n";
//	print "\t\t&middot;\n";
	print "\t\t" . '<a href="' . ROOT . 'login.php?logout=1">Log Out</a>' . "\n";
}
else
{
	print "\t\t" . '<a href="' . ROOT . 'register.php">Register</a>' . "\n";
	print "\t\t&middot;\n";
	print "\t\t" . '<a href="' . ROOT . 'login.php">Log In</a>' . "\n";
}

?>
	</div>
</header>

<nav id="navigation">
	<ul>
		<li<?php print $tab_home; ?>><a href="<?php print ROOT; ?>index.php">Home</a></li>
		<li<?php print $tab_records; ?>><a href="<?php print ROOT; ?>records.php">Elicitations</a></li>
		<li<?php print $tab_dictionary; ?>><a href="<?php print ROOT; ?>dictionary.php">Dictionary</a></li>
<?php

if( !empty( $tab_dev ) )
{
	print "\t\t" . '<li' . $tab_dev . '><a href="' . ROOT . 'dev/">Development</a></li>' . "\n";
}

?>
	</ul>
</nav>

<div id="breadcrumbs">
	<p><?php print $breadcrumbs; ?></p>
</div>

<?php

if( exists( $config['message'][0]['message'] ) )
{
	print '<div id="global-msg">' . "\n";

	foreach( $config['message'] as $message )
	{
		if( !empty( $message['message'] ) )
		{
			print_message( $message['type'], $message['message'], $message['title'] );
		}
	}

	print '</div>' . "\n\n";
}

?>
<div id="page-content">
