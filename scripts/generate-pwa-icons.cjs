/**
 * PWA Icon Generator for HomeBaze
 *
 * Usage: node scripts/generate-pwa-icons.js
 *
 * This script uses Puppeteer to generate PNG icons from the SVG logo.
 * Make sure puppeteer is installed: npm install puppeteer
 */

const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

const sizes = [72, 96, 128, 144, 152, 192, 384, 512];
const outputDir = path.join(__dirname, '../public/icons');

// HomeBaze emerald color
const brandColor = '#059669';

// Maskable icons need full-bleed background (no rounded corners)
// The OS applies its own mask shape (circle, squircle, etc.)
const svgTemplate = (size) => `
<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 512 512">
  <rect width="512" height="512" fill="${brandColor}"/>
  <g transform="translate(76, 76) scale(0.25)">
    <path fill="white" d="M1274.97 1042.15c64.4 27.18 125.1 58.36 182.18 96.48Q728.36 851.08 0 1140.01c2.44-1.86 4.86-3.94 7.5-5.69 52.31-34.5 108.05-62.58 165.48-87.32 9.14-4 11.37-8.71 11.35-17.86q-.45-168.81-.27-337.63v-16.42l58-21.17v366.77l49.87-16.39V637.59c2.74-1.31 4.44-2.30 6.26-3 27.45-10 55-19.82 82.36-30a26.64 26.64 0 0 1 19.69.07c35.67 13.16 71.45 26 107.22 39 23.71 8.57 47.47 17 71.19 25.58 2.24.81 4.38 1.89 8.06 3.5v272.17c4.74 0 7.9.22 11 0 12.3-1 24.58-2.2 37.57-3.38.31-4.22.77-7.68.77-11.15q0-138.78-.1-277.55c0-12.45 0-12.56-12-17.1-11.58-4.4-23.25-8.56-35.33-13V53.27c3.41-1.47 6.94-3.15 10.57-4.55 41.1-15.77 82.27-31.35 123.26-47.39 6.38-2.49 11.46-1.12 17.16 1.22q78.3 32.18 156.7 64.1 58.38 23.78 116.85 47.33c4 1.64 8 3.41 12.42 5.27v187.6l-295.47 121v509.93c15.47 1.15 30.86.43 47.55 1.08v-477.8l94.4-39.47v523.13l48.9 5.18V402.81c13-5.4 25.18-10.43 37.32-15.43 20-8.24 40-16.25 59.9-24.8 6.33-2.73 11.79-2.85 18.22-.16q99.67 41.73 199.54 83c9.24 3.84 18.52 7.57 27.77 11.41 11.31 4.69 11.31 4.72 11.31 16.32q0 91.32-.08 182.63v374.86Z"/>
    <path fill="white" d="M394.28 549.45v-430l134.16-46.76a20 20 0 0 1 2.28 1.31c.28.24.64.59.64.9q.12 257.64.15 515.3c0 2.74-.24 5.49-.37 8.22-8.81-.29-121.96-40.83-136.86-48.97"/>
  </g>
</svg>
`;

async function generateIcons() {
  // Ensure output directory exists
  if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true });
  }

  console.log('Launching browser...');
  const browser = await puppeteer.launch({
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });

  try {
    const page = await browser.newPage();

    for (const size of sizes) {
      console.log(`Generating ${size}x${size} icon...`);

      const svg = svgTemplate(size);
      const html = `
        <!DOCTYPE html>
        <html>
        <head>
          <style>
            body { margin: 0; padding: 0; }
          </style>
        </head>
        <body>${svg}</body>
        </html>
      `;

      await page.setContent(html);
      await page.setViewport({ width: size, height: size });

      const outputPath = path.join(outputDir, `icon-${size}x${size}.png`);
      await page.screenshot({
        path: outputPath,
        type: 'png',
        clip: { x: 0, y: 0, width: size, height: size }
      });

      console.log(`  Created: ${outputPath}`);
    }

    // Generate favicon.ico (using 32x32)
    console.log('Generating favicon...');
    const faviconSvg = svgTemplate(32);
    const faviconHtml = `
      <!DOCTYPE html>
      <html>
      <head>
        <style>body { margin: 0; padding: 0; }</style>
      </head>
      <body>${faviconSvg}</body>
      </html>
    `;
    await page.setContent(faviconHtml);
    await page.setViewport({ width: 32, height: 32 });
    await page.screenshot({
      path: path.join(__dirname, '../public/favicon.png'),
      type: 'png',
      clip: { x: 0, y: 0, width: 32, height: 32 }
    });

    // Generate Apple touch icon (180x180)
    console.log('Generating Apple touch icon...');
    const appleSvg = svgTemplate(180);
    const appleHtml = `
      <!DOCTYPE html>
      <html>
      <head>
        <style>body { margin: 0; padding: 0; }</style>
      </head>
      <body>${appleSvg}</body>
      </html>
    `;
    await page.setContent(appleHtml);
    await page.setViewport({ width: 180, height: 180 });
    await page.screenshot({
      path: path.join(outputDir, 'apple-touch-icon.png'),
      type: 'png',
      clip: { x: 0, y: 0, width: 180, height: 180 }
    });

    console.log('\nAll icons generated successfully!');
    console.log('\nGenerated files:');
    sizes.forEach(size => console.log(`  - public/icons/icon-${size}x${size}.png`));
    console.log('  - public/icons/apple-touch-icon.png');
    console.log('  - public/favicon.png');

  } finally {
    await browser.close();
  }
}

generateIcons().catch(console.error);
