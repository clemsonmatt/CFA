<?php

namespace CFA\Hub\PeopleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use CFA\Hub\SharedBundle\Entity\Person;
use CFA\Hub\SharedBundle\Traits\TimestampableTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cfa.influence")
 */
class Influence
{
    use TimestampableTrait;

    public static function getValidTypes($keysOnly = true)
    {
        $types = [
            'Kudos'                            => 'Kudos',
            'Late Arrival'                     => 'Late Arrival',
            'Cash and Coupon Accountabilility' => 'Cash and Coupon Accountability',
            'Other'                            => 'Other',
        ];

        if ($keysOnly) {
            return array_keys($types);
        }

        return $types;
    }

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="CFA\Hub\SharedBundle\Entity\Person", inversedBy="influences")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    protected $person;

    /**
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="CFA\Hub\SharedBundle\Entity\Person", inversedBy="influenceSubmitters")
     * @ORM\JoinColumn(name="submit_user", referencedColumnName="id")
     */
    private $submitUser;

    /**
     * @ORM\Column(name="comment", type="string", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @ORM\Column(name="start_time", type="time", nullable=true)
     */
    private $startTime;

    /**
     * @ORM\Column(name="arrival_time", type="time", nullable=true)
     */
    private $arrivalTime;

    /**
     * @ORM\Column(name="required_balance", type="string", nullable=true)
     */
    private $requiredBalance;

    /**
     * @ORM\Column(name="actual_balance", type="string", nullable=true)
     */
    private $actualBalance;

    /**
     * @ORM\Column(name="approved", type="boolean")
     */
    private $approved;


    /**
     * Get id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set person
     */
    public function setPerson(Person $person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get submitUser
     */
    public function getSubmitUser()
    {
        return $this->submitUser;
    }

    /**
     * Set submitUser
     */
    public function setSubmitUser($submitUser)
    {
        $this->submitUser = $submitUser;

        return $this;
    }

    /**
     * Get comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get start time
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set start time
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get arrivalTime
     */
    public function getArrivalTime()
    {
        return $this->arrivalTime;
    }

    /**
     * Set arrivalTime
     */
    public function setArrivalTime($arrivalTime)
    {
        $this->arrivalTime = $arrivalTime;

        return $this;
    }

    /**
     * Get requiredBalance
     */
    public function getRequiredBalance()
    {
        return $this->requiredBalance;
    }

    /**
     * Set requiredBalance
     */
    public function setRequiredBalance($requiredBalance)
    {
        $this->requiredBalance = $requiredBalance;

        return $this;
    }

    /**
     * Get actualBalance
     */
    public function getActualBalance()
    {
        return $this->actualBalance;
    }

    /**
     * Set actualBalance
     */
    public function setActualBalance($actualBalance)
    {
        $this->actualBalance = $actualBalance;

        return $this;
    }

    /**
     * Is approved
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * Set approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }
}
