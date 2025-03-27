@if (session()->has('success'))
<div x-data="{show: true}" x-init="setTimeout(() => show = false, 3000)" x-show="show" class="alert alert-success mb-0" role="alert">
    {{session('success')}}
  </div>
@endif

@if (session()->has('error'))
<div x-data="{show: true}" x-init="setTimeout(() => show = false, 10000)" x-show="show" class="alert alert-danger mb-0" role="alert">
    {{session('error')}}
  </div>
@endif