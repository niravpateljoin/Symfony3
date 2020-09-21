<?php

namespace DirectoryPlatform\FrontBundle\Controller;

use DirectoryPlatform\AppBundle\Helper\Hierarchy;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use DirectoryPlatform\AppBundle\Entity\User;

use DirectoryPlatform\FrontBundle\Form\Type\FilterHeroType;

class DefaultController extends Controller
{
	/**
	 * @Route("/", name="homepage")
	 */
	public function indexAction(Request $request)
	{
		$filter = $this->createForm(FilterHeroType::class, [], [
			'router' => $this->get('router'),
			'hierarchy' => new Hierarchy($this->getDoctrine()->getRepository('AppBundle:Category'), 'category', 'categories'),
		]);

   	    $defaultDisplayProductVal = $this->get('app.helper.common')->getShowingProductNumber();

		return $this->render(
			'FrontBundle::Front/index.html.twig', [
				'filter' => $filter->createView(),
				'locations' => $this->getDoctrine()->getRepository('AppBundle:Location')->findRecent($defaultDisplayProductVal),
				'totalLocation' => count($this->getDoctrine()->getRepository('AppBundle:Location')->findParent()),
				'categories' => $this->getDoctrine()->getRepository('AppBundle:Category')->findParent(),
				'categories_popular' => $this->getDoctrine()->getRepository('AppBundle:Category')->findPopular(),
				'posts' => $this->getDoctrine()->getRepository('AppBundle:Post')->findRecent(3),
				'listings' => $this->getDoctrine()->getRepository('AppBundle:Listing')->findRecent($defaultDisplayProductVal),
				'totalListing' => count($this->getDoctrine()->getRepository('AppBundle:Listing')->findRecent(-1)),
			]
		);
	}

	/**
	 * @Route("load-more-data", name="load_more_data")
	 */
	public function loadListAction( Request $request ){
		$count = $request->get('count');
		$offset = $request->get('offset');

        $data = array(
            'listings' => $this->getDoctrine()->getRepository('AppBundle:Listing')->findRecent($count, $offset),
            'listingCount' => count($this->getDoctrine()->getRepository('AppBundle:Listing')->findRecent(-1)),
			'count'    => $offset,
        );
        $response['tabContent'] = $this->renderView( 'FrontBundle::Front/appendList.html.twig', $data );
        $response['data'] = $data;

        return new JsonResponse( $response );
	}
	
	/**
	 * @Route("load-more-location", name="load_more_location")
	 */
	public function loadLocationAction( Request $request ){
		$count = $request->get('count');
		$offset = $request->get('offset');

		$data = array(
            'locations' => $this->getDoctrine()->getRepository('AppBundle:Location')->findRecent($count, $offset),
            'locationCount' => count($this->getDoctrine()->getRepository('AppBundle:Location')->findParent()),
			'count'    => $offset,
        );
        $response['tabContent'] = $this->renderView( 'FrontBundle::Front/appendLocation.html.twig', $data );
        $response['data'] = $data;

        return new JsonResponse( $response );
	}

	/**
	 * @Route("send-OPT", name="send_OPT")
	 * 
	 */
	public function sendOPTAction( Request $request ){
		
		$session = $request->getSession();

		$username = "neelk2929@gmail.com";
		$hash = "fc64e36d79300150a6a0b6d6fb4774f7fb81674ae32c31f092c6d1f15de722b5";

		// Config variables. Consult http://api.textlocal.in/docs for more info.
		$test = "0";

		// Data for text message. This is the text message data.
		$sender = "TXTLCL"; // This is who the message appears to be from.
		$numbers = $request->get('number'); // A single number or a comma-seperated list of numbers
		$otp = rand('100000', '999999');
		$message = "Dear customer, use the code ". $otp . " to verify your business-listing account.To keep your account safe, never share your OTP with anyone.";
		$session->set('OTP', $otp);
		$session->set('NUMBER', $numbers);

		$sessionOTP = $session->get('OTP');
		// 612 chars or less
		// A single number or a comma-seperated list of numbers
		$message = urlencode($message);
		$data = "username=".$username."&hash=".$hash."&message=".$message."&sender=".$sender."&numbers=".$numbers."&test=".$test;
		$ch = curl_init('http://api.textlocal.in/send/?');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch); // This is the result from the API
		curl_close($ch);




        $response['data'] = $data;

        return new JsonResponse( $response );
	}

	/**
	 * @Route("verify-OPT", name="verify_OPT")
	 * 
	 */
	public function verifyOPTAction( Request $request ){
		
		$session = $request->getSession();
		$enteredOTP = $request->get('otp');
		$sessionOTP = $session->get('OTP');
		$sessionNUMBER = $session->get('NUMBER');
		if ( $enteredOTP == $sessionOTP )
		{
			$result['response'] = "success";
			$result['number']  = $sessionNUMBER;
			$result['message'] = "Your OTP is verified!";
		}
		else
		{
			$result['response'] = "error";
			$result['number']  = $sessionNUMBER;
			$result['message'] = "Please enter valid OTP, or use resend button for generate new one.";
		}

		return new JsonResponse( $result );
	}

	/**
	 * @Route("create-user", name="create_user")
	 * 
	 */
	public function createNewUserAction( Request $request ){

		$em = $this->getDoctrine()->getManager();
		$session = $request->getSession();
		$number = $session->get('NUMBER');
		$password = $request->get('password');

		/*$objUser = new User();
        $em->persist($objUser);
        $em->flush();*/
        if( $number && $password )
        {
	        $userManager = $this->container->get('fos_user.user_manager');
			$user = $userManager->createUser();

			$user->setUsername($number);
			$user->setEmail($number);
			$user->setPlainPassword($password);
			$user->setEnabled('1');

			$userManager->updateUser($user);

			$this->addFlash('success', $this->get('translator')->trans('User has been successfully created.'));
			$result['message'] = 'success';
        }

        return new JsonResponse( $result );
	}

	/**
	 * @Route("advance-search", name="advance_search")
	 * 
	 */
	public function advanceSearchAction( Request $request )
	{
		$data = array();
		$response['content'] = $this->renderView( 'FrontBundle::Helper/advanceSeachPopup.html.twig', $data);

        return new JsonResponse($response);
	}

	/**
     * @Route("/set-session-for-login", name = "set_session_of_login")
     */
    public function soicalLoginAction(Request $request )
    {
        $session = $request->getSession();
        $session->set('IS_SOCIAL_LOGIN',true);
        $response = new JsonResponse('success');
        return $response;
    }
}