<?php

namespace MichaelKaefer\AdminGeneratorBundle\Twig;

use Composer\InstalledVersions;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class VersionExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_generator_version', [$this, 'version']),
        ];
    }

    public function version(): string
    {
        $prettyVersion = InstalledVersions::getPrettyVersion('michaelkaefer/admin-generator-bundle');

        return $prettyVersion ?? '[No version found - Admin UI bundle is being replaced or provided but is not really installed]';
    }
}
