<audio id="notificationSound" src="../img/chat_notification.wav" preload="auto" muted></audio>
<script src="https://www.gstatic.com/firebasejs/8.3.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.1/firebase-messaging.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php 
$has_permission = $d -> count_data_direct("fcm_id", "admin_fcm_notification_master", "bms_admin_id='$bms_admin_id' AND active_status='0'");
if ($bms_admin_id > 0 && $has_permission > 0) {
?>
  <script>
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
  </script>
  <script>
    swalQueue = [];

    const messaging = firebase.messaging();
    if (Notification.permission != 'default') {
      requestPermission();
      document.removeEventListener('click', requestPermission);
      document.removeEventListener('keypress', requestPermission);
    }else{
      document.addEventListener('click', requestPermission);
      document.addEventListener('keypress', requestPermission);
    }
    function requestPermission(){
      messaging.requestPermission().then(function(){
      return messaging.getToken();
      }).then(function(token){
        $.ajax({
          url: "setWebToken.php",
          cache: false,
          type: "POST",
          data: {currentToken : token},
          success: function(response){
            // console.log(token);
          }
        });
      }).catch(function(err){
        if (Notification.permission === 'denied') {
          // console.warn('Notifications are blocked by the user.');
          const style = document.createElement('style');
          style.innerHTML = `
            .rounded-image {
              border-radius: 2%;
            }
          `;
          document.head.appendChild(style);
          Swal.fire({
            title: 'Notifications Blocked',
            text: 'Please enable notifications from your browser settings.',
            imageUrl: '../img/guide.png',
            imageWidth: 300,
            imageHeight: 200,
            customClass: {
              image: 'rounded-image',
            },
            width: 400,
            imageAlt: 'Guide to Enable Notifications',
            showConfirmButton: false,
            timerProgressBar: true,
            position: 'top-end', 
            background: '#e3ebec',
            color: '#721c24',
            footer: '<a href="../img/guide.png" download>Download the Guide</a>',
          });

        } else {
          // console.error('Failed to get permission or token:', err);
        }
      })
    }
    messaging.onMessage(function(payload){
      if(is_swal_on==false){
        displaySwal(payload);
        displayQueuedSwals(); 
      }else{
        swalQueue.push(payload);
      }
    });
    function displaySwal(payload) {
      if(payload['data']['icon']=='') {
        var icon = 'success';
      } else {
        var icon = payload['data']['icon'];
      }
      if(payload['data']['click_action']=="New Company Request"){
        var audio = document.getElementById("notificationSound");
        audio.muted = false;
        audio.play().catch(function(error) {
          // console.log("Audio playback prevented. Browser policy:", error);
        });
        audio.play();
      }
      var click_action = "<?=$base_url.'apAdmin/'?>";
      swal({
        title: payload['data']['title'],
        text: payload['data']['body'],
        icon: payload['data']['icon'],
        buttons: true,
        dangerMode: true,
        buttons: ['Ok', 'View'],
      })
      .then((willDelete) => {
        if (willDelete) {
          window.location.href = click_action + payload['data']['click_action'];
        }
        displayQueuedSwals(); 
      });
    }
    function displayQueuedSwals() {
      if (swalQueue.length > 0) {
        displaySwal(swalQueue.shift()); 
      } else {
        is_swal_on = false; 
      }
    }
  </script>
<?php 
  }
?>