<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

import Heading from '@/components/Heading.vue';
import Icon from '@/components/Icon.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogTrigger,
} from '@/components/ui/dialog';
import vendas from '@/routes/vendas';

const props = defineProps<{
    item: Record<string, any>;
}>();

const open = ref(false);

const emit = defineEmits<{
    (e: 'remover-item-local', payload: any): void;
    (e: 'item-removido', payload: any): void;
}>();

function removeItem(id_item: any) {
    const idStr = String(id_item ?? '');

    if (idStr.includes('adicionado_')) {
        emit('remover-item-local', id_item);
        toast.success('Item removido localmente.');
        open.value = false;
        return;
    }

    router.delete(vendas.removerItem(id_item).url, {
        onSuccess: () => {
            toast.success('Item removido com sucesso!');
            emit('item-removido', id_item);
            open.value = false;
        },
        onError: (errors) => {
            toast.error('Erro ao remover o Item.');
            console.error(errors);
        },
    });
}

import { Trash2Icon } from 'lucide-vue-next';

</script>

<template>
    <div class="flex gap-2">
        <Dialog v-model:open="open">
            <DialogTrigger as-child>
                <Button class="mt-2 bg-red-600 text-white hover:bg-destructive/90">
                    <Trash2Icon class="w-4 h-4 mr-2" />
                    Excluir
                </Button>
            </DialogTrigger>
            <DialogContent>
                <div class="flex flex-col gap-4">

                    <span>Confirma a exclusão do item: <br /><strong>{{ item.produto?.nome ?? item.nome }}</strong></span>

                    <div class="grid-cols-1 md:grid-cols-2">
                        <!-- Form fields for creating a new vendedor go here -->
                        <div class="mt-4 flex justify-end gap-2">
                            <Button @click="removeItem(props.item?.id ?? props.item?.id_item)"
                                class="mt-2 bg-red-600 text-white hover:bg-destructive/90" tabindex="5"
                                data-test="register-user-button">
                                <Trash2Icon class="w-4 h-4 mr-2" />
                                Confirmar
                            </Button>
                            <DialogClose as-child>
                                <Button type="button" class="mt-2" tabindex="5" data-test="register-user-button">
                                    Cancelar
                                </Button>
                            </DialogClose>
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>

<style scoped></style>
