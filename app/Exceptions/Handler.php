<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TokenMismatchException && ! $request->expectsJson()) {
            return $this->formExpired($request);
        }

        return parent::render($request, $exception);
    }

    /**
     * Formular war zu lange offen (CSRF-Token abgelaufen): statt der Fehlerseite 419 zurück zum
     * Formular, mit allen Eingaben und einem Hinweis im Fehlerbereich des Formulars.
     */
    private function formExpired(Request $request): RedirectResponse
    {
        /** @var array<string, string> Formular-Scope => Sprungziel auf der Startseite */
        $anchors = ['event' => '#booking', 'contact' => '#contact'];
        $scope = (string) $request->input('_form_scope');

        return redirect()->to(url()->previous().($anchors[$scope] ?? ''))
            ->withInput($request->except(['_token', ...$this->dontFlash]))
            ->withErrors(
                ['expired' => 'Die Seite war zu lange offen. Deine Eingaben sind noch da – bitte sende das Formular nochmals ab.'],
                array_key_exists($scope, $anchors) ? $scope : 'default'
            );
    }
}
