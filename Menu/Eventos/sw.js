self.addEventListener("install", function (event) {
    self.skipWaiting();
  });
  
  self.addEventListener("notificationclick", function (event) {
    event.notification.close();
    event.waitUntil(
      clients.openWindow("https://directorioasambleasvzla.com/Menu/Eventos/")
    );
  });
  