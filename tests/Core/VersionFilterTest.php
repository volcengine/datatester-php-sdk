<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace Core;

use PHPUnit\Framework\TestCase;

class VersionFilterTest extends TestCase
{
    public function test_version_filter_works_without_composer_semver_autoload()
    {
        $script = dirname(__DIR__).'/Mock/VersionFilterWithoutComposerAutoload.php';
        $command = escapeshellarg(PHP_BINARY).' '.escapeshellarg($script).' 2>&1';

        exec($command, $output, $exitCode);

        $this->assertSame(0, $exitCode, implode("\n", $output));
    }

    public function test_version_filter_falls_back_when_composer_semver_autoload_fails()
    {
        $script = dirname(__DIR__).'/Mock/VersionFilterWithBrokenComposerAutoload.php';
        $command = escapeshellarg(PHP_BINARY).' '.escapeshellarg($script).' 2>&1';

        exec($command, $output, $exitCode);

        $this->assertSame(0, $exitCode, implode("\n", $output));
    }
}
