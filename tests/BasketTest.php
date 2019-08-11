<?php
include_once __DIR__ . '/../app/Basket.php';

use PHPUnit\Framework\TestCase;

class BasketTest extends TestCase
{
	public function testGetBasketProducts()
	{
		$basket = new Basket();
		$basket->addProduct(Basket::SALAMI);
		$basket->addProduct(Basket::RUBBER_DUCK);
		$this->assertEquals(array_values($basket->getProducts()), [
			[
				'name' => Basket::SALAMI,
				'price' => 2000,
				'is_megapack' => false,
				'quantity' => 1
			],
			[
				'name' => Basket::RUBBER_DUCK,
				'price' => 3000,
				'is_megapack' => false,
				'quantity' => 1
			],
			[
				'name' => Basket::CUCUMBER,
				'price' => 2800,
				'is_megapack' => true,
				'quantity' => 0
			],
			[
				'name' => Basket::CHESTNUT,
				'price' => 1000,
				'is_megapack' => true,
				'quantity' => 0
			]
		]);
	}

	public function testPayTwoToGetDew()
	{
		$basket = new Basket();
		$basket->addProduct(Basket::SALAMI);
		$basket->addProduct(Basket::SALAMI);
		$basket->addProduct(Basket::SALAMI);
		$this->assertEquals(2000, $basket->getDiscount());
	}

	public function testPayTwoToGetDewIntoMoreProducts()
	{
		$basket = new Basket();
		$basket->addProduct(Basket::SALAMI);
		$basket->addProduct(Basket::SALAMI);
		$basket->addProduct(Basket::SALAMI);
		$basket->addProduct(Basket::RUBBER_DUCK);
		$basket->addProduct(Basket::RUBBER_DUCK);
		$basket->addProduct(Basket::RUBBER_DUCK);
		$this->assertEquals(3000, $basket->getDiscount());
	}

	public function testIsDiscountMegaPack()
	{
		$basket = new Basket();
		for ($i = 1; $i <= 12; $i++) {
			$basket->addProduct(Basket::CUCUMBER);
		}

		$this->assertEquals(6000, $basket->getDiscount());
	}

	public function testIfSetNormalDiscount()
	{
		$basket = new Basket();
		for ($i = 1; $i <= 7; $i++) {
			$basket->addProduct(Basket::RUBBER_DUCK);
		}

		for ($i = 1; $i <= 4; $i++) {
			$basket->addProduct(Basket::SALAMI);
		}

		$basket->setDiscount([
			Basket::RUBBER_DUCK => 7,
			Basket::SALAMI => 4
		],[
			Basket::RUBBER_DUCK => 2,
			Basket::SALAMI => 1
		]);

		$this->assertEquals(8000, $basket->getDiscount());
	}

	public function testIfSetMegaPackDiscount()
	{
		$basket = new Basket();
		for ($i = 1; $i <= 12; $i++) {
			$basket->addProduct(Basket::CUCUMBER);
		}

		for ($i = 1; $i <= 24; $i++) {
			$basket->addProduct(Basket::CHESTNUT);
		}

		$basket->setMegaPackDiscount([
			Basket::CUCUMBER => 12,
			Basket::CHESTNUT => 24
		], 18000);

		$this->assertEquals(18000, $basket->getDiscount());
	}

	public function testDiscountTypeIsMegaPack()
	{
		$basket = new Basket();
		for ($i = 1; $i <= 12; $i++) {
			$basket->addProduct(Basket::CUCUMBER);
		}

		for ($i = 1; $i <= 24; $i++) {
			$basket->addProduct(Basket::CHESTNUT);
		}

		$basket->setMegaPackDiscount([
			Basket::CUCUMBER => 12,
			Basket::CHESTNUT => 24
		], 18000);

		$basket->getDiscount();
		$this->assertEquals(BASKET::MEGA_PACK_TYPE, $basket->getDiscountType());
	}

	public function testDiscountTypeIsNormal()
	{
		$basket = new Basket();
		for ($i = 1; $i <= 7; $i++) {
			$basket->addProduct(Basket::RUBBER_DUCK);
		}

		for ($i = 1; $i <= 4; $i++) {
			$basket->addProduct(Basket::SALAMI);
		}

		$basket->setDiscount([
			Basket::RUBBER_DUCK => 7,
			Basket::SALAMI => 4
		],[
			Basket::RUBBER_DUCK => 2,
			Basket::SALAMI => 1
		]);

		$basket->getDiscount();
		$this->assertEquals(BASKET::NORMAL_TYPE, $basket->getDiscountType());
	}

	public function testTotalPrice()
	{
		$basket = new Basket();
		for ($i = 1; $i <= 7; $i++) {
			$basket->addProduct(Basket::RUBBER_DUCK);
		}

		$this->assertEquals(21000, $basket->getSubTotalPrice());

		$basket = new Basket();

		$this->assertEquals(0, $basket->getSubTotalPrice());
	}

}
