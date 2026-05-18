<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    class?: HTMLAttributes['class'];
    colspan?: number | string;
}>();
</script>

<template>
    <td
        :class="
            cn(
                'p-4 align-middle [&:has([role=checkbox])]:pr-0 [&>[role=checkbox]]:translate-y-[2px]',
                props.class,
            )
        "
        :colspan="colspan"
    >
        <slot />
    </td>
</template>
