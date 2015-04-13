<?php
// src/Acme/AccountBundle/Controller/AccountController.php
namespace Acme\AccountBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Acme\AccountBundle\Entity\User;

use Acme\AccountBundle\Form\Type\RegistrationType;
use Acme\AccountBundle\Form\Model\Registration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    public function registerAction()
    {
        $registration = new Registration();

        $form = $this->createForm(new RegistrationType(), $registration, array(
            'action' => $this->generateUrl('account_create'),
        ));

        return $this->render(
            'AcmeAccountBundle:Account:register.html.twig',
            array('form' => $form->createView())
        );

    }

    public function createAction(Request $request)
    {


//$user = new User();
/*
    $form = $this->createFormBuilder($user)
        ->add('email', 'email')
        ->add('plainPassword', 'repeated', array(
           'first_name'  => 'password',
           'second_name' => 'confirm',
           'type'        => 'password',
        ))
        ->add('firstName','text')
        ->add('lastName','text')
        ->add('save', 'submit', array('label' => 'Create user'))
        ->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();
        echo '<pre>';
        print_r($data);
        // perform some action, such as saving the task to the database
        return new Response('sulod created');
        //return $this->redirectToRoute('task_success');
    } 

    return $this->render(
            'AcmeAccountBundle:Account:register.html.twig',
            array('form' => $form->createView())
        );
*/
        

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new RegistrationType(), new Registration());
        //$form = $this->createForm(new UserType(), new User());

        $form->handleRequest($request);

        //$initialData = "John Doe, this field is not actually mapped to Product";
       // $form->get('nonMappedField')->setData($initialData);

        if ($form->isValid()) {

            $registration = $form->getData();
            //echo '<pre>';
            //print_r(Registration.user);exit;
            $activatioinLink = $registration->getUser()->getEmail();
            $registration->getUser()->setActivationLink($activatioinLink);
            $em->persist($registration->getUser());
            $em->flush();

            //$this->sendEmail($registration->getUser());
            return new Response('Account created');
            //return $this->redirectToRoute('account_create');
        }

        return $this->render(
            'AcmeAccountBundle:Account:register.html.twig',
            array('form' => $form->createView())
        );
        
    }

    public function activateAction($activatioinLink) {
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AcmeAccountBundle:User')->findOneByactivationLink($activatioinLink);

        if (!$user) {
            throw $this->createNotFoundError(
                'Invalid Link'
            );
        }

        $user->setActive(1);
        $em->flush();

        return $this->redirectToRoute('account_register');
        //return new Response('<html><body>Hello '.$activatioinLink.'!</body></html>');
    }

    public function sendEmail($user) 
    {
       /*echo '<pre>';
        print_r($user);
        echo $user->getEmail();
        echo 'send';
*/
        $from = 'menardjosef.morales@chromedia.com';
        $to = $user->getEmail();

        $mailer = $this->get('mailer');
        $message = $mailer->createMessage()
        ->setSubject('You have Completed Registration!')
        ->setFrom($from)
        ->setTo($to)
        ->setBody(
            $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                'Emails/registration.html.twig',
                array('user' => 'test')
            ),
            'text/html'
        )
        /*
         * If you also want to include a plaintext version of the message
        ->addPart(
            $this->renderView(
                'Emails/registration.txt.twig',
                array('name' => $name)
            ),
            'text/plain'
        )
        */
    ;
    $mailer->send($message);
    }
}