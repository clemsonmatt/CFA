<?php

namespace CFA\EventRegisterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use CFA\EventRegisterBundle\Traits\TimestampableTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cfa_event.event")
 */
class Event
{
    use TimestampableTrait;

    public function __toString()
    {
        return $this->title;
    }

    public function getSales()
    {
        $total = 0.00;

        foreach ($this->transactions as $sale) {
            $total += $sale->getTotal();
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
     * @ORM\Column(type="string", name="title", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="date", name="date")
     */
    private $date;

    /**
     * @ORM\Column(type="array", name="menu_items")
     */
    private $menuItems;

    /**
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="event")
     */
    private $transactions;


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
     * Set title
     *
     * @param string $title
     * @return Event
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set date
     *
     * @param Date $date
     * @return Event
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return Date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set menuItems
     *
     * @param mixed $menuItems
     * @return Event
     */
    public function setMenuItems($menuItems)
    {
        $this->menuItems = $menuItems;

        return $this;
    }

    /**
     * Get menuItems
     *
     * @return ArrayCollection
     */
    public function getMenuItems()
    {
        return $this->menuItems;
    }

    /**
     * Get transactions
     *
     * @return string
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}
