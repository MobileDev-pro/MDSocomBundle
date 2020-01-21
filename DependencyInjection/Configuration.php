<?php

namespace MD\SocomBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package MD\SocomBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    const ALIAS = 'md_socom';

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('md_socom');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('price_otag_ht')->end()
                ->scalarNode('pdf_directory')->end()

                ->arrayNode('entities')
                    ->children()
                        ->scalarNode('user')->end()
                        ->scalarNode('operator')->end()
                    ->end()
                ->end()

                ->arrayNode('api')
                    ->children()
                        ->scalarNode('key')->end()
                        ->scalarNode('url')->end()
                    ->end()
                ->end()

            ->end()
        ;

        return $treeBuilder;
    }
}
