<!-- Navbar -->
<nav id="navbar" class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"></a>
        <ul style="list-style: none;" class="my-auto ml-auto row">
{{--            <li class="nav-item col">--}}
{{--                <a class="nav-link active" aria-current="page" href="#">LAN</a>--}}
{{--            </li>--}}
{{--            <li class="nav-item col">--}}
{{--                <a class="nav-link active" aria-current="page" href="#">NOC</a>--}}
{{--            </li>--}}
{{--            <li class="nav-item col">--}}
{{--                <a class="nav-link active" aria-current="page" href="#">DNS</a>--}}
{{--            </li>--}}
            <li style="margin-right: 10px" class="nav-item dropdown col">
                @auth
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>
                    <ul style="left: -13px; min-width: 50px;" class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
                        </li>
                    </ul>
                @endauth

                @guest
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Account
                    </a>
                    <ul style="left: -13px; min-width: 50px;" class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('login') }}">Login</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('register') }}">Register</a></li>
                    </ul>
                @endguest
            </li>


        </ul>
    </div>
</nav>
