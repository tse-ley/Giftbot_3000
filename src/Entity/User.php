<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $is_admin = null;

    /**
     * @var Collection<int, WishList>
     */
    #[ORM\OneToMany(targetEntity: WishList::class, mappedBy: 'user')]
    private Collection $wishLists;

    /**
     * @var Collection<int, Orders>
     */
    #[ORM\OneToMany(targetEntity: Orders::class, mappedBy: 'user')]
    private Collection $orders;

    /**
     * @var Collection<int, CartItems>
     */
    #[ORM\OneToMany(targetEntity: CartItems::class, mappedBy: 'user')]
    private Collection $cartItems;

    public function __construct()
    {
        $this->wishLists = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->cartItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->is_admin;
    }

    public function setIsAdmin(bool $is_admin): static
    {
        $this->is_admin = $is_admin;

        return $this;
    }

    /**
     * @return Collection<int, WishList>
     */
    public function getWishLists(): Collection
    {
        return $this->wishLists;
    }

    public function addWishList(WishList $wishList): static
    {
        if (!$this->wishLists->contains($wishList)) {
            $this->wishLists->add($wishList);
            $wishList->setUser($this);
        }

        return $this;
    }

    public function removeWishList(WishList $wishList): static
    {
        if ($this->wishLists->removeElement($wishList)) {
            // set the owning side to null (unless already changed)
            if ($wishList->getUser() === $this) {
                $wishList->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Orders>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CartItems>
     */
    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    public function addCartItem(CartItems $cartItem): static
    {
        if (!$this->cartItems->contains($cartItem)) {
            $this->cartItems->add($cartItem);
            $cartItem->setUser($this);
        }

        return $this;
    }

    public function removeCartItem(CartItems $cartItem): static
    {
        if ($this->cartItems->removeElement($cartItem)) {
            // set the owning side to null (unless already changed)
            if ($cartItem->getUser() === $this) {
                $cartItem->setUser(null);
            }
        }

        return $this;
    }
}
