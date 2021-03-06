<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\AssortmentBundle\Form\Type\VariantType as BaseVariantType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Product variant type.
 * We need to add only simple price field.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariantType extends BaseVariantType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('price', 'money', array(
                'label' => 'sylius.label.variant.price'
            ))->add('availableOnDemand', 'checkbox', array(
                'label' => 'sylius.label.variant.available_on_demand'
            ))->add('onHand', 'integer', array(
                'label' => 'sylius.label.variant.on_hand'
            ))
        ;
    }
}
