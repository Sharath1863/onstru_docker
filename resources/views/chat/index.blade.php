<!-- Chat CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/chat.css') }}">

<!-- Toggle Button -->
<button class="followersbtn chat-toggle-btn p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#chat">
    <i class="far fa-message fs-5"></i>
    <span class=""></span>
</button>

<div class="offcanvas offcanvas-end" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" id="chat"
    aria-labelledby="offcanvasLabel">
    <div class="offcanvas-body chat-main p-0">

        <!-- Chat List -->
        <div class="chat-list">
            <div class="chat-header">
                <h5 class="mb-0" data-bs-dismiss="offcanvas">
                    <i class="fas fa-angle-left pe-2"></i> Chat
                </h5>
                <div class="chat-search">
                    <div class="inpleftflex">
                        <i class="fas fa-search"></i>
                        <input type="text" id="chatSearch" class="form-control border-0" placeholder="Search"
                            value="{{ request('chatKeyword') }}">
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div class="side-cards shadow-none border-0" id="noChats" style="display: none;">
                <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
                    <img src="{{ asset('assets/images/Empty/NoChats.png') }}" height="200px" class="d-flex mx-auto mb-2"
                        alt="">
                    <h5 class="text-center">No Chats Found</h5>
                    <h6 class="text-center">Currently, no chats are available - try to chat with some peoples around
                        you.</h6>
                </div>
            </div>

            <div class="chat-cards" id="chat_card">
                <div class="text-center d-flex align-items-center justify-content-center flex-column gap-1 mt-3">
                    <img src="{{ asset('assets/images/Favicon.png') }}" alt="Loading" width="40px" height="40px">
                    <h6 class="text-muted" style="font-size: 12px;">Loading</h6>
                </div>
            </div>
        </div>

        <!-- Chat Screen (Hidden) -->
        <div class="chat-screen d-none">
            <div class="d-flex align-items-center justify-content-between">
                <div class="chat-header d-flex align-items-center column-gap-3">
                    <i class="fas fa-angle-left pe-1 back-to-list"></i>
                    <div class="d-flex align-items-center justify-content-start column-gap-2">
                        <div class="avatar-div-30 position-relative">
                            <img id="chatUserImage" class="avatar-30">
                            <img id="chatUserBadge" class="badge-30" alt="">
                            <span class="dotOnline"></span>
                        </div>
                        <div class="chat-user">
                            <h5 id="chatName" class="mb-1"></h5>
                            <h6 id="isOnline" class="mb-0"></h6>
                            <h6 id="chatUsername" class="mb-0 d-none"></h6>
                        </div>
                    </div>
                </div>
                <h5 class="mb-0"><span class="label" id="chatUserRole"></span></h5>
            </div>

            <!-- Chat Messages -->
            <div class="chat-messages py-3" id="chatMessages"></div>

            <!-- Chat Input -->
            <div class="chat-input d-flex align-items-center justify-content-between column-gap-2 py-2">
                <textarea id="chatInput" rows="1" class="form-control" placeholder="Type a message..." autofocus></textarea>
                <button id="sendMessageBtn" class="chatbtn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>

    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- Search Filter -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        chatSearch.addEventListener('input', function() {
            const chatUserCards = document.querySelectorAll('.chat-cards');
            const noChats = document.getElementById('noChats');
            let chatMatch = false;
            const chatKeyword = this.value.toLowerCase();

            chatUserCards.forEach(card => {
                const cardText = card.textContent.toLowerCase();
                if (cardText.includes(chatKeyword)) {
                    card.style.display = 'block';
                    chatMatch = true;
                } else {
                    card.style.display = 'none';
                }
            });

            if (noChats) {
                noChats.style.display = chatMatch ? 'none' : 'block';
            }
        });
    });
</script>

<script>
    var receiverId = null;
    var last_id = null;
    let chatInterval = null;
</script>

<!-- Linkify -->
<script>
    function linkify(text) {
        if (!text) return '';
        const urlPattern = /(?:(?:https?:\/\/|www\.)[^\s<]+|(?:[a-zA-Z0-9-]+\.[a-zA-Z]{2,})(?:[^\s<]*))/gi;
        return text.replace(urlPattern, (raw) => {
            const href = /^https?:\/\//i.test(raw) ? raw : `https://${raw}`;
            return `<a href="${href}" target="_blank" rel="nofollow noopener noreferrer ugc" class="chat-link">${raw}</a>`;
        });
    }

    function applyLinkifyIn(containerEl) {
        containerEl.querySelectorAll('.message h6').forEach((el) => {
            if (el.dataset.linkified === '1') return;
            el.innerHTML = linkify(el.innerHTML);
            el.dataset.linkified = '1';
        });
    }
</script>

