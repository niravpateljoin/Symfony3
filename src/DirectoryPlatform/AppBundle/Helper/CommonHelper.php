<?php

namespace DirectoryPlatform\AppBundle\Helper;

use DirectoryPlatform\AppBundle\Helper\Hierarchy;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use DirectoryPlatform\AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use DirectoryPlatform\FrontBundle\Form\Type\FilterHeroType;
use DirectoryPlatform\FrontBundle\Form\Type\FilterType;
use DirectoryPlatform\FrontBundle\Form\Type\InquireType;
use DirectoryPlatform\FrontBundle\Form\Type\ListingType;
use DirectoryPlatform\FrontBundle\Form\Type\ReviewType;
use DirectoryPlatform\FrontBundle\Form\Type\ProductType;

class CommonHelper {

    private $entityManager;
    private $container;

    public function __construct(EntityManager $entityManager, ContainerInterface  $container, Session $session) {
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->session = $session;
    }

    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string $type    The fully qualified class name of the form type
     * @param mixed  $data    The initial data for the form
     * @param array  $options Options for the form
     *
     * @return Form
     */
    protected function createForm($type, $data = null, array $options = array())
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }

    public function generateSearchFilterForm() {

        $filter = $this->createForm(FilterType::class, [], [
            'router' => $this->container->get('router'),
            'hierarchy_categories' => new Hierarchy($this->entityManager->getRepository('AppBundle:Category'), 'category', 'categories'),
            'hierarchy_locations' => new Hierarchy($this->entityManager->getRepository('AppBundle:Location'), 'location', 'locations'),
        ]);

    	return $filter->createView();

    }

    public function generateFilterFormField($element)
    {   
        $data = array();
        $response = array();
        $boolean = array();
        $listCategory = array();
        $listUserArr = array();
        $listingListArr = array();
        $boolean = ['No','Yes'];
        $listCates = $this->entityManager->getRepository('AppBundle:Category')->findAll();
        foreach ($listCates as $listCate) {
            $listCategory[$listCate->getId()] = $listCate->getName();
        }

        $listUsers = $this->entityManager->getRepository('AppBundle:User')->findAll();
        foreach ($listUsers as $listUser) {
            $listUserArr[$listUser->getId()] = $listUser->getDisplayName();
        }

        $listingLists = $this->entityManager->getRepository('AppBundle:Listing')->findAll();
        foreach ($listingLists as $listingList) {
            $listingListArr[$listingList->getId()] = $listingList->getName();
        }

        switch ( $element )
        {
            case "/admin/listings":
                $data['menu'] = ['title','category','published','featured','verfied'];
                $data['category'] = $listCategory;
                $data['published'] = $boolean;
                $data['featured'] = $boolean;
                $data['verfied'] = $boolean;
                break;
            case "/admin/products":
                $data['menu'] = ['Id','ProductName','Vendor','Category','Sku','Enabled'];
                $data['Vendor'] = $listUserArr;
                $data['Category'] = $listCategory;
                $data['Enabled'] = $boolean;
                break;
            case "/admin/claims":
                $data['menu'] = ['Listing','Message','User','Approved'];
                $data['Approved'] = $boolean;
                $data['User'] = $listUserArr;
                $data['Listing'] = $listingListArr;
                break;
            case "/admin/reports":
                $data['menu'] = ['Listing','User','Message','Approved'];
                $data['Approved'] = $boolean;
                $data['User'] = $listUserArr;
                $data['Listing'] = $listingListArr;
                break;
            case "/admin/posts":
                $data['menu'] = ['title','category','published'];
                $data['category'] = $listCategory;
                $data['published'] = $boolean;
                break;
            case "/admin/reviews":
                $data['menu'] = ['Title','Author','Date','Published'];
                $data['Title'] = $listingListArr;
                $data['Published'] = $boolean;
                $data['Author'] = $listUserArr;
                break;
            case "/admin/orders":
                $data['menu'] = ['Id','Price','Type'];
                break;
            case "/admin/users":
                $data['menu'] = ['Title'];
                break;
            case "/admin/inquires":
                $data['menu'] = ['Name','Listing','Date'];
                $data['Listing'] = $listingListArr;
                break;
            case "/admin/subscribers":
                $data['menu'] = ['Name'];
                break;
            case "/admin/categories":
                $data['menu'] = ['Name','Slug'];
                break;
            case "/admin/locations":
                $data['menu'] = ['name','slug'];
                break;
            case "/admin/amenities":
                $data['menu'] = ['Name','Slug'];
                break;
            case "/admin/emailtemplate":
                $data['menu'] = ['emailKey','subject','isActive','isArchived'];
                $data['isActive'] = $boolean;
                $date['isArchived'] = $boolean;
                break;
            case "/admin/notifications":
                $data['menu'] = ['Name','Message','Read','Archived','Active'];
                $data['Read'] = $boolean;
                $data['Archived'] = $boolean;
                $data['Active'] = $boolean;
                break;
            case "/admin/newslaters":
                $data['menu'] = ['Subject','Body','SentDate','Active'];
                $data['Active'] = $boolean;
                break;
            case "/admin/settings":
                $data['menu'] = ['Key','Value'];
                break;
            default:
                $date['menu'] = array();
                break;
        }
        
        return $data;
    }

    public function getShowingProductNumber($key='') {

        $defaultShowProduct = $this->entityManager->getRepository('AppBundle:Setting')->findOneBy(array("keyVal" => 'SHOW_NUMBER_OF_PRODUCT'));

        return $defaultShowProduct->getValue();

    }

    public function generateRandomString($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function getProfileImage($url,$saveto='facebook.jpg') {

        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $raw=curl_exec($ch);
        curl_close ($ch);
        if(file_exists($saveto)){
            unlink($saveto);
        }
        $fp = fopen($saveto,'x');
        fwrite($fp, $raw);
        fclose($fp);
        // $file = new File($saveto);
        $file = new UploadedFile($saveto,'facebook.jpg','image/jpeg',filesize($saveto),null,true);
        return $file;
    }

    public function getAllCategories() {

        $categories = $this->entityManager->getRepository('AppBundle:Category')->findBy(array(),array("name" => 'ASC'));

        return $categories;
    }
   

}
