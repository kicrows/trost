<?php
/**
 * Shared favicon links.
 * Set $faviconBasePath before including when the page is in a subdirectory.
 */
$faviconBasePath = isset($faviconBasePath) ? $faviconBasePath : '';
?>
<!-- Favicons -->
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo htmlspecialchars($faviconBasePath); ?>favicon_io/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo htmlspecialchars($faviconBasePath); ?>favicon_io/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo htmlspecialchars($faviconBasePath); ?>favicon_io/favicon-16x16.png">
<link rel="manifest" href="<?php echo htmlspecialchars($faviconBasePath); ?>favicon_io/site.webmanifest">
