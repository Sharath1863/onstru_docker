<!DOCTYPE html>
<html>

<head>
    <title>Cashfree Payment</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script> --}}
    {{-- <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js"></script> --}}
</head>

<body>
    <h2>Cashfree Payment</h2>
    <button id="payBtn">Pay Now</button>

    {{-- <script>
        document.getElementById("payBtn").addEventListener("click", function() {
            fetch("beta/pay", {
                    method: "GET",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.payment_session_id) {

                        console.log("Payment Session ID:", data.payment_session_id);
                        const cashfree = Cashfree({
                            mode: "sandbox", // Change to "production" later
                        });
                        cashfree.checkout({
                            paymentSessionId: data.payment_session_id,
                            redirectTarget: "_blank" // opens full page
                        });
                    } else {
                        alert("Payment session ID not found");
                    }
                });
        });
    </script> --}}


    <script>
        // const firebaseConfig = {
        //     apiKey: "AIzaSyAJIkS79WQPd5gx9Ke4i0Gr_2dQhrPG7os",
        //     authDomain: "onstru-super-app.firebaseapp.com",
        //     projectId: "onstru-super-app",
        //     storageBucket: "onstru-super-app.firebasestorage.app",
        //     messagingSenderId: "623687488765",
        //     appId: "1:623687488765:web:7d2e2dee0c87c1001fda94"
        // };

        // firebase.initializeApp(firebaseConfig);
        // const messaging = firebase.messaging();

        // // Register service worker + get token
        // if ('serviceWorker' in navigator) {
        //     navigator.serviceWorker.register('/firebase-messaging-sw.js')
        //         .then((registration) => {
        //             console.log("Service Worker registered:", registration);

        //             return Notification.requestPermission().then((permission) => {
        //                 if (permission === "granted") {
        //                     return messaging.getToken({
        //                         vapidKey: "BGF40c00tA9HY9tgsPG_XCBsIGt84wzwZtz7MY0x4DAjsqYtEKXKuAiFGO3rqjG4gPkb-Kyr4rdB4tBed6_n15A",
        //                         serviceWorkerRegistration: registration
        //                     });
        //                 } else {
        //                     throw new Error("Permission not granted");
        //                 }
        //             });
        //         })
        //         .then((token) => {
        //             console.log("FCM Token:", token);

        //             // Send token to Laravel backend
        //             fetch("/check_fcm", {
        //                 method: "POST",
        //                 headers: {
        //                     "Content-Type": "application/json",
        //                     "X-CSRF-TOKEN": "{{ csrf_token() }}"
        //                 },
        //                 body: JSON.stringify({
        //                     token
        //                 })
        //             });
        //         })
        //         .catch((err) => console.error("FCM error:", err));
        // }

        // Foreground messages
        // messaging.onMessage((payload) => {
        //     console.log("Foreground message:", payload);

        //     const notification = new Notification(payload.notification.title, {
        //         body: payload.notification.body,
        //         icon: payload.notification.icon || "/logo.png",
        //         data: {
        //             // ✅ Attach link
        //             link: payload.fcmOptions?.link || payload.data?.click_action || "https://onstru.com"
        //         }
        //     });

        //     // ✅ Handle click
        //     notification.onclick = (event) => {
        //         event.preventDefault();
        //         const targetUrl = event.target.data?.link || "https://onstru.com";

        //         window.open(targetUrl, "_blank");
        //     };
        // });

        // // Handle notification click
        // self.addEventListener('notificationclick', function(event) {
        //     event.notification.close();

        //     // Get link from data or fallback to home
        //     const clickAction = event.notification?.data?.link || 'https://onstru.com';

        //     event.waitUntil(
        //         clients.matchAll({
        //             type: 'window',
        //             includeUncontrolled: true
        //         }).then(windowClients => {
        //             // Focus an existing window if open
        //             for (let client of windowClients) {
        //                 if (client.url === clickAction && 'focus' in client) {
        //                     return client.focus();
        //                 }
        //             }
        //             // Otherwise open a new window
        //             if (clients.openWindow) {
        //                 return clients.openWindow(clickAction);
        //             }
        //         })
        //     );
        // });
    </script>

</body>

</html>
