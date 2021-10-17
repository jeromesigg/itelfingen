<!-- Side Navbar -->
<nav class="side-navbar">
    <div class="side-navbar-wrapper">
      <!-- Sidebar Header    -->
        <div class="sidenav-header d-flex align-items-center justify-content-center">
            <!-- User Info-->
            <div class="sidenav-header-inner text-center">
                <h2 class="h5">{{Auth::user()->username}}</h2>
            </div>
            <!-- Small Brand information, appears on minimized sidebar-->
            <div class="sidenav-header-logo"><a href="/admin" class="brand-small text-center"> <strong>L</strong><strong class="text-primary">H</strong></a></div>
        </div>
      

        <!-- Sidebar Navigation Menus-->
        <div class="main-menu">
            <h5 class="sidenav-heading">Itelfingen</h5>
            <ul id="side-main-menu" class="side-menu list-unstyled">                  
                <li><a href="/admin"> <i class="fas fa-home"> </i> Dashboard</a></li>  
                <li><a href="/" target="blank"> <i class="fas fa-home"></i> Zur Seite</a></li>
            </ul>
            
        </div>
        <div class="admin-menu">
            <h5 class="sidenav-heading">Hausverwaltung</h5>
            <ul id="side-main-menu" class="side-menu list-unstyled"> 
                <li>
                    <a  href="{{route('contacts.index')}}" ><i class="fas fa-user"></i> Anfragen</a>
                </li>  
                <li>
                    <a href="#EventsDropdown" aria-expanded="false" data-toggle="collapse"> <i class="far fa-calendar-alt"></i> Buchungen</a>
                    <ul id="EventsDropdown" class="collapse list-unstyled ">
                        <li>
                            <a href="{{route('events.index')}}"> Buchungen</a>
                        </li>
                        <li>
                            <a href="{{route('events.create')}}"> Buchung erstellen</a>
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                </li> 
                <li>
                    <a href="#AlbumsDropdown" aria-expanded="false" data-toggle="collapse"> <i class="fas fa-images"></i> Fotoalbum</a>
                    <ul id="AlbumsDropdown" class="collapse list-unstyled ">
                        <li>
                            <a href="{{route('pictures.index')}}"> Fotos</a>
                        </li>
                        <li>
                            <a href="{{route('pictures.create')}}"> Fotos erstellen</a>
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>       
           

            </ul>
        
        <h5 class="sidenav-heading">Administration</h5>
        <ul id="side-main-menu" class="side-menu list-unstyled">     
        @if (Auth::user()->isAdmin())
                <li><a href="#HomepageDropdown" aria-expanded="false" data-toggle="collapse"> <i class="fas fa-clipboard-list"></i> Homepage</a></li>
                <ul id="HomepageDropdown" class="collapse list-unstyled ">
                    <li>
                        <a href="{{route('homepages.index')}}"> Homepage</a>
                    </li>
                    <li>
                        <a href="{{route('homepages.edit',1)}}"> Homepage Anpassen</a>
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>
                <li>
                    <a  href="{{route('testimonials.index')}}" ><i class="far fa-comment"></i> Bewertungen</a>
                </li>  
                <li>
                    <a  href="{{route('people.index')}}" ><i class="fas fa-user-friends"></i> Personen</a>
                </li> 
                <li>
                    <a  href="{{route('histories.index')}}" ><i class="fas fa-history"></i> Geschichte</a>
                </li> 
        @endif
        <li>
            <a  href="{{route('users.index')}}" ><i class="fas fa-user"></i> Benutzer</a>
        </li> 
        <li><a href="/admin/changes"><i class="fas fa-clipboard-list"></i> Änderungen</a></li>
        </ul>

        </div>
            
    </div>
</nav>

