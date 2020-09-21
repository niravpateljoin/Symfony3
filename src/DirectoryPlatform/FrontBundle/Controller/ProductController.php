<?php

namespace DirectoryPlatform\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use DirectoryPlatform\FrontBundle\Form\Type\ProductType;
use DirectoryPlatform\AppBundle\Entity\Field;
use DirectoryPlatform\AppBundle\Helper\Hierarchy;
use DirectoryPlatform\AppBundle\Entity\Product;

class ProductController extends Controller
{
	/**
	 * @Route("/account/products", name="product_my")
	 */
	public function myAction(Request $request)
	{
		$products = $this->getDoctrine()->getRepository('AppBundle:Product')->findByUser($this->getUser());
		$products = $this->get('knp_paginator')->paginate($products, $request->query->getInt('page', 1), 10);                

		return $this->render('FrontBundle::Product/my.html.twig', [
			'products' => $products
		]);
	}
	
	/**
	 * @Route("/account/products/create", name="product_create")
	 */
    public function createAction(Request $request)
    {
		$form = $this->createForm(ProductType::class, null, [
			'currency' => $this->getParameter('app.currency'),
			'hierarchy_categories' => new Hierarchy($this->getDoctrine()->getRepository('AppBundle:Category'), 'category', 'categories'),
			'hierarchy_products' => new Hierarchy($this->getDoctrine()->getRepository('AppBundle:Product'), 'product', 'products'),
		]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$product = $form->getData();
			$product->setUser($this->getUser());
			try {
				$em = $this->getDoctrine()->getManager();
				$em->persist($product);
				$em->flush();
				$this->addFlash('success', $this->get('translator')->trans('Product has been successfully created.'));
			} catch (\Exception $e) {
				$this->addFlash('danger', $this->get('translator')->trans('An error occurred when creating product object.'));
			}

			return $this->redirectToRoute('product_my');
		}

		return $this->render('FrontBundle::Product/create.html.twig', ['form'  => $form->createView()]);
    }
	
	/**
	 * @Route("/account/products/update/{id}", name="product_update", requirements={"id": "\d+"})
     * @ParamConverter("product", class="DirectoryPlatform\AppBundle\Entity\Product")
	*/
    public function updateAction(Request $request, Product $product)
    {
        if ($this->getUser() !== $product->getUser()) {
            throw $this->createAccessDeniedException('You are not allowed to access this page.');
        }

        $form = $this->createForm(ProductType::class, $product, [
            'currency' => $this->getParameter('app.currency'),
            'hierarchy_categories' => new Hierarchy($this->getDoctrine()->getRepository('AppBundle:Category'), 'category', 'categories'),
            'hierarchy_products' => new Hierarchy($this->getDoctrine()->getRepository('AppBundle:Product'), 'product', 'products'),
        ]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Product $product */
            $product = $form->getData();
			$product->setUser($this->getUser());
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();
                $this->addFlash('success', $this->get('translator')->trans('Product has been successfully saved.'));
            } catch (\Exception $e) {
				//$this->get('logger')->error($e->getMessage());
                $this->addFlash('danger', $this->get('translator')->trans('An error occurred when saving product object.'));
            }

            return $this->redirectToRoute('product_update', ['id' => $product->getId()]);
        }

        return $this->render('FrontBundle::Product/update.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }
	
	
	/**
     * @Route("/account/products/delete/{id}", name="product_delete", requirements={"id": "\d+"})
     * @ParamConverter("product", class="DirectoryPlatform\AppBundle\Entity\Product")
     */
    public function deleteAction(Request $request, Product $product)
    {
        if ($this->getUser() !== $product->getUser()) {
            throw $this->createAccessDeniedException('You are not allowed to access this page.');
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
            $this->addFlash('success', $this->get('translator')->trans('Product has been successfully deleted.'));
        } catch (\Exception $e) {
            $this->addFlash('danger', $this->get('translator')->trans('An error occurred when deleting product object.'));
        }

        return $this->redirectToRoute('product_my');
    }
	
}
