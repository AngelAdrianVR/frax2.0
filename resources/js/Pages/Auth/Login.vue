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
            <AuthenticationCardLogo class="w-20 h-20 text-[#0E63B1]" />
        </template>

        <!-- Header del Formulario -->
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-zinc-100 tracking-tight">Bienvenido de nuevo</h2>
            <p class="text-zinc-400 text-sm mt-2">Ingresa a tu panel de control Frax</p>
        </div>

        <div v-if="status" class="mb-6 font-medium text-sm text-[#0E63B1] bg-[#0E63B1]/10 p-3 rounded-lg border border-[#0E63B1]/20 text-center">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" class="text-zinc-300 font-medium" />
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
                <InputLabel for="password" value="Contraseña" class="text-zinc-300 font-medium" />
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
                    <Checkbox v-model:checked="form.remember" name="remember" class="text-[#0E63B1] focus:ring-[#0E63B1] border-zinc-600 rounded" />
                    <span class="ms-2 text-sm text-zinc-400 group-hover:text-zinc-200 transition-colors">Recordarme</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-8">
                <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-zinc-400 hover:text-[#0E63B1] transition-colors font-medium">
                    ¿Olvidaste tu contraseña?
                </Link>

                <PrimaryButton class="ms-4 w-full sm:w-auto shadow-lg shadow-blue-900/20" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Ingresar <i class="pi pi-arrow-right ml-2 text-xs"></i>
                </PrimaryButton>
            </div>

            <!-- Footer Link -->
            <div class="mt-8 border-t border-zinc-800 pt-6 text-center">
                <p class="text-sm text-zinc-400">
                    ¿No tienes cuenta? 
                    <Link :href="route('register')" class="font-bold text-[#0E63B1] hover:text-[#0c5599] transition-colors">
                        Regístrate gratis
                    </Link>
                </p>
            </div>
        </form>
    </AuthenticationCard>
</template>