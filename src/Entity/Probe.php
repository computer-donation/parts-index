<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Probe
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Assert\NotBlank]
    public string $id;

    #[ORM\ManyToOne(targetEntity: Computer::class, inversedBy: 'probes')]
    #[ORM\JoinColumn(nullable: true)]
    public ?Computer $computer = null;

    #[ORM\ManyToOne(targetEntity: Cpu::class, inversedBy: 'probes')]
    #[ORM\JoinColumn(nullable: true)]
    public ?Cpu $cpu = null;

    #[ORM\ManyToOne(targetEntity: GraphicsCard::class, inversedBy: 'probes')]
    #[ORM\JoinColumn(nullable: true)]
    public ?GraphicsCard $graphicsCard = null;
}
