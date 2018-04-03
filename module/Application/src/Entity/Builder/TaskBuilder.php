<?php
/**
 * Created by PhpStorm.
 * User: anvlad11
 * Date: 02.04.2018
 * Time: 2:14
 */

namespace Application\Entity\Builder;

use Application\Entity\Task;
use Application\Entity\User;

class TaskBuilder
{
    private $title = "";

    private $description = "";

    private $createdAt = "";

    /**
     * @var User
     */
    private $user;

    /**
     * @var Task
     */
    private $task;

    public function __construct(Task $task = null)
    {
        if ($task)
        {
            $this->title = $task->getTitle();
            $this->description = $task->getDescription();
            $this->createdAt = $task->getCreatedAt();
            $this->user = $task->getUser();
            $this->task = $task;
        }
    }

    public function withTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    public function withDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    public function withCreatedAt(string $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function forUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function build() : Task
    {
        if (empty($this->task))
        {
            $this->task = new Task();
        }
        $this->task->setTitle($this->title);
        $this->task->setDescription($this->description);
        $this->task->setCreatedAt($this->createdAt);
        $this->task->setUser($this->user);
        return $this->task;
    }

}