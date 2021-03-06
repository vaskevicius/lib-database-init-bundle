<?php

namespace Paysera\Tests;

use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestAppKernel extends Kernel
{
    private $baseDir;
    private $configFile;
    private $additionalBundles;
    private $testCase;

    public function __construct($baseDir, $configFile, array $additionalBundles, $testCase)
    {
        $this->baseDir = $baseDir;
        $this->configFile = $configFile;
        $this->additionalBundles = $additionalBundles;
        $this->testCase = $testCase;

        if ($testCase !== null && !is_dir($baseDir . '/' . $testCase)) {
            throw new InvalidArgumentException(sprintf('The test case "%s" does not exist.', $testCase));
        }

        $configFilePath = null;
        if ($testCase === null) {
            $configFilePath = $baseDir . '/' . $configFile;
        } else {
            $configFilePath = $baseDir . '/' . $testCase . '/' . $configFile;
        }

        if (!is_file($configFilePath)) {
            throw new InvalidArgumentException(sprintf('The config file "%s" does not exist.', $configFile));
        }

        parent::__construct('test', true);
    }

    public function registerBundles()
    {
        return array_merge(
            [
                new FrameworkBundle(),
            ],
            $this->additionalBundles
        );
    }
    public function getCacheDir()
    {
        if ($this->testCase !== null) {
            return $this->baseDir . '/cache/' . $this->testCase . '/cache/' . $this->environment;
        }
        return $this->baseDir . '/cache/' . $this->environment;
    }

    public function getLogDir()
    {
        if ($this->testCase !== null) {
            return $this->baseDir . '/logs/' . $this->testCase . '/logs';
        }
        return $this->baseDir . '/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->baseDir . '/' . $this->configFile);
    }

    public function serialize()
    {
        return serialize([$this->baseDir, $this->configFile, $this->bundles, $this->testCase]);
    }

    public function unserialize($str)
    {
        $a = unserialize($str);
        $this->__construct($a[0], $a[1], $a[2], $a[3]);
    }

    protected function getKernelParameters()
    {
        $parameters = parent::getKernelParameters();
        $parameters['kernel.test_case'] = $this->testCase;

        return $parameters;
    }
}
