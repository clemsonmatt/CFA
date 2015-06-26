<?php

namespace CFA\Hub\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Routing\RequestContext;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use CFA\Hub\SharedBundle\Entity\Person;

/**
 * Security Controller
 */
class SecurityController extends Controller
{
    /**
     * Login screen
     *
     * @Route("/login", name="cfa_security_login")
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContextInterface::AUTHENTICATION_ERROR
            );
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

        return $this->render('CFAHubSecurityBundle:Security:login.html.twig', [
            'username' => $lastUsername,
            'error'    => $error,
        ]);
    }

    /**
     * User forgot password
     *
     * @Route("/forgot", name="cfa_security_forgot")
     */
    public function forgotAction (Request $request)
    {
        $username = $request->request->get('_username');

        $person = new Person();

        $person = $this->getDoctrine()->getRepository('NCEESJournalBundle:Person')
            ->findOneBy(['username' => $username]);

        /* if they have an account, reset pass and send them an email */
        if (count($person)) {
            /* make/set temp password */
            $factory        = $this->get('security.encoder_factory');
            $encoder        = $factory->getEncoder($person);
            $hash           = time();
            $hash           = md5($hash);
            $hash           = substr(str_shuffle($hash), 0, 8);
            $uniquePassword = 'Journal'.$hash;
            $newPassword    = md5($uniquePassword);
            $person->setTempPassword($newPassword);

            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();


            $baseUrl = $request->getScheme().'://'.$request->getHttpHost().$request->getBasePath();
            $route   = $this->get('router')->generate('ncees_security_forgotPassword', ['tempPass' => $uniquePassword]);

            /* email the user */
            $message = \Swift_Message::newInstance()
                ->setSubject('Journal Forgot Password')
                ->setFrom('journal@ncees.org')
                ->setTo($person->getUsername().'@ncees.org')
                ->setBody(
                    'A new password has been requested for your account.
                    <br><br>
                    Please login and change your password.
                    <br><br>
                    <a href="'.$baseUrl.''.$route.'">
                        Click here to change your password
                    </a>
                    <br><br>
                    If you have recieved this in error, please ignore this message.'
                )
                ->setContentType("text/html");
            $this->get('mailer')->send($message);

            /* store flash message */
            $this->get('session')->getFlashBag()->add(
                'success',
                'Your temporary password has been assigned. Please check your email for your temporary password.'
            );
        } else {
            /* user does not have an account */
            $this->get('session')->getFlashBag()->add(
                'error',
                'Incorrect username'
            );
        }

        return $this->redirect($this->generateUrl('ncees_security_login'));
    }


    /**
     * Forgot password login (login with the temp password)
     *
     * @Route("/forgotPassword/{tempPass}", name="cfa_security_forgotPassword")
     */
    public function forgotPasswordAction ($tempPass)
    {
        $person = $this->getDoctrine()->getRepository('NCEESJournalBundle:Person')
            ->findOneBy(['tempPassword' => md5($tempPass)]);

        if (count($person) > 0) {
            /* login the user */
            $token = new UsernamePasswordToken($person, null, 'secured_area', ['ROLE_USER']);
            $this->get('security.context')->setToken($token);

            /* store flash message */
            $this->get('session')->getFlashBag()->add(
                'info',
                'Please update your password.'
            );
        }

        return $this->redirect($this->generateUrl('ncees_settings_updatePassword', ['tempPass' => $tempPass]));
    }
}
