<template>
    <div class="">
        <div class="flex items-center justify-between">
            <h3 id="modal-title" class="text-2xl pl-5 py-3">{{ CONSTANTS.LABELS.ADD_POINT.TITLE }}</h3>
            <button v-on:click="closeModal();">
                <desktop-filter-close-icon></desktop-filter-close-icon>
            </button>
        </div>
        <div class="flex items-center justify-between">
            <span class="pl-5 text-sm">{{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.SUBTITLE }}</span>
        </div>

        <div class="mt-3 sm:mt-5 w-full px-5" v-if="Object.keys(nomenclatures).length">
            <div class="space-y-1 mb-3">
                <Combobox
                    as="div"
                    v-on:update:model-value="stepRequestBody.point_type_id = $event.id"
                >
                    <ComboboxLabel
                        class="block text-sm font-medium leading-6 text-gray-900 required"
                    >
                        {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.POINT_TYPE }}
                    </ComboboxLabel>
                    <div class="relative mt-2">
                        <ComboboxInput
                            :display-value="(point) => point?.display_name"
                            class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            @change="query = $event.target.value"
                        />
                        <ComboboxButton
                            class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                            <ChevronUpDownIcon aria-hidden="true" class="h-5 w-5 text-gray-400"/>
                        </ComboboxButton>

                        <ComboboxOptions
                            v-if="nomenclatures.services.length > 0"
                            class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                        >
                            <ComboboxOption
                                v-for="point in getPointTypes()"
                                :key="point.id"
                                v-slot="{ active, selected }"
                                :value="point"
                                as="template"
                            >
                                <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                    <span :class="['block truncate', selected && 'font-semibold']">
                                      {{ point.display_name }}
                                    </span>

                                    <span
                                        v-if="selected"
                                        :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']"
                                    >
                                    <CheckIcon aria-hidden="true" class="h-5 w-5"/>
                                    </span>
                                </li>
                            </ComboboxOption>
                        </ComboboxOptions>
                    </div>
                </Combobox>

                <template v-if="getError('point_type')">
                    <div class="rounded-md bg-red-50 p-4 mb-2">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">{{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.POINT_TYPE_REQUIRED }}</h3>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="space-y-1 mb-3">
                <label
                    class="block text-sm font-medium leading-6 text-gray-900 required"
                    for="administration"
                >
                    {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.COLLECTED_MATERIALS }}
                </label>

                <treeselect
                    name="demo"
                    :multiple="true"
                    :options="materialTypesFilters"
                    :auto-select-ancestors="false"
                    :auto-select-descendants="true"
                    :auto-deselect-descendants="true"
                    :flat="true"
                    v-model="stepRequestBody.material_recycling_point"
                />

                <template v-if="getError('material_recycling_point')">
                    <div class="rounded-md bg-red-50 p-4 mb-2">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">{{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.COLLECTED_MATERIALS_REQUIRED }}</h3>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="space-y-1 mb-3">
                <label
                    class="block text-sm font-medium leading-6 text-gray-900"
                    for="administration"
                >
                    {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.ADMINISTRATION }}
                </label>
                <div class="mt-2">
                    <input
                        :disabled="administrationUnknown"
                        id="administration"
                        v-model="stepRequestBody.field_types.managed_by"
                        :placeholder="CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.ADMINISTRATION_PLACEHOLDER"
                        class="block w-full rounded-md border border-gray-300 py-1.5 text-neutral-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        name="administration"
                    />
                </div>
            </div>

            <div class="space-y-1 mb-8">
                <div class="flex" style="float: right">
                    <div class="flex h-6 items-center">
                        <input
                            @change="administrationUnknownChanged()"
                            id="unknownAdministration"
                            aria-describedby="unknownAdministration-description"
                            name="unknownAdministration"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                        />
                    </div>
                    <div class="ml-3 text-sm leading-6">
                        <label
                            for="unknownAdministration"
                            class="font-medium text-gray-900"
                        >
                            {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.UNKNOWN_ADMINISTRATION }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="space-y-1 mb-3" v-if="!administrationUnknown">
                <label
                    class="block text-sm font-medium leading-6 text-gray-900"
                    for="website"
                >
                    {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.WEBSITE }}
                </label>
                <div class="mt-2">
                    <input
                        id="website"
                        v-model="stepRequestBody.field_types.website"
                        class="block w-full rounded-md border border-gray-300 py-1.5 text-neutral-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        name="website"
                    />
                </div>
            </div>

            <div class="space-y-1 mb-3" v-if="!administrationUnknown">
                <label
                    class="block text-sm font-medium leading-6 text-gray-900"
                    for="email"
                >
                    {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.EMAIL }}
                </label>
                <div class="mt-2">
                    <input
                        id="email"
                        v-model="stepRequestBody.field_types.email"
                        class="block w-full rounded-md border border-gray-300 py-1.5 text-neutral-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        name="email"
                    />
                </div>
            </div>

            <div class="space-y-1 mb-3" v-if="!administrationUnknown">
                <label
                    class="block text-sm font-medium leading-6 text-gray-900"
                    for="phone"
                >
                    {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.PHONE }}
                </label>
                <div class="mt-2">
                    <input
                        id="phone"
                        v-model="stepRequestBody.field_types.phone_no"
                        class="block w-full rounded-md border border-gray-300 py-1.5 text-neutral-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        name="phone"
                    />
                </div>
            </div>

            <span class="border-gray-300 text-neutral-900">{{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.PROGRAM }}</span>
            <div class="mb-3 flex flex-row mt-3">
                <Combobox
                    :disabled="programUnknown"
                    as="div"
                    v-model="stepRequestBody.opening_hours.startDay"
                >
                    <ComboboxLabel
                        class="block text-sm font-medium leading- text-gray-900"
                    >
                        {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.DAYS }}
                    </ComboboxLabel>
                    <div class="relative mt-2">
                        <ComboboxInput
                            :display-value="day"
                            class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            @change="query = $event.target.value"
                        />
                        <ComboboxButton
                            class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                            <ChevronUpDownIcon aria-hidden="true" class="h-5 w-5 text-gray-400"/>
                        </ComboboxButton>

                        <ComboboxOptions
                            v-if="daysSelectValues.length > 0"
                            class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                        >
                            <ComboboxOption
                                v-for="day in daysSelectValues"
                                :key="day"
                                v-slot="{ active, selected }"
                                :value="day"
                                as="template"
                            >
                                <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                    <span :class="['block truncate', selected && 'font-semibold']">
                                      {{ day }}
                                    </span>

                                    <span
                                        v-if="selected"
                                        :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']"
                                    >
                                    <CheckIcon aria-hidden="true" class="h-5 w-5"/>
                                    </span>
                                </li>
                            </ComboboxOption>
                        </ComboboxOptions>
                    </div>
                </Combobox>

                <Combobox
                    as="div"
                    v-model="stepRequestBody.opening_hours.startHour"
                    :disabled="programUnknown"
                >
                    <ComboboxLabel
                        class="block text-sm font-medium leading- text-gray-900 ml-5"
                    >
                        {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.HOURS }}
                    </ComboboxLabel>
                    <div class="relative mt-2 ml-5">
                        <ComboboxInput
                            :display-value="hour"
                            class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            @change="query = $event.target.value"
                        />
                        <ComboboxButton
                            class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                            <ChevronUpDownIcon aria-hidden="true" class="h-5 w-5 text-gray-400"/>
                        </ComboboxButton>

                        <ComboboxOptions
                            v-if="hoursSelectValues.length > 0"
                            class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                        >
                            <ComboboxOption
                                v-for="hour in hoursSelectValues"
                                :key="hour"
                                v-slot="{ active, selected }"
                                :value="hour"
                                as="template"
                            >
                                <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                    <span :class="['block truncate', selected && 'font-semibold']">
                                      {{ hour }}
                                    </span>

                                    <span
                                        v-if="selected"
                                        :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']"
                                    >
                                    <CheckIcon aria-hidden="true" class="h-5 w-5"/>
                                    </span>
                                </li>
                            </ComboboxOption>
                        </ComboboxOptions>
                    </div>
                </Combobox>
            </div>

            <div class="mb-3 flex flex-row mt-3">
                <Combobox
                    as="div"
                    v-model="stepRequestBody.opening_hours.endDay"
                    :disabled="programUnknown"
                >

                    <div class="relative mt-2">
                        <ComboboxInput
                            :display-value="day"
                            class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            @change="query = $event.target.value"
                        />
                        <ComboboxButton
                            class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                            <ChevronUpDownIcon aria-hidden="true" class="h-5 w-5 text-gray-400"/>
                        </ComboboxButton>

                        <ComboboxOptions
                            v-if="daysSelectValues.length > 0"
                            class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                        >
                            <ComboboxOption
                                v-for="day in daysSelectValues"
                                :key="day"
                                v-slot="{ active, selected }"
                                :value="day"
                                as="template"
                            >
                                <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                    <span :class="['block truncate', selected && 'font-semibold']">
                                      {{ day }}
                                    </span>

                                    <span
                                        v-if="selected"
                                        :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']"
                                    >
                                    <CheckIcon aria-hidden="true" class="h-5 w-5"/>
                                    </span>
                                </li>
                            </ComboboxOption>
                        </ComboboxOptions>
                    </div>
                </Combobox>

                <Combobox
                    as="div"
                    v-model="stepRequestBody.opening_hours.endHour"
                    :disabled="programUnknown"
                >
                    <div class="relative mt-2 ml-5">
                        <ComboboxInput
                            :display-value="hour"
                            class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            @change="query = $event.target.value"
                        />
                        <ComboboxButton
                            class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                            <ChevronUpDownIcon aria-hidden="true" class="h-5 w-5 text-gray-400"/>
                        </ComboboxButton>

                        <ComboboxOptions
                            v-if="hoursSelectValues.length > 0"
                            class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                        >
                            <ComboboxOption
                                v-for="hour in hoursSelectValues"
                                :key="hour"
                                v-slot="{ active, selected }"
                                :value="hour"
                                as="template"
                            >
                                <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                    <span :class="['block truncate', selected && 'font-semibold']">
                                      {{ hour }}
                                    </span>

                                    <span
                                        v-if="selected"
                                        :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']"
                                    >
                                    <CheckIcon aria-hidden="true" class="h-5 w-5"/>
                                    </span>
                                </li>
                            </ComboboxOption>
                        </ComboboxOptions>
                    </div>
                </Combobox>
            </div>

            <div class="space-y-1 mb-8">
                <div class="flex" style="float: right">
                    <div class="flex h-6 items-center">
                        <input
                            @change="programUnknownChanged()"
                            id="unknownProgram"
                            aria-describedby="unknownProgram-description"
                            name="unknownProgram"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                        />
                    </div>
                    <div class="ml-3 text-sm leading-6">
                        <label
                            for="unknownProgram"
                            class="font-medium text-gray-900"
                        >
                            {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.UNKNOWN_PROGRAM }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="space-y-1 mb-3">
                <Combobox
                    as="div"
                    v-on:update:model-value="stepRequestBody.field_types.offers_money = $event.value"
                >
                    <ComboboxLabel
                        class="block text-sm font-medium leading-6 text-gray-900"
                    >
                        {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.OFFERS_MONEY }}
                    </ComboboxLabel>
                    <div class="relative mt-2">
                        <ComboboxInput
                            :display-value="(booleanValue) => booleanValue?.name"
                            class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            @change="query = $event.target.value"
                        />
                        <ComboboxButton
                            class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                            <ChevronUpDownIcon aria-hidden="true" class="h-5 w-5 text-gray-400"/>
                        </ComboboxButton>

                        <ComboboxOptions
                            v-if="booleanSelect.length > 0"
                            class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                        >
                            <ComboboxOption
                                v-for="booleanValue in booleanSelect"
                                :key="booleanValue.id"
                                v-slot="{ active, selected }"
                                :value="booleanValue"
                                as="template"
                            >
                                <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                    <span :class="['block truncate', selected && 'font-semibold']">
                                      {{ booleanValue.name }}
                                    </span>

                                    <span
                                        v-if="selected"
                                        :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']"
                                    >
                                    <CheckIcon aria-hidden="true" class="h-5 w-5"/>
                                    </span>
                                </li>
                            </ComboboxOption>
                        </ComboboxOptions>
                    </div>
                </Combobox>
            </div>

            <div class="space-y-1 mb-3">
                <Combobox
                    as="div"
                    v-on:update:model-value="stepRequestBody.field_types.offers_transport = $event.value"
                >
                    <ComboboxLabel
                        class="block text-sm font-medium leading-6 text-gray-900"
                    >
                        {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.OFFERS_SHIP }}
                    </ComboboxLabel>
                    <div class="relative mt-2">
                        <ComboboxInput
                            :display-value="(booleanValue) => booleanValue?.name"
                            class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            @change="query = $event.target.value"
                        />
                        <ComboboxButton
                            class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                            <ChevronUpDownIcon aria-hidden="true" class="h-5 w-5 text-gray-400"/>
                        </ComboboxButton>

                        <ComboboxOptions
                            v-if="booleanSelect.length > 0"
                            class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                        >
                            <ComboboxOption
                                v-for="booleanValue in booleanSelect"
                                :key="booleanValue.id"
                                v-slot="{ active, selected }"
                                :value="booleanValue"
                                as="template"
                            >
                                <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                    <span :class="['block truncate', selected && 'font-semibold']">
                                      {{ booleanValue.name }}
                                    </span>

                                    <span
                                        v-if="selected"
                                        :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']"
                                    >
                                    <CheckIcon aria-hidden="true" class="h-5 w-5"/>
                                    </span>
                                </li>
                            </ComboboxOption>
                        </ComboboxOptions>
                    </div>
                </Combobox>
            </div>

            <div class="space-y-1 mb-3">
                <label
                    for="comment"
                    class="block text-sm font-medium leading-6 text-gray-900"
                >
                    {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.OBSERVATIONS }}
                </label>
                <div class="mt-2">
                    <textarea
                        v-model="stepRequestBody.field_types.notes"
                        :placeholder="CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.OBSERVATIONS_PLACEHOLDER"
                        rows="4"
                        name="comment"
                        id="comment"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    />
                </div>
            </div>

            <div class="space-y-1 mb-3">
                <label
                    for="comment"
                    class="block text-sm font-medium leading-6 text-gray-900"
                >
                    {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.ADD_PICTURE }}
                </label>
                <p class="text-gray-900 sm:text-sm">
                    {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.ADD_PICTURE_SUBTITLE }}
                </p>
                <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10">
                    <div class="text-center">
                        <PhotoIcon class="mx-auto h-12 w-12 text-gray-300" aria-hidden="true" />
                        <div class="mt-4 flex text-sm leading-6 text-gray-600">
                            <label for="file-upload" class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                <span>Upload a file</span>
                                <input id="file-upload" name="file-upload" multiple type="file" class="sr-only" />
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs leading-5 text-gray-600">PNG, JPG, GIF up to 10MB</p>
                    </div>
                </div>
            </div>

            <div class="py-2 mb-1" style="text-align: end">
                <button
                    v-on:click="$emit('backToStep', 'first')"
                    type="button"
                    class="mr-3 rounded bg-white px-2 py-1 text-sm font-semibold text-gray-600 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                >
                    {{ CONSTANTS.LABELS.ADD_POINT.BACK }}
                </button>
                <button
                    v-on:click="nextStep()"
                    type="button"
                    class="rounded bg-white px-2 py-1 text-sm font-semibold text-gray-600 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                >
                    {{ CONSTANTS.LABELS.ADD_POINT.NEXT_STEP }}
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import _ from 'lodash';
import {CONSTANTS} from "@/constants";
import axios, {HttpStatusCode} from "axios";
import DesktopFilterCloseIcon from "../../svg-icons/desktopFilterCloseIcon.vue";
import {XCircleIcon} from '@heroicons/vue/20/solid';
import eventBus from "../../../eventBus.js";
import {CheckIcon, ChevronUpDownIcon} from '@heroicons/vue/20/solid'
import Treeselect from 'vue3-treeselect'
import 'vue3-treeselect/dist/vue3-treeselect.css'
import { PhotoIcon, UserCircleIcon } from '@heroicons/vue/24/solid'

