<?php
/**
 * Top Navigation Component - Shared across all pages
 * Reads shows from shows/shows.txt and generates consistent navigation
 */

// Function to create URL-friendly slug (only define if not already defined)
if (!function_exists('createSlug')) {
    function createSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^\w\s-]/', '', $slug); // Remove special characters
        $slug = preg_replace('/\s+/', '-', $slug);     // Replace spaces with hyphens
        $slug = preg_replace('/-+/', '-', $slug);      // Replace multiple hyphens with single
        $slug = trim($slug, '-');                      // Remove leading/trailing hyphens
        return $slug;
    }
}

// Determine if we're on a subpage (show page)
$isSubpage = isset($isSubpage) ? $isSubpage : false;
$backLink = $isSubpage ? '../../' : '';
$imgPath = $isSubpage ? '../../' : '';

// Read shows from shows.txt
$showsTxtPath = __DIR__ . '/../shows/shows.txt';
$shows = [];

if (file_exists($showsTxtPath)) {
    $lines = file($showsTxtPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue; // Skip empty lines and comments
        }
        
        $parts = explode('|', $line);
        if (count($parts) >= 3) {
            $name = trim($parts[0]);
            $folder = trim($parts[1]);
            /* Use folder from shows.txt so links match on-disk paths (name slug can drift) */
            $slug = preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/i', $folder) ? $folder : createSlug($name);
            
            $shows[] = [
                'name' => $name,
                'slug' => $slug,
                'url' => $backLink . 'shows/' . $slug . '/'
            ];
        }
    }
    
    // Reverse so shows at bottom of .txt file appear first
    $shows = array_reverse($shows);
}

// Current route: highlight in-nav controls (INFO, current show)
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$navInfoActive = (bool) preg_match('#(^|/)info\.php$#i', $scriptPath);
$navActiveShowSlug = '';
if (preg_match('#/shows/([^/]+)/#', $scriptPath, $navPathMatch)) {
    $navActiveShowSlug = $navPathMatch[1];
}
?>
<?php include __DIR__ . '/custom-cursor.php'; ?>
<div class="frame__top-nav">
	<div class="frame__logo">
		<a href="<?php echo $backLink; ?>index.php">
			<img src="<?php echo $imgPath; ?>img/logo-pony-01.svg" alt="TROST Logo" class="logo-pony">
		</a>
	</div>
	<?php if ($isSubpage): ?>
		<div class="frame__back button">
			<a href="<?php echo $backLink; ?>index.php">
				<span class="back-text-desktop">← Back to Shows</span>
				<span class="back-text-mobile">← BACK</span>
			</a>
		</div>
	<?php else: ?>
		<div class="frame__shows-nav" id="shows-nav">
			<?php if (!empty($shows)): ?>
				<ul class="shows-nav-list">
					<?php foreach ($shows as $show): ?>
						<?php
						$showNavActive = ($navActiveShowSlug !== '' && isset($show['slug']) && $show['slug'] === $navActiveShowSlug);
						?>
						<li class="shows-nav-item<?php echo $showNavActive ? ' shows-nav-item--active' : ''; ?>">
							<a href="<?php echo htmlspecialchars($show['url']); ?>" class="shows-nav-link"<?php echo $showNavActive ? ' aria-current="page"' : ''; ?>>
								<?php echo htmlspecialchars($show['name']); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<div class="shows-nav-empty">No shows available</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<div class="frame__top-nav-right">
		<div class="frame__info button<?php echo $navInfoActive ? ' button--nav-active' : ''; ?>">
			<a href="<?php echo $backLink; ?>info.php"<?php echo $navInfoActive ? ' aria-current="page"' : ''; ?>>
				<span class="info-text-desktop">ABOUT</span>
				<span class="info-text-mobile">ABOUT</span>
			</a>
		</div>
		<div class="frame__instagram button">
			<a href="https://www.instagram.com/trost.spc/" target="_blank" rel="noopener noreferrer">
				<span class="instagram-text-desktop">Instagram</span>
				<span class="instagram-text-mobile">IG</span>
			</a>
		</div>
		<div class="frame__address button">
			<a href="https://maps.app.goo.gl/ZAby8UFXSJ8uBEMr5" target="_blank" rel="noopener noreferrer">
				<span class="address-text-desktop">LESSINGSTRASSE 28, 8010 GRAZ, AUT</span>
				<span class="address-text-mobile">MAP</span>
			</a>
		</div>
	</div>
	<?php if (! $isSubpage && !empty($isIndex)): ?>
	<div class="frame__burger">
		<button type="button" class="unbutton nav-overlay-toggle" id="nav-overlay-toggle-btn" aria-expanded="false" aria-label="Open menu">
			<span class="burger-icon">
				<span class="burger-line"></span>
				<span class="burger-line"></span>
			</span>
		</button>
	</div>
	<div class="nav-overlay" id="nav-overlay">
		<div class="nav-overlay-inner">
			<div class="nav-overlay-shows">
				<?php if (!empty($shows)): ?>
				<ul class="shows-nav-list">
					<?php foreach ($shows as $show): ?>
						<?php
						$showNavActive = ($navActiveShowSlug !== '' && isset($show['slug']) && $show['slug'] === $navActiveShowSlug);
						?>
						<li class="shows-nav-item<?php echo $showNavActive ? ' shows-nav-item--active' : ''; ?>">
							<a href="<?php echo htmlspecialchars($show['url']); ?>" class="shows-nav-link"<?php echo $showNavActive ? ' aria-current="page"' : ''; ?>>
								<?php echo htmlspecialchars($show['name']); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php else: ?>
					<div class="shows-nav-empty">No shows available</div>
				<?php endif; ?>
			</div>
			<div class="nav-overlay-buttons">
				<div class="frame__info button<?php echo $navInfoActive ? ' button--nav-active' : ''; ?>">
					<a href="<?php echo $backLink; ?>info.php"<?php echo $navInfoActive ? ' aria-current="page"' : ''; ?>>
						<span class="info-text-desktop">INFO</span>
						<span class="info-text-mobile">INFO</span>
					</a>
				</div>
				<div class="frame__instagram button">
					<a href="https://www.instagram.com/trost.spc/" target="_blank" rel="noopener noreferrer">
						<span class="instagram-text-desktop">Instagram</span>
						<span class="instagram-text-mobile">IG</span>
					</a>
				</div>
				<div class="frame__address button">
					<a href="https://maps.app.goo.gl/ZAby8UFXSJ8uBEMr5" target="_blank" rel="noopener noreferrer">
						<span class="address-text-desktop">LESSINGSTRASSE 28, 8010 GRAZ, AUT</span>
						<span class="address-text-mobile">MAP</span>
					</a>
				</div>
			</div>
			<div class="nav-overlay-footer" id="nav-overlay-footer"></div>
		</div>
	</div>
	<?php endif; ?>
