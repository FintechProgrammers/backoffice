<template>
    <div
        class="modal fade"
        ref="defaultModalRef"
        id="default_modal"
        tabindex="-1"
        aria-hidden="true"
        data-bs-backdrop="static"
    >
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h6 class="modal-title" id="staticBackdropLabel1">
                        Modal title
                    </h6> -->
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <component
                        :key="modalKey"
                        :is="componentName"
                        v-if="componentName"
                        v-bind="componentProps"
                        @custom-event="handleCustomEvent"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent, shallowRef, watch } from "vue";

export default defineComponent({
    name: "reusable-modal-component",
    components: {},
    props: {
        is: Object || String || null,
        props: Object,
        width: String,
        modalKey: String,
    },
    setup(props, { emit }) {
        const componentName = shallowRef(null);
        const componentProps = shallowRef(null);

        const handleCustomEvent = () => {
            emit("custom-event");
        };

        watch(
            () => props.is,
            (newVal) => {
                componentName.value = newVal !== undefined ? newVal : null;
                componentProps.value = props.props ?? null;
            }
        );

        return {
            componentName,
            componentProps,
            handleCustomEvent,
        };
    },
});
</script>
