<?php
//Для большей надежности, можно было (и нужно на реальном проекте) все свойства 
//сделать закрытыми, использовать геты.

/**
 * Создание нового продукта
 * 
 * @param string $name имя
 * @param numeric $price цена
 */
class Product
{
	public $name;
	public $price;
	public $newPrice;
	
	/**
	 * @param string $name имя
	 * @param numeric $price цена
	 */
	public function __construct($name, $price)
	{
		$this->name = $name;
		$this->price = $price;
		$this->newPrice = $price;
	}
}

class Discount
{
	public $products;
	public $discount = 0;
	
	/**
	 * Добавление в скидку одного или нескольких продуктов
	 * @param Product ...$products Продукт
	 */
	public function setProductSet() {}
	
	/**
	 * Установка размера скидки
	 * @param numeric $param Размер скидки
	 */
	public function setDiscount($param)
	{
		$this->discount = $param;
	}
}

/**
 * Создание скидки для продуктов, где в заказе присутствуют одновременно все
 * указаные продукты
 */
class DiscountFixed extends Discount
{
	public $products = [];
	
	/**
	 * Добавление в скидку одного или нескольких продуктов
	 * @param Product ...$products Продукт
	 */
	public function setProductSet(Product ...$products)
	{
		foreach ($products as $product)
		{
			$this->products[] = $product;
		}
	}
}

/**
 * Создание скидки для продуктов, где одновременно в заказе присутствует
 * комбинация первого товара и любого второго из массива
 */
class DiscountArray extends Discount
{
	public $product;
	public $productsArray = [];
	
	/**
	 * Добавление в скидку одного или нескольких продуктов
	 * @param Product $product Статичные продукт
	 * @param Product array $products Массив динамичных продуктов
	 */
	public function setProductSet(Product $product, array $products)
	{
		$this->product = $product;
		foreach ($products as $product)
		{
			$this->productsArray[] = $product;
		}
	}
}

/**
 * Создание скидки для продуктов, которых в заказе указанное количество или
 * больше
 */
class DiscountCount extends Discount
{
	public $products;
	public $exclude = [];
	
	/**
	 * Добавление в скидку условия, где первое значение, необходимое количество
	 * товаров в заказе, последующие, продукты типа Product, исключенные из
	 * данной скидки
	 */
	public function setProductSet($products, ...$exclude)
	{
		$this->products = $products;
		foreach ($exclude as $item)
		{
			$this->exclude[] = $item;
		}
	}
}

/**
 * Объединение всех скидок
 */
class DiscountManager
{
	public $discounts = [];

	/**
	 * Добавление скидки в менеджер
	 * @param Discount ...$discount Скидка
	 */
	public function add(Discount ...$discounts)
	{
		foreach ($discounts as $discount)
		{
			$this->discounts[] = $discount;
		}
	}
}

/**
 * Формирование заказа из продуктов
 */
class Order
{
	public $order = [];
	
	/**
	 * Добавление в заказ продуктов
	 * @param Product ...$products Продукты
	 */
	public function push(Product ...$products)
	{
		foreach ($products as $product)
		{
			$this->order[] = $product;
		}
	}
}

/**
 * Расчет скидки, согласно заказу и переданных менеджеру скидок
 */
class Calculator
{
	public $order;
	public $orderDiscount;
	public $manager;
	
	/**
	 * Установить заказ для расчета
	 * @param Order $order Заказ
	 */
	public function setOrder(Order $order)
	{
		$this->order = $order;
	}
	
	/**
	 * Установить скидки для расчета
	 * @param DiscountManager $manager Скидки
	 */
	public function setManager(DiscountManager $manager)
	{
		$this->manager = $manager;
	}
	
	/**
	 * Начать расчет
	 * @return Order $order Новый заказ с измененными ценами
	 */
	public function youAreNotPrepared()
	{
		$orderTemp = clone($this->order);
		$orderDiscount = new Order();
		foreach ($this->manager->discounts as $discountItem)
		{
			$discountClass = get_class($discountItem);
			switch ($discountClass)
			{
				case 'DiscountFixed':
					$this->calculateDiscountFixed($discountItem, $orderTemp, $orderDiscount);
					break;
				case 'DiscountArray':
					$this->calculateDiscountArray($discountItem, $orderTemp, $orderDiscount);
					break;
				case 'DiscountCount':
					$this->calculateDiscountCount($discountItem, $orderTemp, $orderDiscount);
					break;
			}
		}
		
		return $this->collectionNewOrder($orderTemp, $orderDiscount);
	}

	/**
	 * Расчет скидки типа DiscountFixed
	 * @param DiscountFixed $discountItem Скидка
	 * @param Order $orderTemp Заказ с продуктами без скидок
	 * @param Order $orderDiscount Заказ с продуктами только со скидками
	 */
	private function calculateDiscountFixed(DiscountFixed $discountItem, &$orderTemp, &$orderDiscount)
	{
		$conformity = 0;
		$key = [];
		$discount = $discountItem->discount;
		
		foreach ($discountItem->products as $discountProduct)
		{
			//if ($discountProduct == )
			if (($key[] = array_search($discountProduct, $orderTemp->order, true)) !== false)
				$conformity++;
			//var_dump($discountProduct);
		}
		//в новый заказ добавляем продукты со скидкой и удаляем из старого
		if (count($discountItem->products) == $conformity)
		{
			//перебираем товары и назначаем им цену со скидкой и добавляем в
			//итоговый заказ
			foreach ($discountItem->products as $discountProduct)
			{
				$orderDiscount->push($this->getNewProduct($discountProduct, $discount));
			}
			//удаляем из старого заказа проверенные продукты
			foreach ($key as $value)
			{
				unset($orderTemp->order[$value]);
			}
		}
	}

