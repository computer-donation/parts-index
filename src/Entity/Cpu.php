<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\CpuVendor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource]
class Cpu
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Assert\NotBlank]
    public string $id;

    #[ORM\Column(type: Types::STRING, length: 5, enumType: CpuVendor::class)]
    #[Assert\NotBlank]
    public CpuVendor $vendor;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    public string $model;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    public string $probe;
}
