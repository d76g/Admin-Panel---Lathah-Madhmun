@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.user_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.user_table')}}</li>
            </ol>
        </div>
        <div>
        </div>
    </div>
    <div class="container-fluid">
       <div class="admin-top-section"> 
        <div class="row">
            <div class="col-12">
                <div class="d-flex top-title-section pb-4 justify-content-between">
                    <div class="d-flex top-title-left align-self-center">
                        <span class="icon mr-3"><img src="{{ asset('images/users.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.user_plural')}}</h3>
                        <span class="counter ml-3 total_count"></span>
                    </div>
                    <div class="d-flex top-title-right align-self-center">
                        <div class="select-box pl-3">
                            <select class="form-control status_selector filteredRecords">
                                <option value="">{{trans("lang.status")}}</option>
                                <option value="active"  >{{trans("lang.active")}}</option>
                                <option value="inactive"  >{{trans("lang.in_active")}}</option>
                            </select>
                        </div>
                        <div class="select-box pl-3">
                            <div id="daterange"><i class="fa fa-calendar"></i>&nbsp;
                                <span></span>&nbsp; <i class="fa fa-caret-down"></i>
                            </div>
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
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.user_table')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.users_table_text')}}</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3"> 
                        <a class="btn-primary btn rounded-full" href="{!! route('users.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.user_create')}}</a>
                     </div>
                   </div>                
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                            <table id="userTable"
                                   class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                   cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <?php if (in_array('user.delete', json_decode(@session('user_permissions'),true))) { ?>
                                    <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active"><a id="deleteAll"
                                    class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label></th>
                                    <?php } ?>
                                    <th>{{trans('lang.user_info')}}</th>
                                    <th>{{trans('lang.email')}}</th>
                                    <th>{{trans('lang.date')}}</th>
                                    <th>{{trans('lang.active')}}</th>
                                    <th>{{trans('lang.wallet_transaction')}}</th>
                                    <!-- <th >{{trans('lang.role')}}</th> -->
                                    <th>{{trans('lang.actions')}}</th>
                                </tr>
                                </thead>                               
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
<script type="text/javascript">
    // Initialize Firebase-dependent variables safely
    var database, ref;
    var placeholderImage = '';
    var user_permissions = '<?php echo @session("user_permissions")?>';
    user_permissions = Object.values(JSON.parse(user_permissions));
    var checkDeletePermission = false;
    if ($.inArray('user.delete', user_permissions) >= 0) {
        checkDeletePermission = true;
    }
    
    // Wait for Firebase to be initialized
    function initializeUsersFirebase() {
        if (typeof firebase !== 'undefined' && firebase.apps && firebase.apps.length > 0) {
            try {
                database = firebase.firestore();
                ref = database.collection('users').where("role", "in", ["customer"]);
                console.log('Users page Firebase initialized successfully');
                initializeUsersPage();
                return true;
            } catch (error) {
                console.error('Error initializing Firebase in users page:', error);
                return false;
            }
        } else {
            console.warn('Firebase not initialized yet in users page, retrying...');
            setTimeout(initializeUsersFirebase, 500);
            return false;
        }
    }
    
    function initializeUsersPage() {
        if (!database || !ref) {
            console.error('Firebase not initialized in initializeUsersPage');
            return;
        }
        
        // Load placeholder image
        var placeholder = database.collection('settings').doc('placeHolderImage');
        placeholder.get().then(async function (snapshotsimage) {
            if (snapshotsimage.exists) {
                var placeholderImageData = snapshotsimage.data();
                placeholderImage = placeholderImageData.image;
            }
        }).catch(function(error) {
            console.error('Error loading placeholder image:', error);
        });
        
        // Initialize DataTable and other components
        initializeDataTable();
        initializeDataTableContent();
        
        // Set up filtered records handler after Firebase is ready
        setupFilteredRecordsHandler();
    }
    
    function initializeDataTable() {
        if (!ref) {
            console.error('ref not initialized');
            return;
        }
        
        // Initialize select2
        $('.status_selector').select2({
            placeholder: '{{trans("lang.status")}}',  
            minimumResultsForSearch: Infinity,
            allowClear: true 
        });
        $('select').on("select2:unselecting", function(e) {
            var self = $(this);
            setTimeout(function() {
                self.select2('close');
            }, 0);
        });
        
        // Set up date picker (but don't trigger change events yet)
        setDate();
    }
    
    function setDate() {
        $('#daterange span').html('{{trans("lang.select_range")}}');
        $('#daterange').daterangepicker({
            autoUpdateInput: false, 
        }, function (start, end) {
            $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            // Only trigger change if Firebase is ready
            if (database && ref) {
                $('.filteredRecords').trigger('change'); 
            }
        });
        $('#daterange').on('apply.daterangepicker', function (ev, picker) {
            $('#daterange span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
            // Only trigger change if Firebase is ready
            if (database && ref) {
                $('.filteredRecords').trigger('change');
            }
        });
        $('#daterange').on('cancel.daterangepicker', function (ev, picker) {
            $('#daterange span').html('{{trans("lang.select_range")}}');
            // Only trigger change if Firebase is ready
            if (database && ref) {
                $('.filteredRecords').trigger('change'); 
            }
        });
    }
    
    // Set up the change handler - this will be called after Firebase is initialized
    function setupFilteredRecordsHandler() {
        $('.filteredRecords').off('change').on('change', async function() {
            if (!database || !ref) {
                console.error('Firebase not initialized in filteredRecords handler');
                return;
            }
            var status = $('.status_selector').val();
            var daterangepicker = $('#daterange').data('daterangepicker');
            ref = database.collection('users').where("role", "in", ["customer"]);
            
            if ($('#daterange span').html() != '{{trans("lang.select_range")}}' && daterangepicker) {
                var from = moment(daterangepicker.startDate).toDate();
                var to = moment(daterangepicker.endDate).toDate();
                if (from && to) { 
                    var fromDate = firebase.firestore.Timestamp.fromDate(new Date(from));
                    ref = ref.where('createdAt', '>=', fromDate);
                    var toDate = firebase.firestore.Timestamp.fromDate(new Date(to));
                    ref = ref.where('createdAt', '<=', toDate);
                }
            }
            
            if (status) {
                ref = (status == "active") ? ref.where('active', '==', true) : ref.where('active', '==', false);
            }
            
            if ($('#userTable').DataTable()) {
                $('#userTable').DataTable().ajax.reload();
            }
        });
    }
    
    // Start waiting for Firebase when page loads
    $(document).ready(function () {
        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });
        
        // Show loading indicator
        jQuery("#data-table_processing").show();
        
        // Listen for Firebase initialization event
        window.addEventListener('firebaseInitialized', function() {
            console.log('Received firebaseInitialized event in users page');
            initializeUsersFirebase();
        });
        
        // Start waiting for Firebase
        function waitForFirebase() {
            if (typeof firebase !== 'undefined' && firebase.apps && firebase.apps.length > 0) {
                if (initializeUsersFirebase()) {
                    return; // Success
                }
            }
            
            // If not ready, wait and retry (max 10 seconds)
            var attempts = (waitForFirebase.attempts || 0) + 1;
            waitForFirebase.attempts = attempts;
            
            if (attempts < 20) { // 20 attempts * 500ms = 10 seconds max
                setTimeout(waitForFirebase, 500);
            } else {
                console.error('Firebase initialization timeout in users page');
                jQuery("#data-table_processing").hide();
            }
        }
        
        waitForFirebase();
    });
    
    // Initialize DataTable after Firebase is ready
    function initializeDataTableContent() {
        if (!ref) {
            console.error('ref not initialized in initializeDataTableContent');
            return;
        }
        
        $(document).on('click', '.dt-button-collection .dt-button', function () {
            $('.dt-button-collection').hide();
            $('.dt-button-background').hide();
        });
        $(document).on('click', function (event) {
            if (!$(event.target).closest('.dt-button-collection, .dt-buttons').length) {
                $('.dt-button-collection').hide();
                $('.dt-button-background').hide();
            }
        });
        var fieldConfig = {
            columns: [
                { key: 'fullName', header: "{{trans('lang.user_info')}}" },
                { key: 'email', header: "{{trans('lang.email')}}" },
                { key: 'active', header: "{{trans('lang.active')}}" },
                { key: 'createdAt', header: "{{trans('lang.created_at')}}" },
            ],
            fileName: "{{trans('lang.user_table')}}",
        };
        const table = $('#userTable').DataTable({
            pageLength: 10,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: function (data, callback, settings) {
                const start = data.start;
                const length = data.length;
                const searchValue = data.search.value.toLowerCase();
                const orderColumnIndex = data.order[0].column;
                const orderDirection = data.order[0].dir;
                const orderableColumns = (checkDeletePermission)? ['','fullName', 'email', 'createdAt','','',''] : ['fullName', 'email', 'createdAt','','',''];
                const orderByField = orderableColumns[orderColumnIndex];
                if (searchValue.length >= 3 || searchValue.length === 0) {
                    $('#data-table_processing').show();
                }
                
                if (!ref) {
                    console.error('ref not initialized in DataTable ajax');
                    $('#data-table_processing').hide();
                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        filteredData: [],
                        data: []
                    });
                    return;
                }
                
                ref.orderBy('createdAt', 'desc').get().then(async function (querySnapshot) {
                    if (querySnapshot.empty) {
                        $('.total_count').text(0); 
                        console.error("No data found in Firestore.");
                        $('#data-table_processing').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            filteredData: [],
                            data: [] // No data
                        });
                        return;
                    }
                    let records = [];
                    let filteredRecords = []; 
                    querySnapshot.forEach(function (doc) {                        
                        let childData = doc.data();
                        childData.id = doc.id; // Ensure the document ID is included in the data
                        childData.fullName = childData.firstName + ' ' + childData.lastName || " "
                        var date = '';
                        var time = '';
                        childData.email = shortEmail(childData.email);
                        if (childData.hasOwnProperty("createdAt")) {
                            try {
                                date = childData.createdAt.toDate().toDateString();
                                time = childData.createdAt.toDate().toLocaleTimeString('en-US');
                            } catch (err) {
                            }
                        }
                        var createdAt = date + ' ' + time;
                        if (searchValue) {                           
                            if (
                                (childData.fullName && childData.fullName.toString().toLowerCase().includes(searchValue)) ||
                                (createdAt && createdAt.toString().toLowerCase().indexOf(searchValue) > -1) || (childData.email && childData.email.toString().includes(searchValue))
                            ) {
                                filteredRecords.push(childData);
                            }
                        } else {
                            filteredRecords.push(childData);
                        }
                    });
                    filteredRecords.sort((a, b) => {
                        let aValue = a[orderByField] ? a[orderByField].toString().toLowerCase() : '';
                        let bValue = b[orderByField] ? b[orderByField].toString().toLowerCase() : '';
                        if (orderByField === 'createdAt') {
                            aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                            bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;
                        }                        
                        if (orderDirection === 'asc') {
                            return (aValue > bValue) ? 1 : -1;
                        } else {
                            return (aValue < bValue) ? 1 : -1;
                        }
                    });
                    const totalRecords = filteredRecords.length;
                    $('.total_count').text(totalRecords); 
                    const paginatedRecords = filteredRecords.slice(start, start + length);
                    paginatedRecords.forEach(function (childData) {
                        var id = childData.id;
                        var route1 = '{{route("users.edit",":id")}}';
                        route1 = route1.replace(':id', id);
                        var user_view = '{{route("users.view",":id")}}';
                        user_view = user_view.replace(':id', id);
                        var trroute1 = '{{route("users.walletstransaction",":id")}}';
                        trroute1 = trroute1.replace(':id', id);
                        var date = '';
                        var time = '';
                        if (childData.hasOwnProperty("createdAt")) {
                            try {
                                date = childData.createdAt.toDate().toDateString();
                                time = childData.createdAt.toDate().toLocaleTimeString('en-US');
                            } catch (err) {
                            }
                        }
                        var chatViewRoute = "{{ route('users.chat', ':id') }}".replace(':id', childData.id);
                        let unreadHtml = '';
                        var vendorImage=childData.profilePictureURL == '' || childData.profilePictureURL == null ? '<img alt="" width="100%" style="width:70px;height:70px;" src="' + placeholderImage + '" alt="image">' : '<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="" width="100%" style="width:70px;height:70px;" src="' + childData.profilePictureURL + '" alt="image">'
                        records.push([
                            checkDeletePermission ? '<td class="delete-all"><input type="checkbox" id="is_open_' + childData.id + '" class="is_open" dataId="' + childData.id + '"><label class="col-3 control-label"\n' + 'for="is_open_' + childData.id + '" ></label></td>' : '',
                            vendorImage+'<a href="' + user_view + '" class="redirecttopage">' + childData.fullName + '</a>',
                            childData.email ? childData.email : ' ',
                            date + ' ' + time,
                            childData.active ? '<label class="switch"><input type="checkbox" checked id="' + childData.id + '" name="isActive"><span class="slider round"></span></label>' : '<label class="switch"><input type="checkbox" id="' + childData.id + '" name="isActive"><span class="slider round"></span></label>',
                            '<a href="' + trroute1 + '">{{trans("lang.transaction")}}</a>',
                            '<span class="action-btn"><a href="' + user_view + '"><i class="mdi mdi-eye"></i></a><a href="' + route1 + '"><i class="mdi mdi-lead-pencil" title="Edit"></i></a><?php if(in_array('user.delete', json_decode(@session('user_permissions'),true))){ ?> <a id="' + childData.id + '" class="delete-btn" name="user-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a></td><?php } ?>' +
                            '<?php if (in_array("users.chat", (array) json_decode(@session("user_permissions"), true))) { ?>' +
                            '<a href="' + chatViewRoute + '" class="chat-message" style="position: relative; display: inline-block;">' +
                            '<i class="mdi mdi-wechat mdi-24px"></i>' + unreadHtml +
                            '</a>' +
                            '<?php } ?>' +
                            '</span>'                           
                        ]);
                    });
                    $('#data-table_processing').hide(); 
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords, 
                        recordsFiltered: totalRecords, 
                        filteredData: filteredRecords,
                        data: records 
                    });
                }).catch(function (error) {
                    console.error("Error fetching data from Firestore:", error);
                    $('#data-table_processing').hide(); 
                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        filteredData: [],
                        data: [] 
                    });
                });
            },           
            order: [3, 'desc'],
            columnDefs: [
                {
                    targets: (checkDeletePermission) ? 3 : 2,
                    type: 'date',
                    render: function (data) {
                        return data;
                    }
                },
                { orderable: false, targets: (checkDeletePermission) ? [0, 4,5, 6] : [3,4, 5] },
            ],
            "language": {
                "zeroRecords": "{{trans("lang.no_record_found")}}",
                "emptyTable": "{{trans("lang.no_record_found")}}",
                "processing": "" 
            },
            dom: 'lfrtipB',
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="mdi mdi-cloud-download"></i> Export as',
                    className: 'btn btn-info',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: 'Export Excel',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'excel',fieldConfig);
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'Export PDF',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'pdf',fieldConfig);
                            }
                        },   
                        {
                            extend: 'csvHtml5',
                            text: 'Export CSV',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'csv',fieldConfig);
                            }
                        }
                    ]
                }
            ],
            initComplete: function() {
                $(".dataTables_filter").append($(".dt-buttons").detach());
                $('.dataTables_filter input').attr('placeholder', 'Search here...').attr('autocomplete','new-password').val('');
                $('.dataTables_filter label').contents().filter(function() {
                    return this.nodeType === 3; 
                }).remove();
            }
        });
        table.columns.adjust().draw();
        function debounce(func, wait) {
            let timeout;
            const context = this;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }
        $('#search-input').on('input', debounce(function () {
            const searchValue = $(this).val();
            if (searchValue.length >= 3) {
                $('#data-table_processing').show();
                table.search(searchValue).draw();
            } else if (searchValue.length === 0) {
                $('#data-table_processing').show();
                table.search('').draw();
            }
        }, 300));
    });
    $("#is_active").click(function () {
        $("#userTable .is_open").prop('checked', $(this).prop('checked'));
    });
    $("#deleteAll").click(function () {
        if ($('#userTable .is_open:checked').length) {
            if (confirm("{{trans('lang.selected_delete_alert')}}")) {
                jQuery("#data-table_processing").show();
                $('#userTable .is_open:checked').each(async function () {
                    var dataId = $(this).attr('dataId');
                    await deleteDocumentWithImage('users',dataId,'profilePictureURL');
                    const getStoreName = deleteUserData(dataId);
                    setTimeout(function () {
                        window.location.reload();
                    }, 7000);
                });
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });
    async function deleteUserData(userId) {
        if (!database) {
            console.error('Firebase not initialized in deleteUserData');
            return;
        }
        
        await database.collection('wallet').where('user_id', '==', userId).get().then(async function (snapshotsItem) {
            if (snapshotsItem.docs.length > 0) {
                snapshotsItem.docs.forEach((temData) => {
                    var item_data = temData.data();
                    database.collection('wallet').doc(item_data.id).delete().then(function () {
                    });
                });
            }
        });
        //delete user from mysql
        database.collection('settings').doc("Version").get().then(function (snapshot) {
            var settingData = snapshot.data();
            if (settingData && settingData.websiteUrl){
                var siteurl = settingData.websiteUrl + "/api/delete-user"; 
                var dataObject = { "uuid": userId };         
                jQuery.ajax({
                    url: siteurl, 
                    method: 'POST',
                    contentType: "application/json; charset=utf-8",
                    data: JSON.stringify(dataObject),
                    success: function (data) {
                        console.log('Delete user from sql success:', data);
                    },
                    error: function (error) {
                        console.log('Delete user from sql error:', error.responseJSON.message);
                    }
                });
            }
        });
        //delete user from authentication
        var dataObject = {"data": {"uid": userId}};
        var projectId = '<?php echo env('FIREBASE_PROJECT_ID') ?>';
        jQuery.ajax({
            url: 'https://us-central1-' + projectId + '.cloudfunctions.net/deleteUser',
            method: 'POST',
            contentType: "application/json; charset=utf-8",
            data: JSON.stringify(dataObject),
            success: function (data) {
                console.log('Delete user success:', data.result);
            },
            error: function (xhr, status, error) {
                var responseText = JSON.parse(xhr.responseText);
                console.log('Delete user error:', responseText.error);
            }
        });
    }    
    $(document).on("click", "a[name='user-delete']", async function (e) {
        var id = this.id;
        await deleteDocumentWithImage('users',id,'profilePictureURL');
            const getStoreName = deleteUserData(id);
            setTimeout(function () {
                window.location.href = '{{ url()->current() }}';
            }, 7000);
    });
    $(document).on("click", "input[name='isActive']", function (e) {
        var ischeck = $(this).is(':checked');
        if (!database) {
            console.error('Firebase not initialized');
            return;
        }
        
        var id = this.id;
        if (ischeck) {
            database.collection('users').doc(id).update({'active': true}).then(function (result) {
            });
        } else {
            database.collection('users').doc(id).update({'active': false}).then(function (result) {
            });
        }
    });
</script>
@endsection