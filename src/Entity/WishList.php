<?php

namespace App\Entity;

use App\Repository\WishListRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: WishListRepository::class)]
class WishList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'wishLists')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'wishLists')]
    private ?Gift $gift = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $added_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column]
    private bool $is_public = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $viewed_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $shared_at = null;

    #[ORM\OneToMany(mappedBy: 'wishlist', targetEntity: WishListItem::class)]
    private Collection $items;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getGift(): ?Gift
    {
        return $this->gift;
    }

    public function setGift(?Gift $gift): static
    {
        $this->gift = $gift;
        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->added_at;
    }

    public function setAddedAt(\DateTimeImmutable $added_at): static
    {
        $this->added_at = $added_at;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function isPublic(): bool
    {
        return $this->is_public;
    }

    public function setIsPublic(bool $is_public): static
    {
        $this->is_public = $is_public;
        return $this;
    }

    public function getViewedAt(): ?\DateTimeImmutable
    {
        return $this->viewed_at;
    }

    public function setViewedAt(\DateTimeImmutable $viewed_at): static
    {
        $this->viewed_at = $viewed_at;
        return $this;
    }

    public function getSharedAt(): ?\DateTimeImmutable
    {
        return $this->shared_at;
    }

    public function setSharedAt(\DateTimeImmutable $shared_at): static
    {
        $this->shared_at = $shared_at;
        return $this;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }
public function addItem(WishListItem $item): static
{
    if (!$this->isItemInWishlist($item)) {
        $this->items->add($item);
        $item->setWishlist($this);
    }

    return $this;
}

public function removeItem(WishListItem $item): static
{
    if ($this->isItemInWishlist($item)) {
        $this->items->remove($item);
        $item->setWishlist(null);
    }

    return $this;
}

private function isItemInWishlist(WishListItem $item): bool
{
    return $this->items->contains($item);
}
    public function __construct()
    {
        $this->items = new ArrayCollection();
    }
}
