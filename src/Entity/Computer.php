<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\ComputerType;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource]
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

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    public string $probe;

    #[ORM\OneToOne(targetEntity: Computer::class, mappedBy: 'computer')]
    #[ORM\JoinColumn(nullable: true)]
    public ?GraphicsCard $graphicsCard = null;
}
