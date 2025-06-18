<?php

namespace App\Entity;

use App\Repository\GiftRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups; // 1. Import the Groups attribute

#[ORM\Entity(repositoryClass: GiftRepository::class)]
class Gift
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['gift:read'])] // Add group
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['gift:read'])] // Add group
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['gift:read'])] // Add group
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)] // 2. Changed scale to 2 for cents
    #[Groups(['gift:read'])] // Add group
    private ?string $price = null;

    #[ORM\Column(length: 255)]
    #[Groups(['gift:read'])] // Add group
    private ?string $category = null;

    #[ORM\Column]
    #[Groups(['gift:read'])] // Add group
    private ?int $stock_quantity = null;

    // 3. Renamed property to 'image' for JS, but mapped to 'image_url' DB column
    #[ORM\Column(length: 255, name: 'image_url')]
    #[Groups(['gift:read'])] // Add group
    private ?string $image = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null; // No group needed here

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['gift:read'])] // Add group
    private ?string $label = null;

    /**
     * @var Collection<int, WishList>
     */
    #[ORM\OneToMany(targetEntity: WishList::class, mappedBy: 'gift')]
    private Collection $wishLists; // DO NOT add a group here

    /**
     * @var Collection<int, OrderItems>
     */
    #[ORM\OneToMany(targetEntity: OrderItems::class, mappedBy: 'gift')]
    private Collection $orderItems; // DO NOT add a group here

    /**
     * @var Collection<int, CartItem>
     */
    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'gift')]
    private Collection $cartItems; // DO NOT add a group here

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

    // Updated getter/setter for the 'image' property
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

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

    // --- RELATIONSHIP GETTERS/SETTERS (UNCHANGED) ---

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
            // set the owning side to null (unless already changed)
            if ($wishList->getGift() === $this) {
                $wishList->setGift(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderItems>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItems $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setGift($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItems $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
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
            // set the owning side to null (unless already changed)
            if ($cartItem->getGift() === $this) {
                $cartItem->setGift(null);
            }
        }

        return $this;
    }
}