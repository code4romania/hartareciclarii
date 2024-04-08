<template>
    <div>
        <h1>Hello home2</h1>

        <Link href="/1">Home1</Link>
    </div>
</template>


<script setup>
    import Layout from "@/Layouts/DefaultLayout.vue";
    import { Link } from "@inertiajs/vue3";

    defineOptions({ layout: Layout });
</script>
