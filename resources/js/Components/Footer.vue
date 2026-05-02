<script setup lang="ts"></script>

<template>
    <footer class="bg-gray-900 text-gray-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div
                class="flex flex-col md:flex-row items-center justify-between gap-4"
            >
                <!-- Brand -->
                <div class="flex items-center gap-2">
                    <svg
                        class="w-5 h-5 text-indigo-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                        />
                    </svg>
                    <span class="text-sm font-semibold text-white"
                        >TodoApp</span
                    >
                </div>

                <!-- Copyright -->
                <p class="text-sm">&copy; {{ new Date().getFullYear() }}</p>
            </div>
        </div>
    </footer>
</template>
