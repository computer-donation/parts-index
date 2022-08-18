<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\Cpu\Vendor;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource]
class Cpu
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Assert\NotBlank]
    public string $id;

    #[ORM\Column(type: Types::STRING, length: 5, enumType: Vendor::class)]
    #[Assert\NotBlank]
    public Vendor $vendor;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    public string $model;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    public string $probe;
}
