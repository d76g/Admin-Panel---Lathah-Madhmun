@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.cashback_create') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>

                    <li class="breadcrumb-item"><a href="{!! route('cashback.index') !!}">{{ trans('lang.cashback_plural') }}</a>
                    </li>

                    <li class="breadcrumb-item active">{{ trans('lang.cashback_create') }}</li>
                </ol>
            </div>
            <div>
                <div class="card-body">
                    <div class="error_top" style="display:none"></div>
                    <div class="row restaurant_payout_create">
                        <div class="restaurant_payout_create-inner">
                            <fieldset>
                                <legend>{{ trans('lang.cashback_create') }}</legend>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.title') }}</label>
                                    <div class="col-7">
                                        <input type="text" type="text" class="form-control cashback_title">
                                        <div class="form-text text-muted">{{ trans('lang.title_help') }}</div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.select_customer') }}</label>
                                    <div class="col-7">
                                        <div class="select2-container-full">
                                            <select id="customer" multiple class="form-control mt-3" required>
                                                <option value="all">{{ trans('lang.all') }}</option>
                                            </select>
                                        </div>
                                        <div class="form-text text-muted">
                                            {{ trans('lang.select_customer') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.select_payment_type') }}</label>
                                    <div class="col-7">
                                        <div class="select2-container-full">
                                            <select id="payment_type" multiple class="form-control mt-3" required>
                                            </select>
                                        </div>
                                        <div class="form-text text-muted">
                                            {{ trans('lang.select_payment_type') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.cashback_type') }}</label>
                                    <div class="col-7">
                                        <select class="form-control cashback_type" id="cashback_type">
                                            <option value="Percent">{{ trans('lang.coupon_percent') }}</option>
                                            <option value="Fixed">{{ trans('lang.coupon_fixed') }}</option>
                                        </select>
                                        <div class="form-text text-muted">{{ trans('lang.cashback_type_help') }}</div>
                                    </div>
                                </div>

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.cashback_amount') }} <span id="cashback_type_label">(%)</span>
                                        <i class="text-dark fs-12 fa-solid fa fa-info" data-toggle="tooltip" title="{{ trans('lang.cashback_amount_tooltip') }}" aria-describedby="tippy-3"></i>
                                    </label>
                                    <div class="col-7">
                                        <input type="number" type="text" class="form-control cashback_amount">
                                        <div class="form-text text-muted">{{ trans('lang.cashback_amount_help') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.minimum_purchase') }} <span class="currentCurrency"></span></label>
                                    <div class="col-7">
                                        <input type="number" type="text" class="form-control minimum_purchase">
                                        <div class="form-text text-muted">{{ trans('lang.minimum_purchase_amount_help') }}</div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.maximum_discount') }} <span class="currentCurrency"></span></label>
                                    <div class="col-7">
                                        <input type="number" type="text" class="form-control maximum_discount">
                                        <div class="form-text text-muted">{{ trans('lang.maximum_discount_amount_help') }}</div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.start_date') }}</label>
                                    <div class="col-7">
                                        <div class='input-group date' id='datetimepicker1'>
                                            <input type='text' id="start_date" class="form-control date_picker start_date_picker input-group-addon" />
                                            <span class=""></span>
                                        </div>

                                        <div class="form-text text-muted">
                                            {{ trans('lang.select_start_date') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.end_date') }}</label>
                                    <div class="col-7">
                                        <div class='input-group date' id='datetimepicker2'>
                                            <input type='text' id="end_date" class="form-control date_picker end_date_picker input-group-addon" />
                                            <span class=""></span>
                                        </div>
                                        <div class="form-text text-muted">
                                            {{ trans('lang.select_end_date') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.limit_for_same_user') }}</label>
                                    <div class="col-7">
                                        <input type="number" type="text" class="form-control limit_user">
                                        <div class="form-text text-muted">{{ trans('lang.limit_for_same_user_help') }}</div>
                                    </div>
                                </div>

                                <div class="form-group row width-100">
                                    <div class="form-check">
                                        <input type="checkbox" class="cashback_enabled" id="cashback_enabled">
                                        <label class="col-3 control-label" for="cashback_enabled">{{ trans('lang.cashback_enabled') }}</label>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary save-form-btn"><i class="fa fa-save"></i> {{ trans('lang.save') }}
                    </button>
                    <a href="{!! route('cashback.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <link href="{{ asset('css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <script>
        var database = firebase.firestore();
        const paymentMethodsMap = {
            stripe: {
                label: '{{ trans('lang.app_setting_stripe') }}',
                ref: 'stripeSettings'
            },
            cod: {
                label: '{{ trans('lang.app_setting_cod_short') }}',
                ref: 'CODSettings'
            },
            razorpay: {
                label: '{{ trans('lang.app_setting_razorpay') }}',
                ref: 'razorpaySettings'
            },
            paypal: {
                label: '{{ trans('lang.app_setting_paypal') }}',
                ref: 'paypalSettings'
            },
            paytm: {
                label: '{{ trans('lang.app_setting_paytm') }}',
                ref: 'PaytmSettings'
            },
            wallet: {
                label: '{{ trans('lang.app_setting_wallet') }}',
                ref: 'walletSettings'
            },
            payfast: {
                label: '{{ trans('lang.payfast') }}',
                ref: 'payFastSettings'
            },
            paystack: {
                label: '{{ trans('lang.app_setting_paystack_lable') }}',
                ref: 'payStack'
            },
            flutterwave: {
                label: '{{ trans('lang.flutterWave') }}',
                ref: 'flutterWave'
            },
            mercadopago: {
                label: '{{ trans('lang.mercadopago') }}',
                ref: 'MercadoPago'
            },
            xendit: {
                label: '{{ trans('lang.app_setting_xendit') }}',
                ref: 'xendit_settings'
            },
            orangepay: {
                label: '{{ trans('lang.app_setting_orangepay') }}',
                ref: 'orange_money_settings'
            },
            midtrans: {
                label: '{{ trans('lang.app_setting_midtrans') }}',
                ref: 'midtrans_settings'
            },
        };

        const paymentTypeSelect = document.getElementById('payment_type');

        // Add "All" option
        const allOption = document.createElement('option');
        allOption.value = 'all';
        allOption.textContent = '{{ trans('lang.all') }}';
        paymentTypeSelect.appendChild(allOption);

        Object.entries(paymentMethodsMap).forEach(([key, {
            label,
            ref
        }]) => {
            database.collection('settings').doc(ref).get().then(doc => {
                if (doc.exists) {
                    const data = doc.data();
                    const isEnabled = data?.isEnabled === true || data?.enable === true || data?.isEnable === true; // check both
                    if (isEnabled) {
                        const option = document.createElement('option');
                        option.value = key;
                        option.textContent = label;
                        paymentTypeSelect.appendChild(option);
                    }
                }
            });
        });
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
            $('.currentCurrency').html('(' + currentCurrency + ')');
        });
        $(document).ready(function() {
            jQuery("#data-table_processing").show();
            $('#cashback_type').on('change', function() {
                var cashbackType = $('#cashback_type').val();
                if (cashbackType == 'Fixed') {
                    $('#cashback_type_label').html('(' + currentCurrency + ')');
                } else {
                    $('#cashback_type_label').html('(%)');
                }
            })
            database.collection('users').where('role', '==', 'customer').orderBy('firstName', 'asc').get().then(async function(snapshots) {
                snapshots.docs.forEach((listval) => {
                    var data = listval.data();
                    $('#customer').append($("<option></option>")
                        .attr("value", data.id)
                        .text(data.firstName + ' ' + data.lastName));
                })
            });

            $(function() {
                $('#datetimepicker1 .date_picker').datepicker({
                    dateFormat: 'mm/dd/yyyy',
                    startDate: new Date(),
                });
            });
            $(function() {
                $('#datetimepicker2 .date_picker').datepicker({
                    dateFormat: 'mm/dd/yyyy',
                    startDate: new Date(),
                });
            });
            $('#customer').select2({
                placeholder: "{{ trans('lang.select_customer') }}",
                allowClear: true,
                width: '100%',
                dropdownAutoWidth: true
            });
            $('#payment_type').select2({
                placeholder: "{{ trans('lang.select_payment_type') }}",
                allowClear: true,
                width: '100%',
                dropdownAutoWidth: true
            });
            let updatingCustomerSelect = false;

            $('#customer').on('change', function() {
                if (updatingCustomerSelect) return;

                updatingCustomerSelect = true;
                let selected = $(this).val() || [];

                if (selected.includes("all")) {
                    $(this).val(["all"]).trigger('change.select2');
                } else {
                    const withoutAll = selected.filter(value => value !== "all");
                    $(this).val(withoutAll).trigger('change.select2');
                }

                updatingCustomerSelect = false;
            });
            let updatingPaymentSelect = false;

            $('#payment_type').on('change', function() {
                if (updatingPaymentSelect) return;

                updatingPaymentSelect = true;
                let selected = $(this).val() || [];

                if (selected.includes("all")) {
                    $(this).val(["all"]).trigger('change.select2');
                } else {
                    const withoutAll = selected.filter(value => value !== "all");
                    $(this).val(withoutAll).trigger('change.select2');
                }

                updatingPaymentSelect = false;
            });


            $(".save-form-btn").click(function() {
                var title = $(".cashback_title").val();
                var customers = $("#customer").val();
                var paymentTypes = $("#payment_type").val();
                var cashbackType = $('#cashback_type').val();
                var cashbackAmount = $('.cashback_amount').val();
                var minumumPurchaseAmount = $('.minimum_purchase').val();
                var maximumDiscountAmount = $('.maximum_discount').val();
                var startDate = new Date($(".start_date_picker").val());
                var startAt = new Date(startDate.setHours(0, 0, 0, 0));
                var endDate = new Date($(".end_date_picker").val());
                var endAt = new Date(endDate.setHours(23, 59, 59, 999));
                var isEnabled = $(".cashback_enabled").is(":checked");
                var limitForSameUser = $('.limit_user').val();
                var allCustomer = false;
                var allPayment = false;


                if (title == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.title_help') }}</p>");
                    window.scrollTo(0, 0);
                    return;
                } else if (customers == '' || customers.length == 0) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.select_customer') }}</p>");
                    window.scrollTo(0, 0);
                    return;
                } else if (paymentTypes == '' || paymentTypes.length == 0) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.select_payment_type') }}</p>");
                    window.scrollTo(0, 0);
                    return;
                } else if (cashbackType == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.cashback_type_help') }}</p>");
                    window.scrollTo(0, 0);
                    return;
                } else if (cashbackAmount == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.cashback_amount_help') }}</p>");
                    window.scrollTo(0, 0);
                    return;
                } else if (cashbackType === 'Percent' && (cashbackAmount < 0 || cashbackAmount > 100)) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.percentage_limit_error') }}</p>");
                    window.scrollTo(0, 0);
                    return;
                } else if (minumumPurchaseAmount == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.minimum_purchase_amount_help') }}</p>");
                    window.scrollTo(0, 0);
                    return;
                } else if (maximumDiscountAmount == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.maximum_discount_amount_help') }}</p>");
                    window.scrollTo(0, 0);
                    return;
                } else if (startAt == '' || startAt == null || startAt == undefined || startAt == 'Invalid Date') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.select_start_date') }}</p>");
                    window.scrollTo(0, 0);
                    return;
                } else if (endAt == '' || endAt == null || endAt == undefined || endAt == 'Invalid Date') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.select_end_date') }}</p>");
                    window.scrollTo(0, 0);
                    return;
                } else if(limitForSameUser == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.limit_for_same_user_help') }}</p>");
                    window.scrollTo(0, 0);
                    return;
                } else {
                    if (customers.includes('all')) {
                        allCustomer = true;
                        customers = null;
                    }
                    if (paymentTypes.includes('all')) {
                        allPayment = true;
                        paymentTypes = null;
                    }

                    jQuery("#data-table_processing").show();
                    var id = database.collection("tmp").doc().id;
              
                    database.collection('cashback').doc(id).set({
                        'title': title,
                        'customerIds': customers,
                        'allCustomer': allCustomer,
                        'paymentMethods':paymentTypes,
                        'allPayment':allPayment,
                        'cashbackType':cashbackType,
                        'cashbackAmount':cashbackAmount,
                        'minumumPurchaseAmount':minumumPurchaseAmount,
                        'maximumDiscount':maximumDiscountAmount,
                        'startDate':startAt,
                        'endDate':endAt,
                        'redeemLimit':limitForSameUser,
                        'isEnabled': isEnabled,
                        'id': id,

                    }).then(function(result) {

                        jQuery("#data-table_processing").hide();
                        window.location.href = '{{ route('cashback.index') }}';

                    })

                }

            });
            jQuery("#data-table_processing").hide();
        });
    </script>
@endsection
