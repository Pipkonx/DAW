<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    show: Boolean,
    post: Object,
});

const emit = defineEmits(['close']);

const form = useForm({
    content: props.post?.content ?? '',
});

watch(() => props.post, (newPost) => {
    form.content = newPost?.content ?? '';
});

const submit = () => {
    if (props.post) {
        form.put(route('social.update', props.post.id), {
            onSuccess: () => closeModal(),
        });
    }
};

const closeModal = () => {
    emit('close');
};
</script>

<template>
    <Modal :show="show" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Editar Publicación
            </h2>

            <div class="mt-6">
                <textarea
                    v-model="form.content"
                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    rows="4"
                    placeholder="¿Qué estás pensando?"
                ></textarea>
            </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeModal"> Cancelar </SecondaryButton>

                <PrimaryButton
                    class="ms-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="submit"
                >
                    Guardar Cambios
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
