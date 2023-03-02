<?php
namespace WBCrypto;

use WBCrypto\DI\Builder;
use Pecee\SimpleRouter\ClassLoader\IClassLoader;
use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;

    class MyClassLoader implements IClassLoader
    {
        protected $container;
        public function __construct()
        {
            // Create our new php-di container
            //$this->container = (new \DI\ContainerBuilder())
            $this->container = (\WBCrypto\DI\Builder::buildContainer());
//                ->useAutowiring(true)
//                ->build();
        }
        /**
         * Load class
         *
         * @param string $class
         * @return object
         * @throws NotFoundHttpException
         */
        public function loadClass(string $class)
        {
            if (class_exists($class) === false) {
                throw new NotFoundHttpException(sprintf('Class "%s" does not exist', $class), 404);
            }
            try {
                return $this->container->get($class);
            } catch (\Exception $e) {
                throw new NotFoundHttpException($e->getMessage(), (int)$e->getCode(), $e->getPrevious());
            }
        }
        /**
         * Called when loading class method
         * @param object $class
         * @param string $method
         * @param array $parameters
         * @return object
         */
        public function loadClassMethod($class, string $method, array $parameters)
        {
            try {
                return $this->container->call([$class, $method], $parameters);
            } catch (\Exception $e) {
                throw new NotFoundHttpException($e->getMessage(), (int)$e->getCode(), $e->getPrevious());
            }
        }
        /**
         * Load closure
         *
         * @param Callable $closure
         * @param array $parameters
         * @return mixed
         */
        public function loadClosure(callable $closure, array $parameters)
        {
            try {
                return $this->container->call($closure, $parameters);
            } catch (\Exception $e) {
                throw new NotFoundHttpException($e->getMessage(), (int)$e->getCode(), $e->getPrevious());
            }
        }
    }