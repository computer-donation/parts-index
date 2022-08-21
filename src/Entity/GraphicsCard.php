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

    #[ORM\OneToMany(targetEntity: Probe::class, mappedBy: 'cpu')]
    protected Collection $probes;

    public function __construct()
    {
        $this->probes = new ArrayCollection();
    }

    public function addProbe(Probe $probe): void
    {
        if (!$this->probes->contains($probe)) {
            $this->probes->add($probe);
            $probe->graphicsCard = $this;
        }
    }
}