import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxLabel,
    ComboboxOption,
    ComboboxOptions,
} from '@headlessui/vue'

export default {
    components: {
        Combobox,
        ComboboxButton,
        ComboboxInput,
        ComboboxLabel,
        ComboboxOption,
        ComboboxOptions,
        DesktopFilterCloseIcon,
        XCircleIcon,
        CheckIcon,
        ChevronUpDownIcon,
        Treeselect
    },
    props: {
        nomenclatures: {
            type: Object,
            required: true,
        },
        previousStepBody: {
            type: Object,
            required: true,
        },
    },
    computed: {
        CONSTANTS() {
            return CONSTANTS;
        },
    },
    data() {
        return {
            errors: {},
            stepRequestBody: {
                point_type_id: null,
                material_recycling_point: [],
                field_types: {
                    managed_by: null,
                    website: null,
                    email: null,
                    phone_no: null,
                    offers_money: 0,
                    offers_transport: 0,
                    notes: ''
                },
                opening_hours: {
                    startDay: null,
                    endDay: null,
                    startHour: null,
                    endHour: null,
                }
            },
            materialTypesFilters: [],
            administrationUnknown: false,
            programUnknown: false,
            daysSelectValues: ['Luni', 'Marti', 'Miercuri', 'Joi', 'Vineri', 'Sambata', 'Duminica'],
            hoursSelectValues: ['06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'],
            booleanSelect: [
                {
                    name: 'Da',
                    value: 1
                },
                {
                    name: 'Nu',
                    value: 0
                }
            ]
        };
    },
    mounted() {},
    methods: {
        closeModal() {
            this.$emit('close');
        },
        getError(key) {
            return _.get(this, ['errors', key], false);
        },
        getPointTypes() {
            let toReturn = [];

            for (const service of _.get(this, 'nomenclatures.services', {})) {
                for (const point of _.get(service, 'point_types', [])) {
                    if (point.service_id === this.previousStepBody.service_id) {
                        toReturn.push(point);
                    }
                }
            }

            return toReturn;
        },
        validate() {
            this.errors = {};

            if (!_.get(this, 'stepRequestBody.point_type_id', false)) {
                this.errors.point_type = true;
            }
            if (!_.get(this, 'stepRequestBody.material_recycling_point', []).length) {
                this.errors.material_recycling_point = true;
            }

            return Object.keys(this.errors).length;
        },
        administrationUnknownChanged() {
            this.administrationUnknown = !this.administrationUnknown;

            if (this.administrationUnknown) {
                this.stepRequestBody.field_types.managed_by = null;
                this.stepRequestBody.field_types.website = null;
                this.stepRequestBody.field_types.email = null;
                this.stepRequestBody.field_types.phone_no = null;
            }
        },
        programUnknownChanged() {
            this.programUnknown = !this.programUnknown;

            if (this.programUnknown) {
                this.stepRequestBody.opening_hours.startDay = null;
                this.stepRequestBody.opening_hours.startHour = null;
                this.stepRequestBody.opening_hours.endDay = null;
                this.stepRequestBody.opening_hours.endHour = null;
            }
        },
        convertMaterialsFiltersToTree() {
            this.materialTypesFilters = [];
            for (const filter of _.get(this, ['nomenclatures', 'material_recycling_points'], [])) {
                if (!filter.parent) {
                    let filterToAppend = {
                        id: filter.id,
                        label: filter.name,
                        children: []
                    };
                    for (const childrenFilter of _.get(this, ['nomenclatures', 'material_recycling_points'], [])) {
                        if (childrenFilter.parent === filter.id) {
                            filterToAppend.children.push({
                                id: childrenFilter.id,
                                label: childrenFilter.name,
                            });
                        }
                    }
                    this.materialTypesFilters.push(filterToAppend);
                }
            }
        },
        nextStep() {
            if (this.validate()) {
                return;
            }

            this.$emit('stepFinished', {
                nextStep: 'third',
                body: this.stepRequestBody
            })

        }
    },
    watch: {
        nomenclatures: {
            handler: function (newVal) {
                this.convertMaterialsFiltersToTree();
            },
            deep: true,
            immediate: true
        }
    },
};
</script>
