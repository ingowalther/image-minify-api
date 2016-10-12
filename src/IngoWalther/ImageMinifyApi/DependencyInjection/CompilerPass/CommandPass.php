<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 10/10/16
 * Time: 23:20
 */
namespace IngoWalther\ImageMinifyApi\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CommandPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('console')) {
            return;
        }

        $definition = $container->findDefinition('console');
        $taggedServices = $container->findTaggedServiceIds('console.command');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    'add',
                    array(new Reference($id))
                );
            }
        }
    }
}