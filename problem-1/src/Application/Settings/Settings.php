<?php

declare(strict_types=1);

namespace App\Application\Settings;

class Settings implements SettingsInterface
{
    /**
     * @var array<int|string, mixed> $settings
     */
    private array $settings;

    /**
     * @param array<int|string, mixed> $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function get(string $key = ''): mixed
    {
        return (empty($key)) ? $this->settings : $this->settings[$key];
    }
}
