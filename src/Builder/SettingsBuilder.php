<?php
declare(strict_types=1);

namespace LesDatabase\Builder;

use SensitiveParameter;

final class SettingsBuilder
{
    /** @var array<string, mixed> */
    private array $settings = [];

    public function withDriver(string $driver): self
    {
        return $this->withOption('driver', $driver);
    }

    public function withHost(string $host): self
    {
        return $this->withOption('host', $host);
    }

    public function withUser(string $user): self
    {
        return $this->withOption('user', $user);
    }

    public function withPassword(#[SensitiveParameter] string $password): self
    {
        return $this->withOption('password', $password);
    }

    public function withCharset(string $charset): self
    {
        return $this->withOption('charset', $charset);
    }

    public function withOption(string $key, #[SensitiveParameter] mixed $value): self
    {
        $clone = clone $this;
        $clone->settings[$key] = $value;

        return $clone;
    }

    /**
     * @return array<string, mixed>
     */
    public function build(): array
    {
        return $this->settings;
    }
}
