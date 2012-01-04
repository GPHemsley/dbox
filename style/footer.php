<?php

/**
 * dbox :: style/footer.php
 *
 * This is the style footer for all pages.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011-2012 Gordon P. Hemsley
 * @license docs/LICENSE Mozilla Public License, v. 2.0
 */

if( !defined( 'ROOT' ) )
{
	exit;
}

@require_once( ROOT . 'inc/inc.main.php' );

?>
</div>

<footer>
	<div id="copyright">
		<p><a href="http://udel.gphemsley.org/dbox/">dbox</a></p>
		<p>Copyright &copy; 2011-2012 <a href="mailto:ghemsley@udel.edu">Gordon P. Hemsley</a></p>
	</div>

	<div id="debug">
		<p><a href="<?php print ROOT; ?>docs/NEWS">Version <?php print DBOX_VERSION; ?></a></p>
	</div>
</footer>

</body>
</html>