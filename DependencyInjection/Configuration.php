<?php

namespace Ioni\PayzenBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ioni_payzen');

        $rootNode
            ->children()
                ->scalarNode('site_id')->cannotBeEmpty()->end()
                ->scalarNode('ctx_mode')->defaultValue('TEST')->cannotBeEmpty()->end()
                ->scalarNode('trans_numbers_path')->cannotBeEmpty()->end()
                ->scalarNode('wsdl')->end()
//                ->scalarNode('namespace')->end()
                ->arrayNode('certificates')
                    ->children()
                        ->scalarNode('prod')->end()
                        ->scalarNode('test')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
