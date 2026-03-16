// We use the CDN version loaded in index.html for performance
// import { loadPyodide } from 'pyodide'; // Don't use this unless installed via npm

let pyodide = null;

export async function initPyodide() {
    if (!pyodide) {
        if (!window.loadPyodide) {
            throw new Error("Pyodide CDN not loaded. Check index.html");
        }
        // Load Pyodide from CDN
        pyodide = await window.loadPyodide({
            indexURL: "https://cdn.jsdelivr.net/pyodide/v0.26.2/full/"
        });
        
        // Pre-load micropip if needed
        await pyodide.loadPackage("micropip");
    }
    return pyodide;
}

const PYTHON_ERROR_TRANSLATIONS = {
    'SyntaxError': 'Error de escritura (Sintaxis)',
    'IndentationError': 'Error de espacios (Sangría)',
    'NameError': 'Nombre desconocido',
    'AttributeError': 'Acción no permitida',
    'TypeError': 'Error de tipo de dato',
    'ZeroDivisionError': 'División por cero',
    'IndexError': 'Elemento fuera de rango',
    'KeyError': 'Llave no encontrada'
};

function simplifyPythonError(error, line) {
    const errorStr = error.toString();
    const errorTypeMatch = errorStr.match(/^(\w+):/);
    const errorType = errorTypeMatch ? errorTypeMatch[1] : 'Error';
    
    let message = error.message || errorStr;
    let title = PYTHON_ERROR_TRANSLATIONS[errorType] || 'Error de ejecución';

    // Personalizar mensajes comunes
    if (errorType === 'IndentationError') {
        message = 'El código dentro de una función, bucle o "if" debe estar movido a la derecha (usa la tecla Tab).';
    } else if (errorType === 'NameError') {
        const nameMatch = message.match(/name '(\w+)' is not defined/);
        const name = nameMatch ? nameMatch[1] : 'desconocido';
        message = `Estás intentando usar "${name}", pero no lo has definido antes.`;
    } else if (errorType === 'SyntaxError') {
        message = 'Hay un error en cómo está escrito el código. Revisa que no falten paréntesis, comillas o dos puntos (:).';
    } else if (errorType === 'AttributeError') {
        const attrMatch = message.match(/'\w+' object has no attribute '(\w+)'/);
        const attr = attrMatch ? attrMatch[1] : 'esa acción';
        message = `El robot no sabe cómo hacer "${attr}". Revisa que el nombre esté bien escrito.`;
    }

    return {
        title,
        message,
        line: line || 'desconocida',
        original: errorStr
    };
}

export async function runPython(code, api) {
    if (!pyodide) await initPyodide();

    // Register API functions in Python global scope
    // We bind them with _js_ prefix so we can wrap them in Python
    for (const [key, func] of Object.entries(api)) {
        pyodide.globals.set('_js_' + key, func);
    }
    
    // Preamble with wrappers and constants
    // We use inspect to capture line numbers for visual debugging
    const preamble = `
import inspect

def _get_line():
    try:
        # Stack: _get_line -> wrapper -> user_code
        # We need the line number of user_code
        f = inspect.currentframe()
        if f and f.f_back and f.f_back.f_back:
            return f.f_back.f_back.f_lineno
        return 0
    except:
        return 0

def move(dir):
    return _js_move(dir, _get_line())

def plant():
    return _js_plant(_get_line())

def harvest():
    return _js_harvest(_get_line())

def log(msg):
    _js_log(msg, _get_line())

def get_pos():
    return _js_get_pos()

def get_cell(x=None, y=None):
    return _js_get_cell(x, y)

UP = 0
RIGHT = 1
DOWN = 2
LEFT = 3
SOIL = 0
WATER = 1
PLANT = 2
ROCK = 3
`;

    try {
        await pyodide.runPythonAsync(preamble + "\n" + code);
        return { success: true };
    } catch (error) {
        console.error("Python Error:", error);
        
        // Parse error to find line number
        let line = null;
        const match = error.toString().match(/line (\d+)/);
        if (match) {
            line = parseInt(match[1]);
            const preambleLines = preamble.split('\n').length;
            line = line - preambleLines;
            if (line < 1) line = 1;
        }
        
        const simplified = simplifyPythonError(error, line);
        
        return { 
            success: false, 
            message: `${simplified.title} en línea ${simplified.line}: ${simplified.message}`, 
            line: line,
            type: error.type 
        };
    }
}

export async function analyzeCode(code) {
    if (!pyodide) await initPyodide();
    
    const analysisScript = `
import ast
import json

class CodeAnalyzer(ast.NodeVisitor):
    def __init__(self):
        self.suggestions = []

    def check_body(self, body):
        consecutive_moves = 0
        start_line = 0
        
        for node in body:
            is_move = False
            if isinstance(node, ast.Expr) and isinstance(node.value, ast.Call):
                if isinstance(node.value.func, ast.Name) and node.value.func.id == 'move':
                    is_move = True
            
            if is_move:
                if consecutive_moves == 0:
                    start_line = node.lineno
                consecutive_moves += 1
            else:
                if consecutive_moves >= 3:
                    self.suggestions.append({
                        "line": start_line,
                        "message": f"Optimization: You are repeating 'move' {consecutive_moves} times. Consider using a loop.",
                        "type": "info"
                    })
                consecutive_moves = 0
        
        if consecutive_moves >= 3:
            self.suggestions.append({
                "line": start_line,
                "message": f"Optimization: You are repeating 'move' {consecutive_moves} times. Consider using a loop.",
                "type": "info"
            })

    def visit_Module(self, node):
        self.check_body(node.body)
        self.generic_visit(node)

    def visit_FunctionDef(self, node):
        self.check_body(node.body)
        self.generic_visit(node)
        
    def visit_For(self, node):
        if not node.body:
            self.suggestions.append({
                "line": node.lineno,
                "message": "Warning: Empty loop detected.",
                "type": "warning"
            })
        self.check_body(node.body)
        self.generic_visit(node)
        
    def visit_While(self, node):
        if isinstance(node.test, ast.Constant) and node.test.value is True:
            has_break = False
            for child in ast.walk(node):
                if isinstance(child, ast.Break):
                    has_break = True
                    break
            if not has_break:
                self.suggestions.append({
                    "line": node.lineno,
                    "message": "Error: Infinite loop detected (while True without break).",
                    "type": "error"
                })
        self.check_body(node.body)
        self.generic_visit(node)

def analyze(code):
    try:
        tree = ast.parse(code)
        analyzer = CodeAnalyzer()
        analyzer.visit(tree)
        return json.dumps(analyzer.suggestions)
    except SyntaxError as e:
        return json.dumps([{
            "line": e.lineno,
            "message": f"Syntax Error: {e.msg}",
            "type": "error"
        }])
    except Exception as e:
        return json.dumps([{
            "line": 0,
            "message": f"Analysis Error: {str(e)}",
            "type": "error"
        }])

analyze(code_to_analyze)
`;

    try {
        pyodide.globals.set("code_to_analyze", code);
        const result = await pyodide.runPythonAsync(analysisScript);
        return JSON.parse(result);
    } catch (e) {
        console.error("Analysis failed:", e);
        return [];
    }
}
