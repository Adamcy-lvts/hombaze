/**
 * Property Image Validator & Processor
 * Enforces 4:3 standards and optimal resolution.
 */

export async function validateAndProcessImage(file) {
    const MIN_WIDTH = 1024;
    const MIN_HEIGHT = 768;
    const MAX_WIDTH = 2560;
    const MAX_HEIGHT = 1920;
    const QUALITY = 0.8;

    return new Promise((resolve, reject) => {
        const reader = new FileReader();

        reader.onload = (e) => {
            const img = new Image();

            img.onload = () => {
                const width = img.naturalWidth;
                const height = img.naturalHeight;

                // 1. Minimum Resolution Check
                if (width < MIN_WIDTH || height < MIN_HEIGHT) {
                    reject(`This photo is too small (low quality). Please select a clearer photo at least ${MIN_WIDTH}x${MIN_HEIGHT}px.`);
                    return;
                }

                // 2. Enforce 4:3 Aspect Ratio (Center Crop)
                const TARGET_RATIO = 4 / 3;
                const sourceRatio = width / height;

                let srcX = 0;
                let srcY = 0;
                let srcW = width;
                let srcH = height;

                if (sourceRatio > TARGET_RATIO) {
                    // Image is wider than 4:3 - Crop sides
                    srcW = height * TARGET_RATIO;
                    srcX = (width - srcW) / 2;
                } else {
                    // Image is taller than 4:3 - Crop top/bottom
                    srcH = width / TARGET_RATIO;
                    srcY = (height - srcH) / 2;
                }

                // 3. Determine Final Output Size (Max 2560x1920)
                // We keep the crop dimensions unless they exceed max, then scale down maintaining 4:3
                let targetWidth = srcW;
                let targetHeight = srcH;

                if (targetWidth > MAX_WIDTH) {
                    targetWidth = MAX_WIDTH;
                    targetHeight = MAX_WIDTH / TARGET_RATIO;
                }

                // Ensure integers
                targetWidth = Math.round(targetWidth);
                targetHeight = Math.round(targetHeight);

                // 4. Draw to Canvas
                const canvas = document.createElement('canvas');
                canvas.width = targetWidth;
                canvas.height = targetHeight;
                const ctx = canvas.getContext('2d');

                // Better quality resizing
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';

                // drawImage(image, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight)
                ctx.drawImage(img, srcX, srcY, srcW, srcH, 0, 0, targetWidth, targetHeight);

                // 4. Export Blob
                canvas.toBlob((blob) => {
                    if (!blob) {
                        reject("We couldn't process this image. Please try another one.");
                        return;
                    }

                    // Create new File object from Blob to mimic original input
                    const newFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });

                    resolve(newFile);
                }, 'image/jpeg', QUALITY);
            };

            img.onerror = () => reject("This file doesn't look like a valid image.");
            img.src = e.target.result;
        };

        reader.onerror = () => reject("We couldn't read this file. Please try again.");
        reader.readAsDataURL(file);
    });
}
