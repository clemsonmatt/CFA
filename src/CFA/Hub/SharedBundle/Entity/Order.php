<?php

namespace CFA\Hub\SharedBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use CFA\Hub\SharedBundle\Entity\Customer;
use CFA\Hub\SharedBundle\Entity\Product;
use CFA\Hub\SharedBundle\Entity\Sale;
use CFA\Hub\SharedBundle\Traits\TimestampableTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cfa.order")
 */
class Order
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="CFA\Hub\SharedBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     **/
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="CFA\Hub\SharedBundle\Entity\Sale", inversedBy="orders")
     * @ORM\JoinColumn(name="sale_id", referencedColumnName="id")
     **/
    private $sale;

    /**
     * @ORM\Column(name="qty", type="integer")
     */
    private $qty;

    /**
     * @ORM\Column(name="special_request", type="text", nullable=true)
     */
    private $specialRequest;


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
     * Set product
     *
     * @param Product $product
     * @return Order
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set sale
     *
     * @param Sale $sale
     * @return Order
     */
    public function setSale(Sale $sale)
    {
        $this->sale = $sale;

        return $this;
    }

    /**
     * Get sale
     *
     * @return Sale
     */
    public function getSale()
    {
        return $this->sale;
    }

    /**
     * Set qty
     *
     * @param integer $qty
     * @return Order
     */
    public function setQty($qty)
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * Get qty
     *
     * @return integer
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * Set specialRequest
     *
     * @param text $specialRequest
     * @return Order
     */
    public function setSpecialRequest($specialRequest)
    {
        $this->specialRequest = $specialRequest;

        return $this;
    }

    /**
     * Get specialRequest
     *
     * @return text
     */
    public function getSpecialRequest()
    {
        return $this->specialRequest;
    }
}
