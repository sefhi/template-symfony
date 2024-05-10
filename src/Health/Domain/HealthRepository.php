<?php

namespace App\Health\Domain;

interface HealthRepository
{
    public function health(): Health;
}
