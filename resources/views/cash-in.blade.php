@section('title','Cash In')
<x-layout :bg=$background_image :og=$og_image >
    <x-header />
    <x-server-info />
    <x-container>
        <form class="mb-2" method="POST" ACTION="{{route('cashin.pay')}}" class="mt-2" enctype="multipart/form-data" id="three_digit_form">
            <!-- Way 1: Display All Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @csrf
            <div class="row rounded p-2 mx-3 mt-2 review-container cashin-bg">
                <div class="col-12 my-2 text-center">
                    <h1 class="heading-cashin text-center mb-2"> Cash In</h1>
                    <p class="avail-balance-cashin text-center"><strong class="text-success sel-pmnt">Available Balance: ₱{{number_format($balance, 2, '.',',')}}</strong></p>

                    <p class="avail-balance-cashin-note text-center bg-danger my-2 py-2">
                        <strong class="text-white sel-pmnt">Ugaliing mag-Cashin bago mag 7:00pm upang hindi maabutan ng GCash Maintenance. Maraming Salamat!</strong>
                    </p>

                    <select class="form-select text-center" name="pg" id="pg" aria-label="Default select example">
                        <option value="" disabled selected>Select Payment</option>
                        {{-- <option value="gcash">GCash Easy Pay</option> --}}
                        @if ($settings->getValueFor('pg_gcash_redirect') == 'yes')
                            <option value="gcash_redirect">GCash (Fast)</option>
                        @endif

                        @if ($settings->getValueFor('pg_gcash_p2p_qr_pay_1') == 'yes')
                            <option value="gcash_p2p">GCash P2P/QR PAY I</option>
                        @endif
                        
                        @if ($settings->getValueFor('pg_gcash_easy_pay_1') == 'yes')
                            <option value="gcash">GCash Easy Pay I</option>
                        @endif

                        @if ($settings->getValueFor('pg_gcash_easy_pay_2') == 'yes')
                            <option value="gcash_qr">GCash Pay II</option>
                        @endif

                        
                        @if ($settings->getValueFor('pg_gcash_qr_pay_1') == 'yes')
                            <option value="gcash_p2p_qr">GCash QR Pay I</option>
                        @endif

                        @if ($settings->getValueFor('pg_paymaya_2') == 'yes')
                            <option value="paymaya_2">PayMaya II</option>
                        @endif

                        @if ($settings->getValueFor('pg_paymaya_1') == 'yes')
                            <option value="paymaya_1">PayMaya I</option>
                        @endif

                        @if ($settings->getValueFor('pg_manual') == 'yes')
                            <option value="manual">GCash Manual</option>
                        @endif
                        
                        
                        
                        
                        
                        
                        
                    </select>
                    <input type="text" class="form-control mt-4 mb-1 text-center" name="amount" id="amount" placeholder="Enter Amount in PHP">
                    <p class="review-note text-center"><strong class="text-danger sel-pmnt sel-pmnt-note">&nbsp;</strong></p>
                    <div class="col-12 my-2 text-center mt-3 manual-container" style="display: none;">
                        <p class="review-note text-center"><strong class="text-danger">Send Amount Here</strong></p>
                        <h3 class="text-center gcash-no font-weight-bold">GCash Account: <br>09XX XXX XXXX</h3>
                        <h3 class="text-center gcash-no font-weight-bold">K**L E* S*</h3>
                        <p class="review-note text-center"><strong class="text-danger">Upload GCash Receipt Below</strong></p>
                        <input type="file" name="file" id="payment_file_upload" class="form-control mt-2 mb-1 text-center" accept=".jpg,.jpeg,.png" />
                    </div>
                </div>
                 
                <div class="col-12 my-2 text-center">
                    <p class="review-note text-center warning-note">Confirmed transactions will not be refunded. <br/>Please make sure the details above are correct.</p>
                    <button type="submit" class="col-12 pay submit-btn-action btn btn-danger bg-[#a83232] hover:bg-[#c23c3c] mt-2 review-pay-now" disabled>PAY NOW</button>
                </div>
                
            </div>

            <div class="row">
                <div class="col-12 my-2 text-center">
                    <x-homepage-banner :homebanner="$home_banner" />
                </div>
            </div>
        </form>
        <x-footer-image-plays />
    </x-container>
   
</x-layout>
<x-js-lib />
{{-- <x-script-functions-3d /> --}}
<script type="text/javascript">
function checkCheckamount(pmnt_method,amt){
    let state = true;
    const min_ci_50 = 50;
    const min_ci_100 = 100;
    //check input methods
    if(pmnt_method == 'gcash' ||  pmnt_method == 'gcash_p2p_qr'){
        $(".sel-pmnt-note").text("Minimum amount is ₱"+min_ci_100+".00");
        if(amt < min_ci_100){
            state = false;
        }
    }
    if(pmnt_method == 'gcash_p2p' || pmnt_method == 'paymaya_1' ){
        $(".sel-pmnt-note").text("Minimum amount is ₱"+min_ci_50+".00");
        if(amt < min_ci_50){
            state = false;
        }
    }
    if(pmnt_method == 'gcash_qr' || pmnt_method == 'paymaya_2'){
        $(".sel-pmnt-note").text("Minimum amount is ₱"+min_ci_100+".00");
        if(amt < min_ci_100){
            state = false;
        }
    }
    if(pmnt_method == 'manual'){
        
        if(amt < min_ci_50){
            $(".sel-pmnt-note").text("Minimum amount is ₱"+min_ci_50+".00");
            state = false;
        } 
        if ($('#payment_file_upload').get(0).files.length === 0) {
            $(".sel-pmnt-note").text("Please input amount matching with receipt to upload. Unmatched amount will not be processed.");

            state = false;
        }
    }
    //check if amount field is filled
    if(amt == '' || amt === null){
        state = false;
    }
    
    
    return state;
}
function checkDetails(){
    $('.pay').attr('disabled',true);
    let pmnt_method = $("#pg").val();
    let manual_container = $(".manual-container");
    let amt = $("#amount").val();
    let pay_button;
    manual_container.hide();
   

    if(pmnt_method == '' || pmnt_method === null){
        $(".sel-pmnt").text("Please select payment method.");
    } else if(pmnt_method == 'manual'){
        manual_container.show();
    }  
    pay_button = checkCheckamount(pmnt_method,amt);
    console.log(pay_button);
    
    if(pay_button !== false){
        $('.pay').removeAttr('disabled');
    }
    
}
    $(function () {
        $('#pg').change(function() {
            checkDetails();
        });
        $('#amount').on('keyup',function() {
            checkDetails();
        });

        $('#payment_file_upload').on('change', function() {
            checkDetails();
        });

        
    });
</script>
{{-- <x-script-preventer /> --}}
<x-digits-only />

<script type="text/JavaScript">
$(document).ready(function() {
  $("#amount").inputFilter(function(value) {
    return /^\d*$/.test(value);    // Allow digits only, using a RegExp
  },"Only digits allowed");
});
</script>