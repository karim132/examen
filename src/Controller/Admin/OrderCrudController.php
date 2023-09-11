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


class OrderCrudController extends AbstractCrudController
{

    private $entityManager; 
    private $adminUrlGenerator;

     public function __construct(EntityManagerInterface $entityManager , AdminUrlGenerator $adminUrlGenerator )
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
        $preparation = Action:: new('preparation','Préparation en cours','fas fa-box')->linkToCrudAction('preparation')  ->addCssClass('btn btn-primary');
        $delivery = Action:: new('delivery','Livraison en cours','fas fa-truck')->linkToCrudAction('delivery')  ->addCssClass('btn btn-warning');
        $delivered = Action:: new('delivered','Livré','fas fa-truck')->linkToCrudAction('delivery')  ->addCssClass('btn btn-success');
        
    

       return $actions
       ->add('edit', $preparation)
       ->add('edit', $delivery)
       ->add('edit', $delivered)
       ->add('index','detail');
       

     

    }

    public function preparation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setStatus(2);
        $this->entityManager->flush();

        $this->addFlash('notice',"<span style= 'color:green;'><strong>La commande ".$order->getReference()." est en cours de préparation </strong></span>");

        $url = $this->adminUrlGenerator
        ->setController(OrderCrudController::class)
        ->setAction('index')
        ->generateUrl();

        return $this->redirect($url);
       
    }

    public function delivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setStatus(3);
        $this->entityManager->flush();

        $this->addFlash('notice',"<span style= 'color:green;'><strong>La commande ".$order->getReference()." est en cours de livraison</strong></span>");

        $url = $this->adminUrlGenerator
        ->setController(OrderCrudController::class)
        ->setAction('index')
        ->generateUrl();

        return $this->redirect($url);
       
    }

    
     public function configureFields(string $pageName): iterable
     {
         return [
             IdField::new('id')->hideOnForm(),
             DateTimeField::new('createdAt',label:'Passée le'),
             TextField::new('user.firstname', label: 'Prénom'),
             TextField::new('user.lastname', label: 'Nom'),
             TextEditorField:: new('delivery','Adresse de livraison')->onlyOnDetail(),
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
