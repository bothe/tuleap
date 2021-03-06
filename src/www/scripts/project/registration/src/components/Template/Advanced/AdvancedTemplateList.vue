<!--
  - Copyright (c) Enalean, 2020 - present. All Rights Reserved.
  -
  -  This file is a part of Tuleap.
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
  -
  -->

<template>
    <div>
        <h3 data-test="project-registration-company-template-title" v-translate>
            For advanced users
        </h3>

        <section
            class="project-registration-default-templates-section"
            data-test="project-load-user-project-list"
            v-on:click="loadUserProjects"
        >
            <div class="project-registration-template-card">
                <input
                    type="radio"
                    v-bind:id="'project-registration-tuleap-template-other-user-project'"
                    class="project-registration-selected-template"
                    name="selected-template"
                />

                <label
                    class="tlp-card project-registration-template-label"
                    data-test="project-registration-card-label"
                    v-bind:for="'project-registration-tuleap-template-other-user-project'"
                >
                    <div class="project-registration-template-glyph"><svg-template /></div>
                    <div class="project-registration-template-content">
                        <h4 class="project-registration-template-card-title" v-translate>
                            From an other project I'm admin of
                        </h4>
                        <div
                            v-if="
                                !should_choose_a_project && !is_loading_project_list && !has_error
                            "
                            class="project-registration-template-card-description"
                            data-test="user-project-description"
                        >
                            <translate>
                                Project configuration will be duplicated into your new project.
                            </translate>
                        </div>
                        <div
                            v-else-if="is_loading_project_list && !has_error"
                            data-test="user-project-spinner"
                        >
                            <i class="fa fa-spinner fa-circle-o-notch"></i>
                        </div>
                        <div
                            v-else-if="has_error"
                            class="tlp-text-danger"
                            data-test="user-project-error"
                        >
                            <translate>Oh snap! Failed to load project you are admin of.</translate>
                        </div>
                        <user-project-list
                            v-else
                            v-bind:project-list="project_list"
                            data-test="user-project-list"
                        />
                    </div>
                </label>
            </div>
        </section>
    </div>
</template>

<script lang="ts">
import Vue from "vue";
import UserProjectList from "./UserProjectList.vue";
import { Component } from "vue-property-decorator";
import SvgTemplate from "./SvgTemplate.vue";
import { getProjectUserIsAdminOf } from "../../../api/rest-querier";
import { TemplateData } from "../../../type";

@Component({
    components: {
        SvgTemplate,
        UserProjectList
    }
})
export default class AdvancedTemplateList extends Vue {
    should_choose_a_project = false;
    is_loading_project_list = false;
    has_error = false;
    project_list: TemplateData[] = [];

    async loadUserProjects(): Promise<void> {
        if (this.project_list.length > 0) {
            this.is_loading_project_list = false;
            this.should_choose_a_project = true;
            return;
        }

        this.has_error = false;
        this.is_loading_project_list = true;
        try {
            this.project_list = await getProjectUserIsAdminOf();
        } catch (error) {
            this.has_error = true;
            throw error;
        } finally {
            this.is_loading_project_list = false;
            this.should_choose_a_project = true;
        }
    }
}
</script>
