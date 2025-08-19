<script setup>

const props = defineProps({
    status: Number
});

</script>

<template>
    <template v-if="status == 0"><span class="badge bg-cyan-lt">Не отправлялось на
            модерацию</span></template>
    <template v-if="status == 1"><span class="badge bg-orange-lt">Запрос на
            модерацию</span></template>
    <template v-if="status == 2"><span class="badge bg-red-lt">Отклонено</span></template>
    <template v-if="status == 3"><span class="badge bg-green-lt">Модерация
            пройдена</span></template>
</template>