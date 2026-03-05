const CACHE_NAME = "dirasam-cache-v5";
const STATIC_CACHE = "static-v2";

// URLs que NUNCA deben cachearse
const NEVER_CACHE_URLS = [
  '/Iniciar_Sesion.php',
  '/Menu/index.php',
  '/conexion.php',
  '/Registrarse.php',
  '/recuperar_contrasena.php',
  '/Menu/AdminUsuario/',
  '/logout.php'
];

// Solo recursos estáticos para cache
const STATIC_RESOURCES = [
  "/",
  "/manifest.json",
  "/Formulario.css",
  "/Copiryt.php",
  
  // Imágenes
  "/iconos/icon2-8.png",

  // CSS, JS estáticos (si los tienes)
  "/css/",
  "/js/",
  "/iconos/"
];

self.addEventListener("install", (event) => {
  console.log('[SW] Instalando...');
  event.waitUntil(
    caches.open(STATIC_CACHE).then(async (cache) => {
      try {
        // Solo cachear recursos estáticos confirmados
        const staticUrls = STATIC_RESOURCES.filter(url => 
          !url.endsWith('/') && !url.includes('.php')
        );
        await cache.addAll(staticUrls);
        console.log("[SW] Recursos estáticos cacheados");
      } catch (err) {
        console.warn("[SW] Error al cachear estáticos:", err);
      }
    })
  );
  self.skipWaiting();
});

self.addEventListener("activate", (event) => {
  console.log('[SW] Activado y tomando control');
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys.map((key) => {
          if (key !== STATIC_CACHE) {
            console.log('[SW] Eliminando cache viejo:', key);
            return caches.delete(key);
          }
        })
      )
    )
  );
  self.clients.claim();
});

self.addEventListener("fetch", (event) => {
  const url = new URL(event.request.url);
  
  // ❌ NO INTERCEPTAR solicitudes críticas
  if (NEVER_CACHE_URLS.some(criticalUrl => url.pathname.includes(criticalUrl))) {
    console.log('[SW] Pasando solicitud crítica:', url.pathname);
    return;
  }
  
  // ❌ NO INTERCEPTAR formularios POST
  if (event.request.method === 'POST') {
    console.log('[SW] Pasando solicitud POST:', url.pathname);
    return;
  }
  
  // ❌ NO INTERCEPTAR archivos PHP (dinámicos)
  if (url.pathname.endsWith('.php')) {
    console.log('[SW] Pasando archivo PHP:', url.pathname);
    return;
  }
  
  // ❌ NO INTERCEPTAR solicitudes de API/JSON
  if (url.pathname.includes('/api/') || event.request.headers.get('accept')?.includes('application/json')) {
    return;
  }

  // ✅ SOLO cachear recursos estáticos (CSS, JS, imágenes)
  if (event.request.destination === 'style' || 
      event.request.destination === 'script' || 
      event.request.destination === 'image' ||
      event.request.destination === 'font') {
    
    event.respondWith(
      caches.match(event.request).then(cachedResponse => {
        // Intentar red primero para recursos estáticos
        return fetch(event.request)
          .then(networkResponse => {
            // Cachear solo si es exitoso
            if (networkResponse.status === 200) {
              const clone = networkResponse.clone();
              caches.open(STATIC_CACHE).then(cache => {
                cache.put(event.request, clone);
              });
            }
            return networkResponse;
          })
          .catch(() => {
            // Fallback al cache si no hay red
            return cachedResponse || new Response('Recurso no disponible', { status: 404 });
          });
      })
    );
    return;
  }

  // ✅ Para navegación (HTML), priorizar red siempre
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request)
        .catch(() => {
          // Solo en caso de offline extremo
          return caches.match('/') || new Response('Página no disponible offline', { 
            status: 503,
            headers: { 'Content-Type': 'text/html; charset=utf-8' }
          });
        })
    );
    return;
  }

  // ✅ Para otras solicitudes, pasar directamente
  console.log('[SW] Pasando solicitud no manejada:', url.pathname);
});