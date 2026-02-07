@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.support_history') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.support_history_table') }}</li>
                </ol>
            </div>
            <div>
            </div>
        </div>

        <div class="container-fluid">
            <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
                {{ trans('lang.processing') }}
            </div>

            <div class="admin-top-section">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex top-title-section pb-4 justify-content-between">
                            <div class="d-flex top-title-left align-self-center">
                                <span class="icon mr-3"><img src="{{ asset('images/document.png') }}"></span>
                                <h3 class="mb-0">{{ trans('lang.support_history') }}</h3>
                                <span class="counter ml-3 total_count"></span>
                            </div>
                            <div class="d-flex top-title-right align-self-center">
                                <div class="select-box pl-3">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="table-list">
                <div class="row">
                    <div class="col-12">

                        <div class="card border">
                            <div class="card-header d-flex justify-content-between align-items-center border-0">
                                <div class="card-header-title">
                                    <h3 class="text-dark-2 mb-2 h4">{{ trans('lang.support_history_table') }}</h3>
                                    <p class="mb-0 text-dark-2">{{ trans('lang.support_history_table_text') }}</p>
                                </div>
                                <div class="card-header-right d-flex align-items-center">
                                    <div class="card-header-btn mr-3">
                                    </div>

                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive m-t-10">
                                    <table id="supportHistoryTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active"><a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i> {{ trans('lang.all') }}</a></label>
                                                </th>
                                                <th>{{ trans('lang.user_info') }}</th>
                                                <th>{{ trans('lang.message') }}</th>
                                                <th>{{ trans('lang.date') }}</th>
                                                <th>{{ trans('lang.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="append_list1">
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Initialize Firebase-dependent variables safely
        var database;
        var refData;
        var placeholderImage = "";
        
        // Wait for Firebase to be initialized
        function initializeSupportFirebase() {
            if (typeof firebase !== 'undefined' && firebase.apps && firebase.apps.length > 0) {
                try {
                    database = firebase.firestore();
                    refData = database.collection('chat_admin');
                    
                    // Load placeholder image
                    var placeholder = database.collection('settings').doc('placeHolderImage');
                    placeholder.get().then(async function (snapshotsimage) {
                        if (snapshotsimage.exists) {
                            var placeholderImageData = snapshotsimage.data();
                            if (placeholderImageData && placeholderImageData.image) {
                                placeholderImage = placeholderImageData.image;
                            }
                        }
                    }).catch(function(error) {
                        console.error('Error loading placeholder image:', error);
                    });
                    
                    return true;
                } catch (error) {
                    console.error('Error initializing Firebase in support history page:', error);
                    return false;
                }
            } else {
                console.warn('Firebase not initialized yet in support history page, retrying...');
                setTimeout(initializeSupportFirebase, 500);
                return false;
            }
        }
        
        // Initialize Firebase before using it
        if (typeof firebase !== 'undefined' && firebase.apps && firebase.apps.length > 0) {
            initializeSupportFirebase();
        } else {
            // Listen for Firebase initialization event
            window.addEventListener('firebaseInitialized', function() {
                console.log('Received firebaseInitialized event in support history page');
                initializeSupportFirebase();
            });
            
            // Start waiting for Firebase
            function waitForFirebase() {
                if (typeof firebase !== 'undefined' && firebase.apps && firebase.apps.length > 0) {
                    if (initializeSupportFirebase()) {
                        return; // Success
                    }
                }
                
                // If not ready, wait and retry (max 10 seconds)
                var attempts = (waitForFirebase.attempts || 0) + 1;
                waitForFirebase.attempts = attempts;
                
                if (attempts < 20) { // 20 attempts * 500ms = 10 seconds max
                    setTimeout(waitForFirebase, 500);
                } else {
                    console.error('Firebase initialization timeout in support history page');
                }
            }
            
            waitForFirebase();
        }

        var append_list = '';

        $(document).ready(function() {
            // Wait for Firebase to be initialized before setting up DataTable
            function initializeDataTable() {
                if (!database || !refData) {
                    console.log('Waiting for Firebase to initialize...');
                    setTimeout(initializeDataTable, 500);
                    return;
                }

                $(document.body).on('click', '.redirecttopage', function() {
                    var url = $(this).attr('data-url');
                    window.location.href = url;
                });

                jQuery("#data-table_processing").show();

                const table = $('#supportHistoryTable').DataTable({
                pageLength: 10,
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: async function(data, callback, settings) {
                    if (!database || !refData) {
                        console.error('Firebase not initialized');
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: []
                        });
                        return;
                    }
                    
                    const start = data.start;
                    const length = data.length;
                    const searchValue = data.search.value.toLowerCase();
                    const orderColumnIndex = data.order[0].column;
                    const orderDirection = data.order[0].dir;
                    const orderableColumns = ['', 'userName', 'message', 'messageDate', ''];
                    const orderByField = orderableColumns[orderColumnIndex];
                    if (searchValue.length >= 3 || searchValue.length === 0) {
                        $('#data-table_processing').show();
                    }
                    
                    try {
                        const snapshot = await refData.orderBy('createdAt', 'desc').get();
                        let allChats = [];

                        await Promise.all(snapshot.docs.map(async (doc) => {
                        const data = doc.data();
                        // console.log(doc.id);
                        const threadSnap = await database.collection("chat_admin")
                            .doc(doc.id)
                            .collection("thread")
                            .orderBy("createdAt", "desc")
                            .limit(1)
                            .get();

                        if (!threadSnap.empty) {
                            const lastMsg = threadSnap.docs[0].data();
                            const createdAt = lastMsg.createdAt.toDate().toDateString();
                            const time = lastMsg.createdAt.toDate().toLocaleTimeString('en-US');
                            var userId = lastMsg.senderId;
                            if (lastMsg.senderId == 'admin') {
                                userId = lastMsg.receiverId;
                            }
                            // console.log(userId);
                            var userData = await getUserName(userId);

                            allChats.push({
                                userName: userData.userName,
                                profilePic: userData.profilePic,
                                message: lastMsg.messageType === 'text' ? lastMsg.message : `[${lastMsg.messageType}]`,
                                createdAt: createdAt,
                                time: time,
                                type: data.type,
                                userId: userId,
                                messageDate: lastMsg.createdAt


                            });
                        }
                        }));
                        let filtered = allChats;
                        if (searchValue) {

                            filtered = allChats.filter(chat =>
                                chat.userName.toLowerCase().includes(searchValue) ||
                                chat.message.toLowerCase().includes(searchValue) ||
                                chat.messageDate.toString().toLowerCase().indexOf(searchValue) > -1
                            );
                        }
                        filtered.sort((a, b) => {

                            let aVal = a[orderByField] ? a[orderByField].toString().toLowerCase() : '';
                            let bVal = b[orderByField] ? b[orderByField].toString().toLowerCase() : '';
                            if (orderByField === 'messageDate') {
                                aVal = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                                bVal = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;

                            }
                            if (orderDirection === 'asc') {
                                return (aVal > bVal) ? 1 : -1;
                            } else {
                                return (aVal < bVal) ? 1 : -1;
                            }

                        });
                        let records = [];
                        const totalRecords = filtered.length;

                        $('.total_count').text(totalRecords);
                        const paginated = filtered.slice(start, start + length);
                        await Promise.all(paginated.map(async (childData) => {
                            childData.unreadCount = await countUnreadMessages(childData.userId);

                            // Build and collect initial HTML
                            const getData = await buildHTML(childData);
                            records.push(getData);
                           
                            listenToUnreadMessages(
                                childData.userId,
                                (unreadCount) => {
                                    childData.unreadCount = unreadCount;
                                    buildHTML(childData).then(updatedHTML => {
                                        const countEl = document.querySelector(`.unread-count.unread-${childData.userId}`);
                                        if (countEl) {
                                            if (unreadCount > 0) {
                                                countEl.innerText = unreadCount;
                                                countEl.style.display = 'inline-block';
                                            } else {
                                                countEl.innerText = '';
                                                countEl.style.display = 'none';
                                            }
                                        }
                                    });
                                }
                            );

                        }));
                        $('#data-table_processing').hide();

                        callback({
                            draw: data.draw,
                            recordsTotal: totalRecords,
                            recordsFiltered: totalRecords,
                            data: records
                        });
                    } catch (error) {
                        console.error('Error loading support history:', error);
                        $('#data-table_processing').hide();
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: []
                        });
                    }

                },
                order: [3, 'desc'],
                columnDefs: [{
                        targets: [0, 4],
                        orderable: false,
                    },
                    {
                        targets: 3,
                        type: 'date',
                        render: function(data) {
                            return data;
                        }
                    },
                ],
                "language": {
                    "zeroRecords": "{{ trans('lang.no_record_found') }}",
                    "emptyTable": "{{ trans('lang.no_record_found') }}",
                    "processing": ""
                },

            });

                function debounce(func, wait) {
                    let timeout;
                    const context = this;
                    return function(...args) {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(context, args), wait);
                    };
                }
                $('#search-input').on('input', debounce(function() {
                    const searchValue = $(this).val();
                    if (searchValue.length >= 3) {
                        $('#data-table_processing').show();
                        table.search(searchValue).draw();
                    } else if (searchValue.length === 0) {
                        $('#data-table_processing').show();
                        table.search('').draw();
                    }
                }, 300));
            }
            
            // Start initializing DataTable
            initializeDataTable();
        });
        async function getUserName(id) {
            var userName = '';
            var profilePic = '';
            var obj = {};
           
            await database.collection('users').doc(id).get().then(async function(snapshot) {
                if (snapshot && snapshot.data()) {
                    var data = snapshot.data();
                    userName = data.firstName + ' ' + data.lastName;
                    profilePic = data.profilePictureURL;

                }
            });
            obj = {
                'userName': userName,
                'profilePic': profilePic
            }
            return obj;
        }


        async function buildHTML(val) {
            var html = [];

            html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + val.userId + '" class="is_open" dataId="' + val.userId + '"><label class="col-3 control-label"\n' +
                'for="is_open_' + val.userId + '" ></label></td>');
            if (val.type == 'customer') {
                var route1 = "{{ route('users.view', ':id') }}"
                route1 = route1.replace(':id', val.userId);
                var chatRoute = "{{ route('users.chat', ':id') }}"
                chatRoute = chatRoute.replace(':id', val.userId);
            } else if(val.type == 'driver'){
                var route1 = "{{ route('drivers.view', ':id') }}"
                route1 = route1.replace(':id', val.userId);
                var chatRoute = "{{ route('drivers.chat', ':id') }}"
                chatRoute = chatRoute.replace(':id', val.userId);
            }else{
                var route1 = "{{ route('vendor.edit', ':id') }}"
                route1 = route1.replace(':id', val.userId);
                var chatRoute = "{{ route('vendors.chat', ':id') }}"
                chatRoute = chatRoute.replace(':id', val.userId);
            }
            if (val.userName != '') {
                if (val.profilePic == '' || val.profilePic == null) {
                    var userImg = '<img width="100%" style="width:70px;height:70px;" src="' + placeholderImage + '" alt="image">';
                } else {
                    var userImg = '<img width="100%" style="width:70px;height:70px;" src="' + val.profilePic + '" alt="image">';
                }
                html.push(userImg + '<a href="' + route1 + '">' + val.userName + '</a>');
            } else {
                html.push("{{ trans('lang.unknown_user') }}");
            }
            html.push('<span class="last-message">'+val.message +'</span>');
            html.push(val.createdAt + '<br>' + val.time);
            var actionHtml = '';
            if (val.userName != '') {
                actionHtml = actionHtml + '<span class="action-btn">';
                actionHtml = actionHtml + `<a href="${chatRoute}" class="chat-message chat-count-message">
                                    <i class="mdi mdi-wechat mdi-24px"></i>
                                    ${ val.unreadCount > 0 ? `<span class="unread-count unread-${val.userId}">${val.unreadCount}</span>` : `<span class="unread-count unread-${val.userId} d-none"></span>`}
                                </a>`;
                actionHtml += '</span>';
                html.push(actionHtml);
            } else {
                html.push('');
            }
            return html;
        }

        async function countUnreadMessages(userId) {
            var unreadCount = 0;
            try {
                // Get all messages and filter in JavaScript to avoid index requirement
                const snapshot = await database.collection('chat_admin').doc(userId).collection("thread")
                    .get();
                
                // Filter messages that are unread and not from admin
                unreadCount = snapshot.docs.filter(doc => {
                    const data = doc.data();
                    return data.seen === false && data.senderId !== 'admin';
                }).length;
            } catch (error) {
                console.error('Error counting unread messages:', error);
                unreadCount = 0;
            }
            return unreadCount;
        }

        function listenToUnreadMessages(userId, callback) {
            try {
                // Use onSnapshot with filtering to avoid index requirement
                const unsubscribe = database.collection('chat_admin')
                    .doc(userId)
                    .collection("thread")
                    .onSnapshot(snapshot => {
                        // Filter messages that are unread and not from admin
                        const unreadMessages = snapshot.docs.filter(doc => {
                            const data = doc.data();
                            return data.seen === false && data.senderId !== 'admin';
                        });
                        const unreadCount = unreadMessages.length;
                        if (typeof callback === 'function') {
                            callback(unreadCount);
                        }
                    }, error => {
                        console.error('Error listening to unread messages:', error);
                        if (typeof callback === 'function') {
                            callback(0);
                        }
                    });
                
                // Return unsubscribe function
                return unsubscribe;
            } catch (error) {
                console.error('Error setting up unread messages listener:', error);
                if (typeof callback === 'function') {
                    callback(0);
                }
                return function() {}; // Return empty unsubscribe function
            }
        }


        $("#is_active").click(function() {
            $("#supportHistoryTable .is_open").prop('checked', $(this).prop('checked'));
        });
        var comfirmDel = "{{ trans('lang.delete_chat_alert') }}"
        $("#deleteAll").click(function() {
            if ($('#supportHistoryTable .is_open:checked').length) {
                if (confirm(comfirmDel)) {
                    jQuery("#overlay").show();
                    $('#supportHistoryTable .is_open:checked').each(function() {
                        var dataId = $(this).attr('dataId');
                        deleteDocumentWithImage('chat_admin', dataId)
                            .then(() => {
                                window.location.reload();
                            })
                            .catch((error) => {
                                console.error('Error deleting document or store data:', error);
                            });
                    });
                }
            } else {
                alert("{{ trans('lang.select_delete_alert') }}");
            }
        });
    </script>
@endsection
