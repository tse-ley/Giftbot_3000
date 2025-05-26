<?php

namespace App\Entity;

use App\Entity\WishList;
use App\Entity\Gift;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

#[ORM\Entity]
class WishListItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    private ?WishList $wishlist = null;

    #[ORM\ManyToOne(inversedBy: 'wishLists')]
    private ?Gift $gift = null;

    #[ORM\ManyToOne(inversedBy: 'wishListItems')]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $added_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWishlist(): ?WishList
    {
        return $this->wishlist;
    }

    public function setWishlist(?WishList $wishlist): static
    {
        $this->wishlist = $wishlist;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
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
}