import { ref } from 'vue';

const activeToasts = ref([]);

export function useToast() {
    const showToast = (message, type = 'success', duration = 3000) => {
        const id = Date.now();
        activeToasts.value.push({ id, message, type, duration });
    };

    const removeToast = (id) => {
        activeToasts.value = activeToasts.value.filter(t => t.id !== id);
    };

    return {
        activeToasts,
        showToast,
        removeToast
    };
}
