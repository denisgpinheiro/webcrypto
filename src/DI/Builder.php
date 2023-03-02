<?php
declare(strict_types=1);

namespace WBCrypto\DI;

use DI\Container;
use DI\ContainerBuilder;

    class Builder
    {
        private static $builder;

        /**
         * buildContainer
         *
         * Returns a new Container Object
         *
         * @return Container
         */
        public static function buildContainer(): Container
        {
            self::$builder = new ContainerBuilder();
            self::$builder->useAutowiring(true);
            self::$builder->addDefinitions(__DIR__.'/config.php');
            //die(__DIR__.'/config.php');

            return self::$builder->build();
        }
    }