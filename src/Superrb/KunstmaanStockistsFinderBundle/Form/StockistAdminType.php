<?php

namespace Superrb\KunstmaanStockistsFinderBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Url;

/**
 * The type for Stockist
 */
class StockistAdminType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('stockist', TextType::class, array(
            'constraints' => array(
                new NotBlank(),
            ),
        ));

        $builder->add('address', TextareaType::class, array(
            'constraints' => array(
                new NotBlank(),
            ),
        ));

        $builder->add('postCode', TextType::class, array(
            'required' => false,
        ));

        $builder->add('website', UrlType::class, array(
            'required' => false,
            'constraints' => array(
                new Url(),
            ),
        ));

        $builder->add('county', TextType::class, array(
            'required' => false,
        ));

        $builder->add('country', CountryType::class, array(
            'preferred_choices' => array(
                'GB', // United Kingdom
                'IE', // Ireland
            ),
        ));

        $builder->add('latitude', TextType::class, array(
            'constraints' => array(
                new NotBlank(),
                new Range(array(
                    'min' => -90,
                    'max' => 90,
                )),
            ),
        ));

        $builder->add('longitude', TextType::class, array(
            'constraints' => array(
                new NotBlank(),
                new Range(array(
                    'min' => -180,
                    'max' => 180,
                )),
            ),
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'StockistAdminType';
    }
}
