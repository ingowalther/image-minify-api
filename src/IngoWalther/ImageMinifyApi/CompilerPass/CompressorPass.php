<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 10/10/16
 * Time: 23:20
 */
namespace IngoWalther\ImageMinifyApi\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CompressorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('minify')) {
            return;
        }

        $definition = $container->findDefinition('minify');
        $taggedServices = $container->findTaggedServiceIds('image.compressor');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    'addCompressor',
                    array(new Reference($id))
                );
            }
        }
    }
}