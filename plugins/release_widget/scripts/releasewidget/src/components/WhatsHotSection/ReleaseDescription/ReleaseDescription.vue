<!--
  - Copyright (c) Enalean, 2019 - present. All Rights Reserved.
  -
  - This file is a part of Tuleap.
  -
  - Tuleap is free software; you can redistribute it and/or modify
  - it under the terms of the GNU General Public License as published by
  - the Free Software Foundation; either version 2 of the License, or
  - (at your option) any later version.
  -
  - Tuleap is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU General Public License for more details.
  -
  - You should have received a copy of the GNU General Public License
  - along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
  -->

<template>
    <div>
        <div class="release-content-description">
            <release-description-badges-tracker v-bind:release_data="release_data" />
            <div v-if="burndown_exits" class="release-chart-burndown-row">
                <h2 class="tlp-pane-subtitle" v-translate>Burndown</h2>
                <burndown-chart v-bind:release_data="release_data" />
            </div>
        </div>
        <div class="release-description-row">
            <div
                v-if="release_data.description"
                class="tlp-tooltip tlp-tooltip-top"
                v-bind:data-tlp-tooltip="release_data.description"
                data-test="tooltip-description"
            >
                <div class="release-description" v-dompurify-html="release_data.description"></div>
            </div>
            <release-buttons-description v-bind:release_data="release_data" />
        </div>
    </div>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop } from "vue-property-decorator";
import { MilestoneData } from "../../../type";
import ReleaseDescriptionBadgesTracker from "./ReleaseDescriptionBadgesTracker.vue";
import BurndownChart from "./Chart/BurndownChart.vue";
import ReleaseButtonsDescription from "./ReleaseButtonsDescription.vue";

@Component({
    components: { ReleaseButtonsDescription, ReleaseDescriptionBadgesTracker, BurndownChart }
})
export default class ReleaseDescription extends Vue {
    @Prop()
    readonly release_data!: MilestoneData;

    get burndown_exits(): boolean {
        if (!this.release_data.resources) {
            return false;
        }

        return this.release_data.resources.burndown !== null;
    }
}
</script>
