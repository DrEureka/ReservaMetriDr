<?php

namespace App\Providers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app['view']->addNamespace('mail', resource_path('views/vendor/mail'));

        Mail::extend('smtp_relaja', function (array $config) {
            $factory = new EsmtpTransportFactory();

            $esquema = $config['scheme'] ?? ($config['port'] == 465 ? 'smtps' : 'smtp');

            $transporte = $factory->create(new Dsn(
                $esquema,
                $config['host'],
                $config['username'] ?? null,
                $config['password'] ?? null,
                $config['port'] ?? null,
                $config
            ));

            $corriente = $transporte->getStream();
            if ($corriente instanceof SocketStream) {
                $opcionesSsl = $corriente->getStreamOptions()['ssl'] ?? [];
                $opcionesSsl['verify_peer']      = false;
                $opcionesSsl['verify_peer_name'] = false;
                $corriente->setStreamOptions(['ssl' => $opcionesSsl]);
            }

            return $transporte;
        });
    }
}
