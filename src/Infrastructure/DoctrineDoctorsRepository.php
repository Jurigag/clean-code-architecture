<?php

namespace App\Infrastructure;

use App\Controller\DoctorEntity;
use App\Models\Doctors;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineDoctorsRepository implements Doctors
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }


    public function getDoctorById(int $id): ?DoctorEntity
    {
        return $this->registry->getManager()->createQueryBuilder()
            ->select('doctor')
            ->from(DoctorEntity::class, 'doctor')
            ->where('doctor.id=:id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}