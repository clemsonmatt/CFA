<?php

namespace CFA\EventRegisterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use CFA\EventRegisterBundle\Traits\TimestampableTrait;
use CFA\EventRegisterBundle\Entity\Event;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cfa_event.transaction")
 */
class Transaction
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="array", name="items")
     */
    private $items;

    /**
     * @ORM\Column(type="string", name="total", length=255)
     */
    private $total;

    /**
     * @ORM\Column(type="string", name="money_recieved", length=255, nullable=true)
     */
    private $moneyRecieved;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="transactions")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     */
    protected $event;


    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set items
     *
     * @param mixed $items
     * @return Transaction
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get items
     *
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set total
     *
     * @param string $total
     * @return Transaction
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set moneyRecieved
     *
     * @param string $moneyRecieved
     * @return Transaction
     */
    public function setMoneyRecieved($moneyRecieved)
    {
        $this->moneyRecieved = $moneyRecieved;

        return $this;
    }

    /**
     * Get moneyRecieved
     *
     * @return string
     */
    public function getMoneyRecieved()
    {
        return $this->moneyRecieved;
    }

    /**
     * Set event
     *
     * @param Event $event
     * @return Transaction
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }
}
