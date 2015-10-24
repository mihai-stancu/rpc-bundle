<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\EventListener;

use Doctrine\Common\Persistence\ObjectRepository;
use MS\RpcBundle\Factory\RequestFactory;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class AuthenticationListener implements ListenerInterface
{
    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /** @var AuthenticationManagerInterface */
    protected $authenticationManager;

    /** @var string Uniquely identifies the secured area */
    protected $providerKey;

    /** @var  RequestFactory */
    protected $requestFactory;

    /** @var  ObjectRepository */
    protected $userRepository;

    /**
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($username and $password) {
            $unauthenticatedToken = new UsernamePasswordToken($username, $password, $this->providerKey);
            $authenticatedToken = $this->authenticationManager->authenticate($unauthenticatedToken);
            $this->tokenStorage->setToken($authenticatedToken);
        }
    }
}
