<?php

namespace Superrb\KunstmaanStockistsFinderBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Intl\Intl;

/**
 * The type for Stockist
 */
class StockistsFinderType extends AbstractType
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
        $countries = Intl::getRegionBundle()->getCountryNames();
        $builder
            ->add('postcode', 'text', array(
                'required' => true,
                'label' => 'Postcode',
                'attr' => array(
                    'placeholder' => 'Postcode'
                ),
            ))
//            ->add('country', 'country', array(
//                'choices' => $countries,
//                'preferred_choices' => array(
//                    'GB', // United Kingdom
//                    'IE', // Ireland
//                ),
//            ))
            ->add('submit', 'submit');
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'stockists_finder_form';
    }
}
