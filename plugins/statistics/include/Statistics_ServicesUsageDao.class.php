<?php
/**
 * Copyright (c) STMicroelectronics, 2009. All Rights Reserved.
 *
 * Originally written by Manuel Vacelet, 2009
 *
 * This file is a part of Codendi.
 *
 * Codendi is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Codendi is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Codendi; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

class Statistics_ServicesUsageDao extends DataAccessObject {

    /**
     * Constructor
     *
     * @param DataAccess $da Data access details
     * @param String $start_date
     * @param String $end_date
     *
     * @return Statistics_DiskUsageDao
     */
    public function __construct(DataAccess $da, $start_date, $end_date) {
        parent::__construct($da);
        $this->start_date = strtotime($start_date);
        $this->end_date   = strtotime($end_date);

    }

    public function getNameOfActiveProjectsBeforeEndDate() {
        $sql = "SELECT group_id, group_name
            FROM groups
            WHERE status='A'
               AND register_time <= $this->end_date
            GROUP BY group_id";

        $return = array();
        $retrieve = $this->retrieve($sql);
        foreach ($retrieve as $res) {
            $return[] = $res;
        }

        return $return;
    }

    public function getDescriptionOfActiveProjectsBeforeEndDate() {
        $sql = "SELECT group_id, REPLACE(REPLACE (short_description, CHAR(13),' '),CHAR(10),' ')
                FROM groups
                WHERE status='A'
                    AND register_time <= $this->end_date
                GROUP BY group_id";

        $return = array();
        $retrieve = $this->retrieve($sql);
        foreach ($retrieve as $res) {
            $return[] = $res;
        }

        return $return;
    }

    public function getRegisterTimeOfActiveProjectsBeforeEndDate() {
        $sql = "SELECT group_id, FROM_UNIXTIME(register_time,'%Y-%m-%d')
                FROM groups
                WHERE status='A'
                    AND register_time <= $this->end_date
                GROUP BY group_id";

        $return = array();
        $retrieve = $this->retrieve($sql);
        foreach ($retrieve as $res) {
            $return[] = $res;
        }

        return $return;
    }

    public function getInfosFromTroveGroupLink() {
        $sql = "SELECT tgl.group_id, tc.shortname
                FROM trove_group_link tgl, trove_cat tc
                WHERE tgl.trove_cat_root='281'
                    AND tc.root_parent=tgl.trove_cat_root
                    AND tc.trove_cat_id=tgl.trove_cat_id
                GROUP BY group_id";

        $return = array();
        $retrieve = $this->retrieve($sql);
        foreach ($retrieve as $res) {
            $return[] = $res;
        }

        return $return;
    }

    public function getAdministrators() {
        $sql = "SELECT g.group_id, u.user_name
                FROM user_group g, user u
                WHERE g.user_id=u.user_id
                    AND u.status='A'
                GROUP BY group_id";

        $return = array();
        $retrieve = $this->retrieve($sql);
        foreach ($retrieve as $res) {
            $return[] = $res;
        }

        return $return;
    }
}

?>
