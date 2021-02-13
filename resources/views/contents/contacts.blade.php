<section id="contact" class="contact section-bg">
  <div class="container" data-aos="fade-up">

    <div class="section-title">
      <h2>Kontakt</h2>
      <p>Kontaktieren Sie uns</p>
    </div>
  </div>

  <div data-aos="fade-up">
    <iframe style="border:0; width: 100%; height: 350px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2422.4666550428083!2d8.472422258755596!3d47.11371962875789!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47855566f883b183%3A0x806d2ed823fdf24b!2sItelfingen%201%2C%206344%20Meierskappel!5e1!3m2!1sde!2sch!4v1612089698401!5m2!1sde!2sch" frameborder="0" allowfullscreen></iframe>
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
          {!! Form::submit('Sende Nachricht', ['class' => 'btn btn-primary'])!!}
        {!! Form::close()!!}
      </div>
    </div>
  </div>
</section>