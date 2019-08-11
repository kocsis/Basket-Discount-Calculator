<?php

require 'Basket.php';

class BasketController
{
	private $view;
	private $basket;

	public function __construct(\Slim\Views\Twig $view)
	{
		$this->view = $view;
		$this->basket = new Basket;
	}

	public function view($response, $request)
	{
		$params = $request->getQueryParams();
		$basket = $this->basket;
		$basket = $this->setProductToBasket($basket, (array) $params['quantity']);
		$products = $basket->getProducts();
		$basket = $this->setDiscount($basket);

		return $this->view->render($response, 'basket.html.twig', [
			'products' => array_values($products),
			'subTotal' => $basket->getSubTotalPrice(),
			'discount' => $basket->getDiscount(),
			'discountType' => $basket->getDiscountType(),
			'total' => $basket->getSubTotalPrice() - $basket->getDiscount()
		]);
	}

	private function setDiscount(Basket $basket)
	{
		$basket->setDiscount([
			Basket::RUBBER_DUCK => 7,
			Basket::SALAMI => 4
		],[
			Basket::RUBBER_DUCK => 2,
			Basket::SALAMI => 1
		]);
		$basket->setMegaPackDiscount([
			Basket::CUCUMBER => 12,
			Basket::CHESTNUT => 24
		], 18000);

		return $basket;
	}

	private function setProductToBasket(Basket $basket, array $products)
	{
		foreach($products as $name => $productCount) {
			for ($i = 1; $i <= $productCount; $i++) {
				$basket->addProduct($name);
			}
		}

		return $basket;
	}
}
