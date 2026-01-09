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

                // 2. Processing Check (Resize if > Max or simply re-compress)
                // We always process to ensure consistent JPEG format and removing metadata if needed
                let targetWidth = width;
                let targetHeight = height;

                if (width > MAX_WIDTH || height > MAX_HEIGHT) {
                    const ratio = Math.min(MAX_WIDTH / width, MAX_HEIGHT / height);
                    targetWidth = Math.round(width * ratio);
                    targetHeight = Math.round(height * ratio);
                }

                // 3. Draw to Canvas
                const canvas = document.createElement('canvas');
                canvas.width = targetWidth;
                canvas.height = targetHeight;
                const ctx = canvas.getContext('2d');

                // Better quality resizing
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';

                ctx.drawImage(img, 0, 0, targetWidth, targetHeight);

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
