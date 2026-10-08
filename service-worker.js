const CACHE_NAME = 'sw-panel-cache-v3';

const urlsToCache = [
  `/index.php`,
  `/img/logo.png`,
  `/img/fav.png`,
  `/img/chat_notification.wav`,
  `/apAdmin/setWebToken.php`,
  `/apAdmin/assets/css/bootstrap.min.css`,
  `/apAdmin/assets/css/app-style.css`,
  `/apAdmin/assets/css/custom.css`,
  `/apAdmin/assets/plugins/simplebar/css/simplebar.css`,
  `/apAdmin/assets/plugins/bootstrap-datatable/css/dataTables.bootstrap4.min.css`,
  `/apAdmin/assets/plugins/fancybox/css/jquery.fancybox.min.css`,
  `/apAdmin/assets/plugins/notifications/css/lobibox.min.css`,
  `/apAdmin/assets/plugins/select2/css/select2.min.css`,
  `/apAdmin/assets/plugins/inputtags/css/bootstrap-tagsinput.css`,
  `/apAdmin/assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css`,
  `/apAdmin/assets/plugins/material-datepicker/css/bootstrap-material-datetimepicker.min.css`,
  `/apAdmin/assets/js/jquery.min.js`,
  `/apAdmin/assets/js/popper.min.js`,
  `/apAdmin/assets/js/bootstrap.min.js`,
  `/apAdmin/assets/js/jquery-ui.js`,
  `/apAdmin/assets/plugins/simplebar/js/simplebar.js`,
  `/apAdmin/assets/js/waves.js`,
  `/apAdmin/assets/js/sidebar-menu.js`,
  `/apAdmin/assets/js/app-script.js`,
  `/apAdmin/assets/plugins/bootstrap-datatable/js/jquery.dataTables.min.js`,
  `/apAdmin/assets/plugins/bootstrap-datatable/js/dataTables.bootstrap4.min.js`,
  `/apAdmin/assets/plugins/bootstrap-datatable/js/dataTables.buttons.min.js`,
  `/apAdmin/assets/plugins/bootstrap-datatable/js/buttons.bootstrap4.min.js`,
  `/apAdmin/assets/plugins/bootstrap-datatable/js/jszip.min.js`,
  `/apAdmin/assets/plugins/bootstrap-datatable/js/pdfmake.min.js`,
  `/apAdmin/assets/plugins/bootstrap-datatable/js/vfs_fonts.js`,
  `/apAdmin/assets/plugins/bootstrap-datatable/js/buttons.html5.min.js`,
  `/apAdmin/assets/plugins/bootstrap-datatable/js/buttons.print.min.js`,
  `/apAdmin/assets/plugins/bootstrap-datatable/js/buttons.colVis.min.js`,
  `/apAdmin/assets/plugins/notifications/js/lobibox.min.js`,
  `/apAdmin/assets/plugins/notifications/js/notifications.min.js`,
  `/apAdmin/assets/plugins/notifications/js/notification-custom-script.js`,
  `/apAdmin/assets/plugins/alerts-boxes/js/sweetalert.min.js`,
  `/apAdmin/assets/plugins/alerts-boxes/js/sweet-alert-script.js`,
  `/apAdmin/assets/plugins/Chart.js/Chart.min.js`,
  `/apAdmin/assets/plugins/peity/jquery.peity.min.js`,
  `/apAdmin/assets/plugins/fancybox/js/jquery.fancybox.min.js`,
  `/apAdmin/assets/plugins/summernote/dist/summernote-bs4.min.js`,
  `/apAdmin/assets/plugins/jquery-validation/js/jquery.validate.min.js`,
  `/apAdmin/assets/plugins/select2/js/select2.min.js`,
  `/apAdmin/assets/plugins/inputtags/js/bootstrap-tagsinput.js`,
  `/apAdmin/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js`,
  `/apAdmin/assets/plugins/jquery-multi-select/jquery.multi-select.js`,
  `/apAdmin/assets/plugins/jquery-multi-select/jquery.quicksearch.js`,
  `/apAdmin/assets/plugins/material-datepicker/js/moment.min.js`,
  `/apAdmin/assets/plugins/material-datepicker/js/bootstrap-material-datetimepicker.min.js`,
  `/apAdmin/assets/plugins/material-datepicker/js/ja.js`,
  `/apAdmin/assets/js/datepicker.js`,
  `/apAdmin/assets/js/lazyload.js`,
  `/apAdmin/assets/js/validate.js`,
  `/apAdmin/assets/js/custom.js`,
  `/apAdmin/assets/plugins/switchery/js/switchery.min.js`,
  `/apAdmin/assets/js/select.js`,
  `https://cdn.jsdelivr.net/npm/apexcharts`,
  `https://cdn.jsdelivr.net/npm/sweetalert2@11`
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        return cache.addAll(urlsToCache).catch((error) => {
          console.error('Failed to cache some resources:', error);
        });
      })
  );
});

self.addEventListener('activate', (event) => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (!cacheWhitelist.includes(cacheName)) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);
  if (event.request.method === 'POST') {
    event.respondWith(fetch(event.request));
    return;
  }
  if (urlsToCache.includes(url.pathname) || urlsToCache.includes(url.href)) {
    event.respondWith(
      fetch(event.request).then((response) => {
        if (response.status === 206) {
          return response;
        }
        if (event.request.method === 'GET') {
          return caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, response.clone());
            return response;
          });
        }
        return response;
      }).catch((error) => {
        console.error("Failed to fetch resource:", event.request.url, error);
        return new Response('Failed to fetch resource', { status: 500 });
      })
    );
  } else {
    event.respondWith(fetch(event.request));
  }
});

