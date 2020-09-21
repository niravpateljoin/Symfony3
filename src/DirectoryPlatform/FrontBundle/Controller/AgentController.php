<?php

namespace DirectoryPlatform\FrontBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use DirectoryPlatform\AppBundle\Entity\Amenity;
use DirectoryPlatform\AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class AgentController extends Controller
{
    /**
     * @Route("/agent/{id}", name="agent", requirements={"id": "\d+"})
     * @ParamConverter("user", class="DirectoryPlatform\AppBundle\Entity\User")
     */
    public function indexAction(Request $request, User $user) {
        $listings = $this->get('knp_paginator')->paginate($user->getListings(), $request->query->getInt('page', 1), 9);

        return $this->render('FrontBundle::Agent/index.html.twig', [
            'listings' => $listings,
            'user' => $user
        ]);
    }

    /**
     * @Route("/agent/sortBy/", name="agent_sort_by")
     */
    public function AgentSortByAction(Request $request) {
        
        $objUser = $this->getDoctrine()->getRepository('AppBundle:User')->findOneById( $request->get('id') );
        $listings = $this->getDoctrine()->getRepository('AppBundle:Listing')->findUsingSortBy($request, $objUser);

        $data = array(
            'listings' => $listings,
        );
        $response['tabContent'] = $this->renderView( 'FrontBundle::Category/sortByCategory.html.twig', $data );
        $response['data'] = $data;
        
        return new JsonResponse( $response );        
    }

}