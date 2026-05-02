<script setup lang="ts">
import { Form } from "@/Components/Todo";
import Layout from "@/Layouts/Layout.vue";
import { Head } from "@inertiajs/vue3";

defineProps<{
    id: string;
}>();
</script>

<template>
    <Head title="Edit Item" />

    <Layout>
        <Form :id="id" />
    </Layout>
</template>
