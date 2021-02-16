<section id="contact" class="contact section-bg">
  <div class="container" data-aos="fade-up">

    <div class="section-title">
      <h2>Kontakt</h2>
      <p>Kontaktieren Sie uns</p>
    </div>
  </div>
  @if ($errors->contact->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->contact->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
  @endif
  @if (session()->has('success_contact'))
      <div class="alert alert-dismissable alert-success">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
          <strong>
              {!! session()->get('success_contact') !!}
          </strong>
      </div>
  @endif

  <div data-aos="fade-up">
    <iframe style="border:0; width: 100%; height: 350px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d605.6247635940863!2d8.473077610557123!3d47.11300787321241!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47855566fbd6fd7f%3A0xf9c4a22f086c0f22!2sItelfingen%203%2C%206344%20Meierskappel!5e1!3m2!1sde!2sch!4v1613503423612!5m2!1sde!2sch" frameborder="0" allowfullscreen></iframe>
  </div>                                           
  <div class="container" data-aos="fade-up">

    <div class="row mt-5">


      <div class="col-lg-4">
        <div class="info">
          <div class="address">
            <i class="icofont-google-map"></i>
            <h4>Hausadresse:</h4>
            <p>{!! nl2br($homepage->address) !!}</p>
          </div>

          <div class="email">
            <i class="icofont-envelope"></i>
            <h4>Email:</h4>
            <p>{{$homepage->mail}}</p>
          </div>

          <div class="phone">
            <i class="icofont-phone"></i>
            <h4>Tel. P (abends):</h4>
            <p>{{$homepage->phone}}</p>
          </div>

        </div>

      </div>

      <div class="col-lg-8 mt-5 mt-lg-0">

        {!! Form::open(['method' => 'POST', 'action'=>'ContactController@store' ])!!}
          <div class="form-row">
            <div class="col-md-6 form-group">
              {!! Form::label('name', 'Name:') !!}
              {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="col-md-6 form-group">
              {!! Form::label('email', 'Email:') !!}
              {!! Form::email('email', null, ['class' => 'form-control', 'required']) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('subject', 'Betreff:') !!}
            {!! Form::text('subject', null, ['class' => 'form-control', 'required']) !!}
          </div>
          <div class="form-group">
            {!! Form::label('content', 'Nachricht:') !!}
            {!! Form::textarea('content', null, ['class' => 'form-control', 'required', 'rorws' => 8]) !!}
          </div>
          <div class="form-group">
            {!! Form::captcha($contact_attributes) !!}<br>
            {!! Form::submit('Sende Nachricht', ['class' => 'btn btn-primary',  'id' => 'ContactSubmit', 'disabled' => 'disabled'])!!}
        </div>
        {!! Form::close()!!}
      </div>
    </div>
  </div>
</section>