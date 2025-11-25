
//    const firebaseConfig = {
//             apiKey: "AIzaSyAJIkS79WQPd5gx9Ke4i0Gr_2dQhrPG7os",
//             authDomain: "onstru-super-app.firebaseapp.com",
//             projectId: "onstru-super-app",
//             storageBucket: "onstru-super-app.firebasestorage.app",
//             messagingSenderId: "623687488765",
//             appId: "1:623687488765:web:7d2e2dee0c87c1001fda94"
//    };
//    firebase.initializeApp(firebaseConfig);

//    const messaging = firebase.messaging();

//    // Foreground handler
//    messaging.onMessage((payload) => {
//        console.log("Foreground message:", payload);

//        const notification = new Notification(payload.notification.title, {
//            body: payload.notification.body,
//            icon: payload.notification.icon || "/logo.png",
//            data: {
//                link: payload.fcmOptions?.link || payload.data?.click_action || "https://onstru.com"
//            }
//        });

//          // Auto-close after 5 seconds (5000ms)
//     setTimeout(() => {
//         notification.close();
//     }, 10000);

//        notification.onclick = (event) => {
//            event.preventDefault();
//            const targetUrl = event.target.data?.link || "https://onstru.com";
//            window.open(targetUrl, "_blank");
//        };
//    });

