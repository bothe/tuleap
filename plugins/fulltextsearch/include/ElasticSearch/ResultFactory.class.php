<?php
/**
 * Copyright (c) Enalean, 2014. All Rights Reserved.
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
 * along with Tuleap; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * I extract data from ElasticSearch 1.2 search results
 * And I build ElasticSearch_SearchResult* objects
 */

class ElasticSearch_1_2_ResultFactory {

    /** @var ProjectManager */
    private $project_manager;

    public function __construct(ProjectManager $project_manager) {
        $this->project_manager = $project_manager;
    }

    public function getChangesetIds(array $data) {
        $results = array();
        if (! empty($data['hits']['total'])) {
            foreach ($data['hits']['hits'] as $hit) {
                $results[$hit['fields']['artifact_id'][0]] = $hit['fields']['changeset_id'][0];
            }
        }

        return $results;
    }

    private function extractGroupIdFromHit(array $hit_data) {
        return $hit_data['fields']['group_id'][0];
    }

    public function getQueryTime(array $data) {
        if (isset($data['time'])) {
            return $data['time'];
        }

        return 0;
    }

    public function getSearchResultProjectsFacetCollection(
            array $result,
            array $submitted_facets
    ) {
        if (isset($result['facets']['projects'])) {
            return new ElasticSearch_SearchResultProjectsFacetCollection(
                $result['facets']['projects'],
                $this->project_manager,
                $submitted_facets
            );
        }
    }

    public function getSearchResults(array $result, $type) {
        $results = array();

        if (isset($result['hits']['hits'])) {
            foreach ($result['hits']['hits'] as $hit) {
                $project = $this->project_manager->getProject($this->extractGroupIdFromHit($hit));

                if (! $project->isError()) {
                    $class = 'ElasticSearch_SearchResult'.$type;
                    if (class_exists($class)) {
                        $results[] = new $class($hit, $project);
                    }
                }
            }
        }

        return $results;
    }

}