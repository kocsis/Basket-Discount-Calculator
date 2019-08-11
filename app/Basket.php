<?php

class Basket
{
	const SALAMI = 'téliszalámi';
	const RUBBER_DUCK = 'gumikacsa';
	const CUCUMBER = 'uborka';
	const CHESTNUT = 'gesztenye';
	const MEGA_PACK_TYPE = 'megapack';
	const NORMAL_TYPE = 'normal';

	private $products = [
		self::SALAMI => [
			'name' => self::SALAMI,
			'price' => 2000,
			'is_megapack' => false,
			'quantity' => 0
		],
		self::RUBBER_DUCK => [
			'name' => self::RUBBER_DUCK,
			'price' => 3000,
			'is_megapack' => false,
			'quantity' => 0
		],
		self::CUCUMBER => [
			'name' => self::CUCUMBER,
			'price' => 2800,
			'is_megapack' => true,
			'quantity' => 0
		],
		self::CHESTNUT => [
			'name' => self::CHESTNUT,
			'price' => 1000,
			'is_megapack' => true,
			'quantity' => 0
		]
	];
	private $basket = [];
	private $counter = [];
	private $discount = [];
	private $type = self::NORMAL_TYPE;

	public function addProduct($name)
	{
		++$this->products[$name]['quantity'];
		$this->basket[] = $this->products[$name];
	}

	public function getSubTotalPrice()
	{
		$subTotalPrice = 0;
		foreach($this->basket as $product) {
			$subTotalPrice += $product['price'];
		}

		return $subTotalPrice;
	}

	public function getProducts()
	{
		return $this->products;
	}

	public function getBasket()
	{
		return $this->basket;
	}

	public function getDiscountType()
	{
		return $this->type;
	}

	public function getDiscount()
	{
		$normalDiscountProducts = [];
		$megapackDiscountProducts = [];
		foreach($this->basket as $product) {
			if($product['is_megapack']) {
				$megapackDiscountProducts[] = $product;
			} else {
				$normalDiscountProducts[] = $product;
			}
		}

		if(!empty($normalDiscountProducts)) {
			$this->discount[] = [
				'discount' => $this->getNormalDiscount($normalDiscountProducts),
				'type' => self::NORMAL_TYPE
			];
		}

		if(!empty($megapackDiscountProducts)) {
			$this->discount[] = [
				'discount' => $this->getMegaPackDiscount($megapackDiscountProducts),
				'type' => self::MEGA_PACK_TYPE
			];
		}

		return $this->getUtmostDiscount($this->discount);
	}

	public function setMegaPackDiscount(array $purchasedItems, int $discount)
	{
		$counter = $this->getProductCounter($this->getBasket());

		$discountCounter = $this->getDiscountCounter($purchasedItems, $counter, true);

		if(count($purchasedItems) === $discountCounter) {
			$this->discount[] = ['discount' => $discount, 'type' => self::MEGA_PACK_TYPE];
		}
	}

	public function setDiscount(array $purchasedItems, array $discountedByProductsPrice)
	{
		$counter = $this->getProductCounter($this->getBasket());

		$discountCounter = $this->getDiscountCounter($purchasedItems, $counter, false);

		if(count($purchasedItems) === $discountCounter) {
			$this->discount[] = $this->getDiscountBySetDiscount($discountedByProductsPrice);
		}
	}

	private function getDiscountCounter(array $purchasedItems, array $productCounter, bool $isMegaPack)
	{
		$discountCounter = 0;
		foreach($purchasedItems as $name => $requiredQuantity) {
			if($productCounter[$name]['count'] >= $requiredQuantity && $this->products[$name]['is_megapack'] === $isMegaPack) {
				++$discountCounter;
			}
		}

		return $discountCounter;
	}

	private function getProductCounter(array $products)
	{
		if($this->counter !== []) {
			return $this->counter;
		}

		foreach($products as $product) {
			if(empty($this->counter[$product['name']]['count'])) {
				$this->counter[$product['name']]['count'] = 0;
			}
			++$this->counter[$product['name']]['count'];
		}

		return $this->counter !== [] ? $this->counter : [];
	}

	private function getDiscountBySetDiscount($discountedByProductsPrice)
	{
		$discount = 0;
		foreach($discountedByProductsPrice as $name => $quantity) {
			$discount += $this->products[$name]['price'] * $quantity;
		}

		return ['discount' => $discount, 'type' => self::NORMAL_TYPE];
	}

	private function getUtmostDiscount($discount)
	{
		usort($discount, function($a, $b) {
			return $b['discount'] - $a['discount'];
		});

		if(isset($discount[0]['discount'])) {
			$this->type = $discount[0]['type'];
			return $discount[0]['discount'];
		}

		return 0;
	}

	private function getMegaPackDiscount($products)
	{
		$discount = 0;
		$counter = $this->getProductCounter($products);

		foreach($products as $product) {
			if($counter[$product['name']]['count'] >= 12 && $discount === 0) {
				$discount = 6000;
			}
		}

		return $discount;
	}

	private function getNormalDiscount($products)
	{
		$discount = [];
		$counter = $this->getProductCounter($products);

		foreach($products as $product) {
			if($counter[$product['name']]['count'] >= 3 && empty($discount[$product['name']])) {
				$discount[$product['name']] = ['discount' => $product['price'], 'type' => self::NORMAL_TYPE];
			}
		}

		return $this->getUtmostDiscount($discount);
	}
}
