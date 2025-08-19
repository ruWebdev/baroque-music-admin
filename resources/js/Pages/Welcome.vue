<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Welcome" />


        <h4 class="h4 text-center mb-4" style="margin-bottom: 80px !important;">
            Кабинет администратора
        </h4>
        <form @submit.prevent="submit">
            <div class="mb-3 mt-4">
                <label class="form-label">Адрес электронной почты</label>
                <TextInput id="email" type="email" class="form-control" v-model="form.email" required autofocus
                    autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div class="mb-2">
                <label class="form-label">
                    Пароль
                    <span class="form-label-description">
                        <Link v-if="canResetPassword" :href="route('password.request')">
                        Я не помню пароль
                        </Link>
                    </span>
                </label>
                <TextInput id="password" type="password" class="form-control" v-model="form.password" required
                    autocomplete="current-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="mb-2">
                <label class="form-check">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="form-check-label">Запомнить на этом устройстве</span>
                </label>
            </div>
            <div class="form-footer">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Войти
                </PrimaryButton>
            </div>
        </form>

    </GuestLayout>
</template>
