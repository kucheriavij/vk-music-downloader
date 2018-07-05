<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AudioRepository")
 */
class Audio
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $downloaded;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $artist_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $track_name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $track_id;

    /**
     * @var \DateTime $created_at
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @var \DateTime $updated_at
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * Audio constructor.
     */
    public function __construct()
    {
        $this->created_at = new \DateTime('NOW');
        $this->updated_at = new \DateTime('NOW');
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool|null
     */
    public function getDownloaded(): ?bool
    {
        return $this->downloaded;
    }

    /**
     * @param bool $downloaded
     * @return Audio
     */
    public function setDownloaded(bool $downloaded): self
    {
        $this->downloaded = $downloaded;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getArtistName(): ?string
    {
        return $this->artist_name;
    }

    /**
     * @param null|string $artist_name
     * @return Audio
     */
    public function setArtistName(?string $artist_name): self
    {
        $this->artist_name = $artist_name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTrackName(): ?string
    {
        return $this->track_name;
    }

    /**
     * @param null|string $track_name
     * @return Audio
     */
    public function setTrackName(?string $track_name): self
    {
        $this->track_name = $track_name;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrackId(): ?int
    {
        return $this->track_id;
    }

    /**
     * @param int|null $track_id
     * @return Audio
     */
    public function setTrackId(?int $track_id): self
    {
        $this->track_id = $track_id;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeInterface $created_at
     * @return Audio
     */
    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTimeInterface $updated_at
     * @return Audio
     */
    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamp()
    {
        $this->setUpdatedAt(new \DateTime('NOW'));
    }
}
