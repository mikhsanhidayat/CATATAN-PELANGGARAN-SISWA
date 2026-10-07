<script setup lang="ts">
import AuthSimpleLayout from '@/layouts/auth/AuthSimpleLayout.vue';

const {
    title = '',
    description = '',
    wide = false,
} = defineProps<{
    title?: string;
    description?: string;
    wide?: boolean;
}>();
</script>

<template>
    <AuthSimpleLayout
        :title="title"
        :description="description"
        :wide="wide"
    >
        <slot />
    </AuthSimpleLayout>
</template>
