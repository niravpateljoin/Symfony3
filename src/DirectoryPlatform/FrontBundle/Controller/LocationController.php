<?php

namespace DirectoryPlatform\FrontBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use DirectoryPlatform\AppBundle\Entity\Location;
use Symfony\Component\HttpFoundation\JsonResponse;

class LocationController extends Controller
{
	/**
	 * @Route("/backup", name="backup")
	 */	
	public function backupAction(Request $request) {
        $locations = $this->getDoctrine()->getRepository('AppBundle:Location')->findAll();
        $locations = $this->get('knp_paginator')->paginate($locations, $request->query->getInt('page', 1), 10);

        return $this->render('FrontBundle::Location/backup.html.twig', ['locations' => $locations]);
	}

	/**
	 * @Route("/locations", name="locations")
	 */	
	public function indexAction(Request $request) {
   	    $defaultDisplayProductVal = $this->get('app.helper.common')->getShowingProductNumber();
        $locations = $this->getDoctrine()->getRepository('AppBundle:Location')->findRecent($defaultDisplayProductVal);
        $totalLocation = count($this->getDoctrine()->getRepository('AppBundle:Location')->findParent());

        return $this->render('FrontBundle::Location/index.html.twig', [
        	'locations'     => $locations,
        	'totalLocation' => $totalLocation,
        ]);
	}


	/**
	 * @Route("/locations/{slug}", name="location_detail")
	 * @ParamConverter("location", class="DirectoryPlatform\AppBundle\Entity\Location")
	 */
	public function detailAction(Request $request, Location $location) {

		$defaultDisplayProductVal = $this->get('app.helper.common')->getShowingProductNumber();

		$clientIp = getenv('REMOTE_ADDR');
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$clientIp  ));

        $lat = $ipdat->geoplugin_latitude;
        $long = $ipdat->geoplugin_longitude;

		$objLocation = $this->getDoctrine()->getRepository('AppBundle:Location')->findOneById( $location->getId() );
		$listings = $this->getDoctrine()->getRepository('AppBundle:Listing')->findRecentLocation1($lat,$long, $defaultDisplayProductVal, $objLocation );

        return $this->render('FrontBundle::Location/detail.html.twig', [
        	'location' => $location,
        	// 'listings' => $location->getPublishedListings(),
        	'listings' => $listings,
        	'totalListing' => count($this->getDoctrine()->getRepository('AppBundle:Listing')->findRecentLocation( -1, $objLocation )),
        ]);
	}

	/**
	 * @Route("/locations/sortBy/", name="location_sort_by")
	 */
	public function LocationSortByAction(Request $request) {
        
        $objLocation = $this->getDoctrine()->getRepository('AppBundle:Location')->findOneById( $request->get('id') );
		$listings = $this->getDoctrine()->getRepository('AppBundle:Listing')->findUsingSortBy( $request, $objLocation );

        $data = array(
            'listings' => $listings,
        );
        $response['tabContent'] = $this->renderView( 'FrontBundle::Location/sortByLocation.html.twig', $data );
        $response['data'] = $data;


        
        return new JsonResponse( $response );        
	}

	/**
	 * @Route("load-more-location-data", name="load_more_location_data")
	 */
	public function loadLocationDataAction( Request $request ){
		$count = $request->get('count');
		$offset = $request->get('offset');

		$locationId = $request->get('locationId');

		$clientIp = getenv('REMOTE_ADDR');
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$clientIp  ));

        $lat = $ipdat->geoplugin_latitude;
        $long = $ipdat->geoplugin_longitude;

		$objLocation = $this->getDoctrine()->getRepository('AppBundle:Location')->findOneById( $locationId );
		$listings = $this->getDoctrine()->getRepository('AppBundle:Listing')->findRecentLocation1($lat,$long, $count, $objLocation, $offset );

        $data = array(
            'listings' => $listings,
            'categoryCount' => count($this->getDoctrine()->getRepository('AppBundle:Listing')->findRecentLocation( -1, $objLocation )),
			'count'    => $count,
        );
        $response['tabContent'] = $this->renderView( 'FrontBundle::Location/appendLocationData.html.twig', $data );
        $response['data'] = $data;

        return new JsonResponse( $response );
	}
}