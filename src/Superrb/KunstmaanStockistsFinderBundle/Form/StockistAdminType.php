<?php

namespace Superrb\KunstmaanStockistsFinderBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Intl\Intl;

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
        $countries = Intl::getRegionBundle()->getCountryNames();

        $builder->add('stockist');
        $builder->add('address');
        $builder->add('postCode');
        $builder->add('website');
        $builder->add('county');
        $builder->add('county');
        $builder->add('country', 'country', array(
            'choices' => $countries,
            'preferred_choices' => array(
                'GB', // United Kingdom
                'IE', // Ireland
            ),
        ));
        $builder->add('latitude');
        $builder->add('longitude');
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'stockist_form';
    }
}
