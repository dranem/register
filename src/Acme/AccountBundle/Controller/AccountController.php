<?php
// src/Acme/AccountBundle/Controller/AccountController.php
namespace Acme\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

    public function loginAction($activationLink = null)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AcmeAccountBundle:User')->findOneBy(
            array('activationLink' => $activationLink, 'active' => 1)
        );

        if($user) 
            $msg = 'You have successfully activated your account.';
        else 
            $msg = '';

        $user->setActivationLink(null);
        $em->flush();
        
        return $this->render(
            'AcmeAccountBundle:Account:login.html.twig',
            array('msg' => $msg));
    }

    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new RegistrationType(), new Registration());

        $form->handleRequest($request);

        if ($form->isValid()) {

            $registration = $form->getData();

            $activationLink = $registration->getUser()->getEmail();
            $registration->getUser()->setActivationLink($activationLink);
            $em->persist($registration->getUser());
            $em->flush();

            return new Response('Account created');
        }

        return $this->render(
            'AcmeAccountBundle:Account:register.html.twig',
            array('form' => $form->createView())
        );
        
    }

    public function activateAction($activationLink) {
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AcmeAccountBundle:User')->findOneByactivationLink($activationLink);

        if (!$user) {
            throw $this->createNotFoundException(
                'Invalid Link'
            );
        }

        $user->setActive(1);
        $em->flush();

        return $this->redirectToRoute('account_login',array('activationLink' => $activationLink));
    }

    public function sendEmail($user) 
    {
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