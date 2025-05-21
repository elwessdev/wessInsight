<?php

namespace App\Entity;

use App\Repository\CvsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;

#[ORM\Entity(repositoryClass: CvsRepository::class)]
#[ORM\Table(name: '`cvs`')]
class Cvs {
    
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[ORM\Column]
    private ?string $fileName = null;

    /**
     * @ORM\Column(type="text", length=65535)
     */
    #[ORM\Column(type: "text", length: 65535)]
    private ?string $cvContent = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    #[ORM\Column(type: "json", nullable: true)]
    private ?array $ai_result = null;

    /**
     * @ORM\Column(type="datetime")
     */
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"], nullable: false)]
    private ?\DateTimeInterface $uploadDate = null;
    /**
     * @ORM\Column(type="integer")
     */
    #[ORM\Column]
    private ?int $userId = null;

    // Getters and Setters

    public function __construct() {
        $this->uploadDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getCvContent(): ?string
    {
        return $this->cvContent;
    }

    public function setCvContent(string $cvContent): self
    {
        $this->cvContent = $cvContent;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getAiResult(): ?array
    {
        return $this->ai_result;
    }
    public function setAiResult(array $ai_result): self
    {
        $this->ai_result = $ai_result;

        return $this;
    }

    public function getUploadDate(): ?\DateTimeInterface
    {
        return $this->uploadDate;
    }

    public function __toString(): string
    {
        return $this->fileName;
    }
}