</div>

<script>
(function() {
    var logo = document.querySelector('.logo-pony');
    if (!logo) return;
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    function rand(min, max) {
        return Math.random() * (max - min) + min;
    }

    function sleep(ms) {
        return new Promise(function(resolve) { setTimeout(resolve, ms); });
    }

    function bounce(jumpPx, durationMs, spinDeg) {
        spinDeg = spinDeg || 0;
        /* Stronger comic squash / stretch (same for spin and no-spin) */
        var sqWindX = 1.14;
        var sqWindY = 0.84;
        var sqAirX = 0.91;
        var sqAirY = 1.09;
        var sqLandX = 1.2;
        var sqLandY = 0.8;
        var keyframes = [
            { transform: 'translateY(0) rotateZ(0deg) scaleX(1) scaleY(1)', offset: 0 },
            { transform: 'translateY(0) rotateZ(0deg) scaleX(' + sqWindX + ') scaleY(' + sqWindY + ')', offset: 0.2 },
            { transform: 'translateY(' + (-jumpPx) + 'px) rotateZ(' + (spinDeg ? spinDeg : 0) + 'deg) scaleX(' + sqAirX + ') scaleY(' + sqAirY + ')', offset: 0.52 },
            { transform: 'translateY(0) rotateZ(' + (spinDeg ? spinDeg : 0) + 'deg) scaleX(' + sqLandX + ') scaleY(' + sqLandY + ')', offset: 0.78 },
            { transform: 'translateY(0) rotateZ(0deg) scaleX(1) scaleY(1)', offset: 1 }
        ];
        var anim = logo.animate(keyframes, {
            duration: durationMs,
            easing: 'cubic-bezier(0.25, 0.55, 0.25, 1)',
            fill: 'forwards'
        });
        return anim.finished;
    }

    (async function loop() {
        while (true) {
            await sleep(rand(3000, 6800)); // calmer pause between main bounces
            // Main bounce: full spin only sometimes (~1 in 4)
            await bounce(rand(18, 24), rand(1150, 1750), Math.random() < 0.25 ? 360 : 0);

            // Sometimes tiny follow-up bounces; longer gaps + slower motion
            if (Math.random() < 0.38) {
                await sleep(rand(480, 980));
                await bounce(rand(8, 14), rand(720, 1180), Math.random() < 0.06 ? 180 : 0);
            }
            if (Math.random() < 0.16) {
                await sleep(rand(380, 780));
                await bounce(rand(6, 10), rand(640, 1040), 0);
            }
        }
    })();
})();
</script>

