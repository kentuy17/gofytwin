@extends('layouts.app')
@section('title','3D Winners')
@section('styles')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endsection
@section('content')
<div class="container-fluid">
    <div class="button-container  p-2 my-2">
        <form method="GET" action="">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="input-group align-items-center">
                    <label class="draw-label mx-1">Date</label>
                    <input type="text" class="form-control" name="date" id="datepicker" value="<?= isset($_GET['date']) ? $_GET['date'] : date('Y-m-d') ?>"/>
                    <select class="form-control mx-1" name="draw_time">
                       <option disabled selected>Draw Time</option>
                       @foreach($draw_types as $draw)
                       <option value="<?=$draw->draw?>" <?= (isset($_GET['draw_time']) && $draw->draw == $_GET['draw_time']) ? 'selected' : '' ?>>{{$draw->draw}}</option>
                       @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary mx-2">Filter</button>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <h1>3D Winners</h1>
    </div>
    <x-reports />
</div>
@endsection

<x-js-lib />
<script type="text/javascript">
    $(function () {
        var table = $('.winning-results').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{route('winners.three')}}?date=<?=isset($_GET['date'])?$_GET['date']:date('Y-m-d')?>&draw_time=<?=isset($_GET['draw_time'])?$_GET['draw_time']:''?>",
                columns: [
                    {data: 'receiver_name'},
                    {data: 'contact_no'},
                    {data: 'reference_no'},
                    {data: 'receiver_address'},
                    {data: 'receiver_mobile_number'},
                    {data: 'payout_remittance'},
                    {data: 'bet_amount'},
                    {data: 'winning_combination'},
                    {data: 'winning_type'},
                    {data: 'winning_amount'},
                    {data: 'draw_time'},
                    {data: 'combination_type'},
                    {data: 'draw_date'},
                    {data: 'receipt'},
                    {data: 'claimed'},
                    {data: 'upload'},
                    {data: 'approved'},
                    {data: 'status'},
                    {data: 'message'},
                ],
                order: [[12, 'desc']]
            });
            $("#datepicker").datepicker({
                dateFormat: 'yy-mm-dd'
            });
        });
  </script>
@section('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
@endsection
