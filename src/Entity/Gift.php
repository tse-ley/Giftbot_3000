<?php

namespace App\Entity;

use App\Repository\GiftRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GiftRepository::class)]
class Gift
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['gift:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['gift:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['gift:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['gift:read'])]
    private ?string $price = null;

    #[ORM\Column(length: 255)]
    #[Groups(['gift:read'])]
    private ?string $category = null;

    #[ORM\Column]
    #[Groups(['gift:read'])]
    private ?int $stock_quantity = null;

    #[ORM\Column(length: 255, name: 'image_url')]
    #[Groups(['gift:read'])]
    private ?string $imageUrl = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['gift:read'])]
    private ?string $label = null;

    /**
     * @var Collection<int, WishList>
     */
    #[ORM\OneToMany(targetEntity: WishList::class, mappedBy: 'gift')]
    private Collection $wishLists;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'gift')]
    private Collection $orderItems;

    /**
     * @var Collection<int, CartItem>
     */
    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'gift')]
    private Collection $cartItems;

    public function __construct()
    {
        $this->wishLists = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
        $this->cartItems = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    // --- GETTERS AND SETTERS ---

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getStockQuantity(): ?int
    {
        return $this->stock_quantity;
    }

    public function setStockQuantity(int $stock_quantity): static
    {
        $this->stock_quantity = $stock_quantity;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;
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
            $wishList->setGift($this);
        }
        return $this;
    }

    public function removeWishList(WishList $wishList): static
    {
        if ($this->wishLists->removeElement($wishList)) {
            if ($wishList->getGift() === $this) {
                $wishList->setGift(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setGift($this);
        }
        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            if ($orderItem->getGift() === $this) {
                $orderItem->setGift(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    public function addCartItem(CartItem $cartItem): static
    {
        if (!$this->cartItems->contains($cartItem)) {
            $this->cartItems->add($cartItem);
            $cartItem->setGift($this);
        }
        return $this;
    }

    public function removeCartItem(CartItem $cartItem): static
    {
        if ($this->cartItems->removeElement($cartItem)) {
            if ($cartItem->getGift() === $this) {
                $cartItem->setGift(null);
            }
        }
        return $this;
    }
}
