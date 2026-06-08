<?php
/**
 * Custom 3D spectrum ball cursor — included from nav-top.php on all pages.
 */
$cursorJsPath = !empty($isSubpage) ? '../../js/custom-cursor.js' : 'js/custom-cursor.js';
?>
<div id="trost-cursor" class="trost-cursor" aria-hidden="true">
	<div class="trost-cursor__ball">
		<span class="trost-cursor__color"></span>
		<span class="trost-cursor__shade"></span>
		<span class="trost-cursor__shine"></span>
	</div>
</div>
<script src="<?php echo htmlspecialchars($cursorJsPath); ?>" defer></script>
