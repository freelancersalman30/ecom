<div class="customer-sidebar">
    <div class="customer-auth">
        <div class="customer-img">
            <div class="img-container">
                @if(Auth::guard('customer')->user()->image)
                    <img src="{{asset(Auth::guard('customer')->user()->image)}}" alt="">
                @else
                    <img src="{{asset('frontEnd/images/user.png')}}" alt="">
                @endif
            </div>
        </div>
        <div class="customer-name">
            <p><small>Welcome back,</small></p>
            <p>{{Auth::guard('customer')->user()->name}}</p>
        </div>
    </div>
    <div class="sidebar-menu">
        <ul>
            <li>
                <a href="{{route('customer.account')}}" class="{{request()->is('customer/account')?'active':''}}">
                    <i data-feather="grid"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{route('customer.orders')}}" class="{{request()->is('customer/orders')?'active':''}}">
                    <i data-feather="shopping-bag"></i> My Orders
                </a>
            </li>
            <li>
                <a href="{{route('customer.profile_edit')}}" class="{{request()->is('customer/profile-edit')?'active':''}}">
                    <i data-feather="user"></i> Edit Profile
                </a>
            </li>
            <li>
                <a href="{{route('customer.change_pass')}}" class="{{request()->is('customer/change-password')?'active':''}}">
                    <i data-feather="lock"></i> Change Password
                </a>
            </li>
            <li>
                <a href="{{ route('customer.logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form-acc').submit();">
                    <i data-feather="log-out"></i> Logout
                </a>
            </li>
            <form id="logout-form-acc" action="{{ route('customer.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </ul>
    </div>
</div>