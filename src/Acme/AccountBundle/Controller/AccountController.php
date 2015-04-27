<?php
// src/Acme/AccountBundle/Controller/AccountController.php
namespace Acme\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints as Assert;
use Acme\AccountBundle\Form\Type\RegistrationType;
use Acme\AccountBundle\Form\Type\LoginType;
use Acme\AccountBundle\Form\Type\UserType;
use Acme\AccountBundle\Form\Type\ForgotPasswordType;
use Acme\AccountBundle\Form\Type\UpdateType;
use Acme\AccountBundle\Form\Type\ChangePasswordType;
use Acme\AccountBundle\Form\Type\ResetPasswordType;
use Acme\AccountBundle\Form\Model\Registration;
use Acme\AccountBundle\Form\Model\ChangePassword;
//use Acme\AccountBundle\Form\Model\UserUpdate;
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
        if(!$this->get('app.manage_controller')->isloginAction())
            return $this->redirectToRoute('login');
            
        return $this->render(
            'AcmeAccountBundle:Account:home.html.twig',
            array('user' => $user)
        );
    }

    public function headerAction(Request $request) {
        $user = $this->get('session')->get('uid');
        if(!$this->get('app.manage_controller')->isloginAction())
            return $this->redirectToRoute('login');
            
        return $this->render(
            'AcmeAccountBundle:Account:header.html.twig',
            array('user' => $user)
        );
    }

    public function logoutAction() {
        $this->get('session')->clear();

        return $this->redirectToRoute('login');
    }

    public function registerAction()
    {
        
        //$user = $this->get('session')->get('uid');
        if($this->get('app.manage_controller')->isloginAction())
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

        if($this->get('app.manage_controller')->isloginAction())
            return $this->redirectToRoute('account_home');

        $dafultvalues = array('email', 'plainPassword');
        $form = $this->createForm(new LoginType());

        $em = $this->getDoctrine()->getManager();

        $form->handleRequest($request);
        
        if ($form->isValid()) {
   
            $userdata = $form->getData();
            $password = $userdata['plainPassword'];

            $em = $this->getDoctrine()->getManager();
            $user_exist = $em->getRepository('AcmeAccountBundle:User')->findOneBy(
                array('email' => $userdata['email'], 'active' => 1)
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
            } else
                $this->addFlash('notice', 'Invalid username or password');

        }
        return $this->render(
            'AcmeAccountBundle:Account:login.html.twig',
            array('form' => $form->createView())
        ); 

        /*
        if($this->get('app.manage_controller')->isloginAction())
            return $this->redirectToRoute('account_home');

        if ($request->getMethod() == 'POST') {

            $emailConstraint = new Assert\Email();

            $emailConstraint->message = 'Invalid email address';

            $errorList = $this->get('validator')->validate(
                $request->request->get('username'),
                $emailConstraint
            );

            if (0 === count($errorList)) {
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
                } else
                    $this->addFlash('notice', 'Invalid username or password');
            } else {
                $errorMessage = $errorList[0]->getMessage();
                $this->addFlash('notice', $errorMessage);  
            }
        }
    
        return $this->render(
            'AcmeAccountBundle:Account:login.html.twig'
        );
        */
    }

    public function loginAction($activationLink = null)
    {
        //echo 1;exit;
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AcmeAccountBundle:User')->findOneBy(
            array('activationLink' => $activationLink, 'active' => 1)
        );

        $form = $this->createForm(new LoginType());
        //$form->handleRequest($request);

        if($user) {
            $this->addFlash('notice', 'You have successfully activated your account.');
            $user->setActivationLink(null);
            $em->flush();
        }
        
        return $this->render(
            'AcmeAccountBundle:Account:login.html.twig',
            array('form' => $form->createView())
        );

    }

    public function createAction(Request $request)
    {
        //$user = $this->get('session')->get('uid');
        if($this->get('app.manage_controller')->isloginAction())
            return $this->redirectToRoute('account_home');

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new RegistrationType(), new Registration());

        $form->handleRequest($request);
        
        if ($form->isValid()) {
   
            $registration = $form->getData();
            $repo = $registration->getUser();

            $repo->setSalt(uniqid(mt_rand())); 
            $activationLink = $repo->getEmail();
            $pass = $repo->getPlainPassword();
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($repo);
            $password = $encoder->encodePassword($repo->getPlainPassword(), $repo->getSalt());
            $repo->setPlainPassword($password);
            $repo->setActivationLink($activationLink);

            $em->persist($repo);
            $em->flush();

            //$this->sendEmail($registration->getUser(), $pass, $registration->getUser()->getActivationLink());
            $url = $this->generateUrl('activate_account', array('activationLink' => $registration->getUser()->getActivationLink()), true);
            $array_data = array('user' => $registration->getUser(), 'subject' => 'You have Completed Registration!', 'password' => $pass, 'activationLink' => $url, 'template' => 'Emails/registration.html.twig');
            $this->get('app.sendemail_controller')->SendEmailToUserAction($array_data);
            $this->addFlash('notice', 'Congratulations, Account created!');
            //return $this->render(
            //    'AcmeAccountBundle:Account:login.html.twig',
            //    array('form' => $form->createView())
            //);
            return $this->redirectToRoute('login');
        }


        return $this->render(
            'AcmeAccountBundle:Account:register.html.twig',
            array('form' => $form->createView())
        );
        
    }

    public function updateUserAction()
    {

        if(!$this->get('app.manage_controller')->isloginAction())
            return $this->redirectToRoute('login');

        $user = $this->get('session')->get('uid');
        $id = $user->getId();

        //if($user->getId() != $id)
        //    return $this->redirectToRoute('update', array('id' => $user->getId()));

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AcmeAccountBundle:User')->find($id);
        //$update = new Registration();
        //$update = new User();
        //$update->setUser($user);
        $form = $this->createForm(new UpdateType(), $user);

        $form->handleRequest($this->getRequest());
        
        if ($form->isValid()) {

            $repo = $form->getData();
            //$repo = $data->getUser();

            $repo->setSalt(uniqid(mt_rand())); 
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($repo);
            $password = $encoder->encodePassword($repo->getPlainPassword(), $repo->getSalt());
            $repo->setPlainPassword($password);

            if (!$user) {
                throw $this->createNotFoundException(
                    'Invalid User'
                );
            }

            $em->flush();
            $session = $this->getRequest()->getSession();
            $session->set('uid', $user);
            $this->addFlash('notice', 'User account successfully updated!');
            //return $this->redirectToRoute('account_home');
        }
        

        return $this->render(
            'AcmeAccountBundle:Account:register.html.twig',
            array('form' => $form->createView())
        );
    }

    public function updatePasswordAction(Request $request)
    {
        if(!$this->get('app.manage_controller')->isloginAction())
            return $this->redirectToRoute('login');

        $user_session = $this->get('session')->get('uid');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AcmeAccountBundle:User')->find($user_session->getId());
        //print_r($user);
        $form = $this->createForm(new ChangePasswordType(), new ChangePassword());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $user->setSalt(uniqid(mt_rand()));
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $password = $encoder->encodePassword($data->getNewPassword(), $user->getSalt());
            $user->setPlainPassword($password);
            $em->flush();
            $this->addFlash('notice', 'Password successfully updated');
            //echo $data->getNewPassword();
            //var_dump($data);exit;
        }

        return $this->render(
            'AcmeAccountBundle:Account:updatePassword.html.twig',
            array('form' => $form->createView())
        );
    }

    public function activatePasswordAction(Request $request, $token)
    {
        if($this->get('app.manage_controller')->isloginAction())
            return $this->redirectToRoute('account_home');
        $form = $this->createForm(new ResetPasswordType());

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AcmeAccountBundle:User')->findOneByresetpassLink($token);

        if(!$user) {
            $this->addFlash('notice', 'Link expired');
            return $this->redirectToRoute('forgot_pass');
        }

        if($user) {
            $date1 = $user->getResetlinkDate();
            $datelink1 = $date1->format('Y-m-d H:i:s');

            $date2 = new \DateTime($datelink1);
            $date2->add(new \DateInterval('P1D'));
            $date2->format('Y-m-d H:i:s');

            $now = new \DateTime('now');
            $now->format('Y-m-d H:i:s');

            if($date2 < $now) {
                $user->setResetpassLink(null);
                $em->flush();
                $this->addFlash('notice', 'Link expired');
                return $this->redirectToRoute('forgot_pass');
            }
        }

        if ($form->isValid()) {
            $data = $form->getData();

            if($user) {
                if($date2 >= $now) {
                    $user->setSalt(uniqid(mt_rand())); 
                    $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                    $password = $encoder->encodePassword($data['plainPassword'], $user->getSalt());
                    $user->setPlainPassword($password);
                    
                    $em->flush();
                    $this->addFlash('notice', 'Password successfully reset');
                    return $this->redirectToRoute('login');
                } else {
                    $user->setResetpassLink(null);
                    $this->addFlash('notice', 'Link expired');
                    return $this->redirectToRoute('forgot_pass');
                }
                    
            }
        }
        return $this->render(
            'AcmeAccountBundle:Account:activatePassword.html.twig',
            array('form' => $form->createView())
        );
    }

    public function forgotPasswordAction(Request $request) {
        if($this->get('app.manage_controller')->isloginAction())
            return $this->redirectToRoute('account_home');

        $form = $this->createForm(new ForgotPasswordType());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AcmeAccountBundle:User')->findOneByemail($data['email']);
            
            if(!$user) {
                $this->addFlash('notice', 'Not a registered user');
            } else {

                $user->setResetlinkDate(new \DateTime("now"));
                $user->setResetpassLink(uniqid(mt_rand()));
                $em->flush();
                $array_data = array('user' => $user, 'subject' => 'Reset Password', 'template' => 'Emails/forgot.html.twig');
                $this->get('app.sendemail_controller')->SendEmailToUserAction($array_data);
                    $this->addFlash('notice', 'Successfully sent. Pleas check your email');
                /*
                $user->setSalt(uniqid(mt_rand()));
                $user->setPlainPassword(substr(md5(uniqid($user->getEmail())),0,8));
                $plainPass = $user->getPlainPassword();
                //echo $user->getPlainPassword();exit;
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                $password = $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
                $user->setPlainPassword($password);
                $em->flush();
                $array_data = array('user' => $user, 'password' => $plainPass);
                $this->get('app.sendemail_controller')->SendEmailToUserAction($array_data);
                    $this->addFlash('notice', 'Successfully sent. Pleas check your email');
*/
                
            }
        }

        return $this->render(
            'AcmeAccountBundle:Account:forgot.html.twig',
            array('form' => $form->createView())
        );
    }

    public function activateAction($activationLink) {
        //echo $activationLink;
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AcmeAccountBundle:User')->findOneByactivationLink($activationLink);
        //var_dump($user);exit;
        if (!$user) {
            $this->addFlash('notice', 'Invalid Link');
            return $this->redirectToRoute('login');
        }

        $user->setActive(1);
        $em->flush();

        $this->addFlash('notice', 'You have successfully activated your account.');
        return $this->redirectToRoute('login');
        //return $this->redirectToRoute('account_login',array('activationLink' => $activationLink));
    }

    public function sendEmail($user, $pass, $activationLink) 
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
                array('user' => $user, 'pass'=> $pass, 'activationLink' => $url)
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