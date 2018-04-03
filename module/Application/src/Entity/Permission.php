<?php
/**
 * Created by PhpStorm.
 * User: anvlad11
 * Date: 31.03.2018
 * Time: 17:39
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="permissions")
 */
class Permission
{
    const PERMISSION_READ = 1; //permission to read tasks
    const PERMISSION_WRITE = 2; //permission to write tasks

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Application\Doctrine\PostgreUuidGenerator")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Entity\Token", inversedBy="permissions")
     * @ORM\JoinColumn(name="token_id", referencedColumnName="id")
     */
    protected $token;

    /**
     * @ORM\Column(name="permission")
     */
    protected $permission;

    /**
     * Permission constructor.
     * @param int $permission
     */
    public function __construct(int $permission)
    {
        $this->permission = $permission;
    }

    public function getId() : string
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

    public function getPermission()
    {
        return $this->permission;
    }

    public function setPermission(int $permission)
    {
        $this->permission = $permission;
    }

    public function getToken() : Token
    {
        return $this->token;
    }

    public function setToken(Token $token)
    {
        $this->token = $token;
    }
}