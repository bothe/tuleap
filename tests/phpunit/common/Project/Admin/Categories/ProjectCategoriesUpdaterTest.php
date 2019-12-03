<?php
/**
 * Copyright (c) Enalean, 2019. All Rights Reserved.
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

namespace Tuleap\Project\Admin\Categories;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Project;
use ProjectHistoryDao;
use TroveCatFactory;

class ProjectCategoriesUpdaterTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Mockery\MockInterface|Project
     */
    private $project;
    /**
     * @var Mockery\MockInterface|ProjectCategoriesUpdater
     */
    private $updater;
    /**
     * @var Mockery\MockInterface|TroveSetNodeFacade
     */
    private $set_node_facade;
    /**
     * @var Mockery\MockInterface|TroveCatFactory
     */
    private $factory;
    /**
     * @var Mockery\MockInterface|ProjectHistoryDao
     */
    private $history_dao;

    /** @before */
    public function instantiateMocks(): void
    {
        $this->project     = Mockery::mock(Project::class);
        $this->factory     = Mockery::mock(TroveCatFactory::class);
        $this->history_dao = Mockery::mock(ProjectHistoryDao::class);
        $this->set_node_facade = Mockery::mock(TroveSetNodeFacade::class);

        $this->project->shouldReceive('getID')->andReturn(42);

        $this->factory->shouldReceive('getTopCategories')->andReturn(
            [
                ['trove_cat_id' => 1, 'nb_max_values' => 3],
                ['trove_cat_id' => 2, 'nb_max_values' => 1]
            ]
        );

        $this->updater = new ProjectCategoriesUpdater($this->factory, $this->history_dao, $this->set_node_facade);
    }

    public function testAddEntryInProjectHistory(): void
    {
        $this->history_dao
            ->shouldReceive('groupAddHistory')
            ->once()
            ->with('changed_trove', "", 42);

        $this->updater->update($this->project, CategoryCollection::buildFromWebPayload([]));
    }

    public function testItUpdatesCategoriesValues(): void
    {
        $this->history_dao->shouldReceive('groupAddHistory');

        $this->factory->shouldReceive('removeProjectTopCategoryValue')->with($this->project, 2)->once();
        $this->factory->shouldReceive('removeProjectTopCategoryValue')->with($this->project, 1)->once();
        $this->set_node_facade->shouldReceive('setNode')->with($this->project, 11, 1)->once();
        $this->set_node_facade->shouldReceive('setNode')->with($this->project, 12, 1)->once();
        $this->set_node_facade->shouldReceive('setNode')->with($this->project, 21, 2)->once();

        $this->updater->update($this->project, CategoryCollection::buildFromWebPayload([1 => ['', '11', '12'], 2 => ['', '21']]));
    }

    public function testItRespectsNbMaxValues(): void
    {
        $this->history_dao->shouldReceive('groupAddHistory');
        $this->factory->shouldReceive('removeProjectTopCategoryValue');
        $this->set_node_facade->shouldReceive('setNode');

        $this->set_node_facade->shouldNotReceive('setNode')->with($this->project, 14, 1);
        $this->set_node_facade->shouldNotReceive('setNode')->with($this->project, 22, 2);

        $this->updater->update($this->project, CategoryCollection::buildFromWebPayload([1 => ['', '11', '12', '13', '14'], 2 => ['', '21', '22']]));
    }

    public function testItIgnoresSubmittedCategoryIfItIsNotInTopLevelOnes(): void
    {
        $this->history_dao->shouldReceive('groupAddHistory');
        $this->factory->shouldNotReceive('removeProjectTopCategoryValue');

        $this->updater->update($this->project, CategoryCollection::buildFromWebPayload([3 => []]));
    }

    public function testItIgnoresSubmittedCategoryIfValueIsNotAnArray(): void
    {
        $this->history_dao->shouldReceive('groupAddHistory');
        $this->factory->shouldNotReceive('removeProjectTopCategoryValue');
        $this->set_node_facade->shouldNotReceive('setNode')->with($this->project, 23, 1);

        $this->updater->update($this->project, CategoryCollection::buildFromWebPayload([1 => '23']));
    }
}
