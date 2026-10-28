@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.on_board_plural')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{!! route('on-board') !!}">{{trans('lang.on_board_plural')}}</a>
                    </li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card pb-4">
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row restaurant_payout_create">
                        <div class="restaurant_payout_create-inner">
                            <fieldset>
                                <legend>{{trans('lang.on_board_details')}}</legend>
                                <div class="form-group row width-100">
                                    <label class="col-3 control-label">{{trans('lang.on_board_title_en')}}<span
                                            class="required-field"></span></label>
                                    <div class="col-7">
                                        <input type="text" class="form-control title-en" dir="ltr">
                                        <div class="form-text text-muted">
                                            {{ trans("lang.on_board_title_en_help") }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-3 control-label">{{trans('lang.on_board_title_ar')}}<span
                                            class="required-field"></span></label>
                                    <div class="col-7">
                                        <input type="text" class="form-control title-ar" dir="rtl">
                                        <div class="form-text text-muted">
                                            {{ trans("lang.on_board_title_ar_help") }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-3 control-label">{{trans('lang.on_board_description_en')}}<span
                                            class="required-field"></span></label>
                                    <div class="col-7">
                                        <textarea rows="6" class="description-en form-control" dir="ltr"></textarea>
                                        <div class="form-text text-muted">
                                            {{ trans("lang.on_board_description_en_help") }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-3 control-label">{{trans('lang.on_board_description_ar')}}<span
                                            class="required-field"></span></label>
                                    <div class="col-7">
                                        <textarea rows="6" class="description-ar form-control" dir="rtl"></textarea>
                                        <div class="form-text text-muted">
                                            {{ trans("lang.on_board_description_ar_help") }}
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-group col-12 text-center btm-btn">
                        <button type="button" class="btn btn-primary  edit-setting-btn"><i class="fa fa-save"></i> {{
                        trans('lang.save')}}</button>
                        <a href="{!! route('on-board') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
                        trans('lang.cancel')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var database = firebase.firestore();
        var requestId = "{{$id}}";
        $(document).ready(function () {
            $('.onboard_menu').addClass('active');
            jQuery("#data-table_processing").show();
            var ref = database.collection('on_boarding').where("id", "==", requestId);
            ref.get().then(async function (snapshots) {
                var data = snapshots.docs[0].data();
                $(".title-en").val(data.titleEn || data.title || '');
                $(".title-ar").val(data.titleAr || '');
                $(".description-en").val(data.descriptionEn || data.description || '');
                $(".description-ar").val(data.descriptionAr || '');
                jQuery("#data-table_processing").hide();
            })
        });
        function showOnBoardError(message) {
            jQuery("#overlay").hide();
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>" + message + "</p>");
            window.scrollTo(0, 0);
        }
        $(".edit-setting-btn").click(function () {
            var titleEn = $(".title-en").val().trim();
            var titleAr = $(".title-ar").val().trim();
            var descriptionEn = $(".description-en").val().trim();
            var descriptionAr = $(".description-ar").val().trim();
            if (titleEn == '') {
                showOnBoardError("{{trans('lang.on_board_title_en_help')}}");
            } else if (titleAr == '') {
                showOnBoardError("{{trans('lang.on_board_title_ar_help')}}");
            } else if (descriptionEn == '') {
                showOnBoardError("{{trans('lang.on_board_description_en_help')}}");
            } else if (descriptionAr == '') {
                showOnBoardError("{{trans('lang.on_board_description_ar_help')}}");
            } else {
                jQuery("#overlay").show();
                database.collection('on_boarding').doc(requestId).update({
                    'title': titleEn,
                    'description': descriptionEn,
                    'titleEn': titleEn,
                    'titleAr': titleAr,
                    'descriptionEn': descriptionEn,
                    'descriptionAr': descriptionAr,
                }).then(function (result) {
                    window.location.href = '{{ route("on-board")}}';
                }).catch(function (error) {
                    showOnBoardError(error);
                })
            }
        })
    </script>
@endsection
