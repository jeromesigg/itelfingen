<!-- ======= Header ======= -->
<header id="header" class="fixed-top">
    <div class="container d-flex align-items-center">

        {{-- <h2 class="logo mr-auto"><a href="" class="scrollto">Ferienhaus Itelfingen</a></h2> --}}
        <a href="#header" class="logo mr-auto me-lg-0"><img src="img/logo.png" alt="" class="img-fluid"></a>
        <nav class="nav-menu d-none d-lg-block">
            <ul id="top-menu">
                {{-- <li><a href="/#rooms">Details</a></li>
                <li><a href="#gallery">Fotos</a></li>
                <li><a href="#pricelist">Preisliste</a></li>
                <li><a href="#booking">Belegungsplan</a></li>
                <li><a href="#locations">Ausflugtipps</a></li>
                <li><a href="#history">Historisches</a></li>
                <li><a href="#contact">Kontakt</a></li> --}}
                <li class="text-center book"><a href="#booking">Jetzt Buchen</a></li>
                @guest
                @else
                    <li><a href="/admin" target="blank">Dashboard</a></li>            
               
                    <!-- Authentication Links -->
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->username }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </nav><!-- .nav-menu -->

    </div>
</header><!-- End Header -->