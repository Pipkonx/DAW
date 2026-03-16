import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useFileSystemStore = defineStore('filesystem', () => {
    const files = ref({
        'main.py': {
            name: 'main.py',
            path: 'main.py',
            content: `
# ¡Bienvenido a Farm Automation!
# Usa la API para controlar tu robot.

def main():
    # Plantar una cuadrícula de 3x3
    for i in range(3):
        for j in range(3):
            plant()
            move(RIGHT)
        move(DOWN)
        move(LEFT)
        move(LEFT)
        move(LEFT)

main()
`,
            isOpen: true,
            lastModified: Date.now()
        },
        'utils.py': {
            name: 'utils.py',
            path: 'utils.py',
            content: `
def harvest_area(width, height):
    # Implementa tu lógica de recolección personalizada aquí
    pass
`,
            isOpen: false,
            lastModified: Date.now()
        }
    });

    const activeFile = ref('main.py');
    
    const folders = ref(['lib']); 
    const directoryHandle = ref(null);
    const folderHandles = ref({}); // Map path -> handle

    let pollInterval = null;

    async function mountLocalFolder() {
        try {
            const handle = await window.showDirectoryPicker();
            directoryHandle.value = handle;
            
            // Clear current state
            files.value = {};
            folders.value = [];
            folderHandles.value = {};
            activeFile.value = null;
            
            await loadFromHandle(handle);
            startPolling();
            return true;
        } catch (e) {
            console.error("Access denied or cancelled", e);
            return false;
        }
    }

    async function loadFromHandle(dirHandle, pathPrefix = '') {
        folderHandles.value[pathPrefix || '/'] = dirHandle;

        for await (const entry of dirHandle.values()) {
            const currentPath = pathPrefix ? `${pathPrefix}/${entry.name}` : entry.name;
            
            if (entry.kind === 'file') {
                if (entry.name.endsWith('.py')) {
                    const file = await entry.getFile();
                    const content = await file.text();
                    files.value[currentPath] = {
                        name: entry.name,
                        path: currentPath,
                        content,
                        isOpen: false,
                        handle: entry, // Store handle for saving
                        lastModified: file.lastModified
                    };
                }
            } else if (entry.kind === 'directory') {
                folders.value.push(currentPath);
                await loadFromHandle(entry, currentPath);
            }
        }
    }

    async function saveFile(path) {
        const file = files.value[path];
        if (file) {
            if (file.handle) {
                try {
                    const writable = await file.handle.createWritable();
                    await writable.write(file.content);
                    await writable.close();
                    
                    // Update timestamp
                    const fileObj = await file.handle.getFile();
                    file.lastModified = fileObj.lastModified;
                    return true;
                } catch (e) {
                    console.error("Failed to save file", e);
                    return false;
                }
            } else {
                // In-memory file, just "save"
                return true;
            }
        }
        return false;
    }

    async function reloadFile(path) {
        const file = files.value[path];
        if (file && file.handle) {
             try {
                 const fileObj = await file.handle.getFile();
                 const content = await fileObj.text();
                 updateFileContent(path, content);
                 file.lastModified = fileObj.lastModified;
                 return true;
             } catch (e) {
                 console.error("Failed to reload file", e);
             }
        }
        return false;
    }

    async function createFile(path, content = '') {
        if (files.value[path]) return false;
        
        // Extract name from path
        const parts = path.split('/');
        const name = parts.pop();
        const folderPath = parts.join('/');
        
        // Try to create on disk if mounted
        let handle = null;
        let lastModified = Date.now();

        if (directoryHandle.value) {
            try {
                // Find parent folder handle
                // If folderPath is empty, use root directoryHandle
                // If not, look in folderHandles
                // Note: folderHandles keys are like "lib" or "lib/utils"
                // Root is stored as "/" or ""? loadFromHandle uses pathPrefix which is "" for root
                
                let parentHandle = directoryHandle.value;
                if (folderPath) {
                    // We need to traverse or use stored handles
                    // Current implementation stores flat paths in folderHandles?
                    // loadFromHandle stores `folderHandles.value[pathPrefix || '/'] = dirHandle;`
                    // So if path is "lib/test.py", folderPath is "lib".
                    // If we have folderHandles["lib"], use it.
                    parentHandle = folderHandles.value[folderPath];
                }

                if (parentHandle) {
                    handle = await parentHandle.getFileHandle(name, { create: true });
                    const writable = await handle.createWritable();
                    await writable.write(content);
                    await writable.close();
                    const fileObj = await handle.getFile();
                    lastModified = fileObj.lastModified;
                }
            } catch (e) {
                console.error("Failed to create file on disk", e);
            }
        }

        files.value[path] = { 
            name, 
            path, 
            content, 
            isOpen: true,
            handle,
            lastModified
        };
        activeFile.value = path;
        return true;
    }
    
    async function createFolder(path) {
        if (!folders.value.includes(path)) {
            folders.value.push(path);
            
            // Create on disk
            if (directoryHandle.value) {
                try {
                    const parts = path.split('/');
                    const name = parts.pop();
                    const parentPath = parts.join('/');
                    
                    let parentHandle = directoryHandle.value;
                    if (parentPath) {
                        parentHandle = folderHandles.value[parentPath];
                    }
                    
                    if (parentHandle) {
                        const newHandle = await parentHandle.getDirectoryHandle(name, { create: true });
                        folderHandles.value[path] = newHandle;
                    }
                } catch(e) {
                    console.error("Failed to create folder on disk", e);
                }
            }
        }
    }

    function openFile(path) {
        if (files.value[path]) {
            files.value[path].isOpen = true;
            activeFile.value = path;
        }
    }

    function closeFile(path) {
        if (files.value[path]) {
            files.value[path].isOpen = false;
            if (activeFile.value === path) {
                const openFiles = Object.values(files.value).filter(f => f.isOpen);
                if (openFiles.length > 0) {
                    activeFile.value = openFiles[openFiles.length - 1].path;
                } else {
                    activeFile.value = null;
                }
            }
        }
    }

    function updateFileContent(path, content) {
        if (files.value[path]) {
            files.value[path].content = content;
        }
    }
    
    function deleteFile(path) {
        if (files.value[path]) {
            // Close if open
            if (files.value[path].isOpen) {
                closeFile(path);
            }
            delete files.value[path];
            // TODO: Delete from disk if mounted?
            // File System Access API removeEntry is experimental/not always available on FileHandle
            // Usually need parentDirectoryHandle.removeEntry(name)
        }
    }

    function startPolling() {
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(async () => {
            if (!directoryHandle.value) return;
            
            // Check active file first
            if (activeFile.value && files.value[activeFile.value]) {
                await checkFileChange(activeFile.value);
            }
        }, 2000);
    }

    async function checkFileChange(path) {
        const file = files.value[path];
        if (file && file.handle) {
            try {
                const fileObj = await file.handle.getFile();
                if (fileObj.lastModified > file.lastModified) {
                    console.log("External change detected:", path);
                    const content = await fileObj.text();
                    // Auto-update content
                    // Note: This overwrites unsaved changes in editor!
                    // Ideally we should warn, but "Detectar cambios externos automáticamente" usually implies sync.
                    updateFileContent(path, content);
                    file.lastModified = fileObj.lastModified;
                }
            } catch {
                // Handle error (file deleted?)
            }
        }
    }

    return {
        files,
        folders,
        activeFile,
        directoryHandle,
        mountLocalFolder,
        saveFile,
        reloadFile,
        createFile,
        createFolder,
        openFile,
        closeFile,
        updateFileContent,
        deleteFile
    };
});
