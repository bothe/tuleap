<?php
/**
 * Copyright (c) Enalean, 2017. All Rights Reserved.
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

namespace Tuleap\Tracker\FormElement;

use Tracker_FormElement_Field_ComputedDao;

class ComputedFieldCalculator implements IProvideArtifactChildrenForComputedCalculation
{
    /**
     * @var Tracker_FormElement_Field_ComputedDao
     */
    private $dao;

    public function __construct(
        Tracker_FormElement_Field_ComputedDao $dao
    ) {
        $this->dao                 = $dao;
    }

    public function fetchChildrenAndManualValuesOfArtifacts(
        array $artifact_ids_to_fetch,
        $timestamp,
        $stop_on_manual_value,
        $target_field_name,
        $computed_field_id
    ) {
        $dar = $this->dao->getComputedFieldValues(
            $artifact_ids_to_fetch,
            $target_field_name,
            $computed_field_id,
            $stop_on_manual_value
        );

        return array(
            'children'   => $dar,
            'manual_sum' => null
        );
    }
}
