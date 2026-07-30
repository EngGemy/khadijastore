/* Alamat Shop — Web Push service worker */
self.addEventListener('push', function (event) {
  let data = { title: 'متجر العلامات', body: '', url: '/', icon: '/favicon.ico', tag: 'alamat-shop' };
  try {
    if (event.data) {
      data = Object.assign(data, event.data.json());
    }
  } catch (e) {
    try {
      data.body = event.data ? event.data.text() : '';
    } catch (_) {}
  }

  event.waitUntil(
    self.registration.showNotification(data.title || 'متجر العلامات', {
      body: data.body || '',
      icon: data.icon || '/favicon.ico',
      badge: data.icon || '/favicon.ico',
      tag: data.tag || 'alamat-shop',
      data: { url: data.url || '/' },
      dir: 'rtl',
      lang: 'ar',
      vibrate: [120, 60, 120],
    })
  );
});

self.addEventListener('notificationclick', function (event) {
  event.notification.close();
  const target = (event.notification.data && event.notification.data.url) || '/';
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
      for (let i = 0; i < clientList.length; i++) {
        const client = clientList[i];
        if ('focus' in client) {
          client.navigate(target);
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(target);
      }
    })
  );
});
