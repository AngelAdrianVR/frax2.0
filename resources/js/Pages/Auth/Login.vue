<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <AuthenticationCard>
        <template #logo>
            <!-- Logo en Teal para resaltar sobre fondo claro -->
            <AuthenticationCardLogo class="w-20 h-20 text-teal-600" />
        </template>

        <!-- Header del Formulario -->
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Bienvenido de nuevo</h2>
            <p class="text-gray-500 text-sm mt-2">Ingresa a tu panel de control Frax</p>
        </div>

        <div v-if="status" class="mb-6 font-medium text-sm text-teal-600 bg-teal-50 p-3 rounded-lg border border-teal-100 text-center">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <!-- Labels en gris oscuro para contraste -->
                <InputLabel for="email" value="Email" class="text-gray-700 !important font-medium" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-2 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="tu@correo.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-5">
                <InputLabel for="password" value="Contraseña" class="text-gray-700 !important font-medium" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-2 block w-full"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-6">
                <label class="flex items-center cursor-pointer group">
                    <!-- Checkbox nativo con acento Teal -->
                    <Checkbox v-model:checked="form.remember" name="remember" class="text-teal-600 focus:ring-teal-500 border-gray-300 rounded" />
                    <span class="ms-2 text-sm text-gray-500 group-hover:text-gray-700 transition-colors">Recordarme</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-8">
                <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-gray-500 hover:text-teal-600 transition-colors font-medium">
                    ¿Olvidaste tu contraseña?
                </Link>

                <PrimaryButton class="ms-4 w-full sm:w-auto shadow-lg shadow-teal-500/20" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Ingresar <i class="pi pi-arrow-right ml-2 text-xs"></i>
                </PrimaryButton>
            </div>

            <!-- Footer Link -->
            <div class="mt-8 border-t border-gray-100 pt-6 text-center">
                <p class="text-sm text-gray-500">
                    ¿No tienes cuenta? 
                    <Link :href="route('register')" class="font-bold text-teal-600 hover:text-teal-700 transition-colors">
                        Regístrate gratis
                    </Link>
                </p>
            </div>
        </form>
    </AuthenticationCard>
</template>