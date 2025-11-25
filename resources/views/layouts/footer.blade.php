@if (Auth::check())
    <!-- Chat -->
    @include('chat.index')
    <!-- Badges -->
    @include('popups.badges')
@endif

@if (Auth::check())
    <!-- Chatbot -->
    @include('chat.chatbot')
@endif

{{-- @include('popups.questions') --}}

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>

<!-- Select 2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Owl Carousel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<!-- Splide JS -->
<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>

<!-- Lightbox -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>

<!-- JS Script -->
<script src="{{ asset('assets/js/script.js') }}"></script>

<!-- Toast Container -->
<div class="toast-main" style="z-index: 1100">
    <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body">
            <!-- Message goes here -->
            <img src="{{ asset('assets/images/Favicon_2.png') }}" height="30px" alt="">
            <span id="toast-message"></span>
        </div>
    </div>
</div>

<!-- Toast Data -->
<div id="toast-data" data-success="{{ session('success') }}" data-error="{{ $errors->any() ? $errors->first() : '' }}">
</div>

<!-- Pusher Script -->
<script>
    // const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // const userId = {{ auth()->id() ?? 'null' }};
    const userId = @json(auth()->id());

    if (userId) {
        window.Pusher = Pusher;
        // if (window.Laravel.user) {


        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: "{{ env('PUSHER_APP_KEY') }}",
            cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
            forceTLS: true,
            authEndpoint: '{{ url('/broadcasting/auth') }}',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'

                }
            }
        });

        window.Echo.private(`chat.${userId}`)
            .listen('.private.message.sent', (e) => {
                var unseencount = check_unseen();
                if (unseencount == 0) {
                    document.getElementsByClassName('chat-toggle-btn')[0].classList.remove('read');
                } else {
                    document.getElementsByClassName('chat-toggle-btn')[0].classList.add('read');
                }
                // sender ID comes from the payload
                const senderId = e.sender_id;
                const messageText = e.text;
                const chatListEl = document.querySelector('.chat-list');

                if (chatListEl && chatListEl.classList.contains('d-none')) {
                    // .chat-list is hidden, load messages for the sender
                    loadChatMessages(senderId);
                } else {
                    // .chat-list is visible, reload chats
                    loadChats();
                }
            });

    } else {
        console.log("User not logged in, skipping Pusher setup.");
    }

    function check_unseen() {
        $.ajax({
            url: "{{ route('chat_unseen') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                let unseenCount = response.unseenCounts || 0;
                const chatToggleBtn = $('.chat-toggle-btn span');
                if (!chatToggleBtn) return;
                if (unseenCount == 0) {
                    chatToggleBtn.removeClass('read');
                } else {
                    chatToggleBtn.addClass('read');
                }
                return response.unseenCounts;
            },
            error: function (xhr) { }
        });
    }

    check_unseen();
    // if (userId != null && userId !== '') {
    //     // User is logged in

    // } else {
    //     // User not logged in, skip setup
    // }

    // if ('serviceWorker' in navigator) {
    //     navigator.serviceWorker.getRegistration('/firebase-messaging-sw.js')
    //         .then(reg => {
    //             if (!reg) {
    //                 return navigator.serviceWorker.register('/firebase-messaging-sw.js')
    //                     .then(newReg => console.log("✅ SW registered:", newReg))
    //                     .catch(err => console.error("❌ SW registration failed:", err));
    //             } else {
    //                 console.log("SW already registered:", reg);
    //             }
    //         });
    // }


    // document.addEventListener("DOMContentLoaded", () => {
    //     initFirebaseMessaging();
    // });

    // async function initFirebaseMessaging() {
    //     // Initialize Firebase
    //     const firebaseConfig = {
    //         apiKey: "AIzaSyAJIkS79WQPd5gx9Ke4i0Gr_2dQhrPG7os",
    //         authDomain: "onstru-super-app.firebaseapp.com",
    //         projectId: "onstru-super-app",
    //         storageBucket: "onstru-super-app.firebasestorage.app",
    //         messagingSenderId: "623687488765",
    //         appId: "1:623687488765:web:7d2e2dee0c87c1001fda94"
    //     };
    //     firebase.initializeApp(firebaseConfig);
    //     const messaging = firebase.messaging();

    //     if ('serviceWorker' in navigator) {
    //         try {
    //             const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
    //             console.log("✅ SW registered:", registration);

    //             // Request notification permission if not already granted
    //             if (Notification.permission !== "granted") {
    //                 await Notification.requestPermission();
    //             }

    //             // Foreground messages
    //             messaging.onMessage(payload => {
    //                 console.log("[Foreground] Message received:", payload);
    //             });

    //         } catch (err) {
    //             console.error("❌ SW registration or messaging failed:", err);
    //         }
    //     }
    // }
</script>

