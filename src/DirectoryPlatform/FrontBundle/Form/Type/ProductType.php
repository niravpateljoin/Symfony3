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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AdamQuaile\Bundle\FieldsetBundle\Form\FieldsetType;
use Vich\UploaderBundle\Form\Type\VichImageType;

use DirectoryPlatform\AppBundle\Entity\Product;

class ProductType extends AbstractType
{
    /**
	* @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('productName', TextType::class)
			->add('sku', TextType::class)
			->add('slug', TextType::class)
			->add('user', HiddenType::class, [
				'label' => false,
				'required' => false,
			])
			/* ->add('enabled', HiddenType::class, [
				'label' => false,
				'required' => false,
			]) */
			->add('enabled', CheckboxType::class, [
				'label' => 'Enabled',
				'required' => false,
			])
			->add('category', EntityType::class, [
						'class' => 'AppBundle:Category',
						'required' => true,
						'choices' => $options['hierarchy_categories']->tree(),
						'choice_label' => function($category) use ($options) {
							return $options['hierarchy_categories']->getName($category);
						},
					])
			->add('description', TextareaType::class, [
				'required' => true,
				'attr' => ['class' => 'wysiwyg', 'rows' => 5],
			])
			->add('inventory', TextType::class, [
				'required' => false,
			])	
			
			->add('settings', FieldsetType::class, [
				'label' => false, 
				'legend' => 'Price Settings',
				'attr' => ['class' => 'col-md-6'],
				'fields' => function(FormBuilderInterface $builder)  use ($options) {
					$builder->add('priceGram', MoneyType::class, [
						'attr' => ['class' => 'col-md-6'],
						'label' => 'Price Gram',
						'currency' => $options['currency'],
						'required' => false,
					])
					->add('price2Gram', MoneyType::class, [
						'attr' => ['class' => 'col-md-6'],
						'label' => 'Price 2 Gram',
						'currency' => $options['currency'],
						'required' => false,
					])
					->add('price35Gram', MoneyType::class, [
						'attr' => ['class' => 'col-md-6'],
						'label' => 'Price 3.5 Gram',
						'currency' => $options['currency'],
						'required' => false,
					])
					->add('price4Gram', MoneyType::class, [
						'attr' => ['class' => 'col-md-6'],
						'label' => 'Price 4 Gram',
						'currency' => $options['currency'],
						'required' => false,
					])
					->add('price7Gram', MoneyType::class, [
						'attr' => ['class' => 'col-md-6'],
						'label' => 'Price 7 Gram',
						'currency' => $options['currency'],
						'required' => false,
					])
					->add('price14Gram', MoneyType::class, [
						'attr' => ['class' => 'col-md-6'],
						'label' => 'Price 14 Gram',
						'currency' => $options['currency'],
						'required' => false,
					])
					->add('price28Gram', MoneyType::class, [
						'attr' => ['class' => 'col-md-6'],
						'label' => 'Price 28 Gram',
						'currency' => $options['currency'],
						'required' => false,
					]);
				}
			])
			
			->add('percentage', FieldsetType::class, [
				'label' => false,
				'legend' => 'Percentage Settings',
				'attr' => ['class' => 'col-md-6'],
				'fields' => function(FormBuilderInterface $builder)  use ($options) {
					$builder->add('percentageThc', TextType::class, [
						'attr' => ['class' => 'col-md-6'],
						'label' => 'THC %',
						'required' => false
					])
					->add('percentageThca', TextType::class, [
						'attr' => ['class' => 'col-md-6'],
						'label' => 'THCA %',
						'required' => false
					])	
					->add('percentageCbd', TextType::class, [
						'attr' => ['class' => 'col-md-6'],
						'label' => 'CBD %',
						'required' => false
					])
					->add('percentageCba', TextType::class, [
						'attr' => ['class' => 'col-md-6'],
						'label' => 'CBA %',
						'required' => false
					])
					->add('percentageCbn', TextType::class, [
						'attr' => ['class' => 'col-md-6'],
						'label' => 'CBN %',
						'required' => false
					]);
				}
			])
			
			->add('save', SubmitType::class, [
				'attr' => ['class' => 'btn-primary'],
			]);
	}
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
			'currency' => 'USD',
			'user' => null,'percentageThc'=>null,'percentageThca'=>null,'percentageCbd'=>null,'percentageCba'=>null,'percentageCbn'=>null,
			'hierarchy_categories' => null,
			'hierarchy_products' => null,
			'data_class' => 'DirectoryPlatform\AppBundle\Entity\Product',
		]);
    }
}
