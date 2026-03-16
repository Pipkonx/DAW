<template>
  <div ref="editorContainer" class="editor-container"></div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import * as monaco from 'monaco-editor';
import editorWorker from 'monaco-editor/esm/vs/editor/editor.worker?worker';

self.MonacoEnvironment = {
  getWorker(_, label) {
    return new editorWorker();
  },
};

const props = defineProps({
  modelValue: String,
  language: {
    type: String,
    default: 'python'
  },
  readOnly: {
    type: Boolean,
    default: false
  },
  errorMarker: {
        type: Object,
        default: null // { line: 1, message: 'Error' }
    },
    executionLine: {
        type: Number,
        default: null
    },
    breakpoints: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['update:modelValue', 'save', 'update:breakpoints']);

const editorContainer = ref(null);
let editorInstance = null;
let breakpointDecorations = [];

// Static flag to prevent duplicate registration
let isIntelliSenseRegistered = false;

// IntelliSense Definition
const registerIntelliSense = () => {
    if (isIntelliSenseRegistered) return;
    
    // Check if python language is available (it should be standard in Monaco)
    if (monaco.languages.getLanguages().some(l => l.id === 'python')) {
         monaco.languages.registerCompletionItemProvider('python', {
            provideCompletionItems: () => {
                const suggestions = [
                    {
                        label: 'move',
                        kind: monaco.languages.CompletionItemKind.Function,
                        insertText: 'move(${1:direction})',
                        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                        detail: 'Mueve el robot',
                        documentation: 'Mueve el robot en la dirección especificada (UP, DOWN, LEFT, RIGHT).'
                    },
                    {
                        label: 'plant',
                        kind: monaco.languages.CompletionItemKind.Function,
                        insertText: 'plant()',
                        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                        detail: 'Planta una semilla',
                        documentation: 'Planta una semilla en la posición actual si el suelo está vacío.'
                    },
                    {
                        label: 'harvest',
                        kind: monaco.languages.CompletionItemKind.Function,
                        insertText: 'harvest()',
                        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                        detail: 'Cosecha cultivo',
                        documentation: 'Cosecha el cultivo en la posición actual.'
                    },
                    {
                        label: 'get_pos',
                        kind: monaco.languages.CompletionItemKind.Function,
                        insertText: 'get_pos()',
                        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                        detail: 'Obtener posición',
                        documentation: 'Devuelve las coordenadas (x, y) actuales.'
                    },
                    {
                        label: 'get_cell',
                        kind: monaco.languages.CompletionItemKind.Function,
                        insertText: 'get_cell(${1:x}, ${2:y})',
                        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                        detail: 'Obtener tipo de celda',
                        documentation: 'Devuelve el tipo de celda en (x, y) o en la posición actual.'
                    },
                    {
                        label: 'log',
                        kind: monaco.languages.CompletionItemKind.Function,
                        insertText: 'log("${1:message}")',
                        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                        detail: 'Registrar mensaje',
                        documentation: 'Imprime un mensaje en la consola del juego.'
                    },
                    // Constants
                    { label: 'UP', kind: monaco.languages.CompletionItemKind.Constant, insertText: 'UP', detail: 'Dirección: 0' },
                    { label: 'RIGHT', kind: monaco.languages.CompletionItemKind.Constant, insertText: 'RIGHT', detail: 'Dirección: 1' },
                    { label: 'DOWN', kind: monaco.languages.CompletionItemKind.Constant, insertText: 'DOWN', detail: 'Dirección: 2' },
                    { label: 'LEFT', kind: monaco.languages.CompletionItemKind.Constant, insertText: 'LEFT', detail: 'Dirección: 3' },
                    { label: 'SOIL', kind: monaco.languages.CompletionItemKind.Constant, insertText: 'SOIL', detail: 'Suelo: 0' },
                    { label: 'WATER', kind: monaco.languages.CompletionItemKind.Constant, insertText: 'WATER', detail: 'Agua: 1' },
                    { label: 'PLANT', kind: monaco.languages.CompletionItemKind.Constant, insertText: 'PLANT', detail: 'Planta: 2' },
                    { label: 'ROCK', kind: monaco.languages.CompletionItemKind.Constant, insertText: 'ROCK', detail: 'Roca: 3' }
                ];
                return { suggestions: suggestions };
            }
        });
    }
}

const updateMarkers = () => {
    if (!editorInstance) return;
    
    const model = editorInstance.getModel();
    if (!model) return;

    if (props.errorMarker && props.errorMarker.line) {
        monaco.editor.setModelMarkers(model, 'owner', [{
            startLineNumber: props.errorMarker.line,
            startColumn: 1,
            endLineNumber: props.errorMarker.line,
            endColumn: 1000,
            message: props.errorMarker.message,
            severity: monaco.MarkerSeverity.Error
        }]);
    } else {
        monaco.editor.setModelMarkers(model, 'owner', []);
    }
};

let decorations = [];

const updateDecorations = () => {
    if (!editorInstance) return;
    const model = editorInstance.getModel();
    if (!model) return;

    const newDecorations = [];
    if (props.executionLine) {
        newDecorations.push({
            range: new monaco.Range(props.executionLine, 1, props.executionLine, 1),
            options: {
                isWholeLine: true,
                className: 'execution-line-highlight',
                marginClassName: 'execution-line-margin',
                zIndex: 100 // Asegurar que esté por encima
            }
        });
    }
    
    // Usar deltaDecorations para actualizar
    decorations = editorInstance.deltaDecorations(decorations, newDecorations);
    
    if (props.executionLine) {
        editorInstance.revealLineInCenterIfOutsideViewport(props.executionLine);
    }
};

const updateBreakpointDecorations = () => {
    if (!editorInstance) return;
    const model = editorInstance.getModel();
    if (!model) return;

    const newDecorations = props.breakpoints.map(line => ({
        range: new monaco.Range(line, 1, line, 1),
        options: {
            isWholeLine: false,
            glyphMarginClassName: 'breakpoint-glyph',
            glyphMarginHoverMessage: { value: 'Breakpoint' }
        }
    }));
    
    breakpointDecorations = editorInstance.deltaDecorations(breakpointDecorations, newDecorations);
};

const focus = () => {
    if (editorInstance) {
        editorInstance.focus();
    }
};

defineExpose({ focus });

    const defineThemes = () => {
        monaco.editor.defineTheme('farmer-dark', {
            base: 'vs-dark',
            inherit: true,
            rules: [
                { token: '', foreground: 'd4d4d4' }, // Default foreground
                { token: 'keyword', foreground: 'c586c0' }, // Purple
                { token: 'comment', foreground: '6a9955', fontStyle: 'italic' }, // Green
                { token: 'string', foreground: 'ce9178' }, // Orange/Red
                { token: 'number', foreground: 'b5cea8' }, // Light Green
                { token: 'delimiter', foreground: 'd4d4d4' },
                { token: 'identifier', foreground: '9cdcfe' }, // Light Blue (Variables)
                { token: 'type.identifier', foreground: '4ec9b0' }, // Teal
                { token: 'function', foreground: 'dcdcaa' }, // Yellow (Functions)
                { token: 'constant', foreground: '4fc1ff' }, // Blue (Constants like UP, DOWN)
                { token: 'operator', foreground: 'd4d4d4' },
            ],
            colors: {
                'editor.foreground': '#d4d4d4',
                'editor.background': '#0d1117', // Solid GitHub-like dark background
                'editor.lineHighlightBackground': '#1e293b80',
                'editorCursor.foreground': '#22d3ee',
                'editor.selectionBackground': '#22d3ee30',
                'editorLineNumber.foreground': '#475569',
                'editorGutter.background': '#0d1117', // Match editor background
            }
        });
    };

    onMounted(() => {
        if (editorContainer.value) {
            if (!isIntelliSenseRegistered) {
                registerIntelliSense();
                isIntelliSenseRegistered = true;
            }

            defineThemes();

            editorInstance = monaco.editor.create(editorContainer.value, {
                value: props.modelValue,
                language: props.language,
                theme: 'farmer-dark',
          fontSize: 14,
          fontFamily: "'Fira Code', 'Consolas', monospace",
          minimap: { enabled: false },
          scrollBeyondLastLine: false,
          automaticLayout: true,
          padding: { top: 16, bottom: 16 },
          lineNumbersMinChars: 3,
          renderLineHighlight: 'line',
          contextmenu: false,
          overviewRulerBorder: false,
          hideCursorInOverviewRuler: true,
          glyphMargin: true,
          readOnly: props.readOnly,
        });

    editorInstance.onMouseDown((e) => {
        if (e.target.type === monaco.editor.MouseTargetType.GUTTER_GLYPH_MARGIN) {
            const line = e.target.position.lineNumber;
            const newBreakpoints = [...props.breakpoints];
            const index = newBreakpoints.indexOf(line);
            
            if (index !== -1) {
                newBreakpoints.splice(index, 1);
            } else {
                newBreakpoints.push(line);
            }
            
            emit('update:breakpoints', newBreakpoints);
        }
    });

    editorInstance.onDidChangeModelContent(() => {
      const value = editorInstance.getValue();
      emit('update:modelValue', value);
      
      // Clear markers on edit if desired, or let parent handle it
    });
    
    // Command + S to save (mock)
    editorInstance.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyS, () => {
        emit('save');
    });
    
    updateMarkers();
    updateDecorations();
    updateBreakpointDecorations();
  }
});

