#!/usr/bin/env node

/**
 * Generate Shows Script
 * Scans shows.txt and automatically:
 * 1. Generates PHP pages (directory-based routing) for each show
 * 2. Creates images.txt and info.txt if missing
 * 3. Copies .txt files to the slug directory
 * 
 * Note: Navigation is now handled dynamically by PHP (includes/nav-top.php)
 * 
 * Run: node js/generate-shows.js
 */

const fs = require('fs');
const path = require('path');

const SHOWS_DIR = path.join(__dirname, 'shows');
const TEMPLATE_DIR = path.join(SHOWS_DIR, 'show-template');
const TEMPLATE_FILE = path.join(TEMPLATE_DIR, 'index.php');
const SHOWS_TXT = path.join(SHOWS_DIR, 'shows.txt');

/**
 * Convert folder name to URL-friendly slug
 */
function createSlug(name) {
    return name
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-+|-+$/g, '');
}

/**
 * Get all image files from a folder
 */
function getImagesFromFolder(folderPath) {
    const files = fs.readdirSync(folderPath);
    const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
    
    return files
        .filter(file => {
            const ext = path.extname(file).toLowerCase();
            return imageExtensions.includes(ext);
        })
        .sort();
}

/**
 * Generate images.txt file for a show folder
 */
function generateImagesTxt(folderPath, folderName) {
    const imagesTxtPath = path.join(folderPath, 'images.txt');
    
    // Skip if already exists
    if (fs.existsSync(imagesTxtPath)) {
        console.log(`  ✓ images.txt already exists for ${folderName}`);
        return;
    }
    
    const images = getImagesFromFolder(folderPath);
    
    if (images.length === 0) {
        console.log(`  ⚠ No images found in ${folderName}`);
        return;
    }
    
    let content = '# Images list - Format: filename|caption\n';
    content += '# One image per line\n';
    content += '# Leave caption empty if no caption needed\n';
    content += '# Example: 01_image.jpg|This is the caption\n\n';
    
    images.forEach(image => {
        content += `${image}|\n`;
    });
    
    fs.writeFileSync(imagesTxtPath, content);
    console.log(`  ✓ Created images.txt for ${folderName} (${images.length} images)`);
}

/**
 * Generate info.txt file for a show folder
 */
function generateInfoTxt(folderPath, folderName) {
    const infoTxtPath = path.join(folderPath, 'info.txt');
    
    // Skip if already exists
    if (fs.existsSync(infoTxtPath)) {
        console.log(`  ✓ info.txt already exists for ${folderName}`);
        return;
    }
    
    let content = '# Exhibition Info - Format: one item per line\n';
    content += '# Leave empty if not needed\n';
    content += '# Lines starting with # are comments\n';
    content += '# Links: Use any label name you want (format: link: Name|URL)\n';
    content += '# You can have 1-3 links (or more), each with its own custom label\n\n';
    content += `headline:${folderName}\n`;
    content += `date:${new Date().toISOString().split('T')[0]}\n`;
    content += 'text:Exhibition description text goes here.\n';
    content += 'link:Name|https://example.com\n';
    content += 'link:Another Name|https://example.com/another\n';
    
    fs.writeFileSync(infoTxtPath, content);
    console.log(`  ✓ Created info.txt for ${folderName}`);
}

/**
 * Generate PHP page for a show (directory-based routing)
 */
function generateShowHtml(folderName, slug, forceUpdate = false) {
    const showDir = path.join(SHOWS_DIR, slug);
    const indexPath = path.join(showDir, 'index.php');
    
    // Skip if already exists and not forcing update
    if (fs.existsSync(indexPath) && !forceUpdate) {
        console.log(`  ✓ PHP page already exists for ${slug}`);
        return;
    }
    
    // Read template
    if (!fs.existsSync(TEMPLATE_FILE)) {
        console.error(`  ✗ Template file not found: ${TEMPLATE_FILE}`);
        return;
    }
    
    let template = fs.readFileSync(TEMPLATE_FILE, 'utf8');
    
    // Create directory if it doesn't exist
    if (!fs.existsSync(showDir)) {
        fs.mkdirSync(showDir, { recursive: true });
    }
    
    // Replace placeholders in template
    template = template.replace(/EXHIBITION_NAME/g, folderName);
    template = template.replace(/EXHIBITION_FOLDER/g, folderName);
    template = template.replace(/EXHIBITION_SLUG/g, slug);
    
    // Write the PHP file
    fs.writeFileSync(indexPath, template);
    console.log(`  ✓ Created PHP page: ${slug}/index.php`);
}

/**
 * Note: index.php now uses PHP to read shows.txt directly via includes/nav-top.php
 * No need to update index.php - navigation is generated dynamically
 */
function updateIndexHtml() {
    // Navigation is now handled by PHP in includes/nav-top.php
    // which reads shows/shows.txt directly
    console.log('  ℹ Navigation is generated dynamically by PHP (no update needed)');
}

/**
 * Parse shows.txt file
 */
