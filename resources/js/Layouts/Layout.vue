<script setup lang="ts">
import Footer from "@/Components/Footer.vue";
import Header from "@/Components/Header.vue";
</script>

<template>
    <div class="flex flex-col min-h-screen bg-gray-50">
        <Header />

        <main
            class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
        >
            <div class="max-w-4xl mx-auto">
                <slot />
            </div>
        </main>

        <Footer />
    </div>
</template>
