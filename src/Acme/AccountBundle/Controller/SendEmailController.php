<?php
// src/Acme/AccountBundle/Controller/SendEmailController.php
namespace Acme\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;


class SendEmailController extends Controller
{
    public function SendEmailToUserAction($data = array())
    {
 
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