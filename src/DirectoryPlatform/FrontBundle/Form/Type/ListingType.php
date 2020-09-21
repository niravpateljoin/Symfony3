<?php

namespace DirectoryPlatform\FrontBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AdamQuaile\Bundle\FieldsetBundle\Form\FieldsetType;

use DirectoryPlatform\AppBundle\Entity\Listing;


class ListingType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('Product_type', ChoiceType::class, [
				'expanded' => true,				
				'choices' => [
					'Found' => Listing::TYPE_FOUND,
					'Lost' => Listing::TYPE_LOST,
				]
			])
			->add('category', EntityType::class, [
				'class' => 'AppBundle:Category',
				'required' => false,
				'choices' => $options['hierarchy_categories']->tree(),
				'label' => 'Choose a category',
			])
			->add('location', EntityType::class, [
				'class' => 'AppBundle:Location',
				'required' => false,				
				'label' => 'Choose your location',
			])
			->add('name', TextType::class, [
				'required' => true,
				'label' => 'Product name',
			])
			->add('description', TextareaType::class, [
				'required' => true,
				'label' => 'Product description',
				'attr' => ['class' => 'wysiwyg', 'rows' => 5],
			])
			->add('header', HiddenType::class, [
				'data' => Listing::HEADER_MAP,
				'required' => false,
			])
			// ->add('header', ChoiceType::class, [
			// 	'expanded' => false,				
			// 	'choices' => [
			// 		'None' => Listing::HEADER_NONE,
			// 		'Single Image' => Listing::HEADER_SINGLE_IMAGE,
			// 		'Gallery' => Listing::HEADER_GALLERY,
			// 		'Map' => Listing::HEADER_MAP,
			// 	]
			// ])

			->add('gallery', FieldsetType::class, [
				'label' => false,
				'legend' => 'Gallery',
				'fields' => function(FormBuilderInterface $builder) {
					$builder->add('images', CollectionType::class, [
                        'entry_type' => ImageType::class,
                        'label' => false,
                        'required' => false,
                        'allow_add' => true,
                        'allow_delete' => false,
                        'by_reference' => false,
                        'attr' => [
                            'class' => 'collection collection-images',
                        ],
                    ]);
				}
			])
			->add('price', MoneyType::class, [
				'currency' => $options['currency'],
				'required' => false,
				'label' => 'Offer amount',
			])
			->add('geolocation', FieldsetType::class, [
				'label' => false,
				'legend' => 'Location',
				'fields' => function(FormBuilderInterface $builder) use ($options) {
					$builder->add('search', TextType::class, [
						'mapped' => false,
						'required' => true,
					])					
					->add('latitude', HiddenType::class, [
						'required' => false,
					])
					->add('longitude', HiddenType::class, [
						'required' => false,
					]);
				}
			])
			->add('amenities', EntityType::class, [
				'class' => 'AppBundle:Amenity',
				'label' => 'Near by area',
				'multiple' => true,
				'expanded' => true,
				'required' => false,				
			])
			->add('videoYoutube', TextType::class, [
				'required' => false,
			])
			->add('custom_fields', FieldsetType::class, [
				'label' => false,
				'legend' => 'Custom Information',
				'fields' => function(FormBuilderInterface $builder) {
					$builder->add('fields', CollectionType::class, [
						'entry_type' => FieldType::class,
						'label' => false,
						'required' => false,
						'allow_add' => true,
						'allow_delete' => true,
						'by_reference' => false,
						'attr' => [
							'class' => 'collection collection-fields',
						],
					]);
				}
			])
			->add('address', HiddenType::class, [
				'required' => false,
				'attr' => ['rows' => 3]
			])
			
			->add('slug', HiddenType::class, [
				'required' => false,
			])
			->add('openingHours', HiddenType::class, [
				'required' => false,
				'attr' => [
					'rows' => 7,
				]
			])
			->add('save', SubmitType::class, [
				'attr' => ['class' => 'btn-primary'],
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'currency' => 'USD',
			'hierarchy_categories' => null,
			'hierarchy_locations' => null,
			'data_class' => 'DirectoryPlatform\AppBundle\Entity\Listing',
		]);
	}
}