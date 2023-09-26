<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\Carrier;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Classe\Mail;


class OrderCrudController extends AbstractCrudController
{

    private $entityManager; 
    private $adminUrlGenerator;

     public function __construct(EntityManagerInterface $entityManager , AdminUrlGenerator $adminUrlGenerator)
     {
        $this->entityManager=$entityManager;
        $this->adminUrlGenerator = $adminUrlGenerator;
     }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions$actions):Actions
    {
        //Configure une custom action
        $preparation = Action:: new('preparation','Préparation en cours','fas fa-box')
        //lien avec la function 'preparation'
        ->linkToCrudAction('preparation')->addCssClass('btn btn-primary');
        $delivery = Action:: new('delivery','Livraison en cours','fas fa-truck')->linkToCrudAction('delivery')->addCssClass('btn btn-warning');
        $delivered = Action:: new('delivered','Livré','fas fa-truck')
        ->linkToCrudAction('delivery')->addCssClass('btn btn-success');
        
    

       return $actions
       //ajoute l'action 
       ->add('edit', $preparation)
       ->add('edit', $delivery)
       ->add('edit', $delivered)
       ->add('index','detail');
       

     

    }

    public function preparation(AdminContext $context)
    {
        // récupère la commande 
        $order = $context->getEntity()->getInstance();
        // set le statut
         $order->setStatus(2);       
        //enregistre en bdd
        $this->entityManager->flush();
        
        $this->addFlash('info',
        "La commande ".$order->getReference()." est en cours de préparation");
        
        // initialisation d'une variable avec adminUrlGenerator
        $url = $this->adminUrlGenerator
        ->setController(OrderCrudController::class)
         // redirige vers la page index 
        ->setAction('index')
        ->generateUrl();


        //création d'un nouveau mail
        $mail = new Mail();
        $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),subject:'commande en cours',
        content:"Bonjour ".$order->getUser()->getFirstname().
        "<br/>Votre commande numero <strong>".$order->getReference()."</strong> est en cours de préparation");
        
        return $this->redirect($url);
       
    }

    public function delivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setStatus(3);
        $this->entityManager->flush();

        $this->addFlash('warning',
        "La commande ".$order->getReference()." est en cours de livraison"
        );

        // initialisation d'une variable avec adminUrlGenerator
        $url = $this->adminUrlGenerator
        ->setController(OrderCrudController::class)
        //redirige vers la page index
        ->setAction('index')
        ->generateUrl();


        $mail = new Mail();
        $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),subject:'commande en cours',
        content:"Bonjour ".$order->getUser()->getFirstname().
        "<br/>Votre commande numero <strong>".$order->getReference()."</strong>est en cours de livraison");

        return $this->redirect($url);  
    }

    
     public function configureFields(string $pageName): iterable
     {
         return [
             IdField::new('id')->hideOnForm(),
             DateTimeField::new('createdAt',label:'Passée le'),
             TextField::new('user.firstname', label: 'Prénom'),
             TextField::new('user.lastname', label: 'Nom'),
             TextEditorField::new('delivery','Adresse de livraison')->onlyOnDetail(),
             MoneyField::new('total', label: 'Total commande')->setCurrency('EUR'),
             TextField::new('carrierName', label:'Transporteur'),
             MoneyField::new('carrierPrice', label: 'Frais de port')->setCurrency('EUR'),
             ChoiceField::new('status', label: 'Etat')->setChoices([
                'Non payée'=> 0,
                'Payée'=> 1,
                'Préparation en cours'=> 2,
                'Livraison en cours'=> 3,
            ]),
            ArrayField::new('orderDetails', label:'Produits achetés')->hideOnIndex()
        ];
    }
    
}
