<?php
/**
 * Created by PhpStorm.
 * User: anvlad11
 * Date: 31.03.2018
 * Time: 17:18
 */

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="tokens")
 */
class Token
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Application\Doctrine\PostgreUuidGenerator")
     */
    protected $id;

    /**
     * @ORM\Column(name="token")
     */
    protected $token;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Entity\User", inversedBy="tokens")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="\Application\Entity\Permission", mappedBy="token", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="token_id")
     */
    protected $permissions;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
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

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token)
    {
        $this->token = $token;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission)
    {
        if (!$this->hasPermission($permission->getPermission()))
        {
            $this->permissions[] = $permission;
        }
    }

    public function removePermission(Permission $permission)
    {
        $this->permissions->removeElement($permission);
    }

    /**
     * @param int $permissionConst from Permission class consts, 1 or 2
     * @return bool
     */
    public function hasPermission(int $permissionConst)
    {
        return !($this->permissions->filter(
            function (Permission $permission) use ($permissionConst) {
                return $permission->getPermission() === $permissionConst;
            })->isEmpty()
        );
    }

}