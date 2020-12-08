<?php

namespace App\Action;

use App\Controller\DoctorEntity;
use App\Controller\SlotEntity;
use App\Models\Doctors;
use App\Models\Slots;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AddSlot
{
    private Doctors $doctors;
    private Slots $slots;

    public function __construct(Doctors $doctors, Slots $slots)
    {
        $this->doctors = $doctors;
        $this->slots = $slots;
    }

    public function __invoke(int $doctorId, Request $request): JsonResponse
    {
        $doctor = $this->doctors->getDoctorById($doctorId);

        if ($doctor === null) {
            return new JsonResponse([], 404);
        }

        $slot = $this->createSlotFromRequest($request, $doctor);
        $this->slots->persistSlot($slot);

        return new JsonResponse(['id' => $slot->getId()]);
    }

    private function createSlotFromRequest(Request $request, DoctorEntity $doctor): SlotEntity
    {
        $slot = new SlotEntity();
        $slot->setDay(new DateTime($request->get('day')));
        $slot->setDoctor($doctor);
        $slot->setDuration((int) $request->get('duration'));
        $slot->setFromHour($request->get('from_hour'));

        return $slot;
    }
}