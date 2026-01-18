<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class Handler extends ExceptionHandler
{

    public function render($request, Throwable $exception)
{
    // Redirect jika terjadi error 419 (TokenMismatch)
    if ($exception instanceof TokenMismatchException) {
        return redirect()->route('tasks.index');
    }

    // Redirect jika halaman tidak ditemukan (404)
    if ($exception instanceof NotFoundHttpException) {
        return redirect()->route('tasks.index');
    }

    return parent::render($request, $exception);
}
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
