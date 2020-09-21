<?php

namespace DirectoryPlatform\FrontBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

use DirectoryPlatform\AppBundle\Entity\Category;

class CategoryController extends Controller
{
	/**
	 * @Route("/categories", name="category")
	 */	
	public function indexAction(Request $request) {
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();
        $categories = $this->get('knp_paginator')->paginate($categories, $request->query->getInt('page', 1), 10);

        return $this->render('FrontBundle::Category/index.html.twig', ['categories' => $categories]);
	}

	/**
	 * @Route("/categories/{slug}", name="category_detail")
	 * @ParamConverter("category", class="DirectoryPlatform\AppBundle\Entity\Category")
	 */
	public function detailAction(Request $request, Category $category) {

   	    $defaultDisplayProductVal = $this->get('app.helper.common')->getShowingProductNumber();

		$clientIp = getenv('REMOTE_ADDR');
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$clientIp  ));

        $lat = $ipdat->geoplugin_latitude;
        $long = $ipdat->geoplugin_longitude;

		$objCategory = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneById( $category->getId() );
		// $listings = $this->getDoctrine()->getRepository('AppBundle:Listing')->findRecentCategory( 6, $objCategory );
		$listings = $this->getDoctrine()->getRepository('AppBundle:Listing')->findRecentCategory1($lat,$long, $defaultDisplayProductVal, $objCategory );

		// dump($listings);
		// exit;
		
        return $this->render('FrontBundle::Category/detail.html.twig', [
        	'category' => $category,
        	'listings' => $listings,
        	'totalListing' => count($this->getDoctrine()->getRepository('AppBundle:Listing')->findRecentCategory( -1, $objCategory )),
        ]);
	}

	/**
	 * @Route("load-more-category", name="load_more_category")
	 */
	public function loadCategoryAction( Request $request ){
		$count = $request->get('count');
		$offset = $request->get('offset');

		$categoryId = $request->get('categoryId');

		$clientIp = getenv('REMOTE_ADDR');
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$clientIp  ));

        $lat = $ipdat->geoplugin_latitude;
        $long = $ipdat->geoplugin_longitude;

		$objCategory = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneById( $categoryId );
		// $listings = $this->getDoctrine()->getRepository('AppBundle:Listing')->findRecentCategory( $count, $objCategory, $offset );
		$listings = $this->getDoctrine()->getRepository('AppBundle:Listing')->findRecentCategory1($lat,$long, $count, $objCategory, $offset );

        $data = array(
            'listings' => $listings,
            'categoryCount' => count($this->getDoctrine()->getRepository('AppBundle:Listing')->findRecentCategory( -1, $objCategory )),
			'count'    => $count,
        );
        $response['tabContent'] = $this->renderView( 'FrontBundle::Category/appendCategory.html.twig', $data );
        $response['data'] = $data;

        return new JsonResponse( $response );
	}

	/**
	 * @Route("/categories/sortBy/", name="category_sort_by")
	 */
	public function CategorySortByAction(Request $request) {
        
        $objCategory = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneById( $request->get('id') );
		$listings = $this->getDoctrine()->getRepository('AppBundle:Listing')->findUsingSortBy( $request, $objCategory );

        $data = array(
            'listings' => $listings,
            'totalListing' => count($this->getDoctrine()->getRepository('AppBundle:Listing')->findRecentCategory( -1, $objCategory )),
        );
        $response['tabContent'] = $this->renderView( 'FrontBundle::Category/sortByCategory.html.twig', $data );
        $response['data'] = $data;
        
        return new JsonResponse( $response );        
	}
}