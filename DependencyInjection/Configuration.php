<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\DependencyInjection;

use MS\RpcBundle\Service\Proxy;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    const ROOT = 'ms_rpc';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(static::ROOT);

        $rootNode
            ->children()
                ->arrayNode('proxy')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('directory')->defaultValue('%kernel.cache_dir%/ms_rpc/proxies/')->end()
                        ->scalarNode('namespace')->defaultValue('MS\\RpcBundle\\Proxies\\__CG__\\')->end()
                        ->scalarNode('base_trait')->defaultValue(Proxy::class)->end()
                    ->end()
            ->end();

        return $treeBuilder;
    }
}
