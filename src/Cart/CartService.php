<?php

namespace App\Cart;

use App\Cart\CartItem;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    public function getBasket(): array
    {
        return $this->session->get('basket', []);
    }

    public function saveBasket(array $basket)
    {
        return $this->session->set('basket', $basket);
    }


    public function add(int $id)
    {
        $basket = $this->getBasket();

        if (array_key_exists($id, $basket)) {
            $basket[$id]++;
        } else {
            $basket[$id] = 1;
        }

        $this->saveBasket($basket);
    }

    public function remove(int $id)
    {
        $basket = $this->getBasket();

        unset($basket[$id]);

        $this->saveBasket($basket);
    }

    public function decrement(int $id)
    {
        $basket = $this->getBasket();

        if (!array_key_exists($id, $basket)) {
            return;
        }

        if ($basket[$id] === 1) {
            $this->remove($id);
            return;
        }

        $basket[$id]--;

        $this->saveBasket($basket);
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->session->get('basket', []) as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) { // Si jamais l'identifiant du produit n'existe pas pour qqe raison il renverra null donc on évite ça avec le 'continue'
                continue;
            }

            $total += $product->getPrice() * $qty;
        }

        return $total;
    }

    public function getDetailedCartItems(): array
    {
        $detailedBasket = [];

        foreach ($this->session->get('basket', []) as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $detailedBasket[] = new CartItem($product, $qty);
        }

        return $detailedBasket;
    }
}
