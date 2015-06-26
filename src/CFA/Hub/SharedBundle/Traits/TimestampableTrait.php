<?php

namespace CFA\Hub\SharedBundle\Traits;

trait TimestampableTrait
{
    /**
     * @var integer
     *
     * @Gedmo\Mapping\Annotation\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="integer", nullable=true)
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @Gedmo\Mapping\Annotation\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="integer", nullable=true)
     */
    private $updatedAt;

    /**
    * Get createdAt
    *
    * @return \DateTime
    */
    public function getCreatedAt()
    {
        return new \DateTime('@'.$this->createdAt);
    }

    /**
    * Get updatedAt
    *
    * @return \DateTime
    */
    public function getUpdatedAt()
    {
        return new \DateTime('@'.$this->updatedAt);
    }
}
