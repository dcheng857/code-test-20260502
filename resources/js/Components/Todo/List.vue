<script setup lang="ts">
import { formatDate } from "@/common/helper";
import { useToast } from "@/common/useToast";
import Loading from "@/Components/Loading.vue";
import type { TodoItem } from "@/types/TodoItem";
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import { onMounted, ref } from "vue";

const { showSuccess, showError } = useToast();

// State
const items = ref<TodoItem[]>([]);
const loading = ref(false);
const deletingId = ref<number | null>(null);

// Fetch all items
const fetchItems = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get<TodoItem[]>("/api/todo-items");
        items.value = data;
    } finally {
        loading.value = false;
    }
};

// Delete item
const confirmDelete = (id: number) => {
    deletingId.value = id;
};

const cancelDelete = () => {
    deletingId.value = null;
};

const deleteItem = async (id: number) => {
    try {
        loading.value = true;
        await axios.delete(`/api/todo-items/${id}`);
        items.value = items.value.filter((i) => i.id !== id);
        deletingId.value = null;
        showSuccess("Item deleted successfully!");
    } catch (error) {
        showError("Something went wrong. Please try again.");
        console.error(error);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchItems);
</script>
<template>
    <!-- Loading -->
    <div v-if="loading">
        <Loading />
    </div>

    <!-- Empty State -->
    <div
        v-else-if="items.length === 0"
        class="flex flex-col items-center justify-center py-24 text-center"
    >
        <svg
            class="w-16 h-16 text-gray-300 mb-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.5"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
            />
        </svg>
        <p class="text-gray-500 text-lg font-medium">No items yet</p>
        <p class="text-gray-400 text-sm mt-1">
            Click "New Item" to get started.
        </p>
    </div>

    <!-- Todo List -->
    <div v-else class="space-y-3">
        <TransitionGroup name="list">
            <div
                v-for="item in items"
                :key="item.id"
                class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow"
            >
                <div class="flex items-start justify-between gap-4">
                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <h3
                            class="text-base font-semibold text-gray-900 truncate"
                        >
                            {{ item.name }}
                        </h3>
                        <p
                            v-if="item.description"
                            class="mt-1 text-sm text-gray-500 line-clamp-2"
                        >
                            {{ item.description }}
                        </p>
                        <p v-else class="mt-1 text-sm text-gray-400 italic">
                            No description
                        </p>
                        <p class="mt-2 text-xs text-gray-400">
                            Created at {{ formatDate(item.created_at) }}
                        </p>
                        <p class="mt-2 text-xs text-gray-400">
                            Updated at {{ formatDate(item.updated_at) }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 shrink-0">
                        <!-- Edit -->
                        <Link :href="'/edit/' + item.id">
                            <button
                                class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                title="Edit"
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
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                    />
                                </svg>
                            </button>
                        </Link>

                        <!-- Delete -->
                        <button
                            @click="confirmDelete(item.id)"
                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                            title="Delete"
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
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Inline Delete Confirmation -->
                <Transition name="fade">
                    <div
                        v-if="deletingId === item.id"
                        class="mt-4 flex items-center justify-between bg-red-50 border border-red-200 rounded-lg px-4 py-3"
                    >
                        <p class="text-sm text-red-700 font-medium">
                            Delete this item?
                        </p>
                        <div class="flex gap-2">
                            <button
                                @click="cancelDelete"
                                class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                @click="deleteItem(item.id)"
                                class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors"
                            >
                                Yes, Delete
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </TransitionGroup>
    </div>
</template>
