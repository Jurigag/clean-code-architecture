<?php
declare(strict_types=1);

namespace App\Controller;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Controller extends AbstractController
{

    function index()
    {
        return new JsonResponse('ReallyDirty API v1.0');
    }

    function doctor(Request $request)
    {
        if ($request->getMethod() === 'GET') {
//get doctor
            $id = $request->get('id');
            /** @var EntityManagerInterface $manager */
            $manager = $this->getDoctrine()->getManager();

// get doctor
            $doctor = $this->getDoctorById($manager, $id);

            if ($doctor) {
                return new JsonResponse([
                    'id' => $doctor->getId(),
                    'firstName' => $doctor->getFirstName(),
                    'lastName' => $doctor->getLastName(),
                    'specialization' => $doctor->getSpecialization(),
                ]);
            } else {
                return new JsonResponse([], 404);
            }
        } elseif ($request->getMethod() === 'POST') {
//add doctor
            $manager = $this->getDoctrine()->getManager();

            $doctor = $this->createDoctorFromRequest($request);
            $this->persistDoctor($manager, $doctor);

// result
            return new JsonResponse(['id' => $doctor->getId()]);
        }

        //TODO other methods?
    }

    function slots(int $doctorId, Request $request)
    {
        /** @var EntityManagerInterface $manager */
        $manager = $this->getDoctrine()->getManager();
// get doctor
        $doctor = $this->getDoctorById($manager, $doctorId);

        if ($doctor) {

            if ($request->getMethod() === 'GET') {
//get slots
                /** @var SlotEntity[] $array */
                $array = $doctor->slots();

                if (count($array)) {
                    $slots = [];
                    foreach ($array as $slot) {
                        $slots[] = [
                            'id' => $slot->getId(),
                            'day' => $slot->getDay()->format('Y-m-d'),
                            'from_hour' => $slot->getFromHour(),
                            'duration' => $slot->getDuration()
                        ];
                    }
                    return new JsonResponse($slots);
                } else {
                    return new JsonResponse([]);
                }
            } elseif ($request->getMethod() === 'POST') {
// add slot
                $slot = $this->createSlotFromRequest($request, $doctor);
                $this->persistSlot($manager, $slot);

// result
                return new JsonResponse(['id' => $slot->getId()]);
            }
        } else {
            return new JsonResponse([], 404);
        }
    }

    private function getDoctorById(EntityManagerInterface $manager, $id): ?DoctorEntity
    {
        return $manager->createQueryBuilder()
            ->select('doctor')
            ->from(DoctorEntity::class, 'doctor')
            ->where('doctor.id=:id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function createDoctorFromRequest(Request $request): DoctorEntity
    {
        $doctor = new DoctorEntity();
        $doctor->setFirstName($request->get('firstName'));
        $doctor->setLastName($request->get('lastName'));
        $doctor->setSpecialization($request->get('specialization'));

        return $doctor;
    }

    private function persistDoctor(\Doctrine\Persistence\ObjectManager $manager, DoctorEntity $doctor): void
    {
        $manager->persist($doctor);
        $manager->flush();
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

    private function persistSlot(EntityManagerInterface $manager, SlotEntity $slot): void
    {
        $manager->persist($slot);
        $manager->flush();
    }
}
