@extends('layouts.app')
@section('title','Total Gross')
@section('styles')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endsection
@section('content')
<div class="container-fluid">
    <div class="button-container  p-2 my-2">
        <form method="GET" action="">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="input-group align-items-center">
                    <div class="input-group align-items-center my-2">
                        <label class="draw-label mx-1">From</label>
                        <input type="text" class="form-control datepicker"  name="from" value="<?= isset($_GET['from']) ? $_GET['from'] : date('Y-m-d', strtotime(date('Y-m-d')));  ?>"/>
                        <label class="draw-label mx-1">To</label>
                        <input type="text" class="form-control mx-1 datepicker" name="to" value="<?= isset($_GET['to']) ? $_GET['to'] : date('Y-m-d') ?>"/>
                    </div>
                    <button type="submit" class="btn btn-primary mx-2">Filter</button>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <h1>Total Gross</h1>
    </div>
    <div class="row">
        <table class="table ci">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fight No.</th>
                    <th>Bet Side</th>
                    <th>Result</th>
                    <th>Total Win ($)</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                 
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-row justify-content-between">
                <div>
                    <p class="draw-label total-amounts">Total Gross: $<span class="text-success">{{number_format($total_sales,2,'.',',')}}</span></p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-row justify-content-between">
                <div>
                    <p class="draw-label total-amounts text-primary">Total Gross Meron: $<span class="text-success">{{number_format($total_sales,2,'.',',')}}</span></p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-row justify-content-between">
                <div>
                    <p class="draw-label total-amounts text-danger">Total Gross Wala: $<span class="text-success">{{number_format($total_sales,2,'.',',')}}</span></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<x-js-lib />
<x-datepicker />
<script type="text/javascript">
    $(function () {
        // var table = $('.ci').DataTable({
        //         "pageLength": 1000,
        //         "lengthMenu": [ [1000, 2000, -1], [1000, 2000,'All'] ],
        //         order: [[0, 'desc']],
        //     });
        var table = $('.ci').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                        "url": "{{route('cashin.info')}}",
                        "data": function ( d ) {
                            d.from = "<?= isset($_GET['from']) ? $_GET['from'] : date('Y-m-d', strtotime(date('Y-m-d')));  ?>";
                            d.to = "<?= isset($_GET['to']) ? $_GET['to'] : date('Y-m-d', strtotime(date('Y-m-d')));  ?>";
                            // d.custom = $('#myInput').val();
                            // etc
                        }
                },
                columns: [
                    {data: 'id'},
                    {data: 'mobile_no'},
                    {data: 'merchant_order_no'},
                    {data: 'gateway'},
                    {data: 'amount'},
                    {data: 'created_at'}
                   
                ],
                "pageLength": 100,
                "lengthMenu": [ [100, 500, 1000, -1], [100, 500, 1000,'All'] ]
            });
        
            $('.modal-close').on('click',function(){
                $('.modal_notif').modal('hide');
            });
     
            $("#datepicker").datepicker({
                dateFormat: 'yy-mm-dd'
            });
    });
</script>
@section('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
@endsection
