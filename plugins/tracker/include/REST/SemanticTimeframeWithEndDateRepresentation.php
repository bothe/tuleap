<?php
/**
 * Copyright (c) Enalean, 2019 - Present. All Rights Reserved.
 *
 *  This file is a part of Tuleap.
 *
 *  Tuleap is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  Tuleap is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Tuleap\Tracker\REST;

use Tuleap\REST\JsonCast;

class SemanticTimeframeWithEndDateRepresentation
{
    /**
     * @var int
     */
    public $start_date_field_id;

    /**
     * @var int
     */
    public $end_date_field_id;

    public function build(int $start_date_field_id, int $end_date_field_id): void
    {
        $this->start_date_field_id = JsonCast::toInt($start_date_field_id);
        $this->end_date_field_id = JsonCast::toInt($end_date_field_id);
    }
}