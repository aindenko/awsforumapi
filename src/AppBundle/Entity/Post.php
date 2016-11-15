<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 *
 * @JMS\ExclusionPolicy("none")
 */
class Post
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"list", "detail"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @JMS\Groups({"list", "detail"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="image_full_url", type="string", length=255)
     * @JMS\Groups({"list", "detail"})
     */
    private $imageFullUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="image_thumb_url", type="string", length=255)
     * @JMS\Groups({"list", "detail"})
     */
    private $imageThumbUrl;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true, nullable=false)
     **/
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @JMS\Groups({"list", "detail"})
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="views", type="integer")
     * @JMS\Groups({"list", "detail"})
     */
    private $views;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Post
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
     * Set imageFullUrl
     *
     * @param string $imageFullUrl
     *
     * @return Post
     */
    public function setImageFullUrl($imageFullUrl)
    {
        $this->imageFullUrl = $imageFullUrl;

        return $this;
    }

    /**
     * Get imageFullUrl
     *
     * @return string
     */
    public function getImageFullUrl()
    {
        return $this->imageFullUrl;
    }

    /**
     * Set imageThumbUrl
     *
     * @param string $imageThumbUrl
     *
     * @return Post
     */
    public function setImageThumbUrl($imageThumbUrl)
    {
        $this->imageThumbUrl = $imageThumbUrl;

        return $this;
    }

    /**
     * Get imageThumbUrl
     *
     * @return string
     */
    public function getImageThumbUrl()
    {
        return $this->imageThumbUrl;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Post
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set views
     *
     * @param integer $views
     *
     * @return Post
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }
}

