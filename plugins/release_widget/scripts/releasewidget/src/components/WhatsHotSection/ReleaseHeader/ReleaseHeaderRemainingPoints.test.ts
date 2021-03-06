/*
 * Copyright (c) Enalean, 2019 - present. All Rights Reserved.
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

import { shallowMount, ShallowMountOptions, Wrapper } from "@vue/test-utils";
import ReleaseHeaderRemainingPoints from "./ReleaseHeaderRemainingPoints.vue";
import { createStoreMock } from "../../../../../../../../src/www/scripts/vue-components/store-wrapper-jest";
import { MilestoneData, StoreOptions } from "../../../type";
import { createReleaseWidgetLocalVue } from "../../../helpers/local-vue-for-test";

let release_data: MilestoneData;
const component_options: ShallowMountOptions<ReleaseHeaderRemainingPoints> = {};

describe("ReleaseHeaderRemainingEffort", () => {
    let store_options: StoreOptions;
    let store;

    async function getPersonalWidgetInstance(
        store_options: StoreOptions
    ): Promise<Wrapper<ReleaseHeaderRemainingPoints>> {
        store = createStoreMock(store_options);

        component_options.mocks = { $store: store };
        component_options.localVue = await createReleaseWidgetLocalVue();

        return shallowMount(ReleaseHeaderRemainingPoints, component_options);
    }

    beforeEach(() => {
        store_options = {
            state: {}
        };

        release_data = {
            label: "mile",
            id: 2,
            start_date: new Date("2017-01-22T13:42:08+02:00").toDateString(),
            capacity: 10,
            number_of_artifact_by_trackers: []
        };

        component_options.propsData = {
            release_data
        };
    });

    describe("Display remaining points", () => {
        it("When there is negative remaining points, Then it displays and percent in tooltip", async () => {
            release_data = {
                label: "mile",
                id: 2,
                planning: {
                    id: "100"
                },
                start_date: null,
                remaining_effort: -1,
                initial_effort: 10,
                number_of_artifact_by_trackers: []
            };

            component_options.propsData = {
                release_data
            };

            const wrapper = await getPersonalWidgetInstance(store_options);

            const remaining_point_text = wrapper.find("[data-test=points-remaining-value]");
            const remaining_point_value = wrapper.find("[data-test=points-progress-value]");

            expect(wrapper.attributes("data-tlp-tooltip")).toEqual("110.00%");
            expect(wrapper.classes()).not.toContain("release-remaining-value-success");
            expect(remaining_point_text.classes()).not.toContain(
                "release-remaining-value-disabled"
            );
            expect(remaining_point_value.classes()).not.toContain(
                "release-remaining-progress-value-disabled"
            );

            expect(remaining_point_text.text()).toEqual("-1");
        });

        it("When there isn't remaining effort points, Then 0 is displayed and message in tooltip", async () => {
            release_data = {
                label: "mile",
                id: 2,
                planning: {
                    id: "100"
                },
                start_date: null,
                initial_effort: 10,
                number_of_artifact_by_trackers: []
            };

            component_options.propsData = {
                release_data
            };

            const wrapper = await getPersonalWidgetInstance(store_options);

            const remaining_point_text = wrapper.find("[data-test=points-remaining-value]");
            const remaining_point_value = wrapper.find("[data-test=points-progress-value]");

            expect(wrapper.attributes("data-tlp-tooltip")).toEqual("No remaining effort defined.");
            expect(remaining_point_text.classes()).toContain("release-remaining-value-disabled");
            expect(remaining_point_value.classes()).toContain(
                "release-remaining-progress-value-disabled"
            );
            expect(remaining_point_text.text()).toEqual("0");
        });

        it("When there is remaining effort point and is null, Then 0 is displayed and message in tooltip", async () => {
            release_data = {
                label: "mile",
                id: 2,
                planning: {
                    id: "100"
                },
                start_date: null,
                remaining_effort: null,
                initial_effort: 10,
                number_of_artifact_by_trackers: []
            };

            component_options.propsData = {
                release_data
            };

            const wrapper = await getPersonalWidgetInstance(store_options);

            const remaining_point_text = wrapper.find("[data-test=points-remaining-value]");
            const remaining_point_value = wrapper.find("[data-test=points-progress-value]");

            expect(wrapper.attributes("data-tlp-tooltip")).toEqual("No remaining effort defined.");
            expect(remaining_point_text.classes()).toContain("release-remaining-value-disabled");
            expect(remaining_point_value.classes()).toContain(
                "release-remaining-progress-value-disabled"
            );
            expect(remaining_point_text.text()).toEqual("0");
        });

        it("When there is remaining effort point, not null and greater than 0, Then it's displayed and percent in tooltip", async () => {
            release_data = {
                label: "mile",
                id: 2,
                planning: {
                    id: "100"
                },
                start_date: null,
                remaining_effort: 5,
                initial_effort: 10,
                number_of_artifact_by_trackers: []
            };

            component_options.propsData = {
                release_data
            };

            const wrapper = await getPersonalWidgetInstance(store_options);

            const remaining_point_text = wrapper.find("[data-test=points-remaining-value]");
            const remaining_point_value = wrapper.find("[data-test=points-progress-value]");

            expect(wrapper.attributes("data-tlp-tooltip")).toEqual("50.00%");
            expect(remaining_point_text.classes()).toContain("release-remaining-value-success");
            expect(remaining_point_value.classes()).toContain(
                "release-remaining-progress-value-success"
            );
            expect(remaining_point_text.text()).toEqual("5");
        });

        it("When there is remaining effort point, equal at 0, Then it's displayed and percent in tooltip", async () => {
            release_data = {
                label: "mile",
                id: 2,
                planning: {
                    id: "100"
                },
                start_date: null,
                remaining_effort: 0,
                initial_effort: 5,
                number_of_artifact_by_trackers: []
            };

            component_options.propsData = {
                release_data
            };

            const wrapper = await getPersonalWidgetInstance(store_options);

            const remaining_point_text = wrapper.find("[data-test=points-remaining-value]");
            const remaining_point_value = wrapper.find("[data-test=points-progress-value]");

            expect(wrapper.attributes("data-tlp-tooltip")).toEqual("100.00%");
            expect(remaining_point_text.classes()).not.toContain("release-remaining-value-success");
            expect(remaining_point_text.classes()).not.toContain(
                "release-remaining-value-disabled"
            );
            expect(remaining_point_value.classes()).not.toContain(
                "release-remaining-progress-value-disabled"
            );
            expect(remaining_point_text.text()).toEqual("0");
        });

        it("When there isn't initial effort point, Then remaining effort is displayed and message in tooltip", async () => {
            release_data = {
                label: "mile",
                id: 2,
                planning: {
                    id: "100"
                },
                start_date: null,
                remaining_effort: 5,
                number_of_artifact_by_trackers: []
            };

            component_options.propsData = {
                release_data
            };

            const wrapper = await getPersonalWidgetInstance(store_options);

            const remaining_point_text = wrapper.find("[data-test=points-remaining-value]");
            const remaining_point_value = wrapper.find("[data-test=points-progress-value]");

            expect(wrapper.attributes("data-tlp-tooltip")).toEqual("No initial effort defined.");
            expect(remaining_point_text.classes()).toContain("release-remaining-value-disabled");
            expect(remaining_point_value.classes()).toContain(
                "release-remaining-progress-value-disabled"
            );
            expect(remaining_point_text.text()).toEqual("5");
        });

        it("When there is initial effort point but null, Then remaining effort is displayed and message in tooltip", async () => {
            release_data = {
                label: "mile",
                id: 2,
                planning: {
                    id: "100"
                },
                start_date: null,
                remaining_effort: 5,
                initial_effort: null,
                number_of_artifact_by_trackers: []
            };

            component_options.propsData = {
                release_data
            };

            const wrapper = await getPersonalWidgetInstance(store_options);

            const remaining_point_text = wrapper.find("[data-test=points-remaining-value]");
            const remaining_point_value = wrapper.find("[data-test=points-progress-value]");

            expect(wrapper.attributes("data-tlp-tooltip")).toEqual("No initial effort defined.");
            expect(remaining_point_text.classes()).toContain("release-remaining-value-disabled");
            expect(remaining_point_value.classes()).toContain(
                "release-remaining-progress-value-disabled"
            );
            expect(remaining_point_text.text()).toEqual("5");
        });

        it("When there is initial effort point but equal at 0, Then remaining effort is displayed and message in tooltip", async () => {
            release_data = {
                label: "mile",
                id: 2,
                planning: {
                    id: "100"
                },
                start_date: null,
                remaining_effort: 5,
                initial_effort: 0,
                number_of_artifact_by_trackers: []
            };

            component_options.propsData = {
                release_data
            };

            const wrapper = await getPersonalWidgetInstance(store_options);

            const remaining_point_text = wrapper.find("[data-test=points-remaining-value]");
            const remaining_point_value = wrapper.find("[data-test=points-progress-value]");

            expect(wrapper.attributes("data-tlp-tooltip")).toEqual("Initial effort equal at 0.");
            expect(remaining_point_text.classes()).toContain("release-remaining-value-disabled");
            expect(remaining_point_value.classes()).toContain(
                "release-remaining-progress-value-disabled"
            );
            expect(remaining_point_text.text()).toEqual("5");
        });

        it("When remaining effort > initial effort, Then remaining effort is displayed and message in tooltip", async () => {
            release_data = {
                label: "mile",
                id: 2,
                planning: {
                    id: "100"
                },
                start_date: null,
                remaining_effort: 100,
                initial_effort: 10,
                number_of_artifact_by_trackers: []
            };

            component_options.propsData = {
                release_data
            };

            const wrapper = await getPersonalWidgetInstance(store_options);

            const remaining_point_text = wrapper.find("[data-test=points-remaining-value]");
            const remaining_point_value = wrapper.find("[data-test=points-progress-value]");

            expect(wrapper.attributes("data-tlp-tooltip")).toEqual(
                "Initial effort (10) should be bigger or equal to remaining effort (100)."
            );
            expect(remaining_point_text.classes()).toContain("release-remaining-value-disabled");
            expect(remaining_point_value.classes()).toContain(
                "release-remaining-progress-value-disabled"
            );
            expect(remaining_point_text.text()).toEqual("100");
        });

        it("When remaining effort == initial effort, Then remaining effort is displayed and marked as success", async () => {
            release_data = {
                label: "mile",
                id: 2,
                planning: {
                    id: "100"
                },
                start_date: null,
                remaining_effort: 100,
                initial_effort: 100,
                number_of_artifact_by_trackers: []
            };

            component_options.propsData = {
                release_data
            };

            const wrapper = await getPersonalWidgetInstance(store_options);
            const points_remaining_value = wrapper.find("[data-test=points-remaining-value]");
            const points_remaining_progress = wrapper.find("[data-test=points-progress-value]");

            expect(points_remaining_value.classes("release-remaining-value-success")).toBe(true);
            expect(points_remaining_value.classes("release-remaining-value-disabled")).toBe(false);

            expect(
                points_remaining_progress.classes("release-remaining-progress-value-success")
            ).toBe(true);
            expect(
                points_remaining_progress.classes("release-remaining-progress-value-disabled")
            ).toBe(false);

            expect(wrapper.attributes("data-tlp-tooltip")).toBe("0.00%");

            expect(points_remaining_value.text()).toBe("100");
            expect(points_remaining_progress.attributes("style")).toBe("width: 0.00%;");
        });
    });
});
