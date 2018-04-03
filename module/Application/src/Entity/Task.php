<?php
/**
 * Created by PhpStorm.
 * User: anvlad11
 * Date: 31.03.2018
 * Time: 16:37
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tasks")
 */
class Task implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Application\Doctrine\PostgreUuidGenerator")
     */
    protected $id;

    /**
     * @ORM\Column(name="title")
     */
    protected $title;

    /**
     * @ORM\Column(name="description")
     */
    protected $description;

    /**
     * @ORM\Column(name="created_at")
     */
    protected $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Entity\User", inversedBy="tasks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @param string $id UUIv4-compatible string
     */
    public function getId() : string
    {
        return $this->id;
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function getCreatedAt() : string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getUser() : User
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => $this->createdAt
        ];
    }
}