watch(
  () => props.modelValue,
  (newValue) => {
    if (editorInstance && newValue !== editorInstance.getValue()) {
      editorInstance.setValue(newValue);
    }
  }
);

watch(
    () => props.errorMarker,
    () => {
        updateMarkers();
    },
    { deep: true }
);

watch(
    () => props.executionLine,
    () => {
        updateDecorations();
    }
);

watch(
    () => props.breakpoints,
    () => {
        updateBreakpointDecorations();
    },
    { deep: true }
);

onBeforeUnmount(() => {
  if (editorInstance) {
    editorInstance.dispose();
  }
});
</script>

<style>
.execution-line-highlight {
    background-color: rgba(56, 189, 248, 0.25) !important;
    box-shadow: inset 4px 0 0 0 #38bdf8 !important;
}
.execution-line-margin {
    background: rgba(56, 189, 248, 0.4) !important;
    border-right: 2px solid #38bdf8 !important;
}
.breakpoint-glyph {
    background: #ef4444; /* red-500 */
    border-radius: 50%;
    width: 12px !important;
    height: 12px !important;
    margin-left: 5px;
    cursor: pointer;
    box-shadow: 0 0 8px #ef444480;
}
</style>

<style scoped>
.editor-container {
  width: 100%;
  height: 100%;
}
</style>
