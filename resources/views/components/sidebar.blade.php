@if (auth()->user()->user_level > 50)
    <li class="nav-item dropdown {{ (request()->is('admin/claim/requests/list') 
                                || request()->is('admin/claim/requests') ) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Withdrawals
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{route('withdrawal')}}">Requests</a></li>
        <li><a class="dropdown-item" href="{{route('withdrawal.list')}}">List</a></li>
      </ul>
    </li>
 
    <li class="nav-item">
      <a class="nav-link" href="{{route('admin.cashin')}}">
        Cash-In List
      </a>
    </li>
    {{-- <li class="nav-item">
      <a class="nav-link" href="{{route('admin.net.income')}}">
        Net Income
      </a>
    </li> --}}
    <li class="nav-item">
      <a class="nav-link" href="{{route('admin.total.gross')}}">
        Total Gross
      </a>
    </li>
    {{-- Nav item --}}
    {{-- End Nav item --}}
    {{-- Nav item --}}
    <li class="nav-item">
      <a class="nav-link" href="{{route('users.list')}}">
        Users List
      </a>
    </li>
    {{-- End Nav item --}}
    {{-- Nav item --}}
    <li class="nav-item">
      <a class="nav-link" href="{{route('users.otp.list')}}">
        OTP List
      </a>
    </li>
    {{-- End Nav item --}}
    {{-- Nav item --}}
    <li class="nav-item">
      <a class="nav-link" href="{{route('spam')}}">
        Spam List
      </a>
    </li>
    {{-- End Nav item --}}
    {{-- Nav item --}}
    <li class="nav-item">
      <a class="nav-link" href="{{route('statistics')}}">
        Statistics Page
      </a>
    </li>
    {{-- End Nav item --}}
@endif
