<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Mailtrap\Helper\ResponseHelper;
use Mailtrap\MailtrapClient;
use Mailtrap\Mime\MailtrapEmail;
use Symfony\Component\Mime\Address;
use App\Services\MailtrapService;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('chat:cleanup')->daily();

Artisan::command('send-mail', function (MailtrapService $mailtrapService) {
    $result = $mailtrapService->sendEmail(
        'pipkon.proyectos@gmail.com',
        '¡Eres increíble!',
        '¡Felicidades por enviar el correo de prueba con Mailtrap!',
        'Prueba de Integración'
    );

    if ($result['success']) {
        $this->info('Email sent successfully!');
    } else {
        $this->error('Error sending email: ' . $result['error']);
    }
    var_dump($result);
})->purpose('Send Mail');
