<script setup lang="ts">
import { TodoList } from "@/Components/Todo";
import Layout from "@/Layouts/Layout.vue";
import { Head, router } from "@inertiajs/vue3";

const onNewItem = () => {
    router.get("/new");
};

defineProps<{
    laravelVersion: string;
    phpVersion: string;
}>();
</script>

<template>
    <Head title="Todo App" />

    <Layout>
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Todo List</h1>
                <p class="text-sm text-gray-500 mt-1">Manage your items</p>
            </div>
            <button
                @click="onNewItem"
                class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors"
            >
                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>
                New Item
            </button>
        </div>

        <TodoList />
    </Layout>
</template>