<!-- <script>
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            const el = document.querySelector('.pulse-div');
            if (el) el.style.setProperty('right', '2%', 'important');
        }, 2000);
    });
</script> -->

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const bot = document.querySelector('.pulse-lb-div');
        if (!bot) return;
        bot.addEventListener('mouseover', () => {
            bot.setAttribute('data-bs-toggle', 'tooltip');
            bot.setAttribute('data-bs-title', 'Onstru ChatBot');
            const tooltip = bootstrap.Tooltip.getInstance(bot) || new bootstrap.Tooltip(bot);
            tooltip.show();
        });
    });
</script>

<!-- Toast Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toastData = document.getElementById('toast-data');
        const success = toastData.getAttribute('data-success');
        const error = toastData.getAttribute('data-error');
        if (success) {
            showToast(success, "success");
        } else if (error) {
            showToast(error, "danger");
        }
    });

    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        // Reset colors
        // toastElement.classList.remove('text-bg-success', 'text-bg-danger', 'text-bg-warning', 'text-bg-info');
        // toastElement.classList.add(`text-bg-${type}`);
        toastMessage.textContent = message;

        // Show toast
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }
</script>

<!-- Bootstrap Tooltip -->
<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl));
</script>

<!-- Validation - Contact / Age / Pincode / Year / Aadhar -->
<script>
    function validate_contact(input) {
        let value = input.value.replace(/\D/g, "");
        if (value.length > 10) {
            value = value.slice(0, 10);
        }
        input.value = value;
    }

    function validate_age(input) {
        let value = input.value.replace(/\D/g, "");
        if (value.length > 3) {
            value = value.slice(0, 3);
        }
        input.value = value;
    }

    function validate_pincode(input) {
        let value = input.value.replace(/\D/g, "");
        if (value.length > 6) {
            value = value.slice(0, 6);
        }
        input.value = value;
    }

    function validate_otp(input) {
        let value = input.value.replace(/\D/g, "");
        if (value.length > 4) {
            value = value.slice(0, 4);
        }
        input.value = value;
    }

    function validate_aadhar(input) {
        let value = input.value.replace(/\D/g, "");
        if (value.length > 12) {
            value = value.slice(0, 12);
        }
        input.value = value;
    }

    // Date Input Year Validation
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('input[type="date"]').forEach(function (dateInput) {
            dateInput.addEventListener("input", function () {
                let value = this.value;
                if (/^\d{5,}-\d{2}-\d{2}$/.test(value)) {
                    let parts = value.split("-");
                    parts[0] = parts[0].slice(0, 4);
                    this.value = parts.join("-");
                }
            });
        });
    });
</script>

<!-- Following / Followers -->
<script>
    addEventListener('click', async (e) => {
        const btn = e.target.closest('.follow-btn');
        if (!btn) return;

        const userId = btn.dataset.userId;
        const following = btn.dataset.following === '1';
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        // const url = `/users/${userId}/follow`;
        const follow = "{{ route('users.follow', ['user' => ':id']) }}".replace(':id', userId);
        const unfollow = "{{ route('users.unfollow', ['user' => ':id']) }}".replace(':id', userId);
        let url;
        if (following) {
            url = unfollow;
        } else {
            url = follow;
        }
        const originalHTML = btn.innerHTML;
        // Show loader immediately
        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;

        const res = await fetch(url, {
            method: following ? 'DELETE' : 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        });
        if (!res.ok) {
            const msg = await res.text();
            showToast(msg || 'Something went wrong!');
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            return;
        }
        const data = await res.json();
        btn.dataset.following = data.status === 'followed' ? '1' : '0';
        btn.innerHTML = `<span class="label">${(data.status === 'followed') ? 'Following' : 'Follow'}</span>`;
        btn.classList.toggle('followingbtn', data.status === 'followed');
        btn.classList.toggle('followersbtn', data.status !== 'followed');
        if (data.status === 'followed') {
            btn.classList.remove('followersbtn');
            btn.classList.add('followingbtn');
        } else {
            btn.classList.remove('followingbtn');
            btn.classList.add('followersbtn');
        }
        const myfollowersCountEl = document.querySelector('.my-followers-count');
        if (myfollowersCountEl) {
            myfollowersCountEl.textContent = data.my_followers_count;
        }
        const myFollowingCountEl = document.querySelector('.my-following-count');
        if (myFollowingCountEl) {
            myFollowingCountEl.textContent = data.my_following_count;
        }
        const followersCountEl = document.querySelector('.followers-count-of-target');
        if (followersCountEl) {
            followersCountEl.textContent = data.followers_count;
        }
        const followingCountEl = document.querySelector('.following-count-of-target');
        if (followingCountEl) {
            followingCountEl.textContent = data.following_count;
        }
    });
</script>

@yield('scripts')

</body>

</html>