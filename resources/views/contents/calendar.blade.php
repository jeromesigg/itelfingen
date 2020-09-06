<section id="booking" class="calendar">
  
  <div class="container" data-aos="fade-up">

    <div class="section-title">
      <h2>Buchungen</h2>
      <p>Buchen Sie gleich Ihren nächsten Aufenthalt bei uns</p>
    </div>

    <div class="container">
      <div class="row">
        <div class="col-lg-4 col-md-12">
          {!! Form::open(['method' => 'POST', 'action'=>'CalendarsController@create', 'id' => 'calendarform']) !!}
            <div class="form-row">
              <div class="col-md-6 form-group">
                {!! Form::label('name', 'Name:') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
              </div>
              <div class="col-md-6 form-group">
                {!! Form::label('firstname', 'Vorname:') !!}
                {!! Form::text('firstname', null, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group">
              {!! Form::label('group_name', 'Gruppe:') !!}
              {!! Form::text('group_name', null, ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="form-group">
              {!! Form::label('email', 'E-Mail:') !!}
              {!! Form::text('email', null, ['class' => 'form-control', 'required', 'email']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('street', 'Strasse:') !!}
                {!! Form::text('street', null, ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="form-row">
              <div class="col-md-3 form-group">
                {!! Form::label('plz', 'PLZ:') !!}
                {!! Form::text('plz', null, ['class' => 'form-control', 'required']) !!}
              </div>
              <div class="col-md-9 form-group">
                {!! Form::label('city', 'Ortschaft:') !!}
                {!! Form::text('city', null, ['class' => 'form-control', 'required']) !!}
              </div>
            </div>
            <div class="form-group">
                {!! Form::label('telephone', 'Telephon:') !!}
                {!! Form::text('telephone', null, ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('comment', 'Bemerkungen:') !!}
                {!! Form::textarea('comment', null, ['class' => 'form-control', 'rows' => 3]) !!}
            </div>
            <div class="form-row">
              <div class="col-md-6 form-group">
                {!! Form::label('start_date', 'Start Datum:') !!}
                {!! Form::date('start_date', null, ['class' => 'form-control', 'required']) !!}
              </div>
              <div class="col-md-6 form-group">
                {!! Form::label('end_date', 'End Datum:') !!}
                {!! Form::date('end_date', null, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div id="datewarn"></div> 
            <div class="form-group">
                {!! Form::submit('Reservieren', ['class' => 'btn btn-primary'])!!}
            </div>
          {!! Form::close()!!}
                  
        </div> 
        <div class="col-lg-8 col-md-12">
          <div id='calendar'></div> 
        </div> 
      </div>
    </div>
  </div>
 
</section>