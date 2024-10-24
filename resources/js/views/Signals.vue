<template>
    <div
        class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb"
    >
        <div>
            <p class="fw-semibold fs-18 mb-0">Signals</p>
        </div>
        <div>
            <button
                type="button"
                class="btn btn-primary btn-wave"
                @click.prevent="createSignal"
            >
                Create Signal
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-nowrap">
                    <thead>
                        <tr>
                            <th scope="col">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="checkboxNoLabel"
                                    value=""
                                    aria-label="..."
                                />
                            </th>
                            <th scope="col">Team Head</th>
                            <th scope="col">Category</th>
                            <th scope="col">Role</th>
                            <th scope="col">Gmail</th>
                            <th scope="col">Team</th>
                            <th scope="col">Work Progress</th>
                            <th scope="col">Revenue</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="checkboxNoLabel1"
                                    value=""
                                    aria-label="..."
                                />
                            </th>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span
                                        class="avatar avatar-xs me-2 online avatar-rounded"
                                    >
                                        <img
                                            src="/assets/images/faces/3.jpg"
                                            alt="img"
                                        /> </span
                                    >Mayor Kelly
                                </div>
                            </td>
                            <td>Manufacturer</td>
                            <td>
                                <span class="badge bg-primary-transparent"
                                    >Team Lead</span
                                >
                            </td>
                            <td>mayorkrlly@gmail.com</td>
                            <td>
                                <div class="avatar-list-stacked">
                                    <span
                                        class="avatar avatar-sm avatar-rounded"
                                    >
                                        <img
                                            src="/assets/images/faces/2.jpg"
                                            alt="img"
                                        />
                                    </span>
                                    <span
                                        class="avatar avatar-sm avatar-rounded"
                                    >
                                        <img
                                            src="/assets/images/faces/8.jpg"
                                            alt="img"
                                        />
                                    </span>
                                    <span
                                        class="avatar avatar-sm avatar-rounded"
                                    >
                                        <img
                                            src="/assets/images/faces/2.jpg"
                                            alt="img"
                                        />
                                    </span>
                                    <a
                                        class="avatar avatar-sm bg-primary text-fixed-white avatar-rounded"
                                        href="javascript:void(0);"
                                    >
                                        +4
                                    </a>
                                </div>
                            </td>
                            <td>
                                <div class="progress progress-xs">
                                    <div
                                        class="progress-bar bg-primary"
                                        role="progressbar"
                                        style="width: 52%"
                                        aria-valuenow="52"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                    ></div>
                                </div>
                            </td>
                            <td>$10,984.29</td>
                            <td>
                                <div class="hstack gap-2 fs-15">
                                    <a
                                        href="javascript:void(0);"
                                        class="btn btn-icon btn-sm btn-success"
                                        ><i class="ri-download-2-line"></i
                                    ></a>
                                    <a
                                        href="javascript:void(0);"
                                        class="btn btn-icon btn-sm btn-info"
                                        ><i class="ri-edit-line"></i
                                    ></a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <ReusableModal
        :key="modalKey"
        :is="dynamicComponent"
        :modal-key="modalKey"
        :props="dynamicComponentProps"
    />
</template>
<script setup>
import {
    defineComponent,
    defineAsyncComponent,
    ref,
    shallowRef,
    onMounted,
} from "vue";
import ReusableModal from "../components/ReusableModal.vue";
import ResusableFunctions from "../composables/ReusableFunctions";
import { showModal } from "../helpers/modal-dom";

const { getAssetPath, toastAlert, useReusableNavigation, generateUUID } =
    ResusableFunctions();

const dynamicComponent = shallowRef("");
const dynamicComponentProps = shallowRef({});
const modalKey = ref("");

modalKey.value = generateUUID();

// Function to pass a modal component
const createSignal = () => {
    dynamicComponent.value =
        defineAsyncComponent(() =>
            import(/* @vite-ignore */ `../components/signals/Create.vue`)
        ) ?? null;
    dynamicComponentProps.value = {};
    showModal(document.getElementById("default_modal"));
};
</script>
