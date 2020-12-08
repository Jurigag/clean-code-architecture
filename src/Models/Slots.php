<?php

namespace App\Models;

use App\Controller\DoctorEntity;
use App\Controller\SlotEntity;

interface Slots
{
    public function persistSlot(SlotEntity $slot): void;
}