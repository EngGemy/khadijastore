const fs = require('fs');
const path = require('path');

const files = [
    'cairo-arabic-400-normal.woff2',
    'cairo-arabic-600-normal.woff2',
    'cairo-arabic-700-normal.woff2',
];

const srcDir = path.join(__dirname, '..', 'node_modules', '@fontsource', 'cairo', 'files');
const destDir = path.join(__dirname, '..', 'public', 'fonts', 'cairo');

fs.mkdirSync(destDir, { recursive: true });

for (const file of files) {
    fs.copyFileSync(path.join(srcDir, file), path.join(destDir, file));
}

console.log(`Copied ${files.length} Cairo font files to public/fonts/cairo/`);
