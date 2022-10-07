<?php

namespace App\Enum;

enum CpuVendor: string
{
    case AMD = 'AMD';
    case INTEL = 'Intel';

    public static function values(): array
    {
        return array_map(
            fn (CpuVendor $vendor) => $vendor->value,
            CpuVendor::cases()
        );
    }

    public function lower(): string
    {
        return strtolower($this->value);
    }
}