	/**
	 * Расчет скидки типа DiscountCount
	 * @param DiscountCount $discountItem Скидка
	 * @param Order $orderTemp Заказ с продуктами без скидок
	 * @param Order $orderDiscount Заказ с продуктами только со скидками
	 */
	private function calculateDiscountCount(DiscountCount $discountItem, &$orderTemp, &$orderDiscount)
	{
		$key = [];
		$discount = $discountItem->discount;
		
		$countExclude = $this->searchArrayInArray($discountItem->exclude, $orderTemp->order);
		if (count($orderTemp->order)-$countExclude >= $discountItem->products)
		{
			foreach ($orderTemp->order as $key => $tempProduct)
			{
				//содержится ли наш продукт в списке исключения
				if (array_search($tempProduct, $discountItem->exclude, true) === false)
				{
					$orderDiscount->push($this->getNewProduct($tempProduct, $discount));
					unset($orderTemp->order[$key]);
				}
			}
		}
	}
	
	/**
	 * Расчет скидки типа DiscountArray
	 * @param DiscountArray $discountItem Скидка
	 * @param Order $orderTemp Заказ с продуктами без скидок
	 * @param Order $orderDiscount Заказ с продуктами только со скидками
	 */
	private function calculateDiscountArray(DiscountArray $discountItem, &$orderTemp, &$orderDiscount)
	{
		$key = [];
		$discount = $discountItem->discount;
		
		if (($key[] = array_search($discountItem->product, $orderTemp->order, true)) === false)
			return;

		foreach ($discountItem->productsArray as $discountProductSecond)
		{
			if (($key[] = array_search($discountProductSecond, $orderTemp->order, true)) !== false)
			{
				$orderDiscount->push($this->getNewProduct($discountItem->product, $discount));
				$orderDiscount->push($this->getNewProduct($discountProductSecond, $discount));
				
				foreach ($key as $value)
					unset($orderTemp->order[$value]);
				
				$this->calculateDiscountArray($discountItem, $orderTemp, $orderDiscount);
			}

		}
	}
	
	/**
	 * Сбор заказа из нескольких заказов
	 * @param Order $orderTemp
	 * @param Order $orderDiscount
	 * @return Order
	 */
	private function collectionNewOrder(Order $orderTemp, Order $orderDiscount)
	{
		$newOrder = new Order();
		$newOrder->push(...array_merge($orderTemp->order, $orderDiscount->order));
		
		return $newOrder;
	}
	
	/**
	 * Получить клон продукта с расчитанной новой ценой с учетом скидки
	 * @param Product $product Продукт
	 * @param numeric $discount Скидка
	 * @return Product
	 */
	private function getNewProduct(Product $product, $discount = 0)
	{
		$newProduct = clone($product);
		$newProduct->newPrice = $newProduct->price - ($newProduct->price * $discount / 100);
		return $newProduct;
	}

	/**
	 * Поиск массива в массиве и возвращение количество вхождение
	 * @param array $array1
	 * @param array $array2
	 * @return integer
	 */
	private function searchArrayInArray(array $array1, array $array2)
	{
		$count = 0;
		foreach ($array1 as $value)
			$count = $count + count(array_keys($array2, $value, true));
		return $count;
	}
}

$productA = new Product('A', 100);
$productB = new Product('B', 100);
$productC = new Product('C', 100);
$productD = new Product('D', 100);
$productE = new Product('E', 100);
$productF = new Product('F', 100);
$productG = new Product('G', 100);
$productH = new Product('H', 100);
$productI = new Product('I', 100);
$productJ = new Product('J', 100);
$productK = new Product('K', 100);
$productL = new Product('L', 100);
$productM = new Product('M', 100);

$discount1 = new DiscountFixed();
$discount1->setProductSet($productA, $productB);
$discount1->setDiscount(10);
$discount2 = new DiscountFixed();
$discount2->setProductSet($productD, $productE);
$discount2->setDiscount(15);
$discount3 = new DiscountArray();
$discount3->setProductSet($productA, [$productK, $productL, $productM]);
$discount3->setDiscount(20);
$discount4 = new DiscountCount();
$discount4->setProductSet(3, $productA, $productC);
$discount4->setDiscount(5);

$order = new Order();
$order->push($productA, $productA, $productM, $productG, $productB, $productE, $productC, $productD);

$manager = new DiscountManager();
$manager->add($discount1, $discount2, $discount3, $discount4);

$calculator = new Calculator();
$calculator->setOrder($order);
$calculator->setManager($manager);

print_r($calculator->youAreNotPrepared());
