@extends('layouts.app')
@section('content')
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <div class="page-wrapper">
        <div class="container-fluid">
            <div class="card-body pb-2 pt-2 px-0">
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="card chat-app">

                            <div class="chat">
                                <div class="chat-header clearfix">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="chat-about">
                                                <div class="d-flex align-items-center">
                                                    <div id="userProfile" class="profile-image"></div>
                                                    <h5 class="m-b-0 userName ml-2"></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="chat-history" id="chat-box">
                                    <ul class="m-b-0">
                                    </ul>
                                </div>
                                <div class="chat-message clearfix">
                                    <div class="input-group mb-0">
                                        <!-- File input trigger -->
                                        <div class="input-group-prepend">
                                            <label for="fileInput" class="input-group-text" style="cursor: pointer;">
                                                <i class="fa fa-file-image-o"></i>
                                            </label>
                                            <input type="file" id="fileInput" accept="image/*,video/*" style="display: none;" />
                                        </div>

                                        <!-- Message input field -->
                                        <input type="text" id="messageInput" class="form-control" placeholder="Type your message..." />

                                        <!-- Send button -->
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" onclick="sendMessage()">
                                                <i class="fa fa-send"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                 <div class="form-group col-12 text-center btm-btn">
                    <a href="{!! route('users.support') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.js"></script>
    <script>
        var id = "{{ $id }}";
        var senderId = "admin";
        var receiverId = '';
        var database = firebase.firestore();
        var defaultUser = "{{ asset('images/default_user.png') }}"
        var driverFcm = '';
        database.collection('users').doc(id).get().then(async function(userSnapshot) {
            if (userSnapshot.exists) {
                var userData = userSnapshot.data();
                if (userData.profilePictureURL != null && userData.profilePictureURL != '') {
                    $('#userProfile').html('<img src="' + userData.profilePictureURL + '" style="max-width: 50px;">')
                } else {
                    $('#userProfile').html('<img src="' + defaultUser + '" style="max-width: 50px;">')

                }
                if (userData.hasOwnProperty('fcmToken') && userData.fcmToken != null && userData.fcmToken != '') {
                    driverFcm = userData.fcmToken;
                }
                $('.userName').html(userData.firstName + ' ' + userData.lastName);
                receiverId = userData.id;
            }
        })

        var threadRef = database.collection('chat_admin').doc(id).collection("thread").orderBy("createdAt");
        threadRef.onSnapshot(snapshot => {
            const chatBox = document.querySelector("#chat-box ul");
            chatBox.innerHTML = '';

            snapshot.forEach(doc => {
                const data = doc.data();
                if (data.senderId !== "admin" && !data.seen) {
                    doc.ref.update({
                        seen: true
                    });
                }
                const isAdmin = data.senderId === "admin";

                let messageContent = '';
                let timestampText = '';

                if (data.createdAt && data.createdAt.toDate) {
                    const date = data.createdAt.toDate();
                    const formattedTime = date.toLocaleString('en-IN', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true,
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                    timestampText = `<span class="message-data-time">${formattedTime}</span>`;
                }

                if (data.messageType === "text") {
                    messageContent = data.message;
                } else if (data.messageType === "image" && data.url?.url) {
                    messageContent = `<a href="${data.url.url}" data-lightbox="chat-images" target="_blank">
                                            <img src="${data.url.url}" alt="Image" style="max-width: 100px; border-radius: 8px;" />
                                        </a>`;
                } else if (data.messageType === "video" && data.url?.url) {
                    messageContent = `<video controls style="max-width: 150px; border-radius: 8px;">
                            <source src="${data.url.url}">
                            Your browser does not support the video tag.
                          </video>`;
                }

                const messageHtml = `
        <li class="clearfix">
            <div class="message-data ${isAdmin ? 'text-right' : ''}">
                ${timestampText}
            </div>
            <div class="message ${isAdmin ? 'other-message float-right' : 'my-message'}">
                ${messageContent}
            </div>
        </li>
    `;

                chatBox.innerHTML += messageHtml;
            });

            // Auto-scroll to bottom
            chatBox.parentElement.scrollTop = chatBox.parentElement.scrollHeight;

        });

        async function sendMessage() {
            const message = document.getElementById("messageInput").value;
            if (!message) return;

            database.collection("chat_admin").doc(id).collection("thread").add({
                id: database.collection("tmp").doc().id,
                message: message,
                senderId: senderId,
                receiverId: receiverId,
                messageType: "text",
                url: null,
                videoThumbnail: "",
                seen: false,
                createdAt: firebase.firestore.FieldValue.serverTimestamp(),
            });

            // Update last message in main chat_admin doc

            const chatDocRef = database.collection("chat_admin").doc(id);

            chatDocRef.get().then(async (doc) => {
                const dataToSet = {
                    lastMessage: message,
                    lastSenderId: senderId,
                    createdAt: firebase.firestore.FieldValue.serverTimestamp(),
                };

                if (!doc.exists) {

                    const userDoc = await database.collection('users').doc(id).get();
                    const userData = userDoc.data();

                    Object.assign(dataToSet, {
                        chatType: "admin",
                        adminId: "admin",
                        adminName: "Admin",
                        userId: userData.id || "",
                        userName: userData.firstName + ' ' + userData.lastName || "",
                        userProfile: userData.profilePictureURL || "",
                        type: 'vendor'
                    });
                }

                // Create or merge fields
                chatDocRef.set(dataToSet, {
                    merge: true
                }).then(async () => {
                    var title = '{{ trans('lang.new_message_from_admin') }}';
                    var body = '{{ trans('lang.you_have_received_new_message_from_admin') }}';
                    const userDoc = await database.collection('users').doc(id).get();
                    const userData = userDoc.data();
                    driverFcm = userData.fcmToken;
                    var fcmtoken = driverFcm;
                    var data = {
                        'type': 'admin_chat',
                        'vendorId': receiverId
                    }
                    var sent = await sendNotification(fcmtoken, title, body, data);
                    if (sent) {
                        console.log('notification sent');
                    }
                });
            });

            document.getElementById("messageInput").value = '';
        }
        document.getElementById('fileInput').addEventListener('change', async function(e) {
            jQuery("#overlay").show();
            const file = e.target.files[0];
            if (!file) return;

            const storageRef = firebase.storage().ref();
            const filePath = `chat_uploads/${Date.now()}_${file.name}`;
            const uploadTask = storageRef.child(filePath).put(file);

            uploadTask.on('state_changed', null,
                function(error) {
                    console.error("Upload failed:", error);
                },
                async function() {
                    const downloadURL = await uploadTask.snapshot.ref.getDownloadURL();
                    const mimeType = file.type;
                    const messageType = mimeType.startsWith("image") ? "image" : "video";

                    const senderId = "admin";
                    const messageId = database.collection("tmp").doc().id;

                    let messageData = {
                        message: "sent a message",
                        messageType: messageType,
                        senderId: senderId,
                        receiverId: receiverId,
                        createdAt: firebase.firestore.FieldValue.serverTimestamp(),
                        id: messageId,
                        seen: false,
                        url: {
                            mime: mimeType,
                            url: downloadURL
                        }
                    };

                    if (messageType === "video") {
                        const thumbnailBlob = await generateVideoThumbnail(file);
                        const thumbPath = `chat_uploads/thumbnails/${Date.now()}_thumb.jpg`;
                        const thumbSnapshot = await storageRef.child(thumbPath).put(thumbnailBlob);
                        const thumbnailURL = await thumbSnapshot.ref.getDownloadURL();
                        messageData.videoThumbnail = thumbnailURL;
                    }

                    // Save message
                    await database.collection("chat_admin").doc(id).collection("thread").add(messageData);

                    // Handle chat_admin doc creation or update
                    const chatDocRef = database.collection("chat_admin").doc(id);
                    const doc = await chatDocRef.get();

                    const dataToSet = {
                        lastMessage: "sent a message",
                        lastSenderId: senderId,
                        createdAt: firebase.firestore.FieldValue.serverTimestamp(),
                    };

                    if (!doc.exists) {
                        try {
                            const userDoc = await database.collection("users").doc(id).get();
                            const userData = userDoc.data();

                            if (userData) {
                                Object.assign(dataToSet, {
                                    chatType: "admin",
                                    adminId: "admin",
                                    adminName: "Admin",
                                    userId: userData.id || "",
                                    userName: userData.firstName + ' ' + userData.lastName || "",
                                    userProfile: userData.profilePictureURL || "",
                                    type: 'driver'
                                });
                            } else {
                                console.warn("data not found for id:", userData.id);
                            }

                        } catch (err) {
                            console.error("Error while fetching  data:", err);
                        }
                    }

                    await chatDocRef.set(dataToSet, {
                        merge: true
                    }).then(async () => {
                        var title = '{{ trans('lang.new_message_from_admin') }}';
                        var body = '{{ trans('lang.you_have_received_new_message_from_admin') }}';
                        const userDoc = await database.collection('users').doc(id).get();
                        const userData = userDoc.data();
                        driverFcm = userData.fcmToken;
                        var fcmtoken = driverFcm;
                        var data = {
                            'type': 'admin_chat',
                            'vendorId': receiverId
                        }
                        var sent = await sendNotification(fcmtoken, title, body, data);
                        if (sent) {
                            console.log('notification sent');
                        }

                    });
                    jQuery("#overlay").hide();
                }
            );

        });
        document.getElementById("messageInput").addEventListener("keydown", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                sendMessage();
            }
        });
        async function generateVideoThumbnail(videoFile) {
            return new Promise((resolve, reject) => {
                const video = document.createElement('video');
                video.src = URL.createObjectURL(videoFile);
                video.crossOrigin = "anonymous";
                video.muted = true;
                video.playsInline = true;

                video.addEventListener('loadeddata', () => {
                    // Ensure the video has enough data
                    video.currentTime = 1;
                });

                video.addEventListener('seeked', () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    canvas.toBlob(blob => {
                        if (blob) resolve(blob);
                        else reject(new Error("Thumbnail generation failed"));
                    }, 'image/jpeg', 0.75);
                });

                video.addEventListener('error', (e) => {
                    reject(e);
                });
            });
        }
    </script>
@endsection
