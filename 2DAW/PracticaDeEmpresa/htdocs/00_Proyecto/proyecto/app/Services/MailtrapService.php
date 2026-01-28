<?php

namespace App\Services;

use Mailtrap\MailtrapClient;
use Mailtrap\Mime\MailtrapEmail;
use Symfony\Component\Mime\Address;
use Mailtrap\Helper\ResponseHelper;

class MailtrapService
{
    protected $client;

    public function __construct()
    {
        $this->client = MailtrapClient::initSendingEmails(
            apiKey: env('MAILTRAP_API_TOKEN')
        );
    }

    public function sendEmail(string $to, string $subject, string $content, string $category = 'Notification')
    {
        $email = (new MailtrapEmail())
            ->from(new Address('hello@demomailtrap.co', 'Sistema de Gestión'))
            ->to(new Address($to))
            ->subject($subject)
            ->category($category)
            ->text($content);

        try {
            $response = $this->client->send($email);
            return ResponseHelper::toArray($response);
        } catch (\Exception $e) {
            \Log::error("Error enviando email con Mailtrap: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
