<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\SalesBundle\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Builds some simple orders.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class LoadOrdersData extends DataFixture
{
    public function load(ObjectManager $manager)
    {
        $repository = $this->getOrderRepository();
        $eventDispatcher = $this->get('event_dispatcher');

        for ($i = 1; $i <= 100; $i++) {
            $order = $repository->createNew();
            $this->buildOrder($order);

            $eventDispatcher->dispatch('sylius_sales.order.pre_create', new GenericEvent($order));

            $manager->persist($order);
            $eventDispatcher->dispatch('sylius_sales.order.post_create', new GenericEvent($order));
            $this->setReference('Sylius.Order-'.$i, $order);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 8;
    }

    private function buildOrder(OrderInterface $order)
    {
        $itemRepository = $this->getOrderItemRepository();

        $order->getItems()->clear();

        $totalItems = rand(3, 6);
        for ($i = 0; $i <= $totalItems; $i++) {
            $variant = $this->getReference('Sylius.Variant-'.rand(1, SYLIUS_FIXTURES_TOTAL_VARIANTS - 1));

            $item = $itemRepository->createNew();
            $item->setQuantity(rand(1, 5));
            $item->setVariant($variant);
            $item->setUnitPrice($variant->getPrice());

            $order->addItem($item);
        }

        $shipment = $this->getShipmentRepository()->createNew();
        $shipment->setMethod($this->getReference('Sylius.ShippingMethod.UPS Ground'));

        foreach ($order->getInventoryUnits() as $item) {
            $shipment->addItem($item);
        }

        $order->addShipment($shipment);

        $order->setUser($this->getReference('Sylius.User-'.rand(1, 15)));
        $order->setDeliveryAddress($this->getReference('Sylius.Address-'.rand(1, 50)));
        $order->setBillingAddress($this->getReference('Sylius.Address-'.rand(1, 50)));

        $order->calculateTotal();
    }
}
