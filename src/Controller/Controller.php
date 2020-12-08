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
            $doctor = $manager->createQueryBuilder()
                ->select('doctor')
                ->from(DoctorEntity::class, 'doctor')
                ->where('doctor.id=:id')
                ->setParameter('id', $id)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

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

            $doctor = new DoctorEntity();
            $doctor->setFirstName($request->get('firstName'));
            $doctor->setLastName($request->get('lastName'));
            $doctor->setSpecialization($request->get('specialization'));

            $manager->persist($doctor);
            $manager->flush();

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
        $doctor = $manager->createQueryBuilder()
            ->select('doctor')
            ->from(DoctorEntity::class, 'doctor')
            ->where('doctor.id=:id')
            ->setParameter('id', $doctorId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

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
                $slot = new SlotEntity();
                $slot->setDay(new DateTime($request->get('day')));
                $slot->setDoctor($doctor);
                $slot->setDuration((int)$request->get('duration'));
                $slot->setFromHour($request->get('from_hour'));

                $manager->persist($slot);
                $manager->flush();

// result
                return new JsonResponse(['id' => $slot->getId()]);
            }
        } else {
            return new JsonResponse([], 404);
        }
    }

}
