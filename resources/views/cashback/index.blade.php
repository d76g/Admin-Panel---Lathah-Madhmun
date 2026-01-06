@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.cashback_plural') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.cashback_table') }}</li>
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
                                <span class="icon mr-3"><img src="{{ asset('images/cashback.png') }}"></span>
                                <h3 class="mb-0">{{ trans('lang.cashback_plural') }}</h3>
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
                                    <h3 class="text-dark-2 mb-2 h4">{{ trans('lang.cashback_table') }}</h3>
                                    <p class="mb-0 text-dark-2">{{ trans('lang.cashback_table_text') }}</p>
                                </div>
                                <div class="card-header-right d-flex align-items-center">
                                    <div class="card-header-btn mr-3">
                                        <a class="btn-primary btn rounded-full" href="{!! route('cashback.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{ trans('lang.cashback_create') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive m-t-10">
                                    <table id="cashbackTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <?php if (in_array('cashback.delete', json_decode(@session('user_permissions'),true))) {
                                                ?>
                                                <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active">
                                                        <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i> {{ trans('lang.all') }}</a></label></th>
                                                <?php }
                                                ?>
                                                <th>{{ trans('lang.cashback_name') }}</th>
                                                <th>{{ trans('lang.cashback_type') }}</th>
                                                <th>{{ trans('lang.cashback_amount') }}</th>
                                                <th>{{ trans('lang.minimum_purchase') }}</th>
                                                <th>{{ trans('lang.total_used') }}</th>
                                                <th>{{ trans('lang.cashback_duration') }}</th>
                                                <th>{{ trans('lang.cashback_status') }}</th>
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
    <script type="text/javascript">
        var database = firebase.firestore();
        var ref = database.collection('cashback').orderBy('title');
        var append_list = '';
        var user_permissions = '<?php echo @session('user_permissions'); ?>';
        user_permissions = Object.values(JSON.parse(user_permissions));
        var checkDeletePermission = false;
        if ($.inArray('cashback.delete', user_permissions) >= 0) {
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
                    const orderableColumns = (checkDeletePermission) ? ['', 'title', 'cashbackType', 'cashbackAmount', 'minumumPurchaseAmount', 'redeemCount', 'duration', '', ''] : ['title', 'cashbackType', 'cashbackAmount', 'minumumPurchaseAmount', 'redeemCount', 'duration', '', '']; // Ensure this matches the actual column names
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
                            childData.redeemCount = await getRedeemCount(childData.id);
                            const normalize = (value) => {
                            if (!value) return '';
                                return value.toString().toLowerCase().replace(/[^a-z0-9]/gi, ''); 
                            };

                            const normalizedSearch = normalize(searchValue);
                            if (searchValue) {
                                if (
                                    (childData.title && normalize(childData.title).includes(normalizedSearch)) ||
                                    (childData.cashbackType && normalize(childData.cashbackType).includes(normalizedSearch)) ||
                                    (childData.cashbackAmount && normalize(childData.cashbackAmount).includes(normalizedSearch)) ||
                                    (childData.minumumPurchaseAmount && normalize(childData.minumumPurchaseAmount).includes(normalizedSearch)) ||
                                    (typeof childData.redeemCount !== 'undefined' && normalize(childData.redeemCount).includes(normalizedSearch))
                                ) {
                                    filteredRecords.push(childData);
                                }
                            } else {
                                filteredRecords.push(childData);
                            }
                        }));
                        filteredRecords.sort((a, b) => {
                            let aValue, bValue;

                            const parseCurrency = (val) => {
                                if (!val) return 0;
                                return parseFloat(val.toString().replace(/[^0-9.]/g, '')) || 0;
                            };

                            if (orderByField === 'cashbackAmount') {
                                const parseCashback = (val) => {
                                    if (!val) return 0;
                                    val = val.toString().trim();
                                    if (val.includes('%')) {
                                        return parseFloat(val.replace('%', ''));
                                    } else {
                                        return parseCurrency(val);
                                    }
                                };
                                aValue = parseCashback(a.cashbackAmount);
                                bValue = parseCashback(b.cashbackAmount);
                            } else if (orderByField === 'minumumPurchaseAmount') {
                                aValue = parseCurrency(a.minumumPurchaseAmount);
                                bValue = parseCurrency(b.minumumPurchaseAmount);
                            } else {
                                aValue = a[orderByField] ? a[orderByField].toString().toLowerCase() : '';
                                bValue = b[orderByField] ? b[orderByField].toString().toLowerCase() : '';
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
                order: (checkDeletePermission) ? [
                    [1, 'asc']
                ] : [
                    [0, 'asc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: (checkDeletePermission) ? [0,6, 7, 8] : [5, 6, 7]
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
            var route1 = '{{ route('cashback.edit', ':id') }}';
            route1 = route1.replace(':id', id);
            if (checkDeletePermission) {
                html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' +
                    id + '"><label class="col-3 control-label"\n' +
                    'for="is_open_' + id + '" ></label></td>');
            }
            html.push('<a href="' + route1 + '">' + val.title + '</a>');
            html.push(val.cashbackType);
            if (val.cashbackType && val.cashbackType === 'Percent') {
                html.push(val.cashbackAmount + '%');
            } else {
                if (currencyAtRight) {
                    html.push(parseFloat(val.cashbackAmount).toFixed(decimal_degits) + "" + currentCurrency);
                } else {
                    html.push(currentCurrency + "" + parseFloat(val.cashbackAmount).toFixed(decimal_degits));
                }

            }
            if (currencyAtRight) {
                html.push(parseFloat(val.minumumPurchaseAmount).toFixed(decimal_degits) + "" + currentCurrency)
            } else {
                html.push(currentCurrency + "" + parseFloat(val.minumumPurchaseAmount).toFixed(decimal_degits))
            }
            var route2 = '{{ route('cashback.redeem', ':id') }}';
            route2 = route2.replace(':id', id);
            html.push('<a href="' + route2 + '">' + val.redeemCount + '</a>');
            if (val.startDate) {
                const startDate = new Date(val.startDate.seconds * 1000);
                const options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                };
                formattedStartDate = startDate.toLocaleDateString('en-GB', options).replace(/ /g, ' ');
            }
            if (val.endDate) {
                const endDate = new Date(val.endDate.seconds * 1000);
                const options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                };
                formattedEndDate = endDate.toLocaleDateString('en-GB', options).replace(/ /g, ' ');
            }
            html.push(formattedStartDate + ' - ' + formattedEndDate);
            if (val.isEnabled) {
                html.push('<label class="switch"><input type="checkbox" checked id="' + val.id + '" name="isActive"><span class="slider round"></span></label>');
            } else {
                html.push('<label class="switch"><input type="checkbox" id="' + val.id + '" name="isActive"><span class="slider round"></span></label>')
            }
            var actionHtml = '';
            actionHtml = actionHtml + '<span class="action-btn"><a href="' + route1 + '"><i class="mdi mdi-lead-pencil" title="Edit"></i></a>';
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
            database.collection('cashback').doc(id).delete().then(function(result) {
                window.location.href = '{{ route('cashback.index') }}';
            });
        });
        $(document).on("click", "input[name='isActive']", function(e) {
            jQuery("#data-table_processing").show();
            var ischeck = $(this).is(':checked');
            var id = this.id;
            if (ischeck) {
                database.collection('cashback').doc(id).update({
                    'isEnabled': true
                }).then(function(result) {
                    jQuery("#data-table_processing").hide();
                });
            } else {
                database.collection('cashback').doc(id).update({
                    'isEnabled': false
                }).then(function(result) {
                    jQuery("#data-table_processing").hide();
                });
            }
        });
        async function getRedeemCount(cashbackId) {
            var count = 0;
            var snapshots = await database.collection('cashback_redeem').where('cashbackId', '==', cashbackId).get();
            count = snapshots.size;
            return count;
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
                        database.collection('cashback').doc(dataId).delete().then(function(result) {
                            window.location.href = '{{ route('cashback.index') }}';
                        });

                    });
                }
            } else {
                alert("{{ trans('lang.select_delete_alert') }}");
            }
        });
    </script>
@endsection
