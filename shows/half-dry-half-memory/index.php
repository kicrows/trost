<?php
/**
 * Exhibition Page - Reads data from .txt files directly
 * Clean URL: shows/half-dry-half-memory/ (no index.php in URL)
 */

// Disable caching for development
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Configuration
$exhibitionName = 'Half Dry, Half Memory';
$exhibitionSlug = 'half-dry-half-memory';

// Paths - files are in the same directory as this index.php
$currentDir = __DIR__;
$infoTxtPath = $currentDir . '/info.txt';
$imagesTxtPath = $currentDir . '/images.txt';

// Debug: Check if files exist (remove in production)
if (isset($_GET['debug'])) {
    echo "Current directory: " . $currentDir . "<br>";
    echo "Info.txt exists: " . (file_exists($infoTxtPath) ? 'YES' : 'NO') . "<br>";
    echo "Info.txt path: " . $infoTxtPath . "<br>";
    echo "Images.txt exists: " . (file_exists($imagesTxtPath) ? 'YES' : 'NO') . "<br>";
    echo "Images.txt path: " . $imagesTxtPath . "<br>";
    echo "<hr>";
}

// Parse info.txt
function parseInfoTxt($filePath) {
    $info = [
        'headline' => '',
        'date' => '',
        'text' => '',
        'artist' => '',
        'curator' => '',
        'photography' => '',
        'photography_url' => '',
        'links' => []
    ];
    
    if (!file_exists($filePath)) {
        return $info;
    }
    
    $lines = file($filePath, FILE_IGNORE_NEW_LINES);
    $inTextBlock = false;
    $inArtistsBlock = false;
    $textLines = [];
    $currentSection = '';
    
    foreach ($lines as $i => $line) {
        $originalLine = $line;
        $line = trim($line);
        
        // Skip comments
        if (strpos($line, '#') === 0) {
            continue;
        }
        
        // Check for section headers first
        if (stripos($line, 'headline:') === 0) {
            $info['headline'] = trim(substr($line, 9));
            $inTextBlock = false;
            $inArtistsBlock = false;
            $currentSection = '';
        } elseif (stripos($line, 'date:') === 0) {
            $info['date'] = trim(substr($line, 5));
            $inTextBlock = false;
            $inArtistsBlock = false;
            $currentSection = '';
        } elseif (preg_match('/^artist:\s*(.+)$/i', $line, $artistMatch)) {
            $info['artist'] = trim($artistMatch[1]);
            $inArtistsBlock = false;
            $inTextBlock = false;
            $currentSection = '';
        } elseif (preg_match('/^artists:\s*(.*)$/i', $line, $artistsMatch)) {
            $rest = trim($artistsMatch[1] ?? '');
            if ($rest !== '') {
                $artistLine = $rest;
                if (stripos($artistLine, 'Artists:') === 0) {
                    $artistLine = trim(substr($artistLine, 8));
                }
                $info['artist'] = $artistLine;
                $inArtistsBlock = false;
            } else {
                $inArtistsBlock = true;
            }
            $inTextBlock = false;
            $currentSection = 'artists';
        } elseif (stripos($line, 'curation:') === 0) {
            $info['curator'] = trim(substr($line, 9));
            $inTextBlock = false;
            $inArtistsBlock = false;
            $currentSection = '';
        } elseif (stripos($line, 'photography:') === 0) {
            $photoContent = trim(substr($line, 12));
            $photoParts = explode('|', $photoContent);
            $info['photography'] = trim($photoParts[0]);
            $info['photography_url'] = isset($photoParts[1]) ? trim($photoParts[1]) : '';
            $inTextBlock = false;
            $inArtistsBlock = false;
            $currentSection = '';
        } elseif (stripos($line, 'text:') === 0) {
            $textStart = trim(substr($line, 5));
            $textLines = $textStart ? [$textStart] : [];
            $inTextBlock = true;
            $inArtistsBlock = false;
            $currentSection = 'text';
        } elseif (stripos($line, 'link:') === 0) {
            $linkParts = explode('|', trim(substr($line, 5)));
            if (count($linkParts) >= 2) {
                $info['links'][] = [
                    'text' => trim($linkParts[0]),
                    'url' => trim($linkParts[1])
                ];
            }
            $inTextBlock = false;
            $inArtistsBlock = false;
            $currentSection = '';
        } elseif ($inArtistsBlock) {
            // Handle artists block - skip empty lines, but capture the first non-empty line
            if (!empty($line)) {
                // Remove "Artists: " prefix if present
                $artistLine = $line;
                if (stripos($artistLine, 'Artists:') === 0) {
                    $artistLine = trim(substr($artistLine, 8));
                }
                $info['artist'] = $artistLine;
                $inArtistsBlock = false;
            }
        } elseif ($inTextBlock) {
            // Add to text block
            $textLines[] = $line;
        }
    }
    
    $info['text'] = implode("\n", $textLines);
    return $info;
}

// Parse images.txt
function parseImagesTxt($filePath) {
    $images = [];
    
    if (!file_exists($filePath)) {
        return $images;
    }
    
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        
        $parts = explode('|', $line);
        $images[] = [
            'filename' => trim($parts[0]),
            'caption' => isset($parts[1]) ? trim($parts[1]) : ''
        ];
    }
    
    return $images;
}

// Load data
$info = parseInfoTxt($infoTxtPath);
$images = parseImagesTxt($imagesTxtPath);

// Debug output (remove in production)
if (isset($_GET['debug'])) {
    echo "<h2>Debug Info:</h2>";
    echo "Number of links found: " . count($info['links']) . "<br>";
    echo "Links: <pre>" . print_r($info['links'], true) . "</pre>";
    echo "<hr>";
    exit; // Stop here to see debug info
}

