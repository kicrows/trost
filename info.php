<?php
/**
 * Info Page
 */
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<!-- Primary Meta Tags -->
		<title>Info • TROST</title>
		<meta name="title" content="Info • TROST">
		<meta name="description" content="TROST is a studio, workspace, and occasional exhibition venue run by René Stiegler and Markus Sworcik in Graz.">
		<meta name="keywords" content="TROST, art studio, exhibition venue, Graz, René Stiegler, Markus Sworcik">
		<meta name="author" content="TROST">
		<meta name="robots" content="index, follow">
		<meta name="language" content="English">
		<meta name="revisit-after" content="7 days">
		
		<!-- Open Graph / Facebook -->
		<meta property="og:type" content="website">
		<meta property="og:url" content="https://trost.space/info.php">
		<meta property="og:title" content="Info • TROST">
		<meta property="og:description" content="TROST is a studio, workspace, and occasional exhibition venue run by René Stiegler and Markus Sworcik in Graz.">
		<meta property="og:image" content="https://trost.space/img/logo-trost-01.jpg">
		<meta property="og:site_name" content="TROST">
		<meta property="og:locale" content="en_US">
		
		<!-- Additional SEO -->
		<meta name="geo.region" content="AT-6">
		<meta name="geo.placename" content="Graz">
		<meta name="geo.position" content="47.0707;15.4395">
		<meta name="ICBM" content="47.0707, 15.4395">
		<meta name="theme-color" content="#000000">
		
		<!-- Favicons -->
		<link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
		<link rel="manifest" href="favicon_io/site.webmanifest">
		
		<!-- Stylesheets -->
		<link rel="stylesheet" href="https://use.typekit.net/bvo6szq.css">
		<link rel="stylesheet" type="text/css" href="css/base.css" />
		
		<!-- Scripts -->
		<script>document.documentElement.className="js";var supportsCssVars=function(){var e,t=document.createElement("style");return t.innerHTML="root: { --tmp-var: bold; }",document.head.appendChild(t),e=!!(window.CSS&&window.CSS.supports&&window.CSS.supports("font-weight","var(--tmp-var)")),t.parentNode.removeChild(t),e};supportsCssVars()||alert("Please view this demo in a modern browser that supports CSS Variables.");</script>
	</head>
	<body class="loading">
		<main class="info-page-main">
			<?php 
			$isSubpage = false;
			include __DIR__ . '/includes/nav-top.php'; 
			?>
			<div class="info-page-container">
				<div class="info-page-content text-container">
					<div class="trost-about-content">
					trost.spc is an extended studio and occasional exhibition place founded by René Stiegler and Markus Sworcik in 2025. They first worked together on a duo exhibition in 2022 and continued through exhibitions, installations, collaborations and curatorial projects with regional, national and international artists.

They work across sculpture, installation, video, sound and other forms, drawn to material tension, behavioral residue, environmental influence, speculation and psychological states that resist becoming fully readable.

The place stays rough. Nothing enters neutral conditions here. Works settle, resist, drift, contaminate or remain unresolved.

trost.spc functions less as a fixed exhibition format and more as a temporary structure for fragments, repetition, unfinished transitions and works that change through contact with the room.
					</div>
				</div>
			</div>
		</main>
		
		<script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis@0.2.28/bundled/lenis.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/ScrollTrigger.min.js"></script>
		<script src="https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js"></script>
		<script src="js/index.js"></script>
	</body>
</html>