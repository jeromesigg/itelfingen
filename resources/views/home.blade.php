@extends('layouts.app')

@section('content')

  @include('includes.header')

  @include('includes.title')

  <div id="app">

    @include('contents.main')
        <!-- ======= Footer ======= -->
    <footer id="footer">

      <div class="container">
        <div class="credits">
          <a href="{{route('impressum')}}">Impressum</a> | <a href="{{ route('login') }}">Login</a>
        </div>
      </div>
    </footer><!-- End Footer -->
    <div id="preloader"></div>
    <a href="#" class="back-to-top"><i class="bx bx-up-arrow-alt"></i></a>
  </div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js" integrity="sha512-0QbL0ph8Tc8g5bLhfVzSqxe9GERORsKhIn1IrpxDAgUsbBGz/V7iSav2zzW325XGd1OMLdL4UiqRJj702IeqnQ==" crossorigin="anonymous"></script>

 <!-- Typeahead.js Bundle -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>

<script src="{{ asset('js/main.js') }}"></script>

<script type="text/javascript">

  //autocomplete script
  $(document).on('focus','.autocomplete_txt',function(){
  type = $(this).attr('name');

  if(type =='city')autoType='name';
  if(type =='zipcode')autoType='plz';
  if(type =='city_id')autoType='id';

  $(this).autocomplete({
      minLength: 2,
      highlight: true,
      source: function( request, response ) {
              $.ajax({
                  url: "{{ route('searchajaxcity') }}",
                  dataType: "json",
                  data: {
                      term : request.term,
                      type : type,
                  },
                  success: function(data) {
                      var array = $.map(data, function (item) {
                      return {
                          label: item['plz'] + ' ' + item['name'],
                          value: item[autoType],
                          data : item
                      }
                  });
                      response(array)
                  }
              });
      },
      select: function( event, ui ) {
          var data = ui.item.data;
          $("[name='city']").val(data.name);
          $("[name='zipcode']").val(data.plz);
          $("[name='city_id']").val(data.id);
      }
  });


  });
</script>

  <!-- ======= Javascript Section ======= -->
  @include('contents.gallery_js')
  @include('contents.event_js')
@endsection
