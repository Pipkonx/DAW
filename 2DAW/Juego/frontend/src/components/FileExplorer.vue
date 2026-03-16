<template>
  <div class="bg-transparent h-full text-sm select-none border-r border-transparent flex flex-col font-sans">
    <!-- Header -->
    <div class="flex items-center justify-between px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-white/5 border-b border-white/5">
        <span>Archivos</span>
        <div class="flex gap-1">
            <button @click="mountFolder" class="hover:text-white hover:bg-white/10 rounded p-1 transition-colors" title="Abrir Carpeta Local">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
                </svg>
            </button>
            <button @click="createFile" class="hover:text-white hover:bg-white/10 rounded p-1 transition-colors" title="Nuevo Archivo">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l4 4a1 1 0 01.586 1.414V19a2 2 0 01-2 2z" />
                </svg>
            </button>
            <button @click="createFolder" class="hover:text-white hover:bg-white/10 rounded p-1 transition-colors" title="Nueva Carpeta">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Tree -->
    <div class="flex-1 overflow-y-auto custom-scroll p-1">
        <FileTreeItem 
            v-for="(item, key) in tree.children" 
            :key="key" 
            :item="item" 
            :depth="0"
            @open="handleOpen"
            @delete="handleDelete"
        />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useFileSystemStore } from '../stores/filesystem';
import FileTreeItem from './FileTreeItem.vue';

const fsStore = useFileSystemStore();

const tree = computed(() => {
    const root = { children: {} };
    
    // Process explicit folders
    fsStore.folders.forEach(path => {
        const parts = path.split('/');
        let current = root;
        parts.forEach((part, index) => {
            if (!current.children[part]) {
                const currentPath = parts.slice(0, index + 1).join('/');
                current.children[part] = { 
                    name: part, 
                    type: 'folder', 
                    path: currentPath,
                    children: {}
                };
            }
            current = current.children[part];
        });
    });

    // Process files
    Object.values(fsStore.files).forEach(file => {
        const parts = file.path.split('/');
        const fileName = parts.pop();
        let current = root;
        
        // Ensure path to file exists (implicit folders)
        parts.forEach((part, index) => {
            if (!current.children[part]) {
                const currentPath = parts.slice(0, index + 1).join('/');
                current.children[part] = { 
                    name: part, 
                    type: 'folder', 
                    path: currentPath,
                    children: {}
                };
            }
            current = current.children[part];
        });
        
        current.children[fileName] = {
            ...file,
            type: 'file'
        };
    });

    return root;
});

const handleOpen = (path) => {
    fsStore.openFile(path);
};

const handleDelete = (path) => {
    if (confirm(`¿Eliminar ${path}?`)) {
        fsStore.deleteFile(path);
    }
};

const createFile = async () => {
    const name = prompt("Nombre del archivo (ej. script.py):", "nuevo_script.py");
    if (name) {
        await fsStore.createFile(name);
    }
};

const createFolder = async () => {
    const name = prompt("Nombre de la carpeta:", "nueva_carpeta");
    if (name) {
        await fsStore.createFolder(name);
    }
};

const mountFolder = async () => {
    try {
        await fsStore.mountLocalFolder();
    } catch (e) {
        console.error("Mount failed", e);
        alert("Error al montar carpeta. " + e.message);
    }
};
</script>
