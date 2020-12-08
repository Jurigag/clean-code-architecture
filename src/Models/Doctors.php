<?php

namespace App\Models;

use App\Controller\DoctorEntity;

interface Doctors
{
    public function getDoctorById(int $doctorId): ?DoctorEntity;
}