@extends('layouts.app')
@section('title','Statistics Report')
@section('styles')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endsection
@section('content')
<div class="container-fluid">
    {{-- <div class="row">
        <h1>Statistics Report</h1>
        <div class="col-12">
            <div class="button-container  p-2 my-2">
                <form method="GET" action="">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-group align-items-center">
                            <label class="draw-label mx-1">From</label>
                            <input type="text" class="form-control datepicker from"  name="from" value="<?= isset($_GET['from']) ? $_GET['from'] : date('Y-m-d');  ?>"/>
                            <label class="draw-label mx-1">To</label>
                            <input type="text" class="form-control datepicker to" name="to" value="<?= isset($_GET['to']) ? $_GET['to'] : date('Y-m-d') ?>"/>
                            <button type="submit" class="btn btn-primary mx-2 rounded-0">Filter</button>
                        </div>
                    </div>
                </form>
                <p class="draw-label total-amounts mt-4">Date Range: <span class="text-success">{{$from .' - '. $to}}</span></p>
            </div>
        </div>
        <div class="col-4">
            <div class="card widget-flat">
                <a href="{{route('gross')}}" class="reports-links">
                    <div class="card-body">
                        <h1 class="mt-3 mb-3"><span class="bi text-danger bi-currency-dollar"></span><span id="gross_amount">Fetching...</span></h1>
                        <h5 class="text-white fw-normal mt-0" title="Number of Customers">Gross Income</h5>
                    </div> <!-- end card-body-->
                </a>
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-4">
            <div class="card widget-flat">
                <a href="{{route('admin.cashin')}}" class="reports-links">
                    <div class="card-body">
                        <h1 class="mt-3 mb-3"><span class="bi text-danger bi-currency-dollar"></span><span id="daily_ci">Fetching...</span></h1>
                        <h5 class="text-white fw-normal mt-0" title="Number of Customers">Daily Cash Ins</h5>
                    </div> <!-- end card-body-->
                </a>
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-4">
            <div class="card widget-flat">
                <a href="{{route('won amount')}}" class="reports-links">
                    <div class="card-body">
                        <h1 class="mt-3 mb-3"><span class="bi text-danger bi-currency-dollar"></span><span id="daily_won_amount">Fetching...</span></h1>
                        <h5 class="text-white fw-normal mt-0" title="Number of Customers">Daily Won Amount</h5>
                    </div> <!-- end card-body-->
                </a>
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div> --}}

    {{-- <div class="row mt-4">
        <div class="col-4">
            <div class="card widget-flat">
                    <div class="card-body">
                        <h1 class="mt-3 mb-3"><span class="bi bi-cash-stack text-danger"></span> <span id="total_bets">Fetching...</span></h1>
                        <h5 class="text-white fw-normal mt-0" title="Number of Customers">Daily Bets</h5>
                    </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

        
        <div class="col-4">
            <div class="card widget-flat">
                <a href="{{route('users')}}" class="reports-links">
                    <div class="card-body">
                        <h1 class="mt-3 mb-3"><i class="bi bi-person-workspace text-danger"></i> <span id="total_users">Fetching...</span></h1>
                        <h5 class="text-white fw-normal mt-0" title="Number of Customers">Total Users</h5>
                    </div> <!-- end card-body-->
                </a>
            </div> <!-- end card-->
        </div> <!-- end col-->



        <div class="col-4">
            <div class="card widget-flat">
                <a href="{{route('prizes.claimed')}}" class="reports-links">
                    <div class="card-body">
                        <h1 class="mt-3 mb-3"><i class="bi bi-gift-fill text-danger"></i> {{$total_claimed}}</h1>
                        <h5 class="text-white fw-normal mt-0" title="Number of Customers">Prizes Claimed</h5>
                    </div> <!-- end card-body-->
                </a>
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div> --}}

    {{-- <div class="row mt-4">
        <div class="col-4">
            <div class="card widget-flat">
                <div class="card-body">
                    <h1 class="mt-3 mb-3"><span class="bi text-danger bi-currency-dollar"></span><span id="gcash_amount">Fetching...</span></h1>
                    <h5 class="text-white fw-normal mt-0" title="Number of Customers">Total GCash Amount</h5>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-4">
            <div class="card widget-flat">
                <div class="card-body">
                    <h1 class="mt-3 mb-3"><span class="bi text-danger bi-currency-dollar"></span><span id="paymaya_amount">Fetching...</span></h1>
                    <h5 class="text-white fw-normal mt-0" title="Number of Customers">Total PayMaya Amount</h5>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->



        <div class="col-4">
            <div class="card widget-flat">
                <a href="{{route('winners')}}" class="reports-links">
                    <div class="card-body">
                        <h1 class="mt-3 mb-3"><span class="bi bi-credit-card text-danger"></span> {{$total_winners}}</h1>
                        <h5 class="text-white fw-normal mt-0" title="Number of Customers">Total Winners</h5>
                    </div> <!-- end card-body-->
                </a>
            </div> <!-- end card-->
        </div> <!-- end col-->

        
    </div> --}}

    {{-- <div class="row mt-4">
        <div class="col-4">
            <div class="card widget-flat">
                <div class="card-body">
                    <h1 class="mt-3 mb-3"><span class="bi text-danger bi-currency-dollar"></span><span id="gcash_qr">Fetching...</span></h1>
                    <h5 class="text-white fw-normal mt-0" title="Number of Customers">Gcash QR Amount</h5>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->
        <div class="col-4">
            <div class="card widget-flat">
                <a href="{{route('admin.cashin')}}" class="reports-links">
                    <div class="card-body">
                        <h1 class="mt-3 mb-3"><span class="bi bi-cash-coin text-danger"></span> <span id="total_cashin">Fetching...</span></h1>
                        <h5 class="text-white fw-normal mt-0" title="Number of Customers">Total Players Cashin</h5>
                    </div> <!-- end card-body-->
                </a>
            </div> <!-- end card-->
        </div> <!-- end col-->
        <div class="col-4">
            <div class="card widget-flat">
                <a href="{{route('withdrawal')}}" class="reports-links">
                    <div class="card-body">
                        <h1 class="mt-3 mb-3"><span class="bi bi-cart4 text-danger"></span> <span id="withdrawal_request">Fetching...</span></h1>
                        <h5 class="text-white fw-normal mt-0" title="Number of Customers">Withdrawal Requests</h5>
                    </div> <!-- end card-body-->
                </a>
            </div> <!-- end card-->
        </div> <!-- end col-->
         
    </div> --}}
   
    {{-- <div class="row mt-4">
        
        <div class="col-4">
            <div class="card widget-flat">
                    <div class="card-body">
                        <h1 class="mt-3 mb-3"><span class="bi bi-currency-exchange text-danger"></span> <span id="total_bettors">Fetching...</span></h1>
                        <h5 class="text-white fw-normal mt-0" title="Number of Customers">Daily Active Bettors</h5>
                    </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->
       
        
    </div> --}}

    {{-- <div class="row mt-4">
        <div class="col-4">
            <div class="card widget-flat">
                <a href="{{route('admin.cashin')}}" class="reports-links">
                    <div class="card-body">
                        <h1 class="mt-3 mb-3"><span class="bi bi-person-x text-danger"></span> <span id="inactive_players">Fetching...</span></h1>
                        <h5 class="text-white fw-normal mt-0" title="Number of Customers">Total Inactive Players ($40)</h5>
                    </div> <!-- end card-body-->
                </a>
            </div> <!-- end card-->
        </div> <!-- end col-->
        <div class="col-4">
            <div class="card widget-flat">
                <a href="{{route('admin.cashin')}}" class="reports-links">
                    <div class="card-body">
                        <h1 class="mt-3 mb-3"><span class="bi bi-person-plus text-danger"></span> <span id="new_registers">Fetching...</span></h1>
                        <h5 class="text-white fw-normal mt-0" title="Number of Customers">Total New Players  ($20)</h5>
                    </div> <!-- end card-body-->
                </a>
            </div> <!-- end card-->
        </div> <!-- end col-->
        <div class="col-4">
            <div class="card widget-flat">
                <div class="card-body">
                    <h1 class="mt-3 mb-3"><span class="bi text-danger bi-currency-dollar"></span><span id="paymaya_qr">Fetching...</span></h1>
                    <h5 class="text-white fw-normal mt-0" title="Number of Customers">PayMaya QR Amount</h5>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div> --}}


    {{-- <div class="row mt-4">
        <div class="col-4">
            <div class="card widget-flat">
                    <div class="card-body">
                        <h1 class="mt-3 mb-3"><span class="bi bi-person-plus text-danger"></span> <span id="daily_new_registers">Fetching...</span></h1>
                        <h5 class="text-white fw-normal mt-0" title="Number of Customers">Total Daily New Registered Players</h5>
                    </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div> --}}

    <div class="row mt-4">
        <div class="col-4">
            <div class="card widget-flat">
                <div class="card-body">
                    <h1 class="mt-3 mb-3"><span class="bi text-danger bi-currency-dollar"></span><span id="total_pg1_wallet">Fetching...</span></h1>
                    <h5 class="text-white fw-normal mt-0" title="Number of Customers">PG1 Wallet</h5>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->


        <div class="col-4">
            <div class="card widget-flat">
                <div class="card-body">
                    <h1 class="mt-3 mb-3"><span class="bi text-danger bi-currency-dollar"></span><span id="total_wallet">Fetching...</span></h1>
                    <h5 class="text-white fw-normal mt-0" title="Number of Customers">TOTAL WALLET MONEY</h5>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

        

    </div>
    
</div>
@endsection
<script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
<x-statistics-ajax />
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>