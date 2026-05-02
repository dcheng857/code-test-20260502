<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import axios from 'axios';
import type { TodoItem } from '@/types/TodoItem';

// State
const items = ref<TodoItem[]>([]);
const loading = ref(false);
const submitting = ref(false);
const errors = ref<{ name?: string; description?: string }>({});

// Form state
const showForm = ref(false);
const editingItem = ref<TodoItem | null>(null);
const form = ref({ name: '', description: '' });

// Delete confirmation
const deletingId = ref<number | null>(null);

// Fetch all items
const fetchItems = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get<TodoItem[]>('/api/todo-items');
        items.value = data;
    } finally {
        loading.value = false;
    }
};

// Open form for create
const openCreate = () => {
    editingItem.value = null;
    form.value = { name: '', description: '' };
    errors.value = {};
    showForm.value = true;
};

// Open form for edit
const openEdit = (item: TodoItem) => {
    editingItem.value = item;
    form.value = { name: item.name, description: item.description ?? '' };
    errors.value = {};
    showForm.value = true;
};

// Close form
const closeForm = () => {
    showForm.value = false;
    editingItem.value = null;
    errors.value = {};
};

// Submit create or update
const submitForm = async () => {
    submitting.value = true;
    errors.value = {};
    try {
        if (editingItem.value) {
            const { data } = await axios.put<TodoItem>(`/api/todo-items/${editingItem.value.id}`, form.value);
            const index = items.value.findIndex(i => i.id === data.id);
            if (index !== -1) items.value[index] = data;
        } else {
            const { data } = await axios.post<TodoItem>('/api/todo-items', form.value);
            items.value.unshift(data);
        }
        closeForm();
    } catch (err: any) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors;
        }
    } finally {
        submitting.value = false;
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
    await axios.delete(`/api/todo-items/${id}`);
    items.value = items.value.filter(i => i.id !== id);
    deletingId.value = null;
};

// Format date
const formatDate = (dateStr: string) => {
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric',
    });
};

onMounted(fetchItems);
</script>

<template>
    <Head title="Todo Items" />

    <Layout>
        <div class="max-w-4xl mx-auto">

            <!-- Page Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Todo Items</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage your tasks</p>
                </div>
                <button
                    @click="openCreate"
                    class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Item
                </button>
            </div>

            <!-- Create / Edit Modal -->
            <Transition name="fade">
                <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
                    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ editingItem ? 'Edit Item' : 'Create New Item' }}
                        </h2>

                        <form @submit.prevent="submitForm" class="space-y-4">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Enter item name"
                                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                                    :class="errors.name ? 'border-red-400' : 'border-gray-300'"
                                    autofocus
                                />
                                <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name[0] }}</p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    placeholder="Enter description (optional)"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition resize-none"
                                ></textarea>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end gap-3 pt-2">
                                <button
                                    type="button"
                                    @click="closeForm"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :disabled="submitting"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50"
                                >
                                    {{ submitting ? 'Saving...' : editingItem ? 'Update' : 'Create' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>

            <!-- Loading -->
            <div v-if="loading" class="flex justify-center py-20">
                <svg class="animate-spin w-8 h-8 text-indigo-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
            </div>

            <!-- Empty State -->
            <div v-else-if="items.length === 0" class="flex flex-col items-center justify-center py-24 text-center">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="text-gray-500 text-lg font-medium">No items yet</p>
                <p class="text-gray-400 text-sm mt-1">Click "New Item" to get started.</p>
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
                                <h3 class="text-base font-semibold text-gray-900 truncate">{{ item.name }}</h3>
                                <p v-if="item.description" class="mt-1 text-sm text-gray-500 line-clamp-2">
                                    {{ item.description }}
                                </p>
                                <p v-else class="mt-1 text-sm text-gray-400 italic">No description</p>
                                <p class="mt-2 text-xs text-gray-400">Created {{ formatDate(item.created_at) }}</p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 shrink-0">
                                <!-- Edit -->
                                <button
                                    @click="openEdit(item)"
                                    class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                    title="Edit"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                <!-- Delete -->
                                <button
                                    @click="confirmDelete(item.id)"
                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Delete"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Inline Delete Confirmation -->
                        <Transition name="fade">
                            <div v-if="deletingId === item.id" class="mt-4 flex items-center justify-between bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                                <p class="text-sm text-red-700 font-medium">Delete this item?</p>
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

        </div>
    </Layout>
</template>

