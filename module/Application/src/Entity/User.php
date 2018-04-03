<?php
/**
 * Created by PhpStorm.
 * User: anvlad11
 * Date: 31.03.2018
 * Time: 16:46
 */

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Mapping as ORM;

use Application\Entity\Task;
use Application\Entity\Token;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Application\Doctrine\PostgreUuidGenerator")
     */
    protected $id;

    /**
     * @ORM\Column(name="login", type="string")
     */
    protected $login;

    /**
     * @ORM\OneToMany(targetEntity="\Application\Entity\Task", mappedBy="user", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="user_id")
     */
    protected $tasks;

    /**
     * @ORM\OneToMany(targetEntity="\Application\Entity\Token", mappedBy="user", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="user_id")
     */
    protected $tokens;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->tokens = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id UUIv4-compatible string
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login)
    {
        $this->login = $login;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    public function getTaskWithId(string $taskId) : ?Task
    {
        $filtered = $this->tasks->filter(function (Task $task) use ($taskId){
            return $task->getId() === $taskId;
        });
        return $filtered->isEmpty() ? null : $filtered->first();
    }

    public function addTask(Task $task)
    {
        if (!$this->tasks->contains($task))
        {
            $this->tasks[] = $task;
        }
    }

    public function removeTask(Task $task)
    {
        $this->tasks->removeElement($task);
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    public function addToken(Token $token)
    {
        if (!$this->tokens->contains($token))
        {
            $this->tokens[] = $token;
        }
    }

    public function removeToken(Token $token)
    {
        $this->tokens->removeElement($token);
    }
}