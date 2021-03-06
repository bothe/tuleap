<?php
/**
 * Copyright (c) Enalean, 2020-Present. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Tuleap\Git\User\AccessKey\Scope;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tuleap\User\AccessKey\Scope\AccessKeyScope;
use Tuleap\User\AccessKey\Scope\AccessKeyScopeIdentifier;
use Tuleap\User\AccessKey\Scope\AccessKeyScopeTestCase;

final class GitRepositoryAccessKeyScopeTest extends AccessKeyScopeTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @inheritDoc
     */
    public function getAccessKeyScopeClassname(): string
    {
        return GitRepositoryAccessKeyScope::class;
    }

    public function testDoesNotCoversAllTheScopes(): void
    {
        $scope = \Mockery::mock(AccessKeyScope::class);
        $scope->shouldReceive('getIdentifier')->andReturn(AccessKeyScopeIdentifier::fromIdentifierKey('foo:bar'));

        $this->assertFalse(GitRepositoryAccessKeyScope::fromItself()->covers($scope));
    }
}
