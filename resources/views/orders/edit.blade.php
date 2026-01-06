@extends('layouts.app')
@section('content')
    <?php
    $countries = file_get_contents(public_path('countriesdata.json'));
    $countries = json_decode($countries);
    $countries = (array) $countries;
    $newcountries = [];
    $newcountriesjs = [];
    foreach ($countries as $keycountry => $valuecountry) {
        $newcountries[$valuecountry->phoneCode] = $valuecountry;
        $newcountriesjs[$valuecountry->phoneCode] = $valuecountry->code;
    }
    ?>
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.order_plural') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <?php if (isset($_GET['eid']) && $_GET['eid'] != '') { ?>
                    <li class="breadcrumb-item"><a href="{{ route('stores.orders', $_GET['eid']) }}">{{ trans('lang.order_plural') }}</a>
                    </li>
                    <?php } else { ?>
                    <li class="breadcrumb-item"><a href="{!! route('orders') !!}">{{ trans('lang.order_plural') }}</a>
                    </li>
                    <?php } ?>
                    <li class="breadcrumb-item">{{ trans('lang.order_edit') }}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card-body pb-5 p-0">
                <div class="text-right print-btn pb-3">
                    <a href="{{ route('vendors.orderprint', $id) }}">
                        <button type="button" class="fa fa-print"></button>
                    </a>
                </div>
                <div class="order_detail" id="order_detail">
                    <div class="order_detail-top">
                        <div class="row">
                            <div class="order_edit-genrl col-lg-7 col-md-12">
                                <div class="card">
                                    <div class="card-header bg-white">
                                        <h3>{{ trans('lang.general_details') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="order_detail-top-box">
                                            <div class="form-group row widt-100 gendetail-col">
                                                <label class="col-12 control-label"><strong>{{ trans('lang.date_created') }}
                                                        : </strong><span id="createdAt"></span></label>
                                            </div>
                                            <div class="form-group row widt-100 gendetail-col payment_method">
                                                <label class="col-12 control-label"><strong>{{ trans('lang.payment_methods') }}
                                                        : </strong><span id="payment_method"></span></label>
                                            </div>
                                            <div class="form-group row widt-100 gendetail-col">
                                                <label class="col-12 control-label"><strong>{{ trans('lang.order_type') }}:</strong>
                                                    <span id="order_type"></span></label>
                                            </div>
                                            <div class="form-group row widt-100 gendetail-col schedule_date">
                                            </div>
                                            <div class="form-group row widt-100 gendetail-col prepare_time">
                                            </div>
                                            <div class="form-group row width-100 ">
                                                <label class="col-3 control-label">{{ trans('lang.status') }}:</label>
                                                <div class="col-7">
                                                    <select id="order_status" class="form-control">
                                                        <option value="Order Placed" id="order_placed">
                                                            {{ trans('lang.order_placed') }}
                                                        </option>
                                                        <option value="Order Accepted" id="order_accepted">
                                                            {{ trans('lang.order_accepted') }}
                                                        </option>
                                                        <option value="Order Rejected" id="order_rejected">
                                                            {{ trans('lang.order_rejected') }}
                                                        </option>
                                                        <option value="Driver Pending" id="driver_pending">
                                                            {{ trans('lang.driver_pending') }}
                                                        </option>
                                                        <option value="Driver Rejected" id="driver_rejected">
                                                            {{ trans('lang.driver_rejected') }}
                                                        </option>
                                                        <option value="Order Shipped" id="order_shipped">
                                                            {{ trans('lang.order_shipped') }}
                                                        </option>
                                                        <option value="In Transit" id="in_transit">
                                                            {{ trans('lang.in_transit') }}
                                                        </option>
                                                        <option value="Order Completed" id="order_completed">
                                                            {{ trans('lang.order_completed') }}
                                                        </option>
                                                        <option value="Order Cancelled" id="order_canceled">
                                                            {{ trans('lang.order_canceled') }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row width-100">
                                                <label class="col-3 control-label"></label>
                                                <div class="col-7 text-right">
                                                    <button type="button" class="btn btn-primary edit-form-btn"><i class="fa fa-save"></i> {{ trans('lang.update') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="order-items-list mt-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <table cellpadding="0" cellspacing="0" class="table table-striped table-valign-middle">
                                                <thead>
                                                    <tr>
                                                        <th>{{ trans('lang.item') }}</th>
                                                        <th class="text-center">{{ trans('lang.price') }}</th>
                                                        <th>{{ trans('lang.qty') }}</th>
                                                        <th>{{ trans('lang.extras') }}</th>
                                                        <th>{{ trans('lang.total') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="order_products">
                                                </tbody>
                                            </table>
                                            <div class="order-data-row order-totals-items">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="table-responsive bk-summary-table">
                                                            <table class="order-totals">
                                                                <tbody id="order_products_total">
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
                            <div class="order_addre-edit col-lg-5 col-md-12">
                                <div class="card">
                                    <div class="card-header bg-white">
                                        <h3>{{ trans('lang.billing_details') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="address order_detail-top-box">
                                            <p>
                                                <strong>{{ trans('lang.name') }}: </strong><span id="billing_name"></span>
                                            </p>
                                            <p>
                                                <strong>{{ trans('lang.address') }}: </strong>
                                                <span id="billing_line1"></span>
                                                <span id="billing_line2"></span>
                                                <span id="billing_country"></span>
                                            </p>
                                            <p><strong>{{ trans('lang.email_address') }}:</strong>
                                                <span id="billing_email"></span>
                                            </p>
                                            <p><strong>{{ trans('lang.phone') }}:</strong>
                                                <span id="billing_phone"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="order_addre-edit driver_details_hide">
                                    <div class="card mt-4">
                                        <div class="card-header bg-white">
                                            <h3>{{ trans('lang.driver_detail') }}</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="address order_detail-top-box">
                                                <p>
                                                    <strong>{{ trans('lang.name') }}: </strong><span id="driver_firstName"></span> <span id="driver_lastName"></span><br>
                                                </p>
                                                <p><strong>{{ trans('lang.email_address') }}:</strong>
                                                    <span id="driver_email"></span>
                                                </p>
                                                <p><strong>{{ trans('lang.phone') }}:</strong>
                                                    <span id="driver_phone"></span>
                                                </p>
                                                <p><strong>{{ trans('lang.zone') }}:</strong>
                                                    <span id="zone_name"></span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="resturant-detail mt-4">
                                    <div class="card">
                                        <div class="card-header bg-white">
                                            <h4 class="card-header-title">{{ trans('lang.store') }}</h4>
                                        </div>
                                        <div class="card-body">
                                            <a href="#" class="row redirecttopage align-items-center" id="resturant-view">
                                                <div class="col-md-3">
                                                    <img src="" class="resturant-img rounded-circle" alt="vendor" width="70px" height="70px">
                                                </div>
                                                <div class="col-md-9">
                                                    <h4 class="vendor-title"></h4>
                                                </div>
                                            </a>
                                            <h5 class="contact-info">{{ trans('lang.contact_info') }}:</h5>
                                            <p><strong>{{ trans('lang.phone') }}:</strong>
                                                <span id="vendor_phone"></span>
                                            </p>
                                            <p><strong>{{ trans('lang.address') }}:</strong>
                                                <span id="vendor_address"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="order_detail-review mt-4">
                                    <div class="rental-review">
                                        <div class="card">
                                            <div class="card-header bg-white box-header">
                                                <h3>{{ trans('lang.customer_reviews') }}</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="review-inner">
                                                    <div id="customers_rating_and_review">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                   <div class="order_detail_cashback mt-4 d-none">
                                    <div class="cashback-detail">
                                        <div class="card">
                                            <div class="card-header bg-white box-header">
                                                <h3>{{ trans('lang.cashback_details') }}</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="cashback-detail-inner">
                                                    <p><strong>{{ trans('lang.title') }}:</strong>
                                                        <span id="cashback_title"></span>
                                                    </p>
                                                    <p><strong>{{ trans('lang.cashback_amount') }}:</strong>
                                                        <span id="cashback_amount"></span>
                                                    </p>
                                                    <p>
                                                        <strong><span id="cashback_amount_description" style="color:green"></span></strong>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn">
                <button type="button" class="btn btn-primary edit-form-btn"><i class="fa fa-save"></i>
                    {{ trans('lang.save') }}
                </button>
                <?php if (isset($_GET['eid']) && $_GET['eid'] != '') { ?>
                <a href="{{ route('stores.orders', $_GET['eid']) }}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel') }}</a>
                <?php } else { ?>
                <a href="{!! route('orders') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel') }}
                </a>
                <?php } ?>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    <div class="modal fade" id="addPreparationTimeModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered location_modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title locationModalTitle">{{ trans('lang.add_preparation_time') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="">
                        <div class="form-row">
                            <div class="form-group row">
                                <div class="form-group row width-100">
                                    <label class="col-12 control-label">{{ trans('lang.time') }}</label>
                                    <div class="col-12">
                                        <input type="text" name="prepare_time" class="form-control time-picker" id="prepare_time">
                                        <div id="add_prepare_time_error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="add-prepare-time-btn">{{ trans('submit') }}
                        </button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                            {{ trans('close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="assignDriverModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered location_modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title locationModalTitle">{{ trans('lang.assign_order') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="">
                        <div class="form-row">
                            <div class="form-group row">
                                <div class="form-group row width-100">
                                    <div class="col-12">
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#addDeliverymanModal" class="add-deliveryman btn btn-success"><i class="fa fa-plus"></i>{{ trans('lang.add_delivery_man') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="form-group row width-100" id="driver_list_div">
                                    <div class="col-12">
                                        <div class="select2-container-full">
                                            <label>{{ trans('lang.select_deliveryman') }}</label>
                                            <select name="deliveryman" class="form-control" id="deliveryman_list">
                                            </select>
                                        </div>
                                        <div id="select_deliveryman" style="color:red"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="order-assign-btn">{{ trans('lang.assign') }}
                        </button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                            {{ trans('close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addDeliverymanModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered location_modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title locationModalTitle">{{ trans('lang.deliveryman_details') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="">
                        <div class="form-row">
                            <div class="error_top"></div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.first_name') }}</label>
                                <div class="col-12">
                                    <input type="text" class="form-control user_first_name" required>
                                    <div id="firstname_err" class="text-danger err"></div>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.last_name') }}</label>
                                <div class="col-12">
                                    <input type="text" class="form-control user_last_name">
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.email') }}</label>
                                <div class="col-12">
                                    <input type="email" class="form-control user_email" required>
                                    <div id="email_err" class="text-danger err"></div>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.password') }}</label>
                                <div class="col-12">
                                    <input type="password" class="form-control password" required>
                                    <div id="password_err" class="text-danger err"></div>
                                </div>
                            </div>
                            <div class="form-group form-material">
                                <label class="col-3 control-label">{{ trans('lang.user_phone') }}</label>
                                <div class="col-12">
                                    <div class="phone-box position-relative" id="phone-box">
                                        <select name="country" id="country_selector">
                                            <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                                            <?php $selected = ''; ?>
                                            <option <?php echo $selected; ?> code="<?php echo $valuecy->code; ?>" value="<?php echo $keycy; ?>">
                                                +<?php echo $valuecy->phoneCode; ?> {{ $valuecy->countryName }}</option>
                                            <?php } ?>
                                        </select>
                                        <input class="form-control user_phone" placeholder="Phone" id="phone" type="phone" name="phone" value="{{ old('phone') }}" required autocomplete="phone" autofocus>
                                        <div id="mobilenumber_err" class="text-danger err"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{ trans('lang.zone') }}<span class="required-field"></span></label>
                                <div class="col-12">
                                    <select id='zone' class="form-control">
                                        <option value="">{{ trans('lang.select_zone') }}</option>
                                    </select>
                                    <div id="zone_err" class="text-danger err"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="add-deliveryman-btn">{{ trans('submit') }}</a>
                        </button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">{{ trans('close') }}</a>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('style')
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.js"></script>
    <script>
        var adminCommission = 0;
        var id_rendom = "<?php echo uniqid(); ?>";
        var id = "<?php echo $id; ?>";
        var driverId = '';
        var subscriptionTotalOrders = -1;
        var old_order_status = '';
        var payment_shared = false;
        var deliveryChargeVal = 0;
        var deliveryCharge = 0;
        var fcmToken = '';
        var manfcmTokenVendor = '';
        var fcmTokenVendor = '';
        var fcmTokenDriver = '';
        var customername = '';
        var vendorname = '';
        var tip_amount_val = 0;
        var tip_amount = 0;
        var page_size = 5;
        var database = firebase.firestore();
        var ref = database.collection('restaurant_orders').where("id", "==", id);
        var ref_review_attributes = database.collection('review_attributes');
        var selected_review_attributes = '';
        var refUserReview = database.collection('foods_review').where('orderid', '==', id);
        var append_procucts_list = '';
        var append_procucts_total = '';
        var total_price = 0;
        var currentCurrency = '';
        var currencyAtRight = false;
        var refCurrency = database.collection('currencies').where('isActive', '==', true);
        var orderPreviousStatus = '';
        var orderTakeAwayOption = false;
        var manname = '';
        var reviewAttributes = {};
        var decimal_degits = 0;
        var orderCustomerId = '';
        var orderPaytableAmount = 0;
        var vendorAuthor = '';
        var orderAcceptedSubject = '';
        var orderAcceptedMsg = '';
        var orderRejectedSubject = '';
        var orderRejectedMsg = '';
        var orderCompletedSubject = '';
        var orderCompletedMsg = '';
        var takeAwayOrderCompletedSubject = '';
        var takeAwayOrderCompletedMsg = '';
        var driverAcceptedMsg = '';
        var driverAcceptedSubject = '';
        var selfDeliveryOrderAssignSubject = '';
        var selfDeliveryOrderAssignMsg = '';
        var selfDeliveryDriverCancelledSub = '';
        var selfDeliveryDriverCancelledMsg = '';
        var selfDeliveryCustomerCancelledSub = '';
        var selfDeliveryCustomerCancelledMsg = '';
        var basePrice = 0;
        var total_tax_amount = 0;
        var isSelfDeliveryGlobally = false;
        var isSelfDeliveryByVendor = false;
        var refGlobal = database.collection('settings').doc("globalSettings");
        refGlobal.get().then(async function(
            settingSnapshots) {
            if (settingSnapshots.data()) {
                var settingData = settingSnapshots.data();
                if (settingData.isSelfDelivery) {
                    isSelfDeliveryGlobally = true;
                }
            }
        })
        var singleOrderReceive = false;
        var refDriverNearBy = database.collection('settings').doc("DriverNearBy");
        refDriverNearBy.get().then(async function(snapshot) {
            var data = snapshot.data();
            if (data.singleOrderReceive) {
                singleOrderReceive = true;
            }
        })
        var scheduleOrderAcceptData = {};
        var scheduleOrderNotificationRef = database.collection('settings').doc("scheduleOrderNotification");
        scheduleOrderNotificationRef.get().then(async function(snapshot) {
            var data = snapshot.data();
            scheduleOrderAcceptData.notifyTime = data.notifyTime;
            scheduleOrderAcceptData.timeUnit = data.timeUnit;
        })
        var subscriptionModel = false;
        database.collection('settings').doc("restaurant").get().then(async function(snapshots) {
            var subscriptionSetting = snapshots.data();
            if (subscriptionSetting.subscription_model == true) {
                subscriptionModel = true;
            }
        });
        database.collection('dynamic_notification').get().then(async function(snapshot) {
            if (snapshot.docs.length > 0) {
                snapshot.docs.map(async (listval) => {
                    val = listval.data();
                    if (val.type == "restaurant_rejected") {
                        orderRejectedSubject = val.subject;
                        orderRejectedMsg = val.message;
                    } else if (val.type == "driver_completed") {
                        orderCompletedSubject = val.subject;
                        orderCompletedMsg = val.message;
                    } else if (val.type == "takeaway_completed") {
                        takeAwayOrderCompletedSubject = val.subject;
                        takeAwayOrderCompletedMsg = val.message;
                    } else if (val.type == "restaurant_accepted") {
                        orderAcceptedSubject = val.subject;
                        orderAcceptedMsg = val.message;
                    } else if (val.type == "driver_accepted") {
                        driverAcceptedSubject = val.subject;
                        driverAcceptedMsg = val.message;
                    } else if (val.type == "assign_order") {
                        selfDeliveryOrderAssignSubject = val.subject;
                        selfDeliveryOrderAssignMsg = val.message;
                    } else if (val.type == "driver_cancelled") {
                        selfDeliveryDriverCancelledSub = val.subject;
                        selfDeliveryDriverCancelledMsg = val.message;
                    } else if (val.type == "restaurant_cancelled") {
                        selfDeliveryCustomerCancelledSub = val.subject;
                        selfDeliveryCustomerCancelledMsg = val.message;
                    }
                });
            }
        });
        refCurrency.get().then(async function(snapshots) {
            var currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;
            if (currencyData.decimal_degits) {
                decimal_degits = currencyData.decimal_degits;
            }
        });
        database.collection('zone').where('publish', '==', true).orderBy('name', 'asc').get().then(async function(snapshots) {
            snapshots.docs.forEach((listval) => {
                var data = listval.data();
                $('#zone').append($("<option></option>")
                    .attr("value", data.id)
                    .text(data.name));
            })
        });
        var geoFirestore = new GeoFirestore(database);
        var place_image = '';
        var ref_place = database.collection('settings').doc("placeHolderImage");
        ref_place.get().then(async function(snapshots) {
            var placeHolderImage = snapshots.data();
            place_image = placeHolderImage.image;
        });
        $(document).ready(function() {
            $('.time-picker').timepicker({
                timeFormat: "HH:mm",
                showMeridian: false,
                format24: true
            });
            $('.time-picker').timepicker().on('changeTime.timepicker', function(e) {
                var hours = e.time.hours,
                    min = e.time.minutes;
                if (hours < 10) {
                    $(e.currentTarget).val('0' + hours + ':' + min);
                }
            });
            var alovelaceDocumentRef = database.collection('restaurant_orders').doc();
            if (alovelaceDocumentRef.id) {
                id_rendom = alovelaceDocumentRef.id;
            }
            $(document.body).on('click', '.redirecttopage', function() {
                var url = $(this).attr('data-url');
                window.location.href = url;
            });
            jQuery("#data-table_processing").show();
            ref.get().then(async function(snapshots) {
                vendorOrder = snapshots.docs[0].data();
                getUserReview(vendorOrder);
                
                var order = snapshots.docs[0].data();
                  if (order.hasOwnProperty('cashback') && order.cashback && order.cashback !== null) {
                    if (order.status !== 'Order Rejected' && order.status !== 'Order Cancelled' && order.status !== 'Driver Rejected') {
                        $('.order_detail_cashback').removeClass('d-none');
                        $('#cashback_title').text(order.cashback.title);
                        var amount = '';
                        if (currencyAtRight) {
                            $('#cashback_amount').text(parseFloat(order.cashback.cashbackValue).toFixed(decimal_degits) + "" + currentCurrency);
                            amount = parseFloat(order.cashback.cashbackValue).toFixed(decimal_degits) + "" + currentCurrency
                        } else {
                            $('#cashback_amount').text(currentCurrency + "" + parseFloat(order.cashback.cashbackValue).toFixed(decimal_degits));
                            amount = currentCurrency + "" +parseFloat(order.cashback.cashbackValue).toFixed(decimal_degits);
                        }
                        if (order.status === "Order Completed") {
                            $('#cashback_amount_description').text("{{ trans('lang.user_has_received_cashback_of') }} " + amount);
                        } else {
                            $('#cashback_amount_description').text("{{ trans('lang.user_will_receive_cashback_of') }} " + amount + " {{ trans('lang.after_order_completion') }}");
                        }
                        
                    }
                } else {
                    $('.order_detail_cashback').addClass('d-none');
                }
                await getDeliverymanList(order.vendorID);

                append_procucts_list = document.getElementById('order_products');
                append_procucts_list.innerHTML = '';
                append_procucts_total = document.getElementById('order_products_total');
                append_procucts_total.innerHTML = '';
                if (order.address.name) {
                    $("#billing_name").text(order.address.name);
                } else {
                    $("#billing_name").text(order.author.firstName + ' ' + order.author.lastName);
                }
                $("#trackng_number").text(id);
                var billingAddressstring = '';
                if (order.address.hasOwnProperty('address')) {
                    $("#billing_line1").text(order.address.address);
                }
                if (order.address.hasOwnProperty('locality')) {
                    billingAddressstring = billingAddressstring + order.address.locality;
                }
                if (order.address.hasOwnProperty('landmark')) {
                    billingAddressstring = billingAddressstring + " " + order.address.landmark;
                }
                $("#billing_line2").text(billingAddressstring);
                if (order.author.hasOwnProperty('phoneNumber')) {
                    $("#billing_phone").text(shortEditNumber(order.author.phoneNumber));
                } else {
                    $("#billing_phone").text("");
                }
                if (order.author.hasOwnProperty('email')) {
                    $("#billing_email").html('<a href="mailto:' + order.author.email + '">' +
                        shortEmail(order.author.email) + '</a>');
                } else {
                    $("#billing_email").html("");
                }
                if (order.createdAt) {
                    var date1 = order.createdAt.toDate().toDateString();
                    var date = new Date(date1);
                    var dd = String(date.getDate()).padStart(2, '0');
                    var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy = date.getFullYear();
                    var createdAt_val = yyyy + '-' + mm + '-' + dd;
                    var time = order.createdAt.toDate().toLocaleTimeString('en-US');
                    $('#createdAt').text(createdAt_val + ' ' + time);
                }
                var payment_method = '';
                if (order.payment_method) {
                    if (order.payment_method == "stripe") {
                        image = '{{ asset('images/stripe.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%">';
                    } else if (order.payment_method == "cod") {
                        image = '{{ asset('images/cashondelivery.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%">';
                    } else if (order.payment_method == "razorpay") {
                        image = '{{ asset('images/razorepay.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%">';
                    } else if (order.payment_method == "paypal") {
                        image = '{{ asset('images/paypal.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%">';
                    } else if (order.payment_method == "payfast") {
                        image = '{{ asset('images/payfast.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '" width="30%" height="30%">';
                    } else if (order.payment_method == "paystack") {
                        image = '{{ asset('images/paystack.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%">';
                    } else if (order.payment_method == "flutterwave") {
                        image = '{{ asset('images/flutter_wave.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%">';
                    } else if (order.payment_method == "mercado pago") {
                        image = '{{ asset('images/marcado_pago.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%">';
                    } else if (order.payment_method == "wallet") {
                        image = '{{ asset('images/gromart_wallet.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%" >';
                    } else if (order.payment_method == "paytm") {
                        image = '{{ asset('images/paytm.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%">';
                    } else if (order.payment_method == "cancelled order payment") {
                        image = '{{ asset('images/cancel_order.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%">';
                    } else if (order.payment_method == "refund amount") {
                        image = '{{ asset('images/refund_amount.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%">';
                    } else if (order.payment_method == "referral amount") {
                        image = '{{ asset('images/reffral_amount.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%">';
                    } else if (order.payment_method == "midtrans") {
                        image = '{{ asset('images/midtrans.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%">';
                    } else if (order.payment_method == "xendit") {
                        image = '{{ asset('images/xendit.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%">';
                    } else if (order.payment_method == "orangepay") {
                        image = '{{ asset('images/orangeMoney.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%">';
                    } else {
                        payment_method = order.payment_method;
                    }
                }
                $('#payment_method').html(payment_method);
                if (order.hasOwnProperty('takeAway') && order.takeAway) {
                    $('#driver_pending').hide();
                    $('#driver_rejected').hide();
                    $('#order_shipped').hide();
                    $('#in_transit').hide();
                    $('#order_type').text('{{ trans('lang.order_takeaway') }}');
                    orderTakeAwayOption = true;
                } else {
                    $('#driver_pending').hide();
                    $('#driver_rejected').hide();
                    $('#order_shipped').hide();
                    $('#order_placed').hide();
                    $('#in_transit').hide();
                    $('#order_completed').hide();
                    $('#order_type').text('{{ trans('lang.order_delivery') }}');
                }
                if ((order.driver != '' && order.driver != undefined) && (order.takeAway == false)) {
                    // $('#driver_carName').text(order.driver.carName);
                    // $('#driver_carNumber').text(order.driver.carNumber);
                    $('#driver_email').html('<a href="mailto:' + order.driver.email + '">' + shortEmail(
                        order.driver.email) + '</a>');
                    $('#driver_firstName').text(order.driver.firstName);
                    $('#driver_lastName').text(order.driver.lastName);
                    $('#driver_phone').text(shortEditNumber(order.driver.phoneNumber));
                    var zoneId = '';
                    if (order.driver.hasOwnProperty('zoneId') && order.driver.zoneId.length > 0) {
                        zoneId = order.driver.zoneId;
                    } else if (order.hasOwnProperty('driverID')) {
                        var driverSnapshot = await database.collection('users').doc(order.driverID)
                            .get();
                        var driverData = driverSnapshot.data();
                        zoneId = driverData.zoneId;
                    }
                    if (zoneId != '') {
                        database.collection('zone').doc(zoneId).get().then(async function(snapshots) {
                            let zone = snapshots.data();
                            $("#zone_name").text(zone.name);
                        });
                    }
                } else {
                    $('.order_edit-genrl').removeClass('col-md-7').addClass('col-md-7');
                    $('.order_addre-edit').removeClass('col-md-5').addClass('col-md-5');
                    $('.driver_details_hide').empty();
                }
                if (order.driverID != '' && order.driverID != undefined) {
                    driverId = order.driverID;
                }
                if (order.vendor && order.vendor.author != '' && order.vendor.author != undefined) {
                    vendorAuthor = order.vendor.author;
                }
                var scheduleTime = '';
                if (order.hasOwnProperty('scheduleTime') && order.scheduleTime != null && order
                    .scheduleTime != '') {
                    scheduleTime = order.scheduleTime;
                    var scheduleDate = scheduleTime.toDate().toDateString();
                    var time = order.scheduleTime.toDate().toLocaleTimeString('en-US');
                    var scheduleDate = new Date(scheduleDate);
                    var dd = String(scheduleDate.getDate()).padStart(2, '0');
                    var mm = String(scheduleDate.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy = scheduleDate.getFullYear();
                    var scheduleDate = yyyy + '-' + mm + '-' + dd;
                    var scheduleDateTime = scheduleDate + ' ' + time;
                    $('.schedule_date').append(
                        '<label class="col-12 control-label"><strong>{{ trans('lang.schedule_date_time') }}:</strong><span id=""> ' +
                        scheduleDateTime + '</span></label>')
                }
                if (order.hasOwnProperty('estimatedTimeToPrepare') && order.estimatedTimeToPrepare !=
                    null && order.estimatedTimeToPrepare != '') {
                    prepareTime = order.estimatedTimeToPrepare;
                    var [h, m] = prepareTime.split(":");
                    var hour = h;
                    if (h.charAt(0) == "0") {
                        hour = h.charAt(1);
                    }
                    time = (h == "00") ? m + " minutes" : hour + " hours" + m + " minutes";
                    $('.prepare_time').append(
                        '<label class="col-12 control-label "><strong>{{ trans('lang.prepare_time') }}:</strong><span id=""> ' +
                        time + '</span></label>')
                }
                fcmToken = order.author.fcmToken;
                vendorname = order.vendor.title;
                fcmTokenVendor = order.vendor.fcmToken;
                customername = order.author.firstName;
                vendorId = order.vendor.id;
                old_order_status = order.status;
                if (order.payment_shared != undefined) {
                    payment_shared = order.payment_shared;
                }
                var productsListHTML = buildHTMLProductsList(order.products);
                var productstotalHTML = await buildHTMLProductstotal(order);
                if (productsListHTML != '') {
                    append_procucts_list.innerHTML = productsListHTML;
                }
                if (productstotalHTML != '') {
                    append_procucts_total.innerHTML = productstotalHTML;
                }
                orderPreviousStatus = order.status;
                if (order.hasOwnProperty('payment_method')) {
                    orderPaymentMethod = order.payment_method;
                }
                $("#order_status option[value='" + order.status + "']").attr("selected", "selected");
                if (order.status == 'Order Placed') {
                    $('#order_canceled').hide();
                }
                if (order.status != 'Order Placed') {
                    $('#order_accepted').hide();
                    $('#order_rejected').hide();
                }
                if (order.status == "Order Rejected" || order.status == "Driver Rejected" || order.status == "Order Cancelled" || order.status == "Order Completed") {
                    $("#order_status").prop("disabled", true);
                }
                var price = 0;
                if (order.authorID) {
                    orderCustomerId = order.authorID;
                }
                if (order.vendorID) {
                    var vendor = database.collection('vendors').where("id", "==", order.vendorID);
                    vendor.get().then(async function(snapshotsnew) {
                        if (snapshotsnew.docs.length > 0) {
                            var vendordata = snapshotsnew.docs[0].data();
                            if (vendordata.hasOwnProperty('isSelfDelivery') && vendordata.isSelfDelivery) {
                                isSelfDeliveryByVendor = true;
                            }
                            if (subscriptionModel) {
                                if (vendordata.hasOwnProperty('subscriptionTotalOrders') && vendordata.subscriptionTotalOrders != null && vendordata.subscriptionTotalOrders != '') {
                                    subscriptionTotalOrders = vendordata.subscriptionTotalOrders;
                                }
                            }
                            if (vendordata.id) {
                                var route_view = '{{ route('stores.view', ':id') }}';
                                route_view = route_view.replace(':id', vendordata.id);
                                $('#resturant-view').attr('data-url', route_view);
                            }
                            if (vendordata.photo != "" && vendordata.photo != null) {
                                $('.resturant-img').attr('src', vendordata.photo);
                            } else {
                                $('.resturant-img').attr('src', place_image);
                            }
                            if (vendordata.title != "" && vendordata.title != null) {
                                $('.vendor-title').html(vendordata.title);
                            }
                            if (vendordata.phonenumber != "" && vendordata.phonenumber !=
                                null) {
                                $('#vendor_phone').text(shortEditNumber(vendordata
                                    .phonenumber));
                            } else {
                                $('#vendor_phone').text("");
                            }
                            if (vendordata.location != "" && vendordata.location != null) {
                                $('#vendor_address').text(vendordata.location);
                            }
                        } else {
                            $('.resturant-img').attr('src', place_image);
                            $('.vendor-title').html("{{ trans('lang.unknown') }}");
                        }
                        if (isSelfDeliveryByVendor && isSelfDeliveryGlobally && order.status == 'Order Placed' && !order.takeAway) {

                            var newOption = $('<option>', {
                                value: 'Order Accepted',
                                text: "{{ trans('lang.assign_delivery_man') }}"
                            });
                            $('#order_status option[value="Order Rejected"]').before(newOption);
                            $('#order_accepted').hide();

                        }
                    });
                    tip_amount = order.tip_amount;
                }
                jQuery("#data-table_processing").hide();
            })
            async function getDeliverymanList(vendorID) {

                database.collection('users').where('role', '==', 'driver').where('vendorID', '==', vendorID).where('isActive', '==', true).get().then(async function(snapshot) {
                    if (snapshot.docs.length > 0) {

                        snapshot.docs.forEach((listval) => {
                            var data = listval.data();

                            if (singleOrderReceive) {

                                let option = $("<option></option>")
                                    .attr("value", data.id)
                                    .attr("fcm", data.fcmToken);

                                if (data.hasOwnProperty('inProgressOrderID') &&
                                    data.inProgressOrderID !== null &&
                                    data.inProgressOrderID !== '' &&
                                    data.inProgressOrderID.length > 0) {

                                    option.prop("disabled", true)
                                        .text(data.firstName + ' ' + data.lastName + ' (Occupied)');
                                } else {

                                    option.text(data.firstName + ' ' + data.lastName);
                                }
                                $('#deliveryman_list').append(option);

                            } else {

                                $('#deliveryman_list').append($("<option></option>")
                                    .attr("value", data.id)
                                    .attr("fcm", data.fcmToken)
                                    .text(data.firstName + ' ' + data.lastName));
                            }

                        });
                        $('#deliveryman_list').select2();

                    }
                })

            }
            $('#deliveryman_list').on('select2:open', function() {
                setTimeout(function() {
                    $('.select2-results__option').each(function() {
                        let $this = $(this);
                        if ($this.text().includes('(Occupied)')) {
                            $this.addClass('occupied-option'); // Add custom class
                        }
                    });
                }, 0);
            });

            function getTwentyFourFormat(h, timeslot) {
                if (h < 10 && timeslot == "PM") {
                    h = parseInt(h) + 12;
                } else if (h < 10 && timeslot == "AM") {
                    h = '0' + h;
                }
                return h;
            }
            $('#order-assign-btn').click(function() {
                var deliveryman = $('#deliveryman_list').val();
                if (deliveryman == '') {
                    $('#select_deliveryman').html('{{ trans('lang.select_deliveryman') }}');
                    return false;
                }
                $('#assignDriverModal').hide();
                $('#addPreparationTimeModal').modal('show');
            });
            $('#add-prepare-time-btn').click(async function() {
                if (parseInt(subscriptionTotalOrders) == 0) {
                    alert('{{ trans('lang.can_not_accept_more_orders') }}');
                    return false;
                } else {
                    var preparationTime = $('#prepare_time').val();
                    if (preparationTime == '') {
                        $('#add_prepare_time_error').text('{{ trans('lang.add_prepare_time_error') }}');
                        return false;
                    }
                    var date = firebase.firestore.FieldValue.serverTimestamp();
                    if (isSelfDeliveryByVendor && isSelfDeliveryGlobally && !orderTakeAwayOption) {
                        var deliveryman = $('#deliveryman_list').val();

                        var orderRequestData = [];
                        var inProgressOrderID = [];
                        var driverData = '';
                        await database.collection('users').where('id', '==', deliveryman).get().then(async function(snapshot) {
                            if (snapshot.docs.length > 0) {
                                driverData = snapshot.docs[0].data();
                                fcmTokenDriver = driverData.fcmToken;
                                if (driverData.hasOwnProperty('orderRequestData') && driverData.orderRequestData != null && driverData.orderRequestData != '') {
                                    orderRequestData = driverData.orderRequestData;

                                }
                                if (driverData.hasOwnProperty('inProgressOrderID') && driverData.inProgressOrderID != null && driverData.inProgressOrderID != '') {
                                    inProgressOrderID = driverData.inProgressOrderID
                                }

                            }
                            orderRequestData.push(id);
                            inProgressOrderID.push(id);
                        })


                        await database.collection('users').doc(deliveryman).update({
                            'orderRequestData': orderRequestData,
                            'inProgressOrderID': inProgressOrderID
                        });

                        var updatedData = {
                            'status': "In Transit",
                            'estimatedTimeToPrepare': preparationTime,
                            'driverID': deliveryman,
                            'driver': driverData
                        }
                    } else {
                        var updatedData = {
                            'status': "Order Accepted",
                            'estimatedTimeToPrepare': preparationTime
                        }
                    }
                    database.collection('restaurant_orders').doc(id).update(updatedData).then(async function(result) {
                        var wId = database.collection('temp').doc().id;
                        database.collection('wallet').doc(wId).set({
                            'amount': parseFloat(basePrice),
                            'date': date,
                            'id': wId,
                            'isTopUp': true,
                            'order_id': "<?php echo $id; ?>",
                            'payment_method': 'Wallet',
                            'payment_status': 'success',
                            'transactionUser': 'vendor',
                            'note': 'Order Amount credited',
                            'user_id': vendorAuthor
                        }).then(async function(result) {
                            var vendorAmount = basePrice;
                            if (total_tax_amount != 0 || total_tax_amount != '') {
                                var wId = database.collection('temp').doc().id;
                                database.collection('wallet').doc(wId).set({
                                    'amount': parseFloat(total_tax_amount),
                                    'date': date,
                                    'id': wId,
                                    'isTopUp': true,
                                    'order_id': "<?php echo $id; ?>",
                                    'payment_method': 'tax',
                                    'payment_status': 'success',
                                    'transactionUser': 'vendor',
                                    'user_id': vendorAuthor,
                                    'note': 'Order Tax credited'
                                }).then(async function(result) {})
                            }
                            database.collection('users').where('id', '==', vendorAuthor)
                                .get().then(async function(snapshotsnew) {
                                    var vendordata = snapshotsnew.docs[0]
                                        .data();
                                    if (vendordata) {
                                        if (parseInt(subscriptionTotalOrders) != -1) {
                                            subscriptionTotalOrders = parseInt(subscriptionTotalOrders) - 1;
                                            await database.collection('vendors').doc(vendordata.vendorID).update({
                                                'subscriptionTotalOrders': subscriptionTotalOrders.toString()
                                            })
                                        }
                                        if (isNaN(vendordata.wallet_amount) ||
                                            vendordata.wallet_amount ==
                                            undefined) {
                                            vendorWallet = 0;
                                        } else {
                                            vendorWallet = parseFloat(vendordata
                                                .wallet_amount);
                                        }
                                        newVendorWallet = vendorWallet + vendorAmount + parseFloat(total_tax_amount);
                                        database.collection('users').doc(
                                            vendorAuthor).update({
                                            'wallet_amount': parseFloat(
                                                newVendorWallet)
                                        }).then(async function(result) {
                                            callAjax();
                                        })
                                    } else {
                                        callAjax();
                                    }
                                });
                        });
                    });
                }
            });
            async function callAjax() {
                if (isSelfDeliveryByVendor && isSelfDeliveryGlobally) {
                    await $.ajax({
                        type: 'POST',
                        url: "<?php echo route('order-status-notification'); ?>",
                        data: {
                            _token: '<?php echo csrf_token(); ?>',
                            'fcm': fcmTokenDriver,
                            'vendorname': manname,
                            'orderStatus': 'Order Accepted',
                            'subject': selfDeliveryOrderAssignSubject,
                            'message': selfDeliveryOrderAssignMsg
                        },

                    });

                }
                var subject = orderAcceptedSubject;
                var msg = orderAcceptedMsg;
                var noitfyStatus = 'Order Accepted';
                var sendToken = manfcmTokenVendor


                await $.ajax({
                    type: 'POST',
                    url: "<?php echo route('order-status-notification'); ?>",
                    data: {
                        _token: '<?php echo csrf_token(); ?>',
                        'fcm': sendToken,
                        'vendorname': manname,
                        'orderStatus': noitfyStatus,
                        'subject': subject,
                        'message': msg
                    },
                    success: function(data) {
                        window.location.href = '{{ route('orders') }}';
                    }
                });
            }
            $(".edit-form-btn").click(async function() {
                var clientName = $(".client_name").val();
                var orderStatus = $("#order_status").val();
                if (old_order_status != orderStatus) {
                    if (orderStatus == "Order Placed") {
                        manfcmTokenVendor = fcmTokenVendor;
                        manname = customername;
                    } else {
                        manfcmTokenVendor = fcmToken;
                        manname = vendorname;
                    }
                    if (orderStatus == "Order Accepted") {

                        ref.get().then(async function(snapshot) {
                            order = snapshot.docs[0].data();
                            id = order.id;
                            var scheduleTime = '';
                            if (order.hasOwnProperty('scheduleTime') && order.scheduleTime != null) {
                                const scheduleTime = order.scheduleTime.toDate();
                                const now = new Date();
                                var notifyTime = scheduleOrderAcceptData.notifyTime;
                                var timeUnit = scheduleOrderAcceptData.timeUnit;
                                let notifyBeforeMs = 0;
                                if (timeUnit === 'minute') {
                                    notifyBeforeMs = notifyTime * 60 * 1000;
                                } else if (timeUnit === 'hour') {
                                    notifyBeforeMs = notifyTime * 60 * 60 * 1000;
                                } else {
                                    notifyBeforeMs = notifyTime * 24 * 60 * 60 * 1000;
                                }
                                const windowStart = new Date(scheduleTime.getTime() - notifyBeforeMs);
                                const windowEnd = scheduleTime;
                                var endDate = order.scheduleTime.toDate().toDateString() + ' ' + order.scheduleTime.toDate().toLocaleTimeString('en-US');
                                var startDate = windowStart.toDateString() + ' ' + windowStart.toLocaleTimeString('en-US');
                                if (now >= windowStart && now <= windowEnd) {
                                    if (isSelfDeliveryGlobally && isSelfDeliveryByVendor && !orderTakeAwayOption) {
                                        $('#assignDriverModal').modal('show');
                                    } else {
                                        $('#addPreparationTimeModal').modal('show');
                                    }
                                } else if (now < windowStart) {

                                    alert("{{ trans('lang.you_can_accept_order_between') }}" + startDate + ' - ' + endDate); // too early
                                    return false;
                                } else {
                                    alert("{{ trans('lang.you_can_accept_order_between') }}" + startDate + ' - ' + endDate); // too late
                                    return false;
                                }

                            } else {
                                if (isSelfDeliveryGlobally && isSelfDeliveryByVendor && !orderTakeAwayOption) {
                                    $('#assignDriverModal').modal('show');
                                } else {
                                    $('#addPreparationTimeModal').modal('show');
                                }
                            }
                        })
                    } else if (orderStatus == 'Order Cancelled') {
                        await getRefund();
                        await removeRedeemCashback();
                    } else {
                        database.collection('restaurant_orders').doc(id).update({
                            'status': orderStatus,
                        }).then(async function(result) {
                            if (orderStatus == 'Order Rejected' || orderStatus == 'Order Cancelled' || orderStatus == 'Driver Rejected') {
                                await removeRedeemCashback();
                            }
                            var subject = '';
                            var message = '';
                            if (orderStatus == "Order Completed" && orderTakeAwayOption ==
                                true) {
                                subject = takeAwayOrderCompletedSubject;
                                message = takeAwayOrderCompletedMsg;
                            } else if (orderStatus == "Order Completed" &&
                                orderTakeAwayOption == false) {
                                subject = orderCompletedSubject;
                                message = orderCompletedMsg;
                            } else if (orderStatus == "Driver Accepted") {
                                subject = driverAcceptedSubject;
                                message = driverAcceptedMsg;
                            } else if (orderStatus == "Order Rejected") {
                                subject = orderRejectedSubject;
                                message = orderRejectedMsg;
                            }
                            if (orderStatus != orderPreviousStatus && payment_shared ==
                                false) {
                                if (orderStatus == 'Order Completed') {
                                    if (orderTakeAwayOption == true) {
                                        await getUserCashbackAmount();
                                    }
                                    driverAmount = parseFloat(deliveryCharge) + parseFloat(
                                        tip_amount);
                                    if (driverId && driverAmount) {
                                        var driver = database.collection('users').where(
                                            "id", "==", driverId);
                                        await driver.get().then(async function(
                                            snapshotsdriver) {
                                            var driverdata = snapshotsdriver
                                                .docs[0].data();
                                            if (driverdata) {
                                                if (isNaN(driverdata
                                                        .wallet_amount) ||
                                                    driverdata.wallet_amount ==
                                                    undefined) {
                                                    driverWallet = 0;
                                                } else {
                                                    driverWallet = driverdata
                                                        .wallet_amount;
                                                }
                                                if (orderPaymentMethod ==
                                                    'cod' &&
                                                    orderTakeAwayOption == true
                                                ) {
                                                    driverWallet = parseFloat(
                                                            driverWallet) -
                                                        parseFloat(total_price);
                                                } else {
                                                    driverWallet = parseFloat(
                                                            driverWallet) +
                                                        parseFloat(
                                                            driverAmount);
                                                }
                                                if (!isNaN(driverWallet)) {
                                                    await database.collection(
                                                            'users').doc(
                                                            driverdata.id)
                                                        .update({
                                                            'wallet_amount': parseFloat(
                                                                driverWallet
                                                            )
                                                        }).then(async function(
                                                            result) {});
                                                }
                                            }
                                        })
                                    }
                                }
                                await $.ajax({
                                    type: 'POST',
                                    url: "<?php echo route('order-status-notification'); ?>",
                                    data: {
                                        _token: '<?php echo csrf_token(); ?>',
                                        'fcm': manfcmTokenVendor,
                                        'vendorname': manname,
                                        'orderStatus': orderStatus,
                                        'subject': subject,
                                        'message': message
                                    },
                                    success: function(data) {
                                        if (orderPreviousStatus !=
                                            'Order Rejected' &&
                                            orderPaymentMethod != 'cod' &&
                                            orderTakeAwayOption == false) {
                                            if (orderStatus ==
                                                'Order Rejected') {
                                                var walletId = database
                                                    .collection('temp').doc()
                                                    .id;
                                                var canceldateNew = new Date();
                                                var orderCancelDate = new Date(
                                                    canceldateNew.setHours(
                                                        23, 59, 59, 999));
                                                database.collection('wallet')
                                                    .doc(walletId).set({
                                                        'amount': parseFloat(
                                                            orderPaytableAmount
                                                        ),
                                                        'date': orderCancelDate,
                                                        'id': walletId,
                                                        'payment_status': 'success',
                                                        'user_id': orderCustomerId,
                                                        'payment_method': 'Cancelled Order Payment'
                                                    }).then(function(result) {
                                                        database.collection(
                                                                'users')
                                                            .where("id",
                                                                "==",
                                                                orderCustomerId
                                                            ).get()
                                                            .then(async function(
                                                                userSnapshots
                                                            ) {
                                                                if (userSnapshots
                                                                    .docs
                                                                    .length >
                                                                    0
                                                                ) {
                                                                    data =
                                                                        userSnapshots
                                                                        .docs[
                                                                            0
                                                                        ]
                                                                        .data();
                                                                    var wallet_amount =
                                                                        0;
                                                                    if (data
                                                                        .wallet_amount !=
                                                                        undefined &&
                                                                        data
                                                                        .wallet_amount !=
                                                                        '' &&
                                                                        data
                                                                        .wallet_amount !=
                                                                        null &&
                                                                        !
                                                                        isNaN(
                                                                            data
                                                                            .wallet_amount
                                                                        )
                                                                    ) {
                                                                        wallet_amount
                                                                            =
                                                                            parseFloat(
                                                                                data
                                                                                .wallet_amount
                                                                            );
                                                                    }
                                                                    var newWalletAmount =
                                                                        wallet_amount +
                                                                        parseFloat(
                                                                            orderPaytableAmount
                                                                        );
                                                                    database
                                                                        .collection(
                                                                            'users'
                                                                        )
                                                                        .doc(
                                                                            orderCustomerId
                                                                        )
                                                                        .update({
                                                                            'wallet_amount': parseFloat(
                                                                                newWalletAmount
                                                                            )
                                                                        })
                                                                        .then(
                                                                            function(
                                                                                result
                                                                            ) {
                                                                                <?php if (isset($_GET['eid']) && $_GET['eid'] != '') { ?>
                                                                                window
                                                                                    .location
                                                                                    .href =
                                                                                    "{{ route('stores.orders', $_GET['eid']) }}";
                                                                                <?php } else { ?>
                                                                                window
                                                                                    .location
                                                                                    .href =
                                                                                    '{{ route('orders') }}';
                                                                                <?php } ?>
                                                                            }
                                                                        )
                                                                } else {
                                                                    <?php if (isset($_GET['eid']) && $_GET['eid'] != '') { ?>
                                                                    window
                                                                        .location
                                                                        .href =
                                                                        "{{ route('stores.orders', $_GET['eid']) }}";
                                                                    <?php } else { ?>
                                                                    window
                                                                        .location
                                                                        .href =
                                                                        '{{ route('orders') }}';
                                                                    <?php } ?>
                                                                }
                                                            });
                                                    })
                                            } else {
                                                <?php if (isset($_GET['eid']) && $_GET['eid'] != '') { ?>
                                                window.location.href =
                                                    "{{ route('stores.orders', $_GET['eid']) }}";
                                                <?php } else { ?>
                                                window.location.href =
                                                    '{{ route('orders') }}';
                                                <?php } ?>
                                            }
                                        } else {
                                            <?php if (isset($_GET['eid']) && $_GET['eid'] != '') { ?>
                                            window.location.href =
                                                "{{ route('stores.orders', $_GET['eid']) }}";
                                            <?php } else { ?>
                                            window.location.href =
                                                '{{ route('orders') }}';
                                            <?php } ?>
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
            })
        })

        function buildHTMLProductsList(snapshotsProducts) {
            var html = '';
            var alldata = [];
            var number = [];
            var totalProductPrice = 0;
            snapshotsProducts.forEach((product) => {
                getProductInfo(product);
                var val = product;
                var product_id = (val.variant_info && val.variant_info.variant_id) ? val.variant_info.variant_id :
                    val.id;
                html = html + '<tr>';
                var extra_html = '';
                if (product.extras != undefined && product.extras != '' && product.extras.length > 0) {
                    extra_html = extra_html + '<span>';
                    var extra_count = 1;
                    try {
                        product.extras.forEach((extra) => {
                            if (extra_count > 1) {
                                extra_html = extra_html + ',' + extra;
                            } else {
                                extra_html = extra_html + extra;
                            }
                            extra_count++;
                        })
                    } catch (error) {}
                    extra_html = extra_html + '</span>';
                }
                html = html + '<td class="order-product"><div class="order-product-box">';
                if (val.photo != '' && val.photo != null) {
                    html = html + '<img  onerror="this.onerror=null;this.src=\'' + place_image +
                        '\'" class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' + val
                        .photo + '" alt="image">';
                } else {
                    html = html + '<img class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' +
                        place_image + '" alt="image">';
                }
                html = html + '</div><div class="orders-tracking"><h6>' + val.name +
                    '</h6><div class="orders-tracking-item-details">';
                if (val.variant_info) {
                    html = html + '<div class="variant-info">';
                    html = html + '<ul>';
                    $.each(val.variant_info.variant_options, function(label, value) {
                        html = html + '<li class="variant"><span class="label">' + label +
                            '</span><span class="value">' + value + '</span></li>';
                    });
                    html = html + '</ul>';
                    html = html + '</div>';
                }
                if (extra_count > 1 || product.size) {
                    html = html + '<strong>{{ trans('lang.extras') }} :</strong>';
                }
                if (extra_count > 1) {
                    html = html +
                        '<div class="extra"><span>{{ trans('lang.extras') }} :</span><span class="ext-item">' +
                        extra_html + '</span></div>';
                }
                if (product.size) {
                    html = html +
                        '<div class="type"><span>{{ trans('lang.type') }} :</span><span class="ext-size">' +
                        product.size + '</span></div>';
                }
                var final_price = '';
                if (val.discountPrice != 0 && val.discountPrice != "" && val.discountPrice != null && !isNaN(val
                        .discountPrice)) {
                    final_price = parseFloat(val.discountPrice);
                } else {
                    final_price = parseFloat(val.price);
                }
                price_item = final_price.toFixed(decimal_degits);
                totalProductPrice = parseFloat(price_item) * parseInt(val.quantity);
                var extras_price = 0;
                if (product.extras != undefined && product.extras != '' && product.extras.length > 0) {
                    extras_price_item = (parseFloat(val.extras_price) * parseInt(val.quantity)).toFixed(
                        decimal_degits);
                    if (parseFloat(extras_price_item) != NaN && val.extras_price != undefined) {
                        extras_price = extras_price_item;
                    }
                    totalProductPrice = parseFloat(extras_price) + parseFloat(totalProductPrice);
                }
                totalProductPrice = parseFloat(totalProductPrice).toFixed(decimal_degits);
                if (currencyAtRight) {
                    price_val = parseFloat(price_item).toFixed(decimal_degits) + "" + currentCurrency;
                    extras_price_val = parseFloat(extras_price).toFixed(decimal_degits) + "" + currentCurrency;
                    totalProductPrice_val = parseFloat(totalProductPrice).toFixed(decimal_degits) + "" +
                        currentCurrency;
                } else {
                    price_val = currentCurrency + "" + parseFloat(price_item).toFixed(decimal_degits);
                    extras_price_val = currentCurrency + "" + parseFloat(extras_price).toFixed(decimal_degits);
                    totalProductPrice_val = currentCurrency + "" + parseFloat(totalProductPrice).toFixed(
                        decimal_degits);
                }
                html = html + '</div></div></td>';
                html = html + '<td class="text-green text-center"><span class="item-price">' + price_val +
                    '</span><br><span class="base-price-' + product_id + ' text-muted"></span></td><td> × ' + val
                    .quantity + '</td><td class="text-green"> + ' + extras_price_val +
                    '</td><td class="text-green">  ' + totalProductPrice_val + '</td>';
                html = html + '</tr>';
                total_price += parseFloat(totalProductPrice);
            });
            totalProductPrice = 0;
            return html;
        }

        function getProductInfo(product) {
            database.collection('vendor_products').doc(product.id).get().then(async function(snapshots) {
                if (snapshots.exists) {
                    var productData = snapshots.data();
                    if (product.variant_info && product.variant_info.variant_id) {
                        var variant_info = $.map(productData.item_attribute.variants, function(v, i) {
                            if (v.variant_sku == product.variant_info.variant_sku) {
                                return v;
                            }
                        });
                        base_price = parseFloat(variant_info[0].variant_price);
                        var product_id = product.variant_info.variant_id;
                    } else {
                        if (parseFloat(productData.disPrice) != 0) {
                            var base_price = productData.disPrice;
                        } else {
                            var base_price = productData.price;
                        }
                        var product_id = product.id;
                    }
                    if (currencyAtRight) {
                        base_price_format = parseFloat(base_price).toFixed(decimal_degits) + "" +
                            currentCurrency;
                    } else {
                        base_price_format = currentCurrency + "" + parseFloat(base_price).toFixed(
                            decimal_degits);
                    }
                    $(".base-price-" + product_id).text('(Base Price: ' + base_price_format + ')');
                }
            });
        }

        function buildHTMLProductstotal(snapshotsProducts) {
            var html = '';
            var alldata = [];
            var number = [];
            adminCommissionValue = snapshotsProducts.adminCommission;
            var adminCommissionType = snapshotsProducts.adminCommissionType;
            var discount = snapshotsProducts.discount;
            var couponCode = snapshotsProducts.couponCode;
            var extras = snapshotsProducts.extras;
            var extras_price = snapshotsProducts.extras_price;
            var rejectedByDrivers = snapshotsProducts.rejectedByDrivers;
            var takeAway = snapshotsProducts.takeAway;
            var tip_amount = snapshotsProducts.tip_amount;
            var notes = snapshotsProducts.notes;
            var tax_amount = snapshotsProducts.vendor.tax_amount;
            var status = snapshotsProducts.status;
            var products = snapshotsProducts.products;
            deliveryCharge = snapshotsProducts.deliveryCharge;
            var specialDiscount = snapshotsProducts.specialDiscount;
            var intRegex = /^\d+$/;
            var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
            if (products) {
                products.forEach((product) => {
                    var val = product;
                });
            }
            total_price = parseFloat(total_price);
            if (currencyAtRight) {
                var sub_total = parseFloat(total_price).toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                var sub_total = currentCurrency + "" + parseFloat(total_price).toFixed(decimal_degits);
            }
            html = html + '<tr><td class="seprater" colspan="2"><hr><span>{{ trans('lang.sub_total') }}</span></td></tr>';
            html = html +
                '<tr class="final-rate"><td class="label">{{ trans('lang.sub_total') }}</td><td class="sub_total" style="color:green">(' +
                sub_total + ')</td></tr>';
            var priceWithCommision = total_price;
            if (intRegex.test(discount) || floatRegex.test(discount)) {
                html = html +
                    '<tr><td class="seprater" colspan="2"><hr><span>{{ trans('lang.discount') }}</span></td></tr>';
                discount = parseFloat(discount).toFixed(decimal_degits);
                total_price -= parseFloat(discount);
                if (currencyAtRight) {
                    discount_val = discount + "" + currentCurrency;
                } else {
                    discount_val = currentCurrency + "" + discount;
                }
                couponCode_html = '';
                if (couponCode) {
                    couponCode_html = '</br><small>{{ trans('lang.coupon_codes') }} :' + couponCode + '</small>';
                }
                html = html + '<tr><td class="label">{{ trans('lang.discount') }}' + couponCode_html +
                    '</td><td class="discount text-danger">(-' + discount_val + ')</td></tr>';
            }
            if (specialDiscount != undefined) {
                special_discount = parseFloat(specialDiscount.special_discount).toFixed(decimal_degits);
                total_price -= parseFloat(special_discount);
                if (currencyAtRight) {
                    special_discount_val = special_discount + "" + currentCurrency;
                } else {
                    special_discount_val = currentCurrency + "" + special_discount;
                }
                special_html = '';
                if (specialDiscount.specialType == "percentage") {
                    special_html = '</br><small>(' + specialDiscount.special_discount_label + '%)</small>';
                }
                html = html + '<tr><td class="label">{{ trans('lang.special_offer') }} {{ trans('lang.discount') }}' +
                    special_html + '</td><td class="special_discount text-danger">(-' + special_discount_val +
                    ')</td></tr>';
            }
            var tax = 0;
            taxlabel = '';
            taxlabeltype = '';
            if (snapshotsProducts.hasOwnProperty('taxSetting') && snapshotsProducts.taxSetting.length > 0) {
                html = html +
                    '<tr><td class="seprater" colspan="2"><hr><span>{{ trans('lang.tax_calculation') }}</span></td></tr>';
                for (var i = 0; i < snapshotsProducts.taxSetting.length; i++) {
                    var data = snapshotsProducts.taxSetting[i];
                    if (data.type && data.tax) {
                        if (data.type == "percentage") {
                            tax = (data.tax * total_price) / 100;
                            taxlabeltype = "%";
                            var taxvalue = data.tax;
                        } else {
                            tax = data.tax;
                            taxlabeltype = "";
                            if (currencyAtRight) {
                                var taxvalue = parseFloat(data.tax).toFixed(decimal_degits) + "" + currentCurrency;
                            } else {
                                var taxvalue = currentCurrency + "" + parseFloat(data.tax).toFixed(decimal_degits);
                            }
                        }
                        taxlabel = data.title;
                    }
                    total_tax_amount += parseFloat(tax);
                    if (!isNaN(tax) && tax != 0) {
                        if (currencyAtRight) {
                            html = html + '<tr><td class="label">' + taxlabel + " (" + taxvalue + taxlabeltype +
                                ')</td><td class="tax_amount" id="greenColor">+' + parseFloat(tax).toFixed(decimal_degits) +
                                '' + currentCurrency + '</td></tr>';
                        } else {
                            html = html + '<tr><td class="label">' + taxlabel + " (" + taxvalue + taxlabeltype +
                                ')</td><td class="tax_amount" id="greenColor">+' + currentCurrency + parseFloat(tax)
                                .toFixed(decimal_degits) + '</td></tr>';
                        }
                    }
                }
                total_price = parseFloat(total_price) + parseFloat(total_tax_amount);
            }
            var totalAmount = total_price;
            if (intRegex.test(deliveryCharge) || floatRegex.test(deliveryCharge)) {
                html = html +
                    '<tr><td class="seprater" colspan="2"><hr><span>{{ trans('lang.delivery_charge') }}</span></td></tr>';
                deliveryCharge = parseFloat(deliveryCharge).toFixed(decimal_degits);
                totalAmount += parseFloat(deliveryCharge);
                if (currencyAtRight) {
                    deliveryCharge_val = deliveryCharge + "" + currentCurrency;
                } else {
                    deliveryCharge_val = currentCurrency + "" + deliveryCharge;
                }
                if (takeAway == '' || takeAway == false) {
                    deliveryChargeVal = deliveryCharge;
                    html = html +
                        '<tr><td class="label">{{ trans('lang.deliveryCharge') }}</td><td class="deliveryCharge " id="greenColor">+' +
                        deliveryCharge_val + '</td></tr>';
                }
            }
            if (intRegex.test(tip_amount) || floatRegex.test(tip_amount)) {
                html = html + '<tr><td class="seprater" colspan="2"><hr><span>{{ trans('lang.tip') }}</span></td></tr>';
                tip_amount = parseFloat(tip_amount).toFixed(decimal_degits);
                totalAmount += parseFloat(tip_amount);
                total_price = parseFloat(total_price).toFixed(decimal_degits);
                if (currencyAtRight) {
                    tip_amount_val = tip_amount + "" + currentCurrency;
                } else {
                    tip_amount_val = currentCurrency + "" + tip_amount;
                }
                if (takeAway == '' || takeAway == false) {
                    tip_amount_val = tip_amount_val;
                    html = html +
                        '<tr><td class="label">{{ trans('lang.tip_amount') }}</td><td class="tip_amount_val " id="greenColor">+' +
                        tip_amount_val + '</td></tr>';
                }
            }
            html += '<tr><td class="seprater" colspan="2"><hr></td></tr>';
            orderPaytableAmount = totalAmount;
            if (currencyAtRight) {
                total_price_val = parseFloat(totalAmount).toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                total_price_val = currentCurrency + "" + parseFloat(totalAmount).toFixed(decimal_degits);
            }
            total_price_val = total_price_val;
            html = html +
                '<tr class="grand-total"><td class="label">{{ trans('lang.total_amount') }}</td><td class="total_price_val " id="greenColor">' +
                total_price_val + '</td></tr>';
            var adminCommHtml = "";
            if (adminCommissionType == "Percent") {
                basePrice = (priceWithCommision / (1 + (parseFloat(adminCommissionValue) / 100)));
                adminCommission = parseFloat(priceWithCommision - basePrice);
                adminCommHtml = "(" + adminCommissionValue + "%)";
            } else {
                basePrice = priceWithCommision - adminCommissionValue;
                adminCommission = parseFloat(priceWithCommision - basePrice);
            }
            if (currencyAtRight) {
                adminCommission_val = parseFloat(adminCommission).toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                adminCommission_val = currentCurrency + "" + parseFloat(adminCommission).toFixed(decimal_degits);
            }
            html = html + '<tr><td class="label"><small>{{ trans('lang.admin_commission') }} ' + adminCommHtml +
                '</small> </td><td style="color:red"><small>( ' + adminCommission_val + ' )</small></td></tr>';
            //}
            if (notes) {
                html = html + '<tr><td class="label">{{ trans('lang.notes') }}</td><td class="adminCommission_val">' +
                    notes + '</td></tr>';
            }
            return html;
        }

        function PrintElem(elem) {
            jQuery('#' + elem).printThis({
                debug: false,
                importStyle: true,
                loadCSS: [
                    '<?php echo asset('assets/plugins/bootstrap/css/bootstrap.min.css'); ?>',
                    '<?php echo asset('css/style.css'); ?>',
                    '<?php echo asset('css/colors/blue.css'); ?>',
                    '<?php echo asset('css/icons/font-awesome/css/font-awesome.css'); ?>',
                    '<?php echo asset('assets/plugins/toast-master/css/jquery.toast.css'); ?>',
                ],
            });
        }
        //Review code GA
        var refReviewAttributes = database.collection('review_attributes');
        refReviewAttributes.get().then(async function(snapshots) {
            if (snapshots != undefined) {
                snapshots.forEach((doc) => {
                    var data = doc.data();
                    reviewAttributes[data.id] = data.title;
                });
            }
        });

        function getUserReview(vendorOrder, reviewAttr) {
            var refUserReview = database.collection('foods_review').where('orderid', "==", vendorOrder.id);
            refUserReview.limit(page_size).get().then(async function(userreviewsnapshot) {
                var reviewHTML = '';
                reviewHTML = buildRatingsAndReviewsHTML(vendorOrder, userreviewsnapshot);
                if (userreviewsnapshot.docs.length > 0) {
                    jQuery("#customers_rating_and_review").append(reviewHTML);
                } else {
                    jQuery("#customers_rating_and_review").html('<h4>No Reviews Found</h4>');
                }
            });
        }

        function buildRatingsAndReviewsHTML(vendorOrder, userreviewsnapshot) {
            var allreviewdata = [];
            var reviewhtml = '';
            userreviewsnapshot.docs.forEach((listval) => {
                var reviewDatas = listval.data();
                reviewDatas.id = listval.id;
                allreviewdata.push(reviewDatas);
            });
            reviewhtml += '<div class="user-ratings">';
            allreviewdata.forEach((listval) => {
                var val = listval;
                vendorOrder.products.forEach((productval) => {
                    if (productval.id == val.productId) {
                        rating = val.rating;
                        reviewhtml = reviewhtml +
                            '<div class="reviews-members py-3 border mb-3"><div class="media">';
                        if (productval.photo != '' && productval.photo != null) {
                            reviewhtml = reviewhtml +
                                '<a href="javascript:void(0);"><img onerror="this.onerror=null;this.src=\'' +
                                place_image + '\'" alt="#" src="' + productval.photo +
                                '" class=" img-circle img-size-32 mr-2" style="width:60px;height:60px"></a>';
                        } else {
                            reviewhtml = reviewhtml + '<a href="javascript:void(0);"><img alt="#" src="' +
                                place_image +
                                '" class=" img-circle img-size-32 mr-2" style="width:60px;height:60px"></a>';
                        }
                        reviewhtml = reviewhtml +
                            '<div class="media-body d-flex"><div class="reviews-members-header"><h6 class="mb-0"><a class="text-dark" href="javascript:void(0);">' +
                            productval.name +
                            '</a></h6><div class="star-rating"><div class="d-inline-block" style="font-size: 14px;">';
                        reviewhtml = reviewhtml + ' <ul class="rating" data-rating="' + rating + '">';
                        reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                        reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                        reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                        reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                        reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                        reviewhtml = reviewhtml + '</ul>';
                        reviewhtml = reviewhtml + '</div></div>';
                        reviewhtml = reviewhtml + '</div>';
                        reviewhtml = reviewhtml + '<div class="review-date ml-auto">';
                        if (val.createdAt != null && val.createdAt != "") {
                            var review_date = val.createdAt.toDate().toLocaleDateString('en', {
                                year: "numeric",
                                month: "short",
                                day: "numeric"
                            });
                            reviewhtml = reviewhtml + '<span>' + review_date + '</span>';
                        }
                        reviewhtml = reviewhtml + '</div>';
                        var photos = '';
                        if (val.photos.length > 0) {
                            photos += '<div class="photos"><ul>';
                            $.each(val.photos, function(key, img) {
                                photos += '<li><img src="' + img + '" width="100"></li>';
                            });
                            photos += '</ul></div>';
                        }
                        reviewhtml = reviewhtml +
                            '</div></div><div class="reviews-members-body w-100"><p class="mb-2">' + val
                            .comment + '</p>' + photos + '</div>';
                        if (val.hasOwnProperty('reviewAttributes') && val.reviewAttributes != null) {
                            reviewhtml += '<div class="attribute-ratings feature-rating mb-2">';
                            var label_feature = "{{ trans('lang.byfeature') }}";
                            reviewhtml += '<h3 class="mb-2">' + label_feature + '</h3>';
                            reviewhtml += '<div class="media-body">';
                            $.each(val.reviewAttributes, function(aid, data) {
                                var at_id = aid;
                                var at_title = reviewAttributes[aid];
                                var at_value = data;
                                reviewhtml +=
                                    '<div class="feature-reviews-members-header d-flex mb-3">';
                                reviewhtml += '<h6 class="mb-0">' + at_title + '</h6>';
                                reviewhtml = reviewhtml +
                                    '<div class="rating-info ml-auto d-flex">';
                                reviewhtml = reviewhtml + '<div class="star-rating">';
                                reviewhtml = reviewhtml + ' <ul class="rating" data-rating="' +
                                    at_value + '">';
                                reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                                reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                                reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                                reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                                reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                                reviewhtml = reviewhtml + '</ul>';
                                reviewhtml += '</div>';
                                reviewhtml += '<div class="count-rating ml-2">';
                                reviewhtml += '<span class="count">' + at_value + '</span>';
                                reviewhtml += '</div>';
                                reviewhtml += '</div></div>';
                            });
                            reviewhtml += '</div></div>';
                        }
                        reviewhtml += '</div>';
                    }
                    reviewhtml += '</div>';
                });
            });
            reviewhtml += '</div>';
            return reviewhtml;
        }
        $('#addDeliverymanModal').on('shown.bs.modal', function() {
            $('#assignDriverModal').hide();
        })

        $('#add-deliveryman-btn').on('click', function() {

            var userFirstName = $(".user_first_name").val();
            var userLastName = $(".user_last_name").val();
            var email = $(".user_email").val();
            var password = $(".password").val();
            var countryCode = '+' + jQuery("#country_selector").val();
            var userPhone = $(".user_phone").val();
            var isActive = true;
            var zoneId = $('#zone option:selected').val();
            if (userFirstName == '') {
                $("#firstname_err").append("{{ trans('lang.user_first_name_help') }}");
            } else if (email == '') {
                $("#email_err").append("{{ trans('lang.user_email_help') }}");
            } else if (password == '') {
                $("#password_err").append("{{ trans('lang.user_password_help') }}");
            } else if (userPhone == '') {
                $("#mobilenumber_err").append("{{ trans('lang.user_phone_help') }}");
            } else if (zoneId == '') {
                $("#zone_err").append("{{ trans('lang.select_zone_help') }}");
            } else {
                var id = database.collection('tmp').doc().id;
                firebase.auth().createUserWithEmailAndPassword(email, password)
                    .then(async function(firebaseUser) {
                        user_id = firebaseUser.user.uid;
                        database.collection('users').doc(user_id).set({
                            'firstName': userFirstName,
                            'lastName': userLastName,
                            'email': email,
                            'countryCode': countryCode,
                            'phoneNumber': userPhone,
                            'role': 'driver',
                            'id': user_id,
                            'createdAt': firebase.firestore.FieldValue.serverTimestamp(),
                            'provider': "email",
                            'appIdentifier': "web",
                            'vendorID': vendorId,
                            'active': isActive,
                            'isDocumentVerify': true,
                            'zoneId': zoneId,
                            'isActive': true,

                        }).then(function(result) {
                            window.location.reload();
                        });
                    }).catch(err => {
                        jQuery("#data-table_processing").hide();
                        $(".error_top").append("<p>" + err + "</p>");
                    });

            }


        })
        async function getRefund() {
            jQuery('#data-table_processing').show();
            ref.get().then(async function(snapshot) {

                orderData = snapshot.docs[0].data();
                try {
                    const vendorId = orderData.vendor?.author;
                    const customerId = orderData.author?.id;
                    let vendorAmount = 0,
                        deliveryCharge = 0,
                        tipAmount = 0,
                        customerAmount = 0;
                    let vendorFcm = '',
                        customerFcm = '',
                        driverFcm = '';
                    let vendorTaxAmount = 0;
                    let vendorBaseAmount = 0;
                    const walletSnapshot = await database.collection('wallet')
                        .where('user_id', '==', vendorId)
                        .where('order_id', '==', orderData.id)
                        .where('isTopUp', '==', true)
                        .get();

                    for (const doc of walletSnapshot.docs) {
                        const data = doc.data();
                        if (data.payment_method == 'tax') {
                            vendorTaxAmount = parseFloat(data.amount);
                        } else {
                            vendorBaseAmount = parseFloat(data.amount);
                        }
                        vendorAmount += parseFloat(data.amount || 0);
                    }

                    if (vendorAmount) {
                        const vendorDoc = await database.collection('users').doc(vendorId).get();
                        if (vendorDoc.exists) {
                            const vendorData = vendorDoc.data();
                            vendorFcm = vendorData.fcmToken || '';
                            const vendorWallet = parseFloat(vendorData.wallet_amount || 0);
                            await vendorDoc.ref.update({
                                wallet_amount: vendorWallet - vendorAmount
                            });
                        }

                        const walletId = database.collection("tmp").doc().id;
                        await database.collection('wallet').doc(walletId).set({
                            amount: vendorBaseAmount,
                            date: firebase.firestore.FieldValue.serverTimestamp(),
                            id: walletId,
                            isTopUp: false,
                            order_id: orderData.id,
                            payment_method: "Wallet",
                            payment_status: 'success',
                            user_id: vendorId,
                            transactionUser: 'vendor',
                            note: 'Order amount refunded to customer'
                        });
                        const walletTaxId = database.collection("tmp").doc().id;
                        await database.collection('wallet').doc(walletTaxId).set({
                            amount: vendorTaxAmount,
                            date: firebase.firestore.FieldValue.serverTimestamp(),
                            id: walletTaxId,
                            isTopUp: false,
                            order_id: orderData.id,
                            payment_method: "tax",
                            payment_status: 'success',
                            user_id: vendorId,
                            transactionUser: 'vendor',
                            note: 'Order tax refunded to customer'
                        });

                    }

                    if (orderData.payment_method !== 'cod') {
                        deliveryCharge = parseFloat(orderData.deliveryCharge || 0);
                        tipAmount = parseFloat(orderData.tip_amount || 0);
                        customerAmount = deliveryCharge + tipAmount + vendorAmount + adminCommission;

                        const customerDoc = await database.collection('users').doc(customerId).get();
                        if (customerDoc.exists) {
                            const customerData = customerDoc.data();
                            customerFcm = customerData.fcmToken || '';
                            const customerWallet = parseFloat(customerData.wallet_amount || 0);
                            await customerDoc.ref.update({
                                wallet_amount: customerWallet + customerAmount
                            });
                        }

                        const walletId = database.collection("tmp").doc().id;
                        await database.collection('wallet').doc(walletId).set({
                            amount: customerAmount,
                            date: firebase.firestore.FieldValue.serverTimestamp(),
                            id: walletId,
                            isTopUp: true,
                            order_id: orderData.id,
                            payment_method: "Wallet",
                            payment_status: 'success',
                            user_id: customerId,
                            transactionUser: 'customer',
                            note: 'Order amount refunded'
                        });
                    } else {
                        const customerDoc = await database.collection('users').doc(customerId).get();
                        if (customerDoc.exists) {
                            customerFcm = customerDoc.data().fcmToken || '';
                        }
                    }
                    if (orderData.hasOwnProperty('driverID') && orderData.driverID != null && orderData.driverID != '') {
                        await database.collection('users').doc(orderData.driverID).get().then(async function(snapshot) {
                            let newOrderRequestData = [];
                            let inProgressOrderID = [];
                            if (snapshot.exists) {
                                var driverData = snapshot.data();
                                driverFcm = driverData.fcmToken;
                                if (driverData.orderRequestData !== undefined) {
                                    newOrderRequestData = driverData.orderRequestData.filter(function(oid) {
                                        return oid !== id;
                                    });
                                }
                                if (driverData.inProgressOrderID !== undefined) {
                                    inProgressOrderID = driverData.inProgressOrderID.filter(function(oid) {
                                        return oid !== id;
                                    });
                                }
                                await database.collection('users').doc(driverData.id).update({
                                    'inProgressOrderID': inProgressOrderID,
                                    'orderRequestData': newOrderRequestData
                                })
                            }

                        })
                        await database.collection('restaurant_orders').doc(orderData.id).update({
                            'status': 'Order Cancelled',
                            'driverID': null,
                            'driver': null
                        });
                    } else {
                        await database.collection('restaurant_orders').doc(orderData.id).update({
                            'status': 'Order Cancelled'
                        });
                    }

                    await $.ajax({
                        type: 'POST',
                        url: "<?php echo route('order-status-notification'); ?>",
                        data: {
                            _token: '<?php echo csrf_token(); ?>',
                            'fcm': customerFcm,
                            'vendorname': manname,
                            'orderStatus': 'Order Cancelled',
                            'subject': selfDeliveryCustomerCancelledSub,
                            'message': selfDeliveryCustomerCancelledMsg
                        },
                        success: function(data) {
                            window.location.href = '{{ route('orders') }}';
                        }
                    });
                    await $.ajax({
                        type: 'POST',
                        url: "<?php echo route('order-status-notification'); ?>",
                        data: {
                            _token: '<?php echo csrf_token(); ?>',
                            'fcm': driverFcm,
                            'vendorname': manname,
                            'orderStatus': 'Order Cancelled',
                            'subject': selfDeliveryDriverCancelledSub,
                            'message': selfDeliveryDriverCancelledMsg
                        },
                        success: function(data) {

                        }
                    });
                    window.location.href = '{{ route('orders') }}';
                } catch (error) {
                    console.error("Error in getRefund:", error);
                }

            })
            $('#data-table_processing').hide();
        }
        async function getUserCashbackAmount() {
            await ref.get().then(async function(snapshot) {
                var orderData = snapshot.docs[0].data();
                if (orderData.hasOwnProperty('cashback') && orderData.cashback != null && orderData.cashback != '') {
                    var snapshotUser = await database.collection('users').doc(orderData.authorID).get();
                    if (snapshotUser.exists) {
                        console.log('userExist');
                        var userData = snapshotUser.data();
                        var walletAmount = 0;
                        if (userData.hasOwnProperty('wallet_amount') && !isNaN(userData.wallet_amount) && userData.wallet_amount != null && userData.wallet_amount != '') {
                            walletAmount = userData.wallet_amount;
                        }
                        var newWalletAmount = parseFloat(walletAmount) + parseFloat(orderData.cashback.cashbackValue);
                        await database.collection('users').doc(userData.id).update({
                            'wallet_amount': newWalletAmount
                        }).then(async function(result) {
                            var wId = database.collection('temp').doc().id;
                            await database.collection('wallet').doc(wId).set({
                                'amount': parseFloat(orderData.cashback.cashbackValue),
                                'date': firebase.firestore.FieldValue.serverTimestamp(),
                                'id': wId,
                                'isTopUp': true,
                                'order_id': "<?php echo $id; ?>",
                                'payment_method': 'cashback',
                                'payment_status': 'success',
                                'transactionUser': 'customer',
                                'user_id': userData.id,
                                'note': 'Order cashback credited'
                            }).then(async function(result) {

                            })
                        })
                    }
                }
            })
        }

        async function removeRedeemCashback() {
            const snapshot = await database.collection('cashback_redeem')
                .where('orderId', '==', "{{ $id }}")
                .limit(1)
                .get();
            if (!snapshot.empty) {
                const docId = snapshot.docs[0].id;
                await database.collection('cashback_redeem').doc(docId).delete();
            }
        }
    </script>
@endsection
