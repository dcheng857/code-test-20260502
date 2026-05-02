<script setup lang="ts">
import Loading from "@/Components/Loading.vue";
import { useToast } from "@/common/useToast";
import { TodoItem } from "@/types/TodoItem";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import { ref } from "vue";

const { showSuccess, showError } = useToast();

const props = defineProps({
    id: {
        type: String,
        default: null,
    },
});

// State
const loading = ref(false);
const submitting = ref(false);
const errors = ref<{ name?: string; description?: string }>({});

// Form state - initialize with empty object for create mode
const editingItem = ref<TodoItem>({
    id: 0,
    name: "",
    description: null,
    created_at: "",
    updated_at: "",
});

// Close form
const onCancel = () => {
    errors.value = {};
    router.get("/");
};

// Fetch item for editing
const fetchItem = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get<TodoItem>(
            `/api/todo-items/${props.id}`,
        );
        editingItem.value = data;
    } finally {
        loading.value = false;
    }
};

// Check if editing or creating
const isEditing = () => props.id && editingItem.value.id > 0;

// Submit create or update
const submitForm = async () => {
    submitting.value = true;
    errors.value = {};
    try {
        loading.value = true;
        if (isEditing()) {
            await axios.put<TodoItem>(
                `/api/todo-items/${editingItem.value.id}`,
                {
                    name: editingItem.value.name,
                    description: editingItem.value.description,
                },
            );
            showSuccess("Item updated successfully!");
        } else {
            await axios.post<TodoItem>("/api/todo-items", {
                name: editingItem.value.name,
                description: editingItem.value.description,
            });
            showSuccess("Item created successfully!");
        }
        router.get("/");
    } catch (err: any) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors;
        } else {
            showError("Something went wrong. Please try again.");
        }
    } finally {
        submitting.value = false;
        loading.value = false;
    }
};

if (props.id) {
    fetchItem();
}
</script>

<template>
    <div v-if="loading">
        <Loading />
    </div>

    <div v-else class="bg-white rounded-xl shadow-xl w-full p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            {{ isEditing() ? "Edit Item" : "Create New Item" }}
        </h2>

        <form @submit.prevent="submitForm" class="space-y-4">
            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Name <span class="text-red-500">*</span></label
                >
                <input
                    v-model="editingItem.name"
                    type="text"
                    placeholder="Enter item name"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                    :class="errors.name ? 'border-red-400' : 'border-gray-300'"
                    autofocus
                />
                <p v-if="errors.name" class="mt-1 text-xs text-red-500">
                    {{ errors.name[0] }}
                </p>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Description</label
                >
                <textarea
                    v-model="editingItem.description"
                    rows="3"
                    placeholder="Enter description"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition resize-none"
                    :class="errors.name ? 'border-red-400' : 'border-gray-300'"
                ></textarea>
                <p v-if="errors.description" class="mt-1 text-xs text-red-500">
                    {{ errors.description[0] }}
                </p>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-2">
                <button
                    type="button"
                    @click="onCancel"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="submitting"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50"
                >
                    {{
                        submitting
                            ? "Saving..."
                            : isEditing()
                              ? "Update"
                              : "Create"
                    }}
                </button>
            </div>
        </form>
    </div>
</template>
