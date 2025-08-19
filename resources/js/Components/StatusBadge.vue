<script setup>

const props = defineProps({
    enabled: Boolean,
    status: Number
});

</script>

<template>
    <template v-if="enabled == 0 && status != 3"><span class="badge bg-red-lt">Не
            показывается</span></template>
    <template v-if="enabled == 0 && status == 3"><span class="badge bg-red-lt">Не
            показывается</span></template>
    <template v-if="enabled == 1 && status == 3"><span class="badge bg-green-lt">Показывается</span></template>
</template>