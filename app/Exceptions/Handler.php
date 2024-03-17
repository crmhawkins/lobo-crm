<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Livewire\Exceptions\PublicPropertyNotFoundException;
use Illuminate\Database\QueryException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use ErrorException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function report(Throwable $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        // Crea un canal de log personalizado para errores
        $log = new Logger('errors');
        $log->pushHandler(new StreamHandler(storage_path('logs/custom_error_log.log'), Logger::DEBUG));

        // Registra el error en tu archivo personalizado
        $log->error($exception->getMessage(), ['exception' => $exception]);

        parent::report($exception);
    }
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ErrorException) {
            // Puedes agregar aquí lógica adicional, como generar un código de error único
            // y almacenarlo en la sesión para mostrarlo en la vista.
            // Session::put('error_code', 'UNIQUE_ERROR_CODE');

            return response()->view('errors.custom_error', [], 500);
        }
        if ($exception instanceof QueryException) {
            // Manejo para errores de base de datos
            return response()->view('errors.custom_error', ['message' => 'Ha ocurrido un problema al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.'], 500);
        }

        if ($exception instanceof PublicPropertyNotFoundException) {
            // Verificar si la solicitud es de Livewire
            if ($request->header('X-Livewire')) {
                return response()->view('errors.custom_error', ['message' => 'Ha ocurrido un error inesperado.'], 500); // Código 422 Unprocessable Entity
            } else {
                // Para solicitudes normales no Livewire
                return response()->view('errors.custom_error', ['message' => 'Ha ocurrido un error inesperado.'], 500);
            }
        }

        return parent::render($request, $exception);
    }
}
