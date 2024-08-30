<template>
    <fieldset class="contents">
        <legend class="text-sm font-medium text-gray-900" v-text="$t('add_point.details.subtitle')" />

        <Select
            name="point_type"
            :label="$t('add_point.details.point_type')"
            :placeholder="$t('add_point.details.point_type_placeholder')"
            v-model="form.point_type"
            :errors="[form.errors.point_type]"
            :options="serviceType.point_types"
            optionValue="id"
            optionLabel="name"
            required
        />

        <Input
            v-if="serviceType.can.have_business_name"
            name="business_name"
            :label="$t('add_point.details.business_name')"
            :placeholder="$t('add_point.details.business_name_placeholder')"
            v-model="form.business_name"
            :errors="[form.errors.business_name]"
            :disabled="form.business_name_unknown"
        />

        <Input
            name="administered_by"
            :label="$t('add_point.details.administered_by')"
            :placeholder="$t('add_point.details.administered_by_placeholder')"
            v-model="form.administered_by"
            :errors="[form.errors.administered_by]"
            :disabled="form.administered_by_unknown"
            required
        />

        <div class="flex justify-end -mt-1">
            <Checkbox
                name="administered_by_unknown"
                :label="$t('add_point.unknown')"
                v-model="form.administered_by_unknown"
            />
        </div>

        <Input
            name="schedule"
            :label="$t('add_point.details.schedule')"
            :placeholder="$t('add_point.details.schedule_placeholder')"
            v-model="form.schedule"
            :errors="[form.errors.schedule]"
            :disabled="form.schedule_unknown"
            required
        />

        <div class="flex justify-end -mt-1">
            <Checkbox name="schedule_unknown" :label="$t('add_point.unknown')" v-model="form.schedule_unknown" />
        </div>

        <Input
            v-if="!form.administered_by_unknown"
            name="website"
            :label="$t('add_point.details.website')"
            :placeholder="$t('add_point.details.website_placeholder')"
            v-model="form.website"
            :errors="[form.errors.website]"
            :disabled="form.website_unknown"
        />

        <Input
            v-if="!form.administered_by_unknown"
            name="email"
            :label="$t('add_point.details.email')"
            :placeholder="$t('add_point.details.email_placeholder')"
            v-model="form.email"
            :errors="[form.errors.email]"
            :disabled="form.email_unknown"
        />

        <Input
            v-if="!form.administered_by_unknown"
            name="phone"
            :label="$t('add_point.details.phone')"
            :placeholder="$t('add_point.details.phone_placeholder')"
            v-model="form.phone"
            :errors="[form.errors.phone]"
            :disabled="form.phone_unknown"
        />

        <Select
            v-if="serviceType.can.offer_money"
            name="offers_money"
            :label="$t('add_point.details.offers_money')"
            :placeholder="$t('add_point.placeholder.select')"
            v-model="form.offers_money"
            :errors="[form.errors.offers_money]"
            :options="ternaryOptions"
        />

        <Select
            v-if="serviceType.can.offer_vouchers"
            name="offers_vouchers"
            :label="$t('add_point.details.offers_vouchers')"
            :placeholder="$t('add_point.placeholder.select')"
            v-model="form.offers_vouchers"
            :errors="[form.errors.offers_vouchers]"
            :options="ternaryOptions"
        />

        <Select
            v-if="serviceType.can.offer_transport"
            name="offers_transport"
            :label="$t('add_point.details.offers_transport')"
            :placeholder="$t('add_point.placeholder.select')"
            v-model="form.offers_transport"
            :errors="[form.errors.offers_transport]"
            :options="ternaryOptions"
        />

        <Select
            v-if="serviceType.can.request_payment"
            name="free_of_charge"
            :label="$t('add_point.details.free_of_charge')"
            :placeholder="$t('add_point.placeholder.select')"
            v-model="form.free_of_charge"
            :errors="[form.errors.free_of_charge]"
            :options="ternaryOptions"
        />

        <Textarea
            name="observations"
            :label="$t('add_point.details.observations')"
            :placeholder="$t('add_point.placeholder.select')"
            v-model="form.observations"
            :errors="[form.errors.observations]"
        />
    </fieldset>
</template>

<script setup>
    import { computed, watch } from 'vue';
    import { trans } from 'laravel-vue-i18n';
    import Checkbox from '@/Components/Form/Checkbox.vue';
    import Input from '@/Components/Form/Input.vue';
    import Select from '@/Components/Form/Select.vue';
    import Textarea from '@/Components/Form/Textarea.vue';

    const props = defineProps({
        form: {
            type: Object,
            required: true,
        },
        serviceType: {
            type: Object,
            required: true,
        },
    });

    const ternaryOptions = computed(() => [
        {
            label: trans('add_point.yes'),
            value: 1,
        },
        {
            label: trans('add_point.no'),
            value: 0,
        },
        {
            label: trans('add_point.unknown'),
            value: -1,
        },
    ]);

    watch(
        () => props.form.administered_by_unknown,
        (value) => {
            if (value) {
                props.form.administered_by = null;
            }
        }
    );

    watch(
        () => props.form.schedule_unknown,
        (value) => {
            if (value) {
                props.form.schedule = null;
            }
        }
    );
</script>
