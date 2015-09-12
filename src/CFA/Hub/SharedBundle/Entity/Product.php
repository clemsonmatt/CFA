<?php

namespace CFA\Hub\SharedBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use CFA\Hub\SharedBundle\Entity\ProductGroup;
use CFA\Hub\SharedBundle\Traits\TimestampableTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cfa.product")
 */
class Product
{
    use TimestampableTrait;

    public function __toString()
    {
        return $this->name;
    }

    public static function getCategoryList($key = null)
    {
        $list = [
            'classic'          => 'Classics',
            'side-item'        => 'Side Items',
            'breakfast'        => 'Breakfast',
            'dessert'          => 'Desserts',
            'beverage'         => 'Beverages',
            'tray'             => 'Chick-fil-A Trays',
            'wrap-salad'       => 'Wrap and Salads',
            'dressing-topping' => 'Dressings and Toppings',
            'kid-meal'         => 'Kid\'s Meal',
        ];

        if ($key !== null) {
            return $list[$key];
        }

        return $list;
    }

    public static function getGroupList($key = null)
    {
        $list = [
            'entree-tray'    => 'Chick-fil-A Entree Trays',
            'breakfast-tray' => 'Chick-fil-A Breakfast Trays',
            'sides-desserts' => 'Chick-fil-A Sides and Desserts',
            'drinks'         => 'Chick-fil-A Drinks by the Gallon',
        ];

        if ($key !== null) {
            return $list[$key];
        }

        return $list;
    }


    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="category", type="string", length=255)
     */
    private $category;

    /**
     * @ORM\Column(name="sub_group", type="string", length=255, nullable=true)
     */
    private $group;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name="serving_size", type="string", length=255, nullable=true)
     */
    private $servingSize;

    /**
     * @ORM\Column(name="count_description", type="string", length=255, nullable=true)
     */
    private $countDescription;

    /**
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @ORM\Column(name="photo", type="string", nullable=true)
     */
    private $photo;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return Product
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set group
     *
     * @param string $group
     * @return Product
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set servingSize
     *
     * @param string $servingSize
     * @return Product
     */
    public function setServingSize($servingSize)
    {
        $this->servingSize = $servingSize;

        return $this;
    }

    /**
     * Get servingSize
     *
     * @return string
     */
    public function getServingSize()
    {
        return $this->servingSize;
    }

    /**
     * Set countDescription
     *
     * @param string $countDescription
     * @return Product
     */
    public function setCountDescription($countDescription)
    {
        $this->countDescription = $countDescription;

        return $this;
    }

    /**
     * Get countDescription
     *
     * @return string
     */
    public function getCountDescription()
    {
        return $this->countDescription;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return Product
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }
}
