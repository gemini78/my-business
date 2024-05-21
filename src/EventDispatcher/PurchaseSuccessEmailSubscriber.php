<?php

namespace App\EventDispatcher;

use App\Entity\User;
use App\Event\PurchaseSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger, private MailerInterface $mailer, private Security $security)
    {
    }
    public static function getSubscribedEvents(): array
    {
        return [
            'purchase.success' => [
                ['sendSuccessEmail']
            ]
        ];
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        /** @var User */
        $currentUser = $this->security->getUser();
        $purchase = $purchaseSuccessEvent->getPurchase();

        $email = new TemplatedEmail();
        $email
            ->from(new Address($currentUser->getEmail(), $currentUser->getFullName()))
            ->to("admin@gmail.com")
            ->htmlTemplate("emails/purchase_success.html.twig")
            ->subject("Bravo, votre commande {$purchase->getId()} a bien été confirmée")
            ->context([
                'purchase' => $purchase,
                'user' => $currentUser
            ]);
        $this->mailer->send($email);
        // Pour simuler l'envoi d'un mail lors d'une prise de commande
        $this->logger->info("Email envoyé pour la commande N° : " . $purchase->getId());
    }
}
