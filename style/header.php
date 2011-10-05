<?php

/**
 * dbox :: style/header.php
 *
 * This is the style header for all pages.
 *
 * @package dbox
 * @copyright (C) 2006-2011 Gordon P. Hemsley
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

$tab_home = $tab_records = $tab_morphology = '';

switch( $tab )
{
	case 'home':
		$tab_home = ' class="selected"';
	break;

	case 'records':
		$tab_records = ' class="selected"';
	break;

	case 'morphology':
		$tab_morphology = ' class="selected"';
	break;
}

?>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>dbox :: <?php print implode( ' :: ', $page_title ); ?></title>

	<link rel="stylesheet" type="text/css" href="<?php print ROOT; ?>style/screen.css" media="screen" />

	<link rel="icon" type="image/png" href="<?php print ROOT; ?>style/images/box_logo.png" />
</head>
<body>

<div id="header">
	<!--div id="logo">
		<a href="<?php print ROOT; ?>index.php"><img src="<?php print ROOT; ?>style/images/box_logo.png" alt="" width="75" height="75" /></a>
	</div-->
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
</div>

<div id="navigation">
	<ul>
		<li<?php print $tab_home; ?>><a href="<?php print ROOT; ?>index.php">Home</a></li>
		<li<?php print $tab_records; ?>><a href="<?php print ROOT; ?>records.php">Records</a></li>
		<li<?php print $tab_morphology; ?>><a href="<?php print ROOT; ?>morphology.php">Morphology</a></li>
	</ul>
</div>

<div id="breadcrumbs">
	<p><?php print implode( ' &rarr; ', $page_title ); ?></p>
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
