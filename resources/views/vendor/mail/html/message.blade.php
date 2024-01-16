@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
    <table>
        <tbody>
            <tr>
                <td>
                    <a href="https://www.itelfingen.ch/"><img src="https://itelfingen.ch/img/mail/icon_link.svg" class="logo img-header"
                                                              width="40px" alt="Ferienhaus Itelfingen"/></a>
                </td>
                <td>
                    <a href="mailto:verwalter@itelfingen.ch"><img src="https://itelfingen.ch/img/mail/icon_mail.svg" class="logo img-header"
                                                                  width="40px" alt="Mail"/></a>
                </td>
                <td>
                    <a href="https://www.instagram.com/itelfingen/"><img src="https://itelfingen.ch/img/mail/icon_instagram.svg" class="logo img-header"
                                                                         width="40px" alt="Instagram - Ferienhaus Itelfingen"/></a>
                </td>
                <td>
                    <a href="https://maps.app.goo.gl/nDkcMkAQLsQPfp557"><img src="https://itelfingen.ch/img/mail/icon_google.svg" class="logo img-header icon-color"
                                                                             width="40px" alt="Google - Ferienhaus Itelfingen"/></a>
                </td>
            </tr>
        </tbody>
    </table>
    <hr>
    <br>
    <b>Ferienhaus Itelfingen</b><br>
    Itelfingen 3 - 6344 Meierskappel <br>
    Mail: <a href="mailto:verwalter@itelfingen.ch">verwalter@itelfingen.ch</a><br>
    <a href="https://www.itelfingen.ch/">https://www.itelfingen.ch/</a><br>
@endcomponent
@endslot
@endcomponent
