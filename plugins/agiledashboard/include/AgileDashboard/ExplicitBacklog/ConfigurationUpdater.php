<?php
/**
 * Copyright (c) Enalean, 2020 - Present. All Rights Reserved.
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

namespace Tuleap\AgileDashboard\ExplicitBacklog;

use Codendi_Request;
use MilestoneReportCriterionDao;
use Tuleap\DB\DBTransactionExecutor;

class ConfigurationUpdater
{
    /**
     * @var ExplicitBacklogDao
     */
    private $explicit_backlog_dao;

    /**
     * @var MilestoneReportCriterionDao
     */
    private $milestone_report_criterion_dao;

    /**
     * @var DBTransactionExecutor
     */
    private $db_transaction_executor;

    public function __construct(
        ExplicitBacklogDao $explicit_backlog_dao,
        MilestoneReportCriterionDao $milestone_report_criterion_dao,
        DBTransactionExecutor $db_transaction_executor
    ) {
        $this->explicit_backlog_dao           = $explicit_backlog_dao;
        $this->milestone_report_criterion_dao = $milestone_report_criterion_dao;
        $this->db_transaction_executor        = $db_transaction_executor;
    }

    public function updateScrumConfiguration(Codendi_Request $request): void
    {
        if (! $request->exist('use-explicit-top-backlog')) {
            return;
        }

        $project_id               = (int) $request->get('group_id');
        $use_explicit_top_backlog = (bool) $request->get('use-explicit-top-backlog');
        if ($this->mustBeDeactivated($use_explicit_top_backlog, $project_id)) {
            $this->deactivateExplicitBacklogManagement($project_id);
        } elseif ($this->mustBeActivated($use_explicit_top_backlog, $project_id)) {
            $this->explicit_backlog_dao->setProjectIsUsingExplicitBacklog($project_id);
        }
    }

    private function deactivateExplicitBacklogManagement(int $project_id): void
    {
        $this->db_transaction_executor->execute(function () use ($project_id) {
            $this->explicit_backlog_dao->setProjectIsNoMoreUsingExplicitBacklog($project_id);
            $this->milestone_report_criterion_dao->updateAllUnplannedValueToAnyInProject($project_id);
        });

        $GLOBALS['Response']->addFeedback(
            \Feedback::INFO,
            dgettext(
                'tuleap-agiledashboard',
                'All tracker reports using "Unplanned" option for "In milestone" report criterion will now use "Any".'
            )
        );
    }

    private function mustBeDeactivated(bool $use_explicit_top_backlog, int $project_id): bool
    {
        return ! $use_explicit_top_backlog &&
            $this->explicit_backlog_dao->isProjectUsingExplicitBacklog($project_id);
    }

    private function mustBeActivated(bool $use_explicit_top_backlog, int $project_id): bool
    {
        return $use_explicit_top_backlog &&
            ! $this->explicit_backlog_dao->isProjectUsingExplicitBacklog($project_id);
    }
}
