<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource]
class GraphicsCard
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Assert\NotBlank]
    public string $id;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    public string $vendor;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Assert\NotBlank]
    public ?string $subVendor = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    public string $device;

    #[ORM\OneToOne(targetEntity: Computer::class, inversedBy: 'graphicsCard')]
    public Computer $computer;
}
