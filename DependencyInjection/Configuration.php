<?php

namespace Ioni\PayzenBundle\DependencyInjection;

use Ioni\PayzenBundle\Service\SignatureHandler;
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
                ->enumNode('ctx_mode')->values(SignatureHandler::MODES)->defaultValue(SignatureHandler::MODE_TEST)->cannotBeEmpty()->end()
                ->scalarNode('trans_numbers_path')->defaultValue('%kernel.root_dir%/../var/payzen/trans_numbers')->cannotBeEmpty()->end()
                ->scalarNode('wsdl')->defaultValue('https://secure.payzen.eu/vads-ws/v5?wsdl')->end()
//                ->scalarNode('namespace')->end()
                ->arrayNode('certificates')
                    ->children()
                        ->scalarNode('prod')->end()
                        ->scalarNode('test')->end()
                    ->end()
                ->end()
                ->scalarNode('return_route')->end()
                ->arrayNode('fetchers')
                    ->children()
                        ->scalarNode('transaction_fetcher')->defaultValue('ioni_payzen.fetchers.simple_transaction_fetcher')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
