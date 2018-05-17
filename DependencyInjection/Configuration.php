<?php

namespace MD\SocomBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const ALIAS = 'md_socom';

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(self::ALIAS);
        $rootNode
            ->children()
                ->scalarNode('price_otag_ht')->end()
                ->scalarNode('pdf_directory')->end()
                ->arrayNode('api')
                ->children()
                    ->scalarNode('key')->end()
                    ->scalarNode('url')->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
