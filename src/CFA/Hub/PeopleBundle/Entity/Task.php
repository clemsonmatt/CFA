<?php

namespace CFA\Hub\PeopleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use CFA\Hub\SharedBundle\Entity\Person;
use CFA\Hub\SharedBundle\Traits\TimestampableTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cfa.action")
 */
class Task
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="CFA\Hub\SharedBundle\Entity\Person", inversedBy="tasks")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    protected $person;

    /**
     * @ORM\Column(name="action", type="string")
     */
    private $action;

    /**
     * @ORM\Column(name="dueDate", type="date", nullable=true)
     */
    private $dueDate;

    /**
     * @ORM\Column(name="completed", type="boolean")
     */
    private $completed;

    /**
     * @ORM\Column(name="admin_approve_create", type="boolean")
     */
    private $adminApprovedCreate;

    /**
     * @ORM\Column(name="admin_approve_complete", type="boolean")
     */
    private $adminApprovedComplete;

    /**
     * @ORM\ManyToOne(targetEntity="CFA\Hub\SharedBundle\Entity\Person", inversedBy="taskSubmitters")
     * @ORM\JoinColumn(name="submit_user", referencedColumnName="id")
     */
    private $submitUser;


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
     * Get action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set action
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get dueDate
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Set dueDate
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Is completed
     */
    public function isCompleted()
    {
        return $this->completed;
    }

    /**
     * Set completed
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * Is adminApprovedCreate
     */
    public function isAdminApprovedCreate()
    {
        return $this->adminApprovedCreate;
    }

    /**
     * Set adminApprovedCreate
     */
    public function setAdminApprovedCreate($adminApprovedCreate)
    {
        $this->adminApprovedCreate = $adminApprovedCreate;

        return $this;
    }

    /**
     * Is adminApprovedComplete
     */
    public function isAdminApprovedComplete()
    {
        return $this->adminApprovedComplete;
    }

    /**
     * Set adminApprovedComplete
     */
    public function setAdminApprovedComplete($adminApprovedComplete)
    {
        $this->adminApprovedComplete = $adminApprovedComplete;

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
}
