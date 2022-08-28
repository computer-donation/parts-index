<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToMany(targetEntity: GraphicsCard::class, inversedBy: 'probes')]
    protected Collection $graphicsCards;

    #[ORM\ManyToMany(targetEntity: EthernetPciCard::class, inversedBy: 'probes')]
    protected Collection $ethernetPciCards;

    public function __construct()
    {
        $this->graphicsCards = new ArrayCollection();
        $this->ethernetPciCards = new ArrayCollection();
    }

    public function getGraphicsCards(): Collection
    {
        return $this->graphicsCards;
    }

    public function getEthernetPciCards(): Collection
    {
        return $this->ethernetPciCards;
    }

    public function addGraphicsCard(GraphicsCard $graphicsCard): void
    {
        if (!$this->graphicsCards->contains($graphicsCard)) {
            $this->graphicsCards->add($graphicsCard);
        }
    }

    public function addEthernetPciCard(EthernetPciCard $ethernetPciCard): void
    {
        if (!$this->ethernetPciCards->contains($ethernetPciCard)) {
            $this->ethernetPciCards->add($ethernetPciCard);
        }
    }
}
