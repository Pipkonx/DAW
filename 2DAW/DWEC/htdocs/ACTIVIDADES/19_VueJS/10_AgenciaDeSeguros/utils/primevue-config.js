// Ayudante para encontrar componente en el objeto global de primevue (maneja sensibilidad a mayúsculas/minúsculas)
const getPrimeVueComponent = (name) => {
    // Comprobar coincidencia exacta, minúsculas o PascalCase
    if (primevue[name]) return primevue[name];
    const pascalCase = name.charAt(0).toUpperCase() + name.slice(1);
    if (primevue[pascalCase]) return primevue[pascalCase];
    const lowerCase = name.toLowerCase();
    if (primevue[lowerCase]) return primevue[lowerCase];
    console.warn(`PrimeVue component ${name} not found`);
    return null;
};

export const registerPrimeVueComponents = (app) => {
    // Asegurar que la configuración de primevue se usa correctamente
    app.use(primevue.config.default);

    // Registrar componentes de forma segura
    const componentsToRegister = [
        { name: 'p-menubar', source: 'menubar' },
        { name: 'p-button', source: 'button' },
        { name: 'p-card', source: 'card' },
        { name: 'p-inputtext', source: 'inputtext' },
        { name: 'p-datatable', source: 'datatable' },
        { name: 'p-column', source: 'column' },
        { name: 'p-dialog', source: 'dialog' },
        { name: 'p-dropdown', source: 'dropdown' },
        { name: 'p-calendar', source: 'calendar' },
        { name: 'p-inputnumber', source: 'inputnumber' },
        { name: 'p-tag', source: 'tag' },
        { name: 'p-message', source: 'message' },
        { name: 'p-tooltip', source: 'tooltip' } // Asegúrate de que Tooltip también se registre si se usa como componente
    ];

    componentsToRegister.forEach(({ name, source }) => {
        const component = getPrimeVueComponent(source);
        if (component) {
            app.component(name, component);
        }
    });
};
