<?php

/**
 * dbox :: style/footer.php
 *
 * This is the style footer for all pages.
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

?>
</div>

<div id="copyright">
	<p><a href="http://udel.gphemsley.org/dbox/">dbox</a></p>
	<p>Copyright &copy; 2011 <a href="mailto:ghemsley@udel.edu">Gordon P. Hemsley</a></p>
</div>

<div id="debug">
	<p>Version <?php print DBOX_VERSION; ?></p>
</div>

</body>
</html>