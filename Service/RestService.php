<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;

class RestService
{
    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->metaFactory = $manager->getMetadataFactory();
    }

    /** @var EntityManagerInterface  */
    protected $manager;

    /** @var ClassMetadataFactory  */
    protected $metaFactory;


    /**
     * @param string     $resource
     * @param array  $query
     *
     * @return bool
     */
    public function head($resource, $query)
    {
        return $this->manager->getRepository($resource)->findBy($query) != null;
    }

    /**
     * @param string $resource
     * @param array  $query
     *
     * @return object
     */
    public function get($resource, array $query = array())
    {
        return $this->manager->getRepository($resource)->findBy($query);
    }

    /**
     * @param object $entity
     *
     * @return bool
     */
    public function post($entity)
    {
        try {
            $this->manager->persist($entity);
            $this->manager->flush($entity);
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }

    /**
     * @param object $entity
     *
     * @return bool
     */
    public function put($entity)
    {
        try {
            $this->manager->remove($entity);
            $this->manager->persist($entity);
            $this->manager->flush($entity);
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }

    /**
     * @param object $entity
     *
     * @return bool
     */
    public function patch($entity)
    {
        try {
            $this->manager->merge($entity);
            $this->manager->flush($entity);
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }

    /**
     * @param object $entity
     *
     * @return bool
     */
    public function delete($entity)
    {
        try {
            $this->manager->remove($entity);
            $this->manager->flush($entity);
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }
}