// Use info headline or fallback to config
$pageTitle = !empty($info['headline']) ? $info['headline'] : $exhibitionName;
$descriptionLength = function_exists('mb_strlen') ? mb_strlen($info['text']) : strlen($info['text']);
$isLongDescription = $descriptionLength > 1300;
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<!-- Primary Meta Tags -->
		<title><?php echo htmlspecialchars($pageTitle); ?> • TROST</title>
		<meta name="title" content="<?php echo htmlspecialchars($pageTitle); ?> • TROST">
		<meta name="description" content="<?php echo htmlspecialchars($pageTitle); ?> at TROST • Studio, Workspace & Exhibition Venue • Graz">
		<meta name="keywords" content="TROST, art studio, exhibition venue, Graz, <?php echo htmlspecialchars($pageTitle); ?>">
		<meta name="author" content="TROST">
		<meta name="robots" content="index, follow">
		<meta name="language" content="English">
		<meta name="revisit-after" content="7 days">
		
		<!-- Open Graph / Facebook -->
		<meta property="og:type" content="website">
		<meta property="og:url" content="https://trost.space/shows/<?php echo $exhibitionSlug; ?>/">
		<meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?> • TROST">
		<meta property="og:description" content="<?php echo htmlspecialchars($pageTitle); ?> at TROST • Studio, Workspace & Exhibition Venue • Graz">
		
		<!-- Twitter -->
		<meta property="twitter:card" content="summary_large_image">
		<meta property="twitter:url" content="https://trost.space/shows/<?php echo $exhibitionSlug; ?>/">
		<meta property="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?> • TROST">
		<meta property="twitter:description" content="<?php echo htmlspecialchars($pageTitle); ?> at TROST • Studio, Workspace & Exhibition Venue • Graz">
		
		<link rel="stylesheet" href="../../css/base.css">
		<link rel="stylesheet" href="../../css/showas-single.css">
	</head>
	<body class="loading">
		<main>
			<?php 
			$isSubpage = true;
			include __DIR__ . '/../../includes/nav-top.php'; 
			?>
			
			<!-- Exhibition Gallery Container -->
			<div class="exhibition-gallery-container">
				<h1 class="exhibition-title" id="exhibition-title">
					<span class="exhibition-title-text"><?php echo htmlspecialchars($pageTitle); ?></span>
					<?php if (!empty($info['date'])): ?>
						<span class="exhibition-date"><?php echo htmlspecialchars($info['date']); ?></span>
					<?php endif; ?>
				</h1>
				
				<!-- Artist, curator, photography: above frosted description (all viewports) -->
				<?php if (!empty($info['artist']) || !empty($info['curator']) || !empty($info['photography'])): ?>
					<div class="exhibition-show-meta">
						<?php if (!empty($info['artist'])): ?>
							<div class="exhibition-artist"><?php echo htmlspecialchars($info['artist']); ?></div>
						<?php endif; ?>
						<?php if (!empty($info['curator'])): ?>
							<div class="exhibition-curator">Curation: <?php echo htmlspecialchars($info['curator']); ?></div>
						<?php endif; ?>
						<?php if (!empty($info['photography'])): ?>
							<div class="exhibition-photography">
								Photography: 
								<?php if (!empty($info['photography_url'])): ?>
									<a href="<?php echo htmlspecialchars($info['photography_url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($info['photography']); ?></a>
								<?php else: ?>
									<?php echo htmlspecialchars($info['photography']); ?>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				
				<details class="exhibition-description exhibition-description-collapsible<?php echo $isLongDescription ? ' exhibition-description--long' : ''; ?>" id="exhibition-description">
					<summary class="exhibition-description-header">
						<span class="exhibition-description-label">DESCRIPTION</span>
						<span class="exhibition-description-line"></span>
						<span class="exhibition-description-toggle"></span>
					</summary>
					<div class="exhibition-description-content">
					<?php if (!empty($info['text'])): ?>
						<div class="exhibition-description-text"><?php echo nl2br(htmlspecialchars($info['text']), false); ?></div>
					<?php endif; ?>
					</div>
				</details>
				<?php if (!empty($info['links'])): ?>
					<div class="exhibition-links exhibition-links--below-description">
						<?php foreach ($info['links'] as $link): ?>
							<a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($link['text']); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="exhibition-gallery" id="exhibition-gallery">
					<?php if (empty($images)): ?>
						<p class="exhibition-empty">No images found in this exhibition.</p>
					<?php else: ?>
						<?php foreach ($images as $imageData): ?>
							<?php
							$imgPath = $currentDir . '/' . $imageData['filename'];
							$orientationClass = 'exhibition-figure--landscape';
							if (file_exists($imgPath)) {
								$imgDims = @getimagesize($imgPath);
								if ($imgDims && isset($imgDims[0], $imgDims[1]) && $imgDims[1] > $imgDims[0]) {
									$orientationClass = 'exhibition-figure--portrait';
								}
							}
							?>
							<figure class="exhibition-figure <?php echo $orientationClass; ?>">
								<img 
									src="<?php echo htmlspecialchars($imageData['filename']); ?>" 
									alt="<?php echo htmlspecialchars($imageData['caption'] ?: $imageData['filename']); ?>"
									class="exhibition-image"
									loading="lazy"
								>
								<?php if (!empty($imageData['caption'])): ?>
									<figcaption class="exhibition-caption"><?php echo htmlspecialchars($imageData['caption']); ?></figcaption>
								<?php else: ?>
									<figcaption class="exhibition-caption"></figcaption>
								<?php endif; ?>
							</figure>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>
		</main>
		
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				document.body.classList.remove('loading');
			});
		</script>
	</body>
</html>

