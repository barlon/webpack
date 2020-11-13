<?php

declare(strict_types=1);

namespace Contributte\Webpack\AssetNameResolver;

use Contributte\Webpack\Manifest\CannotLoadManifestException;
use Contributte\Webpack\Manifest\ManifestLoader;

final class ManifestAssetNameResolver implements AssetNameResolverInterface
{
	private string $manifestName;

	private ManifestLoader $loader;

	/** @var array<string, string>|null */
	private ?array $manifestCache = null;

	public function __construct(string $manifestName, ManifestLoader $loader)
	{
		$this->manifestName = $manifestName;
		$this->loader = $loader;
	}

	public function resolveAssetName(string $asset): string
	{
		if ($this->manifestCache === null) {
			try {
				$this->manifestCache = $this->loader->loadManifest($this->manifestName);
			} catch (CannotLoadManifestException $e) {
				throw new CannotResolveAssetNameException('Failed to load manifest file.', 0, $e);
			}
		}

		if (!isset($this->manifestCache[$asset])) {
			throw new CannotResolveAssetNameException(\sprintf(
				"Asset '%s' was not found in the manifest file '%s'",
				$asset,
				$this->loader->getManifestPath($this->manifestName)
			));
		}

		return $this->manifestCache[$asset];
	}
}
