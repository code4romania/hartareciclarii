<template>
    <div class="space-y-1 mb-5">
        <label
            for="comment"
            class="block text-sm font-medium leading-6 text-gray-900"
        >
            {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.ADD_PICTURE }}
        </label>
        <p class="text-gray-900 sm:text-sm">
            {{ CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.ADD_PICTURE_SUBTITLE }}
        </p>
        <div class="mt-5">
            <FilePond
                allowRemove="false"
                allowMultiple="true"
                v-bind:server="myServer"
                v-on:init="handleFilePondInit"
            />
        </div>
    </div>
</template>

<script>
import {CONSTANTS} from "@/constants";
import axios, {HttpStatusCode} from "axios";
import _ from "lodash";

import vueFilePond from 'vue-filepond';
import 'filepond/dist/filepond.min.css';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';


export default {
    name: "uploadImage",
    components: {
        FilePond: vueFilePond(FilePondPluginImagePreview)
    },
    computed: {
        CONSTANTS() {
            return CONSTANTS;
        },
    },
    data() {
        return {
            myFiles: [],
            myServer: {
                process: (fieldName, file, metadata, load) => {
                    let formData = new FormData();
                    formData.append('images[]', file);
                    axios
                        .post(
                            CONSTANTS.API_DOMAIN + CONSTANTS.ROUTES.STATIC.IMAGE,
                            formData,
                            {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            }
                        )
                        .then((response) => {
                            if (_.get(response, 'status', 0) === HttpStatusCode.Ok) {
                                this.$emit('imageUpload', _.get(response, 'data.images.0', ''));
                                load(Date.now())
                            }
                        })
                        .catch((err) => {
                        });
                },
                load: (source, load) => {
                    // simulates loading a file from the server
                    fetch(source).then(res => res.blob()).then(load);
                }
            },
        }
    },
    mounted() {},
    methods: {
        handleFilePondInit: function () {},
    }
}
</script>

