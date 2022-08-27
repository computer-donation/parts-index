<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource]
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

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $version = null;

    #[ORM\OneToMany(targetEntity: Computer::class, mappedBy: 'motherboard')]
    public Collection $computers;

    public function __construct()
    {
        $this->computers = new ArrayCollection();
    }

    public function getComputers(): Collection
    {
        return $this->computers;
    }

    public function addComputer(Computer $computer): void
    {
        if (!$this->computers->contains($computer)) {
            $this->computers->add($computer);
            $computer->motherboard = $this;
        }
    }
}
