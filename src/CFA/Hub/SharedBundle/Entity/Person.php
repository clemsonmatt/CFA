<?php

namespace CFA\Hub\SharedBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;

use CFA\Hub\SharedBundle\Traits\TimestampableTrait;

/**
 * Person
 *
 * @ORM\Table(name="cfa.person")
 * @ORM\Entity()
 * @UniqueEntity(
 *     fields={"username"},
 *     errorPath="username",
 *     message="This username is already in use."
 * )
 */
class Person implements AdvancedUserInterface, \Serializable
{
    use TimestampableTrait;

    public function __toString()
    {
        return $this->firstName.' '.$this->lastName;
    }

    public static function getValidPositions($keysOnly = true)
    {
        $positions = [
            'Team Member' => 'Team Member',
            'Team Leader' => 'Team Leader',
            'Manager'     => 'Manager',
            'Supervisor'  => 'Supervisor',
            'Director'    => 'Director',
        ];

        if ($keysOnly) {
            return array_keys($positions);
        }

        return $positions;
    }

    public static function getValidRoles($key = null)
    {
        $roles = [
            'ROLE_CFA'           => 'CFA Employee',
            'ROLE_CFA_MANAGE'    => 'CFA Manager',
            'ROLE_CFA_MARKETING' => 'CFA Marketing',
            'ROLE_CFA_ADMIN'     => 'CFA Admin',
        ];

        if ($key !== null) {
            return $roles[$key];
        }

        return $roles;
    }

    public function getImageLocation ()
    {
        if ($this->getPicture() != null) {
            /* user image */
            $imageLocation = '/uploads/'.$this->getPicture();
        } else {
            //$imageLocation = '/uploads/CFA_SquareIcon_text_Reversed_LG.jpg';
            $imageLocation = '/uploads/cow_head.jpg';
        }
        return $imageLocation;
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     * @ORM\Column(name="firstName", type="string", length=50)
     */
    private $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     * @ORM\Column(name="lastName", type="string", length=50)
     */
    private $lastName;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=4, max=255)
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=8, max=255)
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="temp_password", type="string", length=255, nullable=true)
     */
    private $tempPassword;

    /**
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(name="email_private", type="boolean")
     */
    private $emailPrivate;

    /**
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(name="phone_private", type="boolean")
     */
    private $phonePrivate;

    /**
     * @ORM\Column(name="hire_date", type="date")
     */
    private $hireDate;

    /**
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    private $birthday;

    /**
     * @ORM\Column(name="picture", type="string", nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(name="position", type="string")
     */
    private $position;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(name="roles", type="array")
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity="CFA\Hub\PeopleBundle\Entity\Influence", mappedBy="person")
     */
    protected $influences;

    /**
     * @ORM\OneToMany(targetEntity="CFA\Hub\PeopleBundle\Entity\Influence", mappedBy="submitUser")
     */
    protected $influenceSubmitters;

    /**
     * @ORM\OneToMany(targetEntity="CFA\Hub\PeopleBundle\Entity\Task", mappedBy="person")
     */
    protected $tasks;

    /**
     * @ORM\OneToMany(targetEntity="CFA\Hub\PeopleBundle\Entity\Task", mappedBy="submitUser")
     */
    protected $taskSubmitters;


    public function __construct()
    {
        //$this->isActive = true;
        // may not be needed, see section on salt below
        //$this->salt = md5(uniqid(null, true));

        $this->posts = new ArrayCollection();
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->active;
    }


    /**
     * Get isActive
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set isActive
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get notifications
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Set notifications
     */
    public function setNotifications($notifications)
    {
        $this->notifications = $notifications;

        return $this;
    }

    /**
     * Get position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set position
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }



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
     * Set firstName
     *
     * @param string $firstName
     * @return Person
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Person
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Person
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set theme
     *
     * @param string $theme
     * @return Person
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Set picture
     *
     * @param string $picture
     * @return Person
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Person
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set tempPassword
     *
     * @param string $tempPassword
     * @return Person
     */
    public function setTempPassword($tempPassword)
    {
        $this->tempPassword = $tempPassword;

        return $this;
    }

    /**
     * Get tempPassword
     *
     * @return string
     */
    public function getTempPassword()
    {
        return $this->tempPassword;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        //return array('ROLE_CFA');
        return $this->roles;
    }

    /**
     * Set Roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }

    /**
     * Get hireDate
     */
    public function getHireDate()
    {
        return $this->hireDate;
    }

    /**
     * Set hireDate
     */
    public function setHireDate($hireDate)
    {
        $this->hireDate = $hireDate;

        return $this;
    }

    /**
     * Set birthday
     *
     * @param string $birthday
     * @return Person
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return string
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Get email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get emailPrivate
     */
    public function isEmailPrivate()
    {
        return $this->emailPrivate;
    }

    /**
     * Set emailPrivate
     */
    public function setEmailPrivate($emailPrivate)
    {
        $this->emailPrivate = $emailPrivate;

        return $this;
    }

    /**
     * Get phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Is phonePrivate
     */
    public function isPhonePrivate()
    {
        return $this->phonePrivate;
    }

    /**
     * Set phonePrivate
     */
    public function setPhonePrivate($phonePrivate)
    {
        $this->phonePrivate = $phonePrivate;

        return $this;
    }

    /**
     * Get tasks
     */
    public function getTasks()
    {
        return $this->tasks;
    }
}
