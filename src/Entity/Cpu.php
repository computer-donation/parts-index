<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\CpuVendor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;
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

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    public ?int $cores = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    public ?int $threads = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    public ?int $maxSpeed = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    public ?int $minSpeed = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $l2Cache = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $l3Cache = null;

    #[ORM\OneToMany(targetEntity: Probe::class, mappedBy: 'cpu')]
    #[Ignore]
    protected Collection $probes;

    public function __construct()
    {
        $this->probes = new ArrayCollection();
    }

    public function addProbe(Probe $probe): void
    {
        if (!$this->probes->contains($probe)) {
            $this->probes->add($probe);
            $probe->cpu = $this;
        }
    }
}
