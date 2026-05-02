<script setup lang="ts">
import { Form } from "@/Components/Todo";
import Layout from "@/Layouts/Layout.vue";
import { Head } from "@inertiajs/vue3";
</script>

<template>
    <Head title="New Item" />

    <Layout>
        <Form />
    </Layout>
</template>
