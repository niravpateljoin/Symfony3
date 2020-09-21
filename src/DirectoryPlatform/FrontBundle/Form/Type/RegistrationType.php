<?php
// src/AppBundle/Form/RegistrationType.php

namespace DirectoryPlatform\FrontBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AdamQuaile\Bundle\FieldsetBundle\Form\FieldsetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('address', TextType::class, [
                        'required' => true,
                        'label' => 'Address',
                    ])                  
                    ->add('latitude', HiddenType::class, [
                        'required' => false,
                    ])
                    ->add('longitude', HiddenType::class, [
                        'required' => false,
                    ]);
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }
}