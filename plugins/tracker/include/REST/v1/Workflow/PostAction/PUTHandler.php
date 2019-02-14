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

namespace Tuleap\Tracker\REST\v1\Workflow\PostAction;

use PFUser;
use Transition;
use Tuleap\REST\ProjectStatusVerificator;
use Tuleap\Tracker\REST\v1\Workflow\PostAction\Update\PostActionCollectionJsonParser;
use Tuleap\Tracker\REST\v1\Workflow\PostActionsPUTRepresentation;
use Tuleap\Tracker\REST\v1\Workflow\TransitionsPermissionsChecker;
use Tuleap\Tracker\Workflow\PostAction\Update\PostActionCollectionUpdater;

class PUTHandler
{
    /**
     * @var TransitionsPermissionsChecker
     */
    private $transitions_permissions_checker;

    /**
     * @var ProjectStatusVerificator
     */
    private $project_status_verificator;

    /**
     * @var PostActionCollectionJsonParser
     */
    private $post_action_collection_json_parser;
    /**
     * @var PostActionCollectionUpdater
     */
    private $action_collection_updater;

    public function __construct(
        TransitionsPermissionsChecker $transitions_permissions_checker,
        ProjectStatusVerificator $project_status_verificator,
        PostActionCollectionJsonParser $post_action_collection_json_parser,
        PostActionCollectionUpdater $action_collection_updater
    ) {
        $this->transitions_permissions_checker    = $transitions_permissions_checker;
        $this->project_status_verificator         = $project_status_verificator;
        $this->post_action_collection_json_parser = $post_action_collection_json_parser;
        $this->action_collection_updater          = $action_collection_updater;
    }

    /**
     * @throws \DataAccessQueryException
     * @throws \Luracast\Restler\RestException
     * @throws \Tuleap\REST\I18NRestException
     * @throws \Tuleap\Tracker\Workflow\PostAction\Update\Internal\InvalidPostActionException
     * @throws \Tuleap\Tracker\Workflow\PostAction\Update\Internal\UnknownPostActionIdsException
     * @throws \Tuleap\Tracker\Workflow\Transition\OrphanTransitionException
     */
    public function handle(
        PFUser $current_user,
        Transition $transition,
        PostActionsPUTRepresentation $post_actions_representation
    ) {
        $this->transitions_permissions_checker->checkRead($current_user, $transition);
        $project = $transition->getWorkflow()->getTracker()->getProject();
        $this->project_status_verificator->checkProjectStatusAllowsAllUsersToAccessIt($project);

        $post_actions = $this->post_action_collection_json_parser->parse($post_actions_representation->post_actions);
        $this->action_collection_updater->updateByTransition($transition, $post_actions);
    }
}