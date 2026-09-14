// service-worker.js
// Minimal service worker - its main job is to make the site installable
// on phones ("Add to Home Screen" / "Install app" in Chrome). It also
// does light caching of static assets (CSS/JS/images) so the site loads
// faster on repeat visits; PHP pages themselves are always fetched fresh
// so products/cart/orders are never served stale.

const CACHE_NAME = 'decomat-static-v1';
const STATIC_CACHE_PATTERNS = [/\/assets\/(css|js|images)\//];

self.addEventListener('install', (event) => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);
  const isStaticAsset = STATIC_CACHE_PATTERNS.some((re) => re.test(url.pathname));

  if (!isStaticAsset || event.request.method !== 'GET') {
    // Let every dynamic PHP page (home, shop, cart, checkout, orders...) go
    // straight to the network - never cache anything that changes per user.
    return;
  }

  event.respondWith(
    caches.open(CACHE_NAME).then((cache) =>
      cache.match(event.request).then((cached) => {
        const fetchPromise = fetch(event.request)
          .then((networkResponse) => {
            cache.put(event.request, networkResponse.clone());
            return networkResponse;
          })
          .catch(() => cached);
        return cached || fetchPromise;
      })
    )
  );
});
