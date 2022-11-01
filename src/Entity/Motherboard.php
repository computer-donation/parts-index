<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(operations: [new Get()])]
class Motherboard
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Assert\NotBlank]
    public string $id;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    public string $manufacturer;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    public string $productName;

    #[ORM\Column(type: Types::STRING)]
    public string $version;
}
