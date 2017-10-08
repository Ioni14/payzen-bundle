<?php

namespace Ioni\PayzenBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class IoniPayzenExtension.
 *
 * @author Thomas Talbot <talbot.thomas14@gmail.com>
 */
class IoniPayzenExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // injects parameters to the services
        $formFieldsGeneratorDef = $container->getDefinition('ioni_payzen.form_fields_generator');
        $formFieldsGeneratorDef->addMethodCall('setTransNumbersPath', [$config['trans_numbers_path']]);
        $formFieldsGeneratorDef->addMethodCall('setSiteId', [$config['site_id']]);

        $signatureHandlerDef = $container->getDefinition('ioni_payzen.signature_handler');
        $signatureHandlerDef->addMethodCall('setCtxMode', [$config['ctx_mode']]);
        $signatureHandlerDef->addMethodCall('setCertificateProd', [$config['certificates']['prod'] ?? '']);
        $signatureHandlerDef->addMethodCall('setCertificateTest', [$config['certificates']['test'] ?? '']);

        $container->setParameter('payzen_return_route', $config['return_route']);
    }
}