function parseShowsTxt() {
    if (!fs.existsSync(SHOWS_TXT)) {
        return [];
    }
    
    const content = fs.readFileSync(SHOWS_TXT, 'utf8');
    const shows = [];
    
    content.split('\n').forEach(line => {
        const trimmed = line.trim();
        if (!trimmed || trimmed.startsWith('#')) return;
        
        const parts = trimmed.split('|');
        if (parts.length >= 2) {
            shows.push({
                name: parts[0].trim(),
                folder: parts[1].trim(),
                date: parts.length >= 3 ? parts[2].trim() : new Date().toISOString().split('T')[0]
            });
        }
    });
    
    return shows;
}

/**
 * Main function
 */
function main() {
    console.log('🔍 Checking shows.txt and folders...\n');
    
    if (!fs.existsSync(SHOWS_DIR)) {
        console.error(`✗ Shows directory not found: ${SHOWS_DIR}`);
        process.exit(1);
    }
    
    // Read shows.txt first
    const showsFromTxt = parseShowsTxt();
    console.log(`Found ${showsFromTxt.length} show(s) in shows.txt\n`);
    
    if (showsFromTxt.length === 0) {
        console.log('⚠ No shows found in shows.txt');
        console.log('   Add shows to shows.txt first, then run this script');
        return;
    }
    
    // Get all folders in shows/
    const items = fs.readdirSync(SHOWS_DIR, { withFileTypes: true });
    const existingFolders = items
        .filter(item => item.isDirectory() && item.name !== '.git')
        .map(item => item.name);
    
    let generatedCount = 0;
    let skippedCount = 0;
    let missingFolderCount = 0;
    
    // Process each show from shows.txt
    showsFromTxt.forEach(show => {
        const folderPath = path.join(SHOWS_DIR, show.folder);
        const slug = createSlug(show.name);
        const showDir = path.join(SHOWS_DIR, slug);
        const indexPath = path.join(showDir, 'index.php');
        
        // Check if folder exists
        if (!fs.existsSync(folderPath)) {
            console.log(`⚠ Show "${show.name}" in shows.txt but folder "${show.folder}" not found`);
            missingFolderCount++;
            return;
        }
        
        // Generate everything for this show
        const isNewPage = !fs.existsSync(indexPath);
        if (isNewPage) {
            console.log(`📁 Generating: ${show.name}`);
        } else {
            console.log(`🔄 Updating: ${show.name}`);
        }
        console.log(`   Folder: ${show.folder}`);
        console.log(`   Slug: ${slug}`);
        
        // Generate images.txt if missing (in the original folder)
        generateImagesTxt(folderPath, show.folder);
        
        // Generate info.txt if missing (in the original folder)
        generateInfoTxt(folderPath, show.folder);
        
        // Copy images.txt and info.txt to the slug directory if they don't exist there
        const slugImagesTxt = path.join(showDir, 'images.txt');
        const slugInfoTxt = path.join(showDir, 'info.txt');
        const folderImagesTxt = path.join(folderPath, 'images.txt');
        const folderInfoTxt = path.join(folderPath, 'info.txt');
        
        if (fs.existsSync(folderImagesTxt) && !fs.existsSync(slugImagesTxt)) {
            fs.copyFileSync(folderImagesTxt, slugImagesTxt);
            console.log(`  ✓ Copied images.txt to ${slug}/`);
        }
        if (fs.existsSync(folderInfoTxt) && !fs.existsSync(slugInfoTxt)) {
            fs.copyFileSync(folderInfoTxt, slugInfoTxt);
            console.log(`  ✓ Copied info.txt to ${slug}/`);
        }
        
        // Generate/Update PHP page
        generateShowHtml(show.folder, slug, isNewPage);
        
        if (isNewPage) {
            generatedCount++;
        } else {
            skippedCount++;
        }
        console.log('');
    });
    
    // Check for folders that aren't in shows.txt
    const foldersNotInTxt = existingFolders.filter(folder => {
        return !showsFromTxt.some(show => show.folder === folder);
    });
    
    if (foldersNotInTxt.length > 0) {
        console.log(`\n⚠ Found ${foldersNotInTxt.length} folder(s) not in shows.txt:`);
        foldersNotInTxt.forEach(folder => {
            console.log(`   - ${folder}`);
        });
        console.log('   Add them to shows.txt if you want them in the navigation\n');
    }
    
    // Note: index.php navigation is generated dynamically by PHP
    console.log('📄 Navigation is handled by PHP (includes/nav-top.php)');
    updateIndexHtml();
    console.log('');
    
    // Summary
    console.log('✅ Done!');
    console.log(`   • Generated ${generatedCount} new PHP page(s)`);
    console.log(`   • Updated ${skippedCount} existing page(s)`);
    if (missingFolderCount > 0) {
        console.log(`   • ⚠ ${missingFolderCount} show(s) in shows.txt but folder missing`);
    }
    if (foldersNotInTxt.length > 0) {
        console.log(`   • ⚠ ${foldersNotInTxt.length} folder(s) not in shows.txt`);
    }
}

// Run
main();

