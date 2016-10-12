<?php

namespace IngoWalther\ImageMinifyApi\DependencyInjection;

use IngoWalther\ImageMinifyApi\DependencyInjection\CompilerPass\CommandPass;
use IngoWalther\ImageMinifyApi\DependencyInjection\CompilerPass\CompressorPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class ContainerBuilder
 * @package IngoWalther\ImageMinifyApi\DependencyInjection
 */
class ContainerBuilder
{
    /**
     * @param $basePath
     * @return ContainerBuilder
     */
    public function build($basePath)
    {
        $container = new SymfonyContainerBuilder();
        $container->addCompilerPass(new CompressorPass());
        $container->addCompilerPass(new CommandPass());
        $container->setParameter('basePath', $basePath);
        $loader = new YamlFileLoader($container, new FileLocator($basePath .'/config'));
        $loader->load('services.yml');
        $container->compile();

        return $container;
    }
}