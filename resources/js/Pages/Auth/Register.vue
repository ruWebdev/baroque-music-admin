<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Регистрация" />

        <form @submit.prevent="submit">

            <h4 class="h4 text-center mb-4" style="margin-bottom: 80px !important;">
                Регистрация аккаунта
            </h4>
            <div class="mb-3">
                <label class="form-label">Ваше имя</label>
                <TextInput id="name" type="text" class="form-control" v-model="form.name" required autofocus
                    autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
                <small>Это поле необходимо только для внутреннего пользования</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Адрес электронной почты</label>
                <TextInput id="email" type="email" class="form-control" v-model="form.email" required
                    autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
                <small><b>Обратите внимание:</b> Потребуется подтверждение вашего E-mail</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Пароль</label>
                <TextInput id="password" type="password" class="form-control" v-model="form.password" required
                    autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="mb-3">
                <label class="form-label">Подтверждение пароля</label>
                <TextInput id="password_confirmation" type="password" class="form-control"
                    v-model="form.password_confirmation" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>
            <div class="mb-3">
                <label class="form-check">
                    <input type="checkbox" class="form-check-input" />
                    <span class="form-check-label">Я подтверждаю согласие с <a href="./terms-of-service.html"
                            tabindex="-1">Правилами пользования сайтом</a> и с <a href="./terms-of-service.html"
                            tabindex="-1">Политикой обработки персональных данных</a>.</span>
                </label>
            </div>
            <div class="form-footer">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Зарегистрироваться
                </PrimaryButton>
            </div>

        </form>
        <div class="text-center text-muted mt-3">
            Уже есть аккаунт? <a href="/" tabindex="-1">Войдите</a>
        </div>

    </GuestLayout>
</template>
