<?php
/**
 * Created by PhpStorm.
 * User: anvlad11
 * Date: 01.04.2018
 * Time: 22:56
 */

namespace Application\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

class PostgreUuidGenerator extends AbstractIdGenerator
{
    /**
     * Генератор UUIDv4 из расширения pgcrypto PostgreSQL
     * {@inheritDoc}
     */
    public function generate(EntityManager $em, $entity)
    {
        $conn = $em->getConnection();
        $sql = 'SELECT gen_random_uuid()';

        return $conn->query($sql)->fetchColumn(0);
    }
}