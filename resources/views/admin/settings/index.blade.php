@extends('layouts.app')
@section('title','General Settings')
@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" />
@endsection
@section('content')
@php 
    $gcash_gateway = isset($settings->where('field','gcash_gateway')->first()->value)?$settings->where('field','gcash_gateway')->first()->value:'';
    $paymaya_gateway = isset($settings->where('field','paymaya_gateway')->first()->value)?$settings->where('field','paymaya_gateway')->first()->value:'';
    $captcha_threshold = isset($settings->where('field','captcha_threshold')->first()->value)?$settings->where('field','captcha_threshold')->first()->value:'';
    $sms_api_key = isset($settings->where('field','sms_api_key')->first()->value)?$settings->where('field','sms_api_key')->first()->value:'';
    $sms_sender = isset($settings->where('field','sms_sender')->first()->value)?$settings->where('field','sms_sender')->first()->value:'';
    $text_blast_api_key = isset($settings->where('field','text_blast_api_key')->first()->value)?$settings->where('field','text_blast_api_key')->first()->value:'';
    $text_blast_sender = isset($settings->where('field','text_blast_sender')->first()->value)?$settings->where('field','text_blast_sender')->first()->value:'';
    $sms_message_endpoint = isset($settings->where('field','sms_message_endpoint')->first()->value)?$settings->where('field','sms_message_endpoint')->first()->value:'';
    $otp_endpoint = isset($settings->where('field','otp_endpoint')->first()->value)?$settings->where('field','otp_endpoint')->first()->value:'';
    $account_endpoint = isset($settings->where('field','account_endpoint')->first()->value)?$settings->where('field','account_endpoint')->first()->value:'';
    $custom_date = isset($settings->where('field','custom_date')->first()->value)?$settings->where('field','custom_date')->first()->value:'';
    $messenger_link = isset($settings->where('field','messenger_link')->first()->value)?$settings->where('field','messenger_link')->first()->value:'';
    $telegram_link = isset($settings->where('field','telegram_link')->first()->value)?$settings->where('field','telegram_link')->first()->value:'';
    $is_maintenance = isset($settings->where('field','is_maintenance')->first()->value)?$settings->where('field','is_maintenance')->first()->value:'';
    $maintenance_text = isset($settings->where('field','maintenance_text')->first()->value)?$settings->where('field','maintenance_text')->first()->value:'';

    $pg_gcash_bayad = isset($settings->where('field','pg_gcash_bayad')->first()->value)?$settings->where('field','pg_gcash_bayad')->first()->value:'';
    $pg_gcash_easy_pay_1 = isset($settings->where('field','pg_gcash_easy_pay_1')->first()->value)?$settings->where('field','pg_gcash_easy_pay_1')->first()->value:'';
    $pg_gcash_easy_pay_2 = isset($settings->where('field','pg_gcash_easy_pay_2')->first()->value)?$settings->where('field','pg_gcash_easy_pay_2')->first()->value:'';
    $pg_gcash_p2p_qr_pay_1 = isset($settings->where('field','pg_gcash_p2p_qr_pay_1')->first()->value)?$settings->where('field','pg_gcash_p2p_qr_pay_1')->first()->value:'';
    $pg_gcash_p2p_qr_pay_1_min = isset($settings->where('field','pg_gcash_p2p_qr_pay_1_min')->first()->value)?$settings->where('field','pg_gcash_p2p_qr_pay_1_min')->first()->value:'';
    $pg_gcash_qr_pay_1 = isset($settings->where('field','pg_gcash_qr_pay_1')->first()->value)?$settings->where('field','pg_gcash_qr_pay_1')->first()->value:'';
    $pg_paymaya_2 = isset($settings->where('field','pg_paymaya_2')->first()->value)?$settings->where('field','pg_paymaya_2')->first()->value:'';
    $pg_paymaya_1 = isset($settings->where('field','pg_paymaya_1')->first()->value)?$settings->where('field','pg_paymaya_1')->first()->value:'';
    $pg_manual = isset($settings->where('field','pg_manual')->first()->value)?$settings->where('field','pg_manual')->first()->value:'';
    $pg_gcash_redirect = isset($settings->where('field','pg_gcash_redirect')->first()->value)?$settings->where('field','pg_gcash_redirect')->first()->value:'';
    $pg_gcash_redirect_min = isset($settings->where('field','pg_gcash_redirect_min')->first()->value)?$settings->where('field','pg_gcash_redirect_min')->first()->value:'';
