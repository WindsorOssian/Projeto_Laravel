<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
</script>

<template>
    <AuthBase
        title="Crie sua conta"
        description="Coloque seus dados para se registrar"
    >
        <Head title="Registro" />

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Nome</Label>
                    <Input
                        id="name"
                        type="text"
                        
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Nome completo"
                        maxlength="255"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="text"
                        
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="Digite aqui seu email..."
                        maxlength="255"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Senha</Label>
                    <Input
                        id="password"
                        type="password"
                        
                        :tabindex="3"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Senha"
                        maxlength="255"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirma senha</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirma senha"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    tabindex="5"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <Spinner v-if="processing" />
                    Criar sua conta
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Já possui conta?
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                    :tabindex="6"
                    >Faça o login aqui</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
