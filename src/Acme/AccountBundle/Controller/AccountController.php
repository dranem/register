<?php
// src/Acme/AccountBundle/Controller/AccountController.php
namespace Acme\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\AccountBundle\Form\Type\RegistrationType;
use Acme\AccountBundle\Form\Model\Registration;
use Acme\AccountBundle\Entity\User;
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

        if($user) {
            $this->addFlash('notice', 'You have successfully activated your account.');
            $user->setActivationLink(null);
        }
        
        $em->flush();
        
        return $this->render(
            'AcmeAccountBundle:Account:login.html.twig');
    }

    public function createAction(Request $request)
    {
        //$user = new User();
        

        
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new RegistrationType(), new Registration());

        $form->handleRequest($request);


        if ($form->isValid()) {

            $registration = $form->getData();
            $repo = $registration->getUser();

            //$password = $registration->getUser()->getPlainPassword();
            //$registration->getUser()->setPlainPassword(hash('sha256',$password));
            $repo->setSalt(uniqid(mt_rand())); 
            $activationLink = $repo->getEmail();
            //$activationLink = $registration->getUser()->getEmail();
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($repo);
            $password = $encoder->encodePassword($repo->getPlainPassword(), $repo->getSalt());
            $repo->getPlainPassword($password);
            $repo->setActivationLink($activationLink);
            //$registration->getUser()->setActivationLink($activationLink);

            $em->persist($repo);
            //$em->persist($registration->getUser());
            $em->flush();

            $this->sendEmail($registration->getUser(),$password,$registration->getUser()->getActivationLink());

            $this->addFlash('notice', 'Congratulations, Account created!');
            //return new Response('Account created');
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
            //throw $this->createNotFoundException(
            //    'Invalid Link'
            //);
            
            return $this->render(
                'AcmeAccountBundle:Account:login.html.twig',
                array('msg' => 'Invalid Link'));
        }

        $user->setActive(1);
        $em->flush();

        return $this->redirectToRoute('account_login',array('activationLink' => $activationLink));
    }

    public function sendEmail($user, $password, $activationLink) 
    {
        $from = 'menardjosef.morales@chromedia.com';
        $to = $user->getEmail();
        
        $url = $this->generateUrl('activate_account', array('activationLink' => $activationLink), true);

        $mailer = $this->get('mailer');
        $message = $mailer->createMessage()
        ->setSubject('You have Completed Registration!')
        ->setFrom($from)
        ->setTo($to)
        ->setBody(
            $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                'Emails/registration.html.twig',
                array('user' => $user, 'password' => $password, 'activationLink' => $url)
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