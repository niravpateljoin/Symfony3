<?php
namespace DirectoryPlatform\AppBundle\Security\Core\User;

use DirectoryPlatform\AppBundle\Entity\User;
use DirectoryPlatform\AppBundle\Entity\Profile;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Util\TokenGeneratorInterface;

class OAuthProvider extends OAuthUserProvider
{
    protected $session, $doctrine, $admins, $router, $tokenGenerator;
 
    public function __construct($session, $doctrine, $service_container, $router,$tokenGenerator)
    {
        $this->session = $session;
        $this->doctrine = $doctrine;
        $this->container = $service_container;
        $this->router = $router;
        $this->tokenGenerator = $tokenGenerator;

    }
 
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {

        $em = $this->doctrine->getManager();
        $data = $response->getData();
        
        
        $social_id = $response->getUsername();
        $email = $response->getEmail();
        $realname = $response->getRealName();
        $firstName = $response->getFirstName();
        $lastName = $response->getLastName();
        $avatar = $response->getProfilePicture();
        $service = $response->getResourceOwner()->getName();
        $name = explode(' ', $realname);
        $firstNameData = $name[0];
        $lastNameData = $name[1];
        
        $profileImage = $this->container->get('app.helper.common')->getProfileImage($avatar); 
        
        // dump($profileImage);
        // exit;

        if($email == '')
        {
            $strErrMessage = 'We are unable to fetch your email address. Please provide email address.';
            
                $registerUrl = $this->router->generate('fos_user_registration_register');
                $this->session->getFlashBag()->add( 'danger', $strErrMessage );
                echo "<form action='".$registerUrl."' method='GET' id='frm' name='frm'></form>";
                echo "<script type= 'text/javascript'>document.getElementById('frm').submit();</script>";
                exit;
            
        }
        
        //Check if this Social user already exists in our app DB
        
        $user = $this->doctrine->getManager()->getRepository('AppBundle:User')->findOneBy(array('email' => $email));
        
        //add to database if doesn't exists
        if($user) {

            if(!$user->IsEnabled()){
                $strErrMessage = 'Please activate your account.';
                $this->session->set('oauth_error', $strErrMessage);
                throw new AuthenticationException($strErrMessage);
            }
           
            switch ($service) {
                case 'google':
                    $user->setGoogleID($social_id);
                    $user->setGoogleAccessToken( $response->getAccessToken() );
                    break;
                case 'facebook':
                    $user->setFacebookID($social_id);
                    $user->setFacebookAccessToken( $response->getAccessToken() );
                    break;
                case 'linkedin':
                    $user->setLinkedinID($social_id);
                    $user->setLinkedinAccessToken( $this->tokenGenerator->generateToken() );
                    break;
            }
            
            $em->persist($user);
            $em->flush();

            return $user;
        } 
        else 
        { 
            
             if($this->session->has('IS_SOCIAL_LOGIN')){

                $this->session->remove('IS_SOCIAL_LOGIN');
                // $registerUrl = $this->router->generate('fos_user_registration_register');
                // $this->session->getFlashBag()->add( 'danger', 'We were not able to find your account in our system using the '.$email.'. Please register below.' );
                // echo "<form action='".$registerUrl."' method='GET' id='frm' name='frm'></form>";
                // echo "<script type= 'text/javascript'>document.getElementById('frm').submit();</script>";
                // exit;
        
            }

            $userManager = $this->container->get('fos_user.user_manager');
            $user = $userManager->createUser();

            $user->setUsername($firstNameData.' '.$lastNameData);
            $user->setEmail($email);
            $user->setPlainPassword($this->container->get('app.helper.common')->generateRandomString(7));
            $user->setEnabled('1');
            switch ($service) {
                case 'google':
                    $user->setGoogleID($social_id);
                    $user->setGoogleAccessToken( $response->getAccessToken() );
                    break;
                case 'facebook':
                    $user->setFacebookID($social_id);
                    $user->setFacebookAccessToken( $response->getAccessToken() );
                    break;
                case 'linkedin':
                    $user->setLinkedinID($social_id);
                    break;
            }

            $userManager->updateUser($user);


            $profile = new Profile();

            $profile->setAvatarImage($profileImage);
            $profile->setFirstName($firstNameData);
            $profile->setLastName($lastNameData);
            $profile->setUser($user);
            $em->persist($profile);
            $em->flush();

            return $user;


            /*$url = $this->router->generate( 'fos_user_registration_check_email' );
         
            echo "<form action='".$url."' method='GET' id='frm' name='frm'></form>";
            echo "<script type= 'text/javascript'>document.getElementById('frm').submit();</script>";
            exit;*/
            
        }
        
    }
 
    public function supportsClass($class)
    {
        return $class === 'DirectoryPlatform\\AppBundle\\Security\\Core\\User\\OAuthProvider';
    }
}


