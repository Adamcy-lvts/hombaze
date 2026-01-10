// 🔥 Service Worker Kill Switch
// This file clears all caches and unregisters itself

self.addEventListener('install', (event) => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    (async () => {
      // Delete all caches
      const cacheNames = await caches.keys();
      await Promise.all(
        cacheNames.map((cache) => caches.delete(cache))
      );

      // Unregister this service worker
      await self.registration.unregister();

      // Take control and reload clients
      const clientsList = await self.clients.matchAll({ type: 'window' });
      clientsList.forEach(client => {
        client.navigate(client.url);
      });
    })()
  );
});