<!-- Chat Script -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chatList = document.querySelector(".chat-list");
        const chatScreen = document.querySelector(".chat-screen");
        const backBtn = document.querySelector(".back-to-list");
        const chatName = document.getElementById("chatName");
        const chatUsername = document.getElementById("chatUsername");
        const chatUserImage = document.getElementById("chatUserImage");
        const chatUserBadge = document.getElementById("chatUserBadge");
        const chatUserRole = document.getElementById("chatUserRole");
        const chatMessages = document.getElementById("chatMessages");
        const chatInput = document.getElementById("chatInput");
        const sendMessageBtn = document.getElementById("sendMessageBtn");
        const assetBaseUrl = "{{ asset('assets/images/Badge_') }}"

        // Assuming #chat_card is the container for the dynamic chat cards
        $("#chat_card").on("click", ".chat-user-card", function() {
            receiverId = this.dataset.id;
            chatName.textContent = this.dataset.name;
            chatUsername.textContent = this.dataset.username;
            chatUserImage.src = "https://onstru-social.s3.ap-south-1.amazonaws.com/" + this.dataset
                .image;
            chatUserBadge.src = assetBaseUrl + this.dataset.badge + '.png';
            chatUserRole.textContent = this.dataset.role;

            $("#chatMessages").html("");

            // startChatPolling();
            loadChatMessages(receiverId)
            chatList.classList.add("d-none");
            chatScreen.classList.remove("d-none");
        });

        // Back to chat list
        backBtn.addEventListener("click", async function() {
            var unseencount = check_unseen();
            if (unseencount == 0) {
                $('.chat-toggle-btn').removeClass('read');
            } else {
                $('.chat-toggle-btn').addClass('read');
            }
            $.ajax({
                url: "{{ route('chat_open_update') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    console.log("");
                },
                error: function(xhr) {
                    alert("Failed to update chat open status.");
                }
            });
            await loadChats();
            chatScreen.classList.add("d-none");
            chatList.classList.remove("d-none");
        });

        // Send message
        function sendMessage() {
            const message = chatInput.value.trim();
            const emptyContent = document.querySelector('.empty-content');
            const user = document.querySelector('#chatUsername').textContent;
            if (message !== "") {

                $.ajax({
                    url: "{{ route('chat_msg_ind') }}",
                    method: "POST",
                    data: {
                        rec_id: receiverId ?? user,
                        msg: message,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $("#chatMessages").append(response.html);
                        last_id = response.msg_id;
                        applyLinkifyIn(document.getElementById('chatMessages'));
                        $("#chatInput").val('');
                        setTimeout(scrollToLatest, 50);
                        sendMessageBtn.disabled = false;
                    },
                    error: function(xhr) {
                        chatMessages.innerHTML =
                            `<h6 class="text-center text-danger">Failed to load messages</h6>`;
                    }
                });
            }
        }

        sendMessageBtn.addEventListener("click", function() {
            sendMessageBtn.disabled = true;
            sendMessage();
        });

        chatInput.addEventListener("keypress", function(e) {
            if (e.key === "Enter" && !e.shiftKey) {
                e.preventDefault();
                if (!sendMessageBtn.disabled) {
                    sendMessageBtn.disabled = true;
                    sendMessage();
                }
            }
        });

    });

    // function startChatPolling() {
    //     stopChatPolling(); // clear any old interval
    //     if (chatInterval) return;
    //     chatInterval = setInterval(function() {
    //         if (!$("#chatScreen").hasClass("d-none") && receiverId) {
    //             $.ajax({
    //                 url: "{{ route('chat_rec_send') }}",
    //                 method: "POST",
    //                 data: {
    //                     rec_id: receiverId,
    //                     last_id: last_id,
    //                     _token: "{{ csrf_token() }}"
    //                 },
    //                 success: function(response) {
    //                     if (response.html && response.html !== "") {
    //                         $("#chatMessages").append(response.html);
    //                         last_id = response.last_id;
    //                         applyLinkifyIn(document.getElementById('chatMessages'));
    //                         setTimeout(scrollToLatest, 50);
    //                     }
    //                 },
    //                 error: function(xhr) {
    //                     console.error("Polling error:", xhr.responseText);
    //                 }
    //             });
    //         }
    //     }, 5000);
    // }
    $("#closeChatBtn").on("click", function() {
        // stopChatPolling();
    });
</script>

<script>
    function scrollToLatest() {
        document.getElementById("chatMessages").scrollTop = chatMessages.scrollHeight;
    }

    // Load Chat Messages
    function loadChatMessages(receiverId) {
        // console.log("Function called with ID:");
        $("#isOnline").html('Loading');
        $.ajax({
            url: "{{ route('chat_msg') }}",
            method: "POST",
            data: {
                rec_id: receiverId,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                $("#chatMessages").html(response.html);
                $("#isOnline").html(response.is_online ?? '');

                // Check actual online status from response
                if (response.is_online && response.is_online === 'Online') {
                    $('.dotOnline').addClass('online');
                } else {
                    $('.dotOnline').removeClass('online');
                }

                last_id = response.msg_id;
                applyLinkifyIn(document.getElementById('chatMessages'));
                setTimeout(scrollToLatest, 50);
            },
            error: function(xhr) {
                $("#chatMessages").html(
                    `<h6 class="text-center text-danger">Failed to load messages</h6>`
                );
            }
        });
    }

    // Load Chats
    function loadChats(userClicked = null) {
        $.ajax({
            url: "{{ route('chat_card') }}",
            method: "GET",
            success: function(response) {
                $("#chat_card").html(response.html);
                if (userClicked) {
                    let targetCard = $(`.chat-user-card[data-id="${userClicked}"]`);
                    if (targetCard.length) {
                        targetCard.trigger("click");
                    } else {
                        document.querySelector(".chat-list").classList.add("d-none");
                        document.querySelector(".chat-screen").classList.remove("d-none");
                        loadChatMessages(userClicked);
                    }
                }
            },
            error: function(xhr) {
                $("#chat_card").html('<h6 class="text-center text-danger">Error loading chats</h6>');
            }
        });
    }
    // Call it once when page loads
    $('.followersbtn').on('click', function() {
        let userClicked = $(this).data("user-id") ?? null;
        loadChats(userClicked);
    });
</script>
