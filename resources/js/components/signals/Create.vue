<template>
    <VForm
        @submit="submit"
        :validation-schema="validationSchema"
        v-slot="{ errors }"
    >
        <Field
            type="text"
            class="form-control border-rad-10"
            :class="errors.reason ? 'is-invalid' : ''"
            name="reason"
            v-model="payload.reason"
            placeholder="Enter your reason for account request"
        />
        <ErrorMessage class="invalid-feedback" name="reason" />
    </VForm>
</template>
<script setup>
import { Form as VForm, ErrorMessage, Field } from "vee-validate";
import * as Yup from "yup";

const validationSchema = Yup.object().shape({
    currency_id: Yup.string().required().label("Currency"),
});
</script>
