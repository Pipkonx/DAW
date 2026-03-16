import { ref } from 'vue';

// Force dark mode always
export const isDark = ref(true);

// No-op function to maintain API compatibility
export function useTheme() {
    return {
        isDark,
        toggleTheme: () => {} 
    };
}
