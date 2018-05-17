<?php

namespace MD\SocomBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class MDSocomExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(Configuration::ALIAS . ".pdf_directory", $config['pdf_directory']);
        $container->setParameter(Configuration::ALIAS . ".price_otag_ht", $config['price_otag_ht']);

        foreach ($config['api'] as $key => $val) {
            $container->setParameter(Configuration::ALIAS . ".api.$key", $val);
        }
    }
}
