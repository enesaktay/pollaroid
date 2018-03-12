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
     * @ORM\Column(type="boolean")
     */
    protected $acceptedTos;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $acceptedPp;

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
    public function getAnswerArrayId()
    {
        return $this->answerArrayId;
    }

    /**
     * @param mixed $answerArrayId
     */
    public function setAnswerArrayId($answerArrayId)
    {
        $this->answerArrayId = $answerArrayId;
    }

    /**
     * @return mixed
     */
    public function getAcceptedTos()
    {
        return $this->acceptedTos;
    }

    /**
     * @param mixed $acceptedTos
     */
    public function setAcceptedTos($acceptedTos)
    {
        $this->acceptedTos = $acceptedTos;
    }

    /**
     * @return mixed
     */
    public function getAcceptedPp()
    {
        return $this->acceptedPp;
    }

    /**
     * @param mixed $acceptedPp
     */
    public function setAcceptedPp($acceptedPp)
    {
        $this->acceptedPp = $acceptedPp;
    }

}