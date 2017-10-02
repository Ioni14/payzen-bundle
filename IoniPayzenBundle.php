<?php

namespace Ioni\PayzenBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class IoniPayzenBundle.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class IoniPayzenBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $modelFcqn = 'Ioni\PayzenBundle\Model';

        if (class_exists(DoctrineOrmMappingsPass::class) && $container->has('doctrine.orm.entity_manager')) {
            $container->addCompilerPass(
                DoctrineOrmMappingsPass::createYamlMappingDriver(
                    [realpath(__DIR__.'/Resources/config/doctrine-orm') => $modelFcqn],
                    ['doctrine.orm.entity_manager'],
                    false,
                    ['IoniPayzenBundle' => $modelFcqn]
                )
            );
        }
    }
}