@endphp
<div id="main">
    <div class="row">
        <div class="col-12 justify-content-center">
            @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                      <li class="text-danger">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <h1>{{ __('General Settings') }}</h1>
            <form method="POST" action="{{route('settings.store')}}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <div class="form-group row py-2">
                            <span class="draw-label">Favicon</span>
                            <div class="col-12 col-sm-12">
                            <div class="input-icon mobile-icon">
                                <input type="file" class="form-control" id="favicon" name="favicon" onchange="preview('favicon')">
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 py-4">
                        <img id="logo-image" width="100%" 
                             src="{{URL($settings->where('field','favicon')->first()->value?$settings->where('field','favicon')->first()->value:'')}}" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <div class="form-group row py-2">
                            <span class="draw-label">Header Image</span>
                            <div class="col-12 col-sm-12">
                            <div class="input-icon mobile-icon">
                                <input type="file" class="form-control" id="header-image" name="header_image" onchange="preview('header-image')">
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 py-4">
                        <img id="header-image" width="100%" 
                             src="{{URL($settings->where('field','header_image')->first()->value?$settings->where('field','header_image')->first()->value:'')}}" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <div class="form-group row py-2">
                            <span class="draw-label">Banner Image</span>
                            <div class="col-12 col-sm-12">
                            <div class="input-icon mobile-icon">
                                <input type="file" class="form-control" id="banner-image" name="banner_image" onchange="preview('banner-image')">
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 py-4">
                        <img id="banner-image" width="100%" 
                             src="{{URL($settings->where('field','banner_image')->first()->value?$settings->where('field','banner_image')->first()->value:'')}}" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <div class="form-group row py-2">
                            <span class="draw-label">Login Banner Image</span>
                            <div class="col-12 col-sm-12">
                            <div class="input-icon mobile-icon">
                                <input type="file" class="form-control" id="login-banner-image" name="login_banner_image" onchange="preview('logi-banner-image')">
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 py-4">
                        <img id="logi-banner-image" width="100%" 
                             src="{{URL($settings->where('field','login_banner_image')->first()->value?$settings->where('field','login_banner_image')->first()->value:'')}}" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <div class="form-group row py-2">
                            <span class="draw-label">Cash Banner Image</span>
                            <div class="col-12 col-sm-12">
                            <div class="input-icon mobile-icon">
                                <input type="file" class="form-control" id="cashin-banner-image" name="cashin_banner" onchange="preview('logi-banner-image')">
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 py-4">
                        <img id="logi-banner-image" width="100%" 
                             src="{{URL($settings->where('field','cashin_banner')->first()->value?$settings->where('field','cashin_banner')->first()->value:'')}}" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <div class="form-group row py-2">
                            <span class="draw-label">Background Image</span>
                            <div class="col-12 col-sm-12">
                            <div class="input-icon mobile-icon">
                                <input type="file" class="form-control" id="background-image" name="background_image" onchange="preview('background-image')">
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 py-4">
                        <img id="background-image" width="100%" 
                             src="{{URL($settings->where('field','background_image')->first()->value?$settings->where('field','background_image')->first()->value:'')}}" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <div class="form-group row py-2">
                            <span class="draw-label">OG Image</span>
                            <div class="col-12 col-sm-12">
                            <div class="input-icon mobile-icon">
                                <input type="file" class="form-control" id="og-image" name="og_image" onchange="preview('og-image')">
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 py-4">
                        <img id="og-image" width="100%" 
                             src="{{URL($settings->where('field','og_image')->first()->value?$settings->where('field','og_image')->first()->value:'images/site-bg.png')}}" />
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">GCash Gateway</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <select class="form-control" name="gcash_gateway">
                                <option value="gcash" <?php echo (isset($gcash_gateway) && $gcash_gateway == 'gcash') ? 'selected' : '' ?>>GCash Direct</option>
                                <option value="gcash_qr" <?php echo (isset($gcash_gateway) && $gcash_gateway == 'gcash_qr') ? 'selected' : '' ?>>GCash QR</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">PayMaya Gateway</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <select class="form-control" name="paymaya_gateway">
                                <option value="paymaya" <?php echo (isset($paymaya_gateway) && $paymaya_gateway == 'paymaya') ? 'selected' : '' ?>>PayMaya Direct</option>
                                <option value="paymaya_qr" <?php echo (isset($paymaya_gateway) && $paymaya_gateway == 'paymaya_qr') ? 'selected' : '' ?>>PayMaya QR</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">Captcha Threshold</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <input type="text" class="form-control" id="captcha_threshold" value="<?php echo (isset($captcha_threshold)) ? $captcha_threshold : '' ?>" name="captcha_threshold" />
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">SMS API Key</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <input type="text" class="form-control" id="sms_api_key" value="<?php echo (isset($sms_api_key)) ? $sms_api_key : '' ?>" name="sms_api_key" />
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">SMS Sender Name</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <input type="text" class="form-control" id="sms_sender" value="<?php echo (isset($sms_sender)) ? $sms_sender : '' ?>" name="sms_sender" />
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">Text Blast API Key</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <input type="text" class="form-control" id="text_blast_api_key" value="<?php echo (isset($text_blast_api_key)) ? $text_blast_api_key : '' ?>" name="text_blast_api_key" />
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">Text Blast Sender</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <input type="text" class="form-control" id="text_blast_sender" value="<?php echo (isset($text_blast_sender)) ? $text_blast_sender : '' ?>" name="text_blast_sender" />
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">Semaphore SMS Message Endpoint</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <input type="text" class="form-control" id="sms_message_endpoint" value="<?php echo (isset($sms_message_endpoint)) ? $sms_message_endpoint : '' ?>" name="sms_message_endpoint" />
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">Semaphore SMS OTP Endpoint</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <input type="text" class="form-control" id="otp_endpoint" value="<?php echo (isset($otp_endpoint)) ? $otp_endpoint : '' ?>" name="otp_endpoint" />
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">Semaphore SMS Account Endpoint</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <input type="text" class="form-control" id="account_endpoint" value="<?php echo (isset($account_endpoint)) ? $account_endpoint : '' ?>" name="account_endpoint" />
                        </div>
                        </div>
                    </div>
                </div>
                @if (auth()->user()->id == '1')
                    <div class="row">
                        <div class="form-group row py-2">
                            <span class="draw-label">Custom Winning Date (yyyy-mm-dd)</span>
                            <span class="text-warning mb-1">Example: 2024-04-31</span>
                            <div class="col-12 col-sm-12">
                            <div class="input-icon mobile-icon">
                                <input type="text" class="form-control" id="custom_date" value="<?php echo (isset($custom_date)) ? $custom_date : '' ?>" name="custom_date" />
                            </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">Messanger Link</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <input type="text" class="form-control" id="messenger_link" value="<?php echo (isset($messenger_link)) ? $messenger_link : '' ?>" name="messenger_link" />
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">Telegram Link</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <input type="text" class="form-control" id="telegram_link" value="<?php echo (isset($telegram_link)) ? $telegram_link : '' ?>" name="telegram_link" />
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">Maintenance Mode</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <select class="form-control" name="is_maintenance">
                                <option value="yes" <?php echo (isset($is_maintenance) && $is_maintenance == 'yes') ? 'selected' : '' ?>>Yes</option>
                                <option value="no" <?php echo (isset($is_maintenance) && $is_maintenance == 'no') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">GCash Easy Pay I (Luckypay)</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <select class="form-control" name="pg_gcash_easy_pay_1">
                                <option value="yes" <?php echo (isset($pg_gcash_easy_pay_1) && $pg_gcash_easy_pay_1 == 'yes') ? 'selected' : '' ?>>Yes</option>
                                <option value="no" <?php echo (isset($pg_gcash_easy_pay_1) && $pg_gcash_easy_pay_1 == 'no') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">GCash Pay II (GTpay)</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <select class="form-control" name="pg_gcash_easy_pay_2">
                                <option value="yes" <?php echo (isset($pg_gcash_easy_pay_2) && $pg_gcash_easy_pay_2 == 'yes') ? 'selected' : '' ?>>Yes</option>
                                <option value="no" <?php echo (isset($pg_gcash_easy_pay_2) && $pg_gcash_easy_pay_2 == 'no') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">GCash Fast 1 (JDPay)</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <select class="form-control" name="pg_gcash_p2p_qr_pay_1">
                                <option value="yes" <?php echo (isset($pg_gcash_p2p_qr_pay_1) && $pg_gcash_p2p_qr_pay_1 == 'yes') ? 'selected' : '' ?>>Yes</option>
                                <option value="no" <?php echo (isset($pg_gcash_p2p_qr_pay_1) && $pg_gcash_p2p_qr_pay_1 == 'no') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">GCash Fast 1 Minimum Amount</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <input type="text" class="form-control" id="pg_gcash_p2p_qr_pay_1_min" value="<?php echo (isset($pg_gcash_p2p_qr_pay_1_min)) ? $pg_gcash_p2p_qr_pay_1_min : '' ?>" name="pg_gcash_p2p_qr_pay_1_min" />
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">GCash QR Pay I (Luckypay)</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <select class="form-control" name="pg_gcash_qr_pay_1">
                                <option value="yes" <?php echo (isset($pg_gcash_qr_pay_1) && $pg_gcash_qr_pay_1 == 'yes') ? 'selected' : '' ?>>Yes</option>
                                <option value="no" <?php echo (isset($pg_gcash_qr_pay_1) && $pg_gcash_qr_pay_1 == 'no') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">PayMaya II (GTpay)</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <select class="form-control" name="pg_paymaya_2">
                                <option value="yes" <?php echo (isset($pg_paymaya_2) && $pg_paymaya_2 == 'yes') ? 'selected' : '' ?>>Yes</option>
                                <option value="no" <?php echo (isset($pg_paymaya_2) && $pg_paymaya_2 == 'no') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">Gcash Bayad II (GTpay)</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <select class="form-control" name="pg_gcash_bayad">
                                <option value="yes" <?php echo (isset($pg_gcash_bayad) && $pg_gcash_bayad == 'yes') ? 'selected' : '' ?>>Yes</option>
                                <option value="no" <?php echo (isset($pg_gcash_bayad) && $pg_gcash_bayad == 'no') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">GCash Safe (JDpay)</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <select class="form-control" name="pg_gcash_redirect">
                                <option value="yes" <?php echo (isset($pg_gcash_redirect) && $pg_gcash_redirect == 'yes') ? 'selected' : '' ?>>Yes</option>
                                <option value="no" <?php echo (isset($pg_gcash_redirect) && $pg_gcash_redirect == 'no') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">GCash Safe Min Amount (JDpay)</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <input  type="text" 
                                    class="form-control" 
                                    id="pg_gcash_redirect_min" 
                                    value="<?php echo (isset($pg_gcash_redirect_min)) ? $pg_gcash_redirect_min : '' ?>" 
                                    name="pg_gcash_redirect_min" />
                        </div>
                        </div>
                    </div>
                </div>




                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">PayMaya Fast (JDPay)</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <select class="form-control" name="pg_paymaya_1">
                                <option value="yes" <?php echo (isset($pg_paymaya_1) && $pg_paymaya_1 == 'yes') ? 'selected' : '' ?>>Yes</option>
                                <option value="no" <?php echo (isset($pg_paymaya_1) && $pg_paymaya_1 == 'no') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">GCash Manual</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <select class="form-control" name="pg_manual">
                                <option value="yes" <?php echo (isset($pg_manual) && $pg_manual == 'yes') ? 'selected' : '' ?>>Yes</option>
                                <option value="no" <?php echo (isset($pg_manual) && $pg_manual == 'no') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group row py-2">
                        <span class="draw-label">Maintenance Text</span>
                        <div class="col-12 col-sm-12">
                        <div class="input-icon mobile-icon">
                            <textarea class="form-control" rows="3" name="maintenance_text"><?=$maintenance_text?></textarea>
                        </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection

<x-js-lib />
<script type="text/javascript">
    function preview(image) {
        switch(image) {
            case 'favicon': $('img#favicon').attr('src',URL.createObjectURL(event.target.files[0]));
                break;
            case 'header-image': $('img#header-image').attr('src',URL.createObjectURL(event.target.files[0]));
                break;
            case 'banner-image': $('img#banner-image').attr('src',URL.createObjectURL(event.target.files[0]));
                break;
            case 'login-banner-image': $('img#login-banner-image').attr('src',URL.createObjectURL(event.target.files[0]));
                break;
            case 'cashin-banner-image': $('img#cashin-banner-image').attr('src',URL.createObjectURL(event.target.files[0]));
                break;
            case 'background-image': $('img#background-image').attr('src',URL.createObjectURL(event.target.files[0]));
                break;
            case 'og-image': $('img#og-image').attr('src',URL.createObjectURL(event.target.files[0]));
            break;
            default: 
                break;
        }
    }
  </script>