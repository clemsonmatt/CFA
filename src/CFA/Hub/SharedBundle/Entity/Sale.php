<?php

namespace CFA\Hub\SharedBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use CFA\Hub\SharedBundle\Entity\Customer;
use CFA\Hub\SharedBundle\Traits\TimestampableTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cfa.sale")
 */
class Sale
{
    use TimestampableTrait;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getTotal()
    {
        $total = 0;

        foreach ($this->getOrders() as $order) {
            $total += $order->getQty() * $order->getProduct()->getPrice();
        }

        return $total;
    }


    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="CFA\Hub\SharedBundle\Entity\Customer", inversedBy="sales")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=true)
     **/
    private $customer;

    /**
     * @ORM\Column(name="comments", type="text", nullable=true)
     */
    private $comments;

    /**
     * @ORM\Column(name="pickup_date", type="date", length=255, nullable=true)
     */
    private $pickupDate;

    /**
     * @ORM\Column(name="pickup_time", type="time", length=255, nullable=true)
     */
    private $pickupTime;

    /**
     * @ORM\OneToMany(targetEntity="CFA\Hub\SharedBundle\Entity\Order", mappedBy="sale", orphanRemoval=true)
     */
    private $orders;


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
     * Set customer
     *
     * @param Customer $customer
     * @return Sale
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set comments
     *
     * @param text $comments
     * @return Sale
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return text
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set pickupDate
     *
     * @param Date $pickupDate
     * @return Sale
     */
    public function setPickupDate($pickupDate)
    {
        $this->pickupDate = $pickupDate;

        return $this;
    }

    /**
     * Get pickupDate
     *
     * @return Date
     */
    public function getPickupDate()
    {
        return $this->pickupDate;
    }

    /**
     * Set pickupTime
     *
     * @param string $pickupTime
     * @return Sale
     */
    public function setPickupTime($pickupTime)
    {
        $this->pickupTime = $pickupTime;

        return $this;
    }

    /**
     * Get pickupTime
     *
     * @return string
     */
    public function getPickupTime()
    {
        return $this->pickupTime;
    }

    /**
     * Set orders
     *
     * @param ArrayCollection $orders
     * @return Sale
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * Get orders
     *
     * @return ArrayCollection
     */
    public function getOrders()
    {
        return $this->orders;
    }
}
