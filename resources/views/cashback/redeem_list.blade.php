@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.cashback_redeem') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.cashback_redeem') }}</li>
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
                                <span class="icon mr-3"><img src="{{ asset('images/category.png') }}"></span>
                                <h3 class="mb-0">{{ trans('lang.cashback_redeem') }}</h3>
                                <span class="counter ml-3 attribute_count"></span>
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
                                    <h3 class="text-dark-2 mb-2 h4">{{ trans('lang.cashback_redeem') }}</h3>
                                    <p class="mb-0 text-dark-2">{{ trans('lang.cashback_redeem_table_text') }}</p>
                                </div>

                            </div>
                            <div class="card-body">
                                <div class="table-responsive m-t-10">
                                    <table id="cashbackTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <?php if (in_array('cashback.redeem.delete', json_decode(@session('user_permissions'),true))) {
                                                ?>
                                                <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active">
                                                        <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i> {{ trans('lang.all') }}</a></label></th>
                                                <?php }
                                                ?>
                                                <th>{{ trans('lang.customer_name') }}</th>
                                                <th>{{ trans('lang.order_id') }}</th>
                                                <th>{{ trans('lang.cashback_amount') }}</th>
                                                <th> {{ trans('lang.date') }}</th>
                                                <?php if (in_array('cashback.redeem.delete', json_decode(@session('user_permissions'), true))) { ?>
                                                <th>{{ trans('lang.actions') }}</th>
                                                <?php  }?>
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
    <script type="text/javascript">
        var database = firebase.firestore();
        var id = "{{ $id }}";
        var ref = database.collection('cashback_redeem').where('cashbackId', '==', id);
        var append_list = '';
        var user_permissions = '<?php echo @session('user_permissions'); ?>';
        user_permissions = Object.values(JSON.parse(user_permissions));
        var checkDeletePermission = false;
        if ($.inArray('cashback.redeem.delete', user_permissions) >= 0) {
            checkDeletePermission = true;
        }

        var currency = database.collection('settings');
        var currentCurrency = '';
        var currencyAtRight = false;
        var decimal_degits = 0;
        var refCurrency = database.collection('currencies').where('isActive', '==', true);
        refCurrency.get().then(async function(snapshots) {
            var currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;
            if (currencyData.decimal_degits) {
                decimal_degits = currencyData.decimal_degits;
            }
        });

        $(document).ready(function() {
            jQuery("#data-table_processing").show();
            const table = $('#cashbackTable').DataTable({
                pageLength: 10, // Number of rows per page
                processing: false, // Show processing indicator
                serverSide: true, // Enable server-side processing
                responsive: true,
                ajax: function(data, callback, settings) {
                    const start = data.start;
                    const length = data.length;
                    const searchValue = data.search.value.toLowerCase();
                    const orderColumnIndex = data.order[0].column;
                    const orderDirection = data.order[0].dir;
                    const orderableColumns = (checkDeletePermission) ? ['', 'customerName', 'orderId', 'cashbackAmount', 'createdAt', ''] : ['customerName', 'orderId', 'cashbackAmount', 'createdAt']; // Ensure this matches the actual column names
                    const orderByField = orderableColumns[orderColumnIndex]; // Adjust the index to match your table
                    if (searchValue.length >= 3 || searchValue.length === 0) {
                        $('#data-table_processing').show();
                    }
                    ref.get().then(async function(querySnapshot) {
                        if (querySnapshot.empty) {
                            console.error("No data found in Firestore.");
                            $('#data-table_processing').hide(); // Hide loader
                            callback({
                                draw: data.draw,
                                recordsTotal: 0,
                                recordsFiltered: 0,
                                data: [] // No data
                            });
                            return;
                        }
                        let records = [];
                        let filteredRecords = [];
                        $('.attribute_count').text(querySnapshot.docs.length);
                        await Promise.all(querySnapshot.docs.map(async (doc) => {
                            let childData = doc.data();
                            childData.id = doc.id; // Ensure the document ID is included in the data  
                            childData.customerName = await getCustomer(childData.userId);
                            childData.cashbackAmount = await getCashbackAmount(childData.orderId);
                            if (childData.cashbackAmount == '') {
                                childData.orderId = "{{ trans('lang.unknown') }}";
                            }

                            var date = '';
                            var time = '';
                            if (childData.hasOwnProperty("createdAt")) {
                                try {
                                    date = childData.createdAt.toDate().toDateString();
                                    time = childData.createdAt.toDate().toLocaleTimeString('en-US');
                                } catch (err) {}
                            }
                            var createdAt = date + ' ' + time;
                            childData.createDate = createdAt;
                            if (searchValue) {

                                if (
                                    (childData.customerName && childData.customerName.toLowerCase().toString().includes(searchValue)) ||
                                    (childData.orderId && childData.orderId.toLowerCase().toString().includes(searchValue)) ||
                                    (childData.cashbackAmount && childData.cashbackAmount.toLowerCase().toString().includes(searchValue)) ||
                                    (createdAt && createdAt.toString().toLowerCase().indexOf(searchValue) > -1)
                                ) {
                                    filteredRecords.push(childData);
                                }
                            } else {
                                filteredRecords.push(childData);
                            }
                        }));
                        filteredRecords.sort((a, b) => {
                            let aValue = a[orderByField] ? a[orderByField].toString().toLowerCase() : '';
                            let bValue = b[orderByField] ? b[orderByField].toString().toLowerCase() : '';
                            if (orderByField === 'createdAt') {
                                try {
                                    aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                                    bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;
                                } catch (err) {}
                            }
                            if (orderDirection === 'asc') {
                                return (aValue > bValue) ? 1 : -1;
                            } else {
                                return (aValue < bValue) ? 1 : -1;
                            }
                        });
                        const totalRecords = filteredRecords.length;
                        const paginatedRecords = filteredRecords.slice(start, start + length);
                        await Promise.all(paginatedRecords.map(async (childData) => {
                            var getData = await buildHTML(childData);
                            records.push(getData);
                        }));
                        $('#data-table_processing').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: totalRecords, // Total number of records in Firestore
                            recordsFiltered: totalRecords, // Number of records after filtering (if any)
                            data: records // The actual data to display in the table
                        });
                    }).catch(function(error) {
                        console.error("Error fetching data from Firestore:", error);
                        $('#data-table_processing').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: [] // No data due to error
                        });
                    });
                },
                order: (checkDeletePermission) ? [[4, 'desc']] : [[3, 'desc']],
                columnDefs: [{
                    orderable: false,
                    targets: (checkDeletePermission) ? [0, 5] : []
                }, ],
                "language": {
                    "zeroRecords": "{{ trans('lang.no_record_found') }}",
                    "emptyTable": "{{ trans('lang.no_record_found') }}",
                    "processing": "" // Remove default loader
                },
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
        });

        function buildHTML(val) {
            var html = [];
            newdate = '';
            var id = val.id;
            var routeOrder = '{{ route('orders.edit', ':id') }}';
            routeOrder = routeOrder.replace(':id', val.orderId);

            var routeCustomer = '{{ route('users.view', ':id') }}';
            routeCustomer = routeCustomer.replace(':id', val.userId);
            if (checkDeletePermission) {
                html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' +
                    id + '"><label class="col-3 control-label"\n' +
                    'for="is_open_' + id + '" ></label></td>');
            }
            if (val.customerName != '') {
                html.push('<a href="' + routeCustomer + '">' + val.customerName + '</a>');
            } else {
                html.push("{{ trans('lang.unknown') }}");
            }

            if (val.orderId == "{{ trans('lang.unknown') }}") {
                html.push(val.orderId);
            } else {
                html.push('<a href="' + routeOrder + '">' + val.orderId + '</a>');
            }
            if (val.cashbackAmount != '') {
                if (currencyAtRight) {
                    html.push(parseFloat(val.cashbackAmount).toFixed(decimal_degits) + "" + currentCurrency)
                } else {
                    html.push(currentCurrency + "" + parseFloat(val.cashbackAmount).toFixed(decimal_degits))
                }
            } else {
                html.push('-')
            }

            html.push(val.createDate);

            var actionHtml = '';
            if (checkDeletePermission) {
                actionHtml += '<a id="' + val.id + '" name="cashback-delete" class="delete-btn" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';
            }
            actionHtml += '</span>';
            html.push(actionHtml);
            return html;
        }
        $(document).on("click", "a[name='cashback-delete']", function(e) {
            var id = this.id;
            jQuery("#data-table_processing").show();
            database.collection('cashback_redeem').doc(id).delete().then(function(result) {
                window.location.reload();
            });
        });
        async function getCashbackAmount(orderId) {

            var cashbackAmount = '';
            var snapshot = await database.collection('restaurant_orders').doc(orderId).get();

            if (snapshot.exists) {
                var data = snapshot.data();
                if (data.hasOwnProperty('cashback') && data.cashback != null && data.cashback != '') {
                    cashbackAmount = data.cashback.cashbackValue;
                }
            }
            return cashbackAmount;
        }
        async function getCustomer(userId) {
            var customerName = '';
            var snapshot = await database.collection('users').doc(userId).get();
            if (snapshot.exists) {
                var data = snapshot.data();
                customerName = data.firstName + ' ' + data.lastName;
            }
            return customerName;
        }
        $("#is_active").click(function() {
            $("#cashbackTable .is_open").prop('checked', $(this).prop('checked'));
        });
        $("#deleteAll").click(function() {
            if ($('#cashbackTable .is_open:checked').length) {
                if (confirm("{{ trans('lang.selected_delete_alert') }}")) {
                    jQuery("#data-table_processing").show();
                    $('#cashbackTable .is_open:checked').each(async function() {
                        var dataId = $(this).attr('dataId');
                        database.collection('cashback_redeem').doc(dataId).delete().then(function(result) {
                            window.location.reload();
                        });

                    });
                }
            } else {
                alert("{{ trans('lang.select_delete_alert') }}");
            }
        });
    </script>
@endsection
