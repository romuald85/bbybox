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

    public function add(int $id)
    {
        $basket = $this->session->get('basket', []);

        if (array_key_exists($id, $basket)) {
            $basket[$id]++;
        } else {
            $basket[$id] = 1;
        }

        $this->session->set('basket', $basket);
    }

    public function remove(int $id)
    {
        $cart = $this->session->get('basket', []);

        unset($cart[$id]);

        $this->session->set('basket', $cart);
    }

    public function decrement(int $id)
    {
        $cart = $this->session->get('basket', []);

        if (!array_key_exists($id, $cart)) {
            return;
        }

        if ($cart[$id] === 1) {
            $this->remove($id);
            return;
        }

        $cart[$id]--;

        $this->session->set('basket', $cart);
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->session->get('basket') as $id => $qty) {
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

        foreach ($this->session->get('basket') as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $detailedBasket[] = new CartItem($product, $qty);
        }

        return $detailedBasket;
    }
}
