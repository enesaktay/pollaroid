<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="poll")
 * @ORM\HasLifecycleCallbacks
 */
class Poll
{
    /**
     * @ORM\Column(type="shortid", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="PUGX\Shortid\Doctrine\Generator\ShortidGenerator")
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
     * @ORM\Column(type="string", length=100)
     */
    protected $question;

    /**
     * @ORM\Column(type="json_array")
     */
    protected $answer;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $expirationDate;

    /**
     * @ORM\OneToMany(targetEntity="Vote", mappedBy="poll")
     */
    protected $votes;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $allowMultipleAnswers;

    /**
     * @ORM\Column(type="integer", length=2)
     */
    protected $allowedAnswerCount;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $acceptedTos;

//    /**
//     * @ORM\Column(type="boolean")
//     */
//    protected $acceptedPp;

    /**
     * Poll constructor.
     */
    public function __construct()
    {
        $this->votes = new ArrayCollection();
    }

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
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return mixed
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param mixed $answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return mixed
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param mixed $expirationDate
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
    }

    /**
     * @return mixed
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param mixed $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    }

    /**
     * @return mixed
     */
    public function getAllowMultipleAnswers()
    {
        return $this->allowMultipleAnswers;
    }

    /**
     * @param mixed $allowMultipleAnswers
     */
    public function setAllowMultipleAnswers($allowMultipleAnswers)
    {
        $this->allowMultipleAnswers = $allowMultipleAnswers;
    }

    /**
     * @return mixed
     */
    public function getAllowedAnswerCount()
    {
        return $this->allowedAnswerCount;
    }

    /**
     * @param mixed $allowedAnswerCount
     */
    public function setAllowedAnswerCount($allowedAnswerCount)
    {
        $this->allowedAnswerCount = $allowedAnswerCount;
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

//    /**
//     * @return mixed
//     */
//    public function getAcceptedPp()
//    {
//        return $this->acceptedPp;
//    }
//
//    /**
//     * @param mixed $acceptedPp
//     */
//    public function setAcceptedPp($acceptedPp)
//    {
//        $this->acceptedPp = $acceptedPp;
//    }


}