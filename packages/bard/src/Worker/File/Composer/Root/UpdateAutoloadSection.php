<?php

namespace SonsOfPHP\Bard\Worker\File\Composer\Root;

use SonsOfPHP\Bard\JsonFile;
use SonsOfPHP\Bard\Worker\WorkerInterface;

/**
 * @author Joshua Estes <joshua@sonsofphp.com>
 */
final class UpdateAutoloadSection implements WorkerInterface
{
    private JsonFile $pkgComposerJsonFile;

    public function __construct(JsonFile $pkgComposerJsonFile)
    {
        $this->pkgComposerJsonFile = $pkgComposerJsonFile;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(JsonFile $rootComposerJsonFile): JsonFile
    {
        $rootDir = pathinfo($rootComposerJsonFile->getFilename(), PATHINFO_DIRNAME);
        $pkgDir  = pathinfo($this->pkgComposerJsonFile->getFilename(), PATHINFO_DIRNAME);
        $path    = ltrim(str_replace($rootDir, '', $pkgDir), '/');

        $rootAutoloadSection = $rootComposerJsonFile->getSection('autoload');
        $pkgAutoloadSection  = $this->pkgComposerJsonFile->getSection('autoload');

        foreach ($pkgAutoloadSection as $section => $config) {
            if ('psr-4' === $section) {
                foreach ($config as $namespace => $pkgPath) {
                    $rootAutoloadSection['psr-4'][$namespace] = $path.$pkgPath;
                }
            }

            if ('exclude-from-classmap' === $section) {
                foreach ($config as $pkgPath) {
                    $rootAutoloadSection['exclude-from-classmap'][] = $path.$pkgPath;
                }
            }
        }

        $rootAutoloadSection['psr-4']                 = array_unique($rootAutoloadSection['psr-4']);
        $rootAutoloadSection['exclude-from-classmap'] = array_unique($rootAutoloadSection['exclude-from-classmap']);

        return $rootComposerJsonFile->setSection('autoload', $rootAutoloadSection);
    }
}
