<!--
  - Copyright (c) Enalean, 2019 - present. All Rights Reserved.
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
    <div class="project-registration-template-card">
        <input
            type="radio"
            v-bind:id="'project-registration-tuleap-template-' + template.id"
            v-bind:value="template.id"
            class="project-registration-selected-template"
            name="selected-template"
            data-test="project-registration-radio"
            v-on:change="storeSelectedTemplate()"
        />

        <label
            class="tlp-card project-registration-template-label"
            data-test="project-registration-card-label"
            v-bind:for="'project-registration-tuleap-template-' + template.id"
        >
            <div
                class="project-registration-template-glyph"
                v-dompurify-html:svg="template.glyph"
                data-test="scrum-template-svg"
            />
            <div class="project-registration-template-content">
                <h4 class="project-registration-template-card-title">{{ template.title }}</h4>
                <span class="project-registration-template-card-description">
                    {{ template.description }}
                </span>
            </div>
        </label>
    </div>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop } from "vue-property-decorator";
import { TemplateData } from "../../type";

@Component({})
export default class TemplateCard extends Vue {
    @Prop({ required: true })
    readonly template!: TemplateData;

    storeSelectedTemplate(): void {
        this.$store.dispatch("setSelectedTemplate", this.template);
    }
}
</script>
