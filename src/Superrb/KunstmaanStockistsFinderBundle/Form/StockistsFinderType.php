<?php

namespace Superrb\KunstmaanStockistsFinderBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Intl\Intl;

/**
 * The type for Stockist
 */
class StockistsFinderType extends AbstractType
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

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

        $records = $this->em->createQueryBuilder()
            ->select('s.country')
            ->from('SuperrbKunstmaanStockistsFinderBundle:stockist', 's')
            ->getQuery()->getResult();

        $countriesList = Intl::getRegionBundle()->getCountryNames();

        $countries = array();

        foreach ($records as $country) {
            $code = $country['country'];
            $name = $countriesList[$code];
            $countries[$code] = $name;
        }

        $builder
            ->add('postcode', 'text', array(
                'required' => true,
                'label' => 'Postcode',
                'attr' => array(
                    'placeholder' => 'Postcode'
                ),
            ))
           ->add('country', 'country', array(
               'choices' => $countries,
                'placeholder' => 'Select a country',
                'empty_data'  => null
           ))
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
