<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="vote")
 * @ORM\HasLifecycleCallbacks
 */
class Vote
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="shortid", length=20)
     * @ORM\ManyToOne(targetEntity="Poll", inversedBy="votes")
     * @ORM\JoinColumn(name="poll_id", referencedColumnName="id")
     */
    protected $poll;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $answerArrayId;

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime('now'));

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getPoll()
    {
        return $this->poll;
    }

    /**
     * @param mixed $poll
     */
    public function setPoll($poll)
    {
        $this->poll = $poll;
    }

    /**
     * @return mixed
     */
    public function getAnswerJsonId()
    {
        return $this->answerJsonId;
    }

    /**
     * @param mixed $answerJsonId
     */
    public function setAnswerJsonId($answerJsonId)
    {
        $this->answerJsonId = $answerJsonId;
    }

}