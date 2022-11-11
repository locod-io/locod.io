<?php

/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Locodio\Infrastructure\Web\Twig;

use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ViteBuildExtension extends AbstractExtension
{
    public function __construct(private KernelInterface $kernel)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'vite_build_script_tags',
                [$this, 'renderViteBuildScriptTags'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'vite_build_style_tags',
                [$this, 'renderViteBuildStyleTags'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function renderViteBuildScriptTags(string $app, string $key): string
    {
        $manifest = $this->readManifestFile($app);
        $jsFile = $manifest[$key]['file'];
        $scriptTag = "<script type=\"module\" src=\"/$jsFile\"></script>";

        return $scriptTag;
    }

    public function renderViteBuildStyleTags(string $app, string $key): string
    {
        $manifest = $this->readManifestFile($app);
        $cssFiles = $manifest[$key]['css'];
        $styleTags = '';
        foreach ($cssFiles as $cssFile) {
            $styleTags .= " <link rel=\"stylesheet\" href=\"/$cssFile\" />";
        }

        return $styleTags;
    }

    /**
     * @return array<mixed>
     * @throws \JsonException
     */
    private function readManifestFile(string $app): array
    {
        $manifestFile = $this->kernel->getProjectDir() . '/public/' . $app . '/manifest.json';
        $manifestContent = file_get_contents($manifestFile);
        $manifestVariables = json_decode($manifestContent, true, 512, \JSON_THROW_ON_ERROR);

        return $manifestVariables;
    }
}
