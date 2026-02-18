import { ref } from 'vue';

const isPrivacyMode = ref(localStorage.getItem('privacy_mode') === 'true');

export function usePrivacy() {
    const togglePrivacyMode = () => {
        isPrivacyMode.value = !isPrivacyMode.value;
        localStorage.setItem('privacy_mode', isPrivacyMode.value);
    };

    return {
        isPrivacyMode,
        togglePrivacyMode
    };
}
