importScripts("https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js");

firebase.initializeApp({
    apiKey: "AIzaSyAJIkS79WQPd5gx9Ke4i0Gr_2dQhrPG7os",
    authDomain: "onstru-super-app.firebaseapp.com",
    projectId: "onstru-super-app",
    storageBucket: "onstru-super-app.firebasestorage.app",
    messagingSenderId: "623687488765",
    appId: "1:623687488765:web:7d2e2dee0c87c1001fda94"
});

const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage(payload => {
  console.log('[SW] Background message:', payload);

  const title = payload.data?.title || 'No title';
  const body  = payload.data?.body || '';
  const data  = payload.data || {};

  self.registration.showNotification(title, {
    body,
    data,
    icon: 'https://onstru.com/assets/images/Logo_Admin.png',
  });
});

self.addEventListener("notificationclick", function(event) {
    event.notification.close();

    const payload = event.notification.data;
    console.log("[SW] Push payload:", payload);

    const targetUrl = payload?.link || "https://onstru.com/";
    console.log("[SW] Notification link:", targetUrl);

    event.waitUntil(
        clients.matchAll({ type: "window", includeUncontrolled: true }).then(clientList => {
            // Check if any window tab already has the URL open
            for (const client of clientList) {
                if ('url' in client && client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }
            // Open a new window if no existing tab found
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});




// // Background messages
// messaging.onBackgroundMessage((payload) => {
//     console.log('[SW] Background message received:', payload);

//     // sender_id from data payload
//     const senderId = payload.data?.sender_id;

//     // Check if any client has the chat open with this sender
//     clients.matchAll({ type: "window", includeUncontrolled: true }).then(clientList => {
//         const chatOpen = clientList.some(client => {
//             try {
//                 // Pass info via URL or postMessage
//                 return client.chatSenderId && client.chatSenderId == senderId;
//             } catch(e) { return false; }
//         });

//         if (!chatOpen) {
//             self.registration.showNotification(payload.notification?.title || "No Title", {
//                 body: payload.notification?.body || "No body",
//                 icon: payload.notification?.icon || "/default-icon.png",
//                 data: payload.data
//             });
//         } else {
//             console.log('[SW] Notification blocked because chat is open for sender', senderId);
//         }
//     });
// });

// Background message handler
// messaging.onBackgroundMessage(function(payload) {
//     console.log("[firebase-messaging-sw.js] Background message ", payload);

//     const data = payload.data || {};

//     // 🔹 Log the raw data object
//     console.log("[firebase-messaging-sw.js] Parsed data:", data);


//     const notificationTitle = data.title || "Notification";
//     const notificationOptions = {
//         body: data.body || "",
//         icon: data.icon || "/icon.png",
//         data: {
//             link: data.link || "/"
//         }
//     };

//     self.registration.showNotification(notificationTitle, notificationOptions);
// });



// Notification click handler
// self.addEventListener("notificationclick", function(event) {
//     // event.notification.close();

//     //  const link = event.notification.data?.link || 'https://onstru.com/';

//      const payload = event.notification.data;
//     console.log("[SW] Push payload:", payload);

//    const link = payload.link;
//     console.log("[SW] Notification link:", link);

//     const targetUrl = link || "https://onstru.com/";

    


//     event.waitUntil(
//         clients.matchAll({ type: "window", includeUncontrolled: true }).then(clientList => {
//             const fullUrl = new URL(targetUrl, self.origin).href;
//             for (const client of clientList) {
//                 if (client.url === fullUrl && "focus" in client) {
//                     return client.focus();
//                 }
//             }
//             if (clients.openWindow) {
//                 return clients.openWindow(fullUrl);
//             }
//         })
//     );
// });

    // self.addEventListener("push", function(event) {
    //     // alert("Push received");
    //     console.log("🔔 Push event received!", event);

    //     const data = event.data ? event.data.json() : {};
    //     console.log("Push payload:", data);

    //     event.waitUntil(
    //         (async () => {
    //             // Example: just log before showing notification
    //             if (data.sender_id) {
    //                 console.log("Message from sender ID:", data.sender_id);
    //             }

    //             // Optionally show notification
    //             await self.registration.showNotification(data.title || "No Title", {
    //                 body: data.body || "No body",
    //                 icon: data.icon || "/default-icon.png",
    //                 data: data
    //             });
    //         })()
    //     );
    // });


// importScripts("https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js");
// importScripts("https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js");

// firebase.initializeApp({
//     apiKey: "AIzaSyAJIkS79WQPd5gx9Ke4i0Gr_2dQhrPG7os",
//     authDomain: "onstru-super-app.firebaseapp.com",
//     projectId: "onstru-super-app",
//     storageBucket: "onstru-super-app.firebasestorage.app",
//     messagingSenderId: "623687488765",
//     appId: "1:623687488765:web:7d2e2dee0c87c1001fda94"
// });

// const messaging = firebase.messaging();

// // messaging.onBackgroundMessage(function(payload) {
// //     self.registration.showNotification(payload.notification.title, {
// //         body: payload.notification.body,
// //         icon: payload.notification.icon
// //     });
// // });

// messaging.onBackgroundMessage(function(payload) {
//     console.log("[firebase-messaging-sw.js] Received background message ", payload);

//     const notificationTitle = payload.notification.title;
//     const notificationOptions = {
//         body: payload.notification.body,
//         icon: payload.notification.icon || "/icon.png",
//         data: {
//             // ✅ Store link in notification data
//             link: payload.fcmOptions?.link || payload.data?.click_action || "https://onstru.com"
//         }
//     };

//     self.registration.showNotification(notificationTitle, notificationOptions);
// });

// // ✅ Handle notification click
// self.addEventListener("notificationclick", function(event) {
//   event.notification.close();

//   const targetUrl = event.notification.data?.link || "https://onstru.com"; // fallback

//   event.waitUntil(
//     clients.matchAll({ type: "window", includeUncontrolled: true }).then(clientList => {
//       for (const client of clientList) {
//         if (client.url === targetUrl && "focus" in client) {
//           return client.focus();
//         }
//       }
//       if (clients.openWindow) {
//         return clients.openWindow(targetUrl);
//       }
//     })
//   );
// });
