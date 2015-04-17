<?php
// src/Acme/AccountBundle/Controller/AccountController.php
namespace Acme\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Acme\AccountBundle\Form\Type\RegistrationType;
use Acme\AccountBundle\Form\Model\Registration;
use Acme\AccountBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    public function isLogin($user)
    {
        if($user) 
            return true;
        else
            return false;
    }

    public function homeAction(Request $request)
    {
        
        $user = $this->get('session')->get('uid');
        if(!$this->isLogin($user))
            return $this->redirectToRoute('login');
            
        return $this->render(
            'AcmeAccountBundle:Account:home.html.twig',
            array('user' => $user)
        );
    }

    public function logoutAction() {
        $this->get('session')->clear();
        return $this->render(
            'AcmeAccountBundle:Account:login.html.twig'
        );
    }

    public function registerAction()
    {
        
        $user = $this->get('session')->get('uid');
        if($this->isLogin($user))
            return $this->redirectToRoute('account_home');
        
        $registration = new Registration();

        $form = $this->createForm(new RegistrationType(), $registration, array(
            'action' => $this->generateUrl('account_create'),
        ));

        return $this->render(
            'AcmeAccountBundle:Account:register.html.twig',
            array('form' => $form->createView())
        ); 
    }

    public function doLoginAction(Request $request)
    {
        $user_exist = false;
        $checkpassword = false;

        $user = $this->get('session')->get('uid');
        if($user)
            return $this->redirectToRoute('account_home');

        if ($request->getMethod() == 'POST') {
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $em = $this->getDoctrine()->getManager();
            $user_exist = $em->getRepository('AcmeAccountBundle:User')->findOneBy(
                array('email' => $username, 'active' => 1)
            );

            if($user_exist) {
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($user_exist);
                $checkpassword = $encoder->isPasswordValid($user_exist->getPlainPassword(), $password, $user_exist->getSalt());
            }
            if($checkpassword) {
                $session = $request->getSession();
                $session->start();
                $session->set('uid', $user_exist);
                return $this->redirectToRoute('account_home');
            }
            else
                $this->addFlash('notice', 'Invalid username or password');
        }
    
        return $this->render(
            'AcmeAccountBundle:Account:login.html.twig'
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
            $em->flush();
        }
        
        return $this->render(
            'AcmeAccountBundle:Account:login.html.twig'
        );
    }

    public function createAction(Request $request)
    {
        $user = $this->get('session')->get('uid');
        if($user)
            return $this->redirectToRoute('account_home');

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new RegistrationType(), new Registration());

        $form->handleRequest($request);
        
        if ($form->isValid()) {
   
            $registration = $form->getData();
            $repo = $registration->getUser();

            $repo->setSalt(uniqid(mt_rand())); 
            $activationLink = $repo->getEmail();
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($repo);
            $password = $encoder->encodePassword($repo->getPlainPassword(), $repo->getSalt());
            $repo->setPlainPassword($password);
            $repo->setActivationLink($activationLink);

            $em->persist($repo);
            $em->flush();

            $this->sendEmail($registration->getUser(), $registration->getUser()->getActivationLink());

            $this->addFlash('notice', 'Congratulations, Account created!');
            return $this->render(
                'AcmeAccountBundle:Account:login.html.twig'
            );
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
            return $this->render(
                'AcmeAccountBundle:Account:login.html.twig',
                array('msg' => 'Invalid Link'));
        }

        $user->setActive(1);
        $em->flush();

        return $this->redirectToRoute('account_login',array('activationLink' => $activationLink));
    }

    public function sendEmail($user, $activationLink) 
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
                array('user' => $user, 'activationLink' => $url)
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