<?php

namespace App\Console\Traits;

trait HasActivationCause
{
  public function getActivationCause(): string
  {
    return $this->argument('cause');
  }

  public function getActivationMeasureId(): ?string
  {
    return $this->argument('measureId') ?? null;
  }
}
