<?php

namespace App\Twig;
use App\Entity\Category;
use App\Entity\City;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

//Renvoie en globals les noms pour afficher dans le header

class TwigGlobalSubscriber implements EventSubscriberInterface {

    /**
    * @var \Twig\Environment
    */
    private $twig;
    /**
    * @var \Doctrine\ORM\EntityManagerInterface
    */
    private $manager;

    public function __construct( Environment $twig, EntityManagerInterface $manager ) {
        $this->twig    = $twig;
        $this->manager = $manager;
    }

    public function onKernelRequest( RequestEvent $event)
    {

        $categories = $this->manager->getRepository( Category::class )->findAll();
        $this->twig->addGlobal( 'categories', $categories );

        $cities = $this->manager->getRepository( City::class )->findAll();
        $this->twig->addGlobal( 'cities', $cities );
    }

    public static function getSubscribedEvents()
    {
        return [
            // On doit définir une priorité élevée
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}