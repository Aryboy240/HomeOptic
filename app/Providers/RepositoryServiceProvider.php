<?php

namespace App\Providers;

use App\Contracts\AppointmentRepositoryInterface;
use App\Contracts\ExaminationRepositoryInterface;
use App\Contracts\PatientRepositoryInterface;
use App\Repositories\EloquentAppointmentRepository;
use App\Repositories\EloquentExaminationRepository;
use App\Repositories\EloquentPatientRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PatientRepositoryInterface::class, EloquentPatientRepository::class);
        $this->app->bind(ExaminationRepositoryInterface::class, EloquentExaminationRepository::class);
        $this->app->bind(AppointmentRepositoryInterface::class, EloquentAppointmentRepository::class);
    }
}
