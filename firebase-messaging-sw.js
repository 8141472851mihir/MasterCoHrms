importScripts('https://www.gstatic.com/firebasejs/8.6.8/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.6.8/firebase-messaging.js');

var firebaseConfig = {
     apiKey: "AIzaSyD2omSGWr9uzO03zxFwuNJ9k2PhK3oJQ10",
     authDomain: "my-company-dbf3b.firebaseapp.com",
     projectId: "my-company-dbf3b",
     storageBucket: "my-company-dbf3b.appspot.com",
     messagingSenderId: "106314474651",
     appId: "1:106314474651:web:9732de40c3a002dd4e523e",
     measurementId: "G-NPM26ZSXZ9"
};

firebase.initializeApp(firebaseConfig);
// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

// messaging.setBackgroundMessageHandler(function(payload){
//   return self.registration.showNotification();
// });

messaging.onBackgroundMessage((payload) => {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  // Customize notification here
  const notificationTitle = payload['data']['title'];
  const notificationOptions = {
    body: payload['data']['body'],
    icon: 'https://master.my-company.app/img/logo.png',
    data: payload['data']['click_action']
  };

  self.registration.showNotification(notificationTitle,
    notificationOptions);
});


self.addEventListener('notificationclick', function(event) {

    let url = 'https://master.my-company.app/apAdmin/'+event.notification['data'];
    event.notification.close(); // Android needs explicit close.
    event.waitUntil(

         clients.matchAll({ includeUncontrolled: true, type: 'window' }).then( windowClients => {
            // Check if there is already a window/tab open with the target URL
            for (var i = 0; i < windowClients.length; i++) {
                var client = windowClients[i];
                // If so, just focus it.
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            // If not, then open the target URL in a new window/tab.
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});