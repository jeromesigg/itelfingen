<!-- ======= Header ======= -->
<header id="header" class="fixed-top">
    <div class="container d-flex align-items-center">

        <h1 class="logo mr-auto"><a href="{{ url('/') }}">Ferienhaus Itelfingen</a></h1>
        <!-- Uncomment below if you prefer to use an image logo -->
        <!-- <a href="index.html"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->


        <nav class="nav-menu d-none d-lg-block">
            <ul id="top-menu">
                <li class="active"><a href="#header">Home</a></li>
                <li><a href="#why-us">Details</a></li>
                <li><a href="#pricelist">Preisliste</a></li>
                <li><a href="#booking">Belegungen</a></li>
                <li><a href="#gallery">Fotos</a></li>
                <li><a href="#locations">Ausflugtipps</a></li>
                <li><a href="#history">Historisches</a></li>
                <li><a href="#contact">Kontakt</a></li>
                <li class="book-a-table text-center"><a href="#booking">Haus Buchen</a></li>
                @guest
                <li> <a href="{{ route('login') }}">Login</a></li>
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