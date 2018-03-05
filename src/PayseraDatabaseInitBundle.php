<?php

namespace Paysera\Bundle\DatabaseInitBundle;

use Paysera\Component\DependencyInjection\AddTaggedCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PayseraDatabaseInitBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddTaggedCompilerPass(
            'paysera_database_init.database_initializer',
            'paysera_database_init.initializer',
            'addInitializer',
            ['priority']
        ));
    }
}
