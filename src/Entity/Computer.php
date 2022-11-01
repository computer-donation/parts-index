<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Enum\ComputerType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(operations: [new Get()])]
class Computer
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Assert\NotBlank]
    public string $id;

    #[ORM\Column(type: Types::STRING, length: 11, enumType: ComputerType::class)]
    #[Assert\NotBlank]
    public ComputerType $type;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    public string $vendor;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    public string $model;
}
