<?php

namespace App\Infrastructure;

use App\Controller\SlotEntity;
use App\Models\Slots;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineSlotsRepository implements Slots
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function persistSlot(SlotEntity $slot): void
    {
        $this->registry->getManager()->persist($slot);
        $this->registry->getManager()->flush();
    }
}