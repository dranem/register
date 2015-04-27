<?php
// src/Acme/AccountBundle/Controller/SendEmailController.php
namespace Acme\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;


class SendEmailController extends Controller
{
    /*private $templating;
    private $session;
    private $mailer;

    public function __construct(EngineInterface $templating, \Swift_Mailer $mailer, Session $session)
    {
        $this->templating = $templating;
        $this->session = $session;
        $this->mailer = $mailer;
    }*/

    public function SendEmailToUserAction($data = array())
    {
        /*return $this->render(
                // app/Resources/views/Emails/registration.html.twig
                'Emails/forgot.html.twig',
                array('user' => $data)
            );
            */
        
        $from = 'menardjosef.morales@chromedia.com';
        $to = $data['user']->getEmail();

        $mailer = $this->get('mailer');
        $message = $mailer->createMessage()
        ->setSubject($data['subject'])
        ->setFrom($from)
        ->setTo($to)
        ->setBody(
            $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                $data['template'],
                array('user' => $data)
            ),
            'text/html'
        );
    $mailer->send($message);
    
    }
}