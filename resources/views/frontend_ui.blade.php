<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeX - Compact Code Executor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.58.2/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.58.2/theme/monokai.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.58.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.58.2/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.58.2/mode/python/python.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.58.2/mode/clike/clike.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.58.2/addon/edit/closebrackets.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.58.2/addon/edit/matchbrackets.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.58.2/addon/selection/active-line.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #fff;
            height: 100vh;
            overflow: hidden;
        }

        .header {
            background: rgba(0, 0, 0, 0.3);
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .language-selector {
            padding: 5px 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            color: #fff;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .btn-execute {
            padding: 8px 16px;
            background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
            border: none;
            border-radius: 15px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-execute:hover {
            background: linear-gradient(45deg, #ff5252, #ff7979);
            transform: translateY(-1px);
        }

        .main-layout {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            height: calc(100vh - 60px);
            gap: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        .panel {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(5px);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .panel-header {
            background: rgba(0, 0, 0, 0.3);
            padding: 8px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 40px;
        }

        .panel-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #fff;
            display: flex;
            align-items: center;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #4ecdc4;
            margin-right: 8px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .panel-content {
            flex: 1;
            overflow: hidden;
        }

        /* Question Panel */
        .question-content {
            padding: 15px;
            overflow-y: auto;
            font-size: 0.9rem;
            line-height: 1.6;
            height: 100%;
        }

        .question-content h3 {
            color: #4ecdc4;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .question-content p {
            margin-bottom: 10px;
            color: #e0e6ed;
        }

        .question-content code {
            background: rgba(0, 0, 0, 0.3);
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Consolas', monospace;
            color: #ff6b6b;
        }

        .question-content pre {
            background: rgba(0, 0, 0, 0.3);
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
            margin: 10px 0;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
        }

        .difficulty-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .difficulty-easy {
            background: #4ecdc4;
            color: #000;
        }

        .difficulty-medium {
            background: #ff9f43;
            color: #000;
        }

        .difficulty-hard {
            background: #ff6b6b;
            color: #fff;
        }

        /* Code Editor */
        .CodeMirror {
            height: 100% !important;
            font-size: 13px;
            line-height: 1.4;
            font-family: 'Consolas', 'Monaco', monospace;
        }

        /* Output Panel */
        .output-content {
            padding: 15px;
            overflow-y: auto;
            font-family: 'Consolas', monospace;
            font-size: 13px;
            line-height: 1.5;
            background: rgba(0, 0, 0, 0.2);
            height: 100%;
        }

        .output-content::-webkit-scrollbar {
            width: 6px;
        }

        .output-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .output-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .question-content::-webkit-scrollbar {
            width: 6px;
        }

        .question-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .question-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .output-line {
            margin-bottom: 5px;
            padding: 3px 0;
        }

        .output-error {
            color: #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
            padding: 5px;
            border-radius: 3px;
            border-left: 3px solid #ff6b6b;
        }

        .output-success {
            color: #4ecdc4;
            background: rgba(78, 205, 196, 0.1);
            padding: 5px;
            border-radius: 3px;
            border-left: 3px solid #4ecdc4;
        }

        .output-info {
            color: #e0e6ed;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
            color: #4ecdc4;
        }

        .loading::after {
            content: '';
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #4ecdc4;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 8px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .panel-actions {
            display: flex;
            gap: 5px;
        }

        .btn-small {
            padding: 4px 8px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-small:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .question-selector {
            padding: 4px 8px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            color: #fff;
            font-size: 0.8rem;
            cursor: pointer;
        }

        @media (max-width: 1200px) {
            .main-layout {
                grid-template-columns: 1fr 1fr;
                grid-template-rows: 1fr 1fr;
            }

            .question-panel {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 768px) {
            .main-layout {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr auto;
            }

            .header {
                flex-direction: column;
                gap: 10px;
                padding: 10px;
            }

            .header-controls {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>CodeX</h1>
        <div class="header-controls">
            <select class="language-selector" id="language-select">
                <option value="javascript">JavaScript</option>
                <option value="python">Python</option>
                <option value="java">Java</option>
                <option value="cpp">C++</option>
            </select>
            <button class="btn-execute" id="execute-button">▶ Run</button>
        </div>
    </div>

    <div class="main-layout">
        <!-- Question Panel -->
        <div class="panel question-panel">
            <div class="panel-header">
                <div class="panel-title">
                    <span class="status-dot"></span>
                    Problem
                </div>
                <select class="question-selector" id="question-select">
                    <option value="array-sum">Array Sum</option>
                    <option value="palindrome">Palindrome Check</option>
                    <option value="fibonacci">Fibonacci</option>
                    <option value="sorting">Bubble Sort</option>
                    <option value="factorial">Factorial</option>
                </select>
            </div>
            <div class="panel-content">
                <div class="question-content" id="question-content">
                    <div class="difficulty-badge difficulty-easy">Easy</div>
                    <h3>Sum of Array Elements</h3>
                    <p>Write a function that takes an array of numbers and returns the sum of all elements.</p>

                    <p><strong>Example:</strong></p>
                    <pre>Input: [1, 2, 3, 4, 5]
                        Output: 15</pre>

                                            <p><strong>Function Signature:</strong></p>
                                            <pre>function sumArray(arr) {
                            // Your code here
                        }</pre>

                    <p><strong>Requirements:</strong></p>
                    <p>• Handle empty arrays (return 0)</p>
                    <p>• Handle negative numbers</p>
                    <p>• Use efficient algorithm</p>

                    <p><strong>Test Cases:</strong></p>
                    <pre>sumArray([1, 2, 3, 4, 5]) → 15
                        sumArray([]) → 0
                        sumArray([-1, 1, -2, 2]) → 0</pre>
                </div>
            </div>
        </div>

        <!-- Code Editor Panel -->
        <div class="panel editor-panel">
            <div class="panel-header">
                <div class="panel-title">
                    <span class="status-dot"></span>
                    Code Editor
                </div>
                <div class="panel-actions">
                    <button class="btn-small" id="clear-editor">Clear</button>
                    <button class="btn-small" id="format-code">Format</button>
                </div>
            </div>
            <div class="panel-content">
                <textarea id="code-editor">function sumArray(arr) {
                        // Write your solution here
                        let sum = 0;
                        for (let i = 0; i < arr.length; i++) {
                            sum += arr[i];
                        }
                        return sum;
                    }

                    // Test the function
                    console.log(sumArray([1, 2, 3, 4, 5])); // Expected: 15
                    console.log(sumArray([])); // Expected: 0
                    console.log(sumArray([-1, 1, -2, 2])); // Expected: 0
                </textarea>
            </div>
        </div>

        <!-- Output Panel -->
        <div class="panel output-panel">
            <div class="panel-header">
                <div class="panel-title">
                    <span class="status-dot"></span>
                    Output
                </div>
                <div class="panel-actions">
                    <button class="btn-small" id="clear-output">Clear</button>
                </div>
            </div>
            <div class="panel-content">
                <div class="output-content" id="output-content">
                    <div class="output-success">
                        <strong>🚀 Ready to code!</strong><br>
                        Click "Run" to execute your code and see the results here.
                    </div>
                </div>
                <div class="loading" id="loading">
                    Executing...
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize CodeMirror
        const codeEditor = CodeMirror.fromTextArea(document.getElementById('code-editor'), {
            mode: 'javascript',
            theme: 'monokai',
            lineNumbers: true,
            autoCloseBrackets: true,
            matchBrackets: true,
            styleActiveLine: true,
            indentUnit: 2,
            tabSize: 2,
            lineWrapping: true
        });

        // Sample questions
        const questions = {
            'array-sum': {
                difficulty: 'easy',
                title: 'Sum of Array Elements',
                description: 'Write a function that takes an array of numbers and returns the sum of all elements.',
                example: 'Input: [1, 2, 3, 4, 5]\nOutput: 15',
                signature: 'function sumArray(arr) {\n    // Your code here\n}',
                requirements: ['Handle empty arrays (return 0)', 'Handle negative numbers', 'Use efficient algorithm'],
                testCases: 'sumArray([1, 2, 3, 4, 5]) → 15\nsumArray([]) → 0\nsumArray([-1, 1, -2, 2]) → 0',
                code: `function sumArray(arr) {
    // Write your solution here
    let sum = 0;
    for (let i = 0; i < arr.length; i++) {
        sum += arr[i];
    }
    return sum;
}

// Test the function
console.log(sumArray([1, 2, 3, 4, 5])); // Expected: 15
console.log(sumArray([])); // Expected: 0
console.log(sumArray([-1, 1, -2, 2])); // Expected: 0`
            },
            'palindrome': {
                difficulty: 'easy',
                title: 'Palindrome Check',
                description: 'Write a function that checks if a string is a palindrome (reads the same forwards and backwards).',
                example: 'Input: "racecar"\nOutput: true',
                signature: 'function isPalindrome(str) {\n    // Your code here\n}',
                requirements: ['Ignore case sensitivity', 'Handle empty strings', 'Ignore spaces and punctuation'],
                testCases: 'isPalindrome("racecar") → true\nisPalindrome("hello") → false\nisPalindrome("A man a plan a canal Panama") → true',
                code: `function isPalindrome(str) {
    // Write your solution here
    const cleaned = str.toLowerCase().replace(/[^a-z0-9]/g, '');
    return cleaned === cleaned.split('').reverse().join('');
}

// Test the function
console.log(isPalindrome("racecar")); // Expected: true
console.log(isPalindrome("hello")); // Expected: false
console.log(isPalindrome("A man a plan a canal Panama")); // Expected: true`
            },
            'fibonacci': {
                difficulty: 'medium',
                title: 'Fibonacci Sequence',
                description: 'Write a function that returns the nth number in the Fibonacci sequence.',
                example: 'Input: 7\nOutput: 13',
                signature: 'function fibonacci(n) {\n    // Your code here\n}',
                requirements: ['Handle n = 0 and n = 1', 'Use efficient algorithm', 'Return correct sequence'],
                testCases: 'fibonacci(0) → 0\nfibonacci(1) → 1\nfibonacci(7) → 13',
                code: `function fibonacci(n) {
    // Write your solution here
    if (n <= 1) return n;
    
    let a = 0, b = 1;
    for (let i = 2; i <= n; i++) {
        let temp = a + b;
        a = b;
        b = temp;
    }
    return b;
}

// Test the function
console.log(fibonacci(0)); // Expected: 0
console.log(fibonacci(1)); // Expected: 1
console.log(fibonacci(7)); // Expected: 13`
            },
            'sorting': {
                difficulty: 'medium',
                title: 'Bubble Sort Algorithm',
                description: 'Implement the bubble sort algorithm to sort an array of numbers in ascending order.',
                example: 'Input: [64, 34, 25, 12, 22, 11, 90]\nOutput: [11, 12, 22, 25, 34, 64, 90]',
                signature: 'function bubbleSort(arr) {\n    // Your code here\n}',
                requirements: ['Sort in ascending order', 'Modify array in place', 'Handle empty arrays'],
                testCases: 'bubbleSort([64, 34, 25, 12, 22, 11, 90]) → [11, 12, 22, 25, 34, 64, 90]\nbubbleSort([]) → []',
                code: `function bubbleSort(arr) {
    // Write your solution here
    const n = arr.length;
    for (let i = 0; i < n - 1; i++) {
        for (let j = 0; j < n - i - 1; j++) {
            if (arr[j] > arr[j + 1]) {
                // Swap elements
                let temp = arr[j];
                arr[j] = arr[j + 1];
                arr[j + 1] = temp;
            }
        }
    }
    return arr;
}

// Test the function
console.log(bubbleSort([64, 34, 25, 12, 22, 11, 90]));
console.log(bubbleSort([]));`
            },
            'factorial': {
                difficulty: 'easy',
                title: 'Factorial Function',
                description: 'Write a function that calculates the factorial of a number.',
                example: 'Input: 5\nOutput: 120',
                signature: 'function factorial(n) {\n    // Your code here\n}',
                requirements: ['Handle n = 0 (return 1)', 'Handle negative numbers',
                    'Use iterative or recursive approach'
                ],
                testCases: 'factorial(5) → 120\nfactorial(0) → 1\nfactorial(1) → 1',
                code: `function factorial(n) {
    // Write your solution here
    if (n < 0) return undefined;
    if (n === 0 || n === 1) return 1;
    
    let result = 1;
    for (let i = 2; i <= n; i++) {
        result *= i;
    }
    return result;
}

// Test the function
console.log(factorial(5)); // Expected: 120
console.log(factorial(0)); // Expected: 1
console.log(factorial(1)); // Expected: 1`
            }
        };

        // Language modes
        const languageModes = {
            javascript: 'javascript',
            python: 'python',
            java: 'text/x-java',
            cpp: 'text/x-c++src'
        };

        // Update question display
        function updateQuestion(questionId) {
            const question = questions[questionId];
            const content = document.getElementById('question-content');

            const difficultyClass = `difficulty-${question.difficulty}`;
            content.innerHTML = `
                <div class="difficulty-badge ${difficultyClass}">${question.difficulty.charAt(0).toUpperCase() + question.difficulty.slice(1)}</div>
                <h3>${question.title}</h3>
                <p>${question.description}</p>
                
                <p><strong>Example:</strong></p>
                <pre>${question.example}</pre>
                
                <p><strong>Function Signature:</strong></p>
                <pre>${question.signature}</pre>
                
                <p><strong>Requirements:</strong></p>
                ${question.requirements.map(req => `<p>• ${req}</p>`).join('')}
                
                <p><strong>Test Cases:</strong></p>
                <pre>${question.testCases}</pre>
            `;

            codeEditor.setValue(question.code);
        }

        // Question selector
        document.getElementById('question-select').addEventListener('change', (e) => {
            updateQuestion(e.target.value);
        });

        // Language selector
        document.getElementById('language-select').addEventListener('change', (e) => {
            const language = e.target.value;
            codeEditor.setOption('mode', languageModes[language]);
        });

        // Execute code
        document.getElementById('execute-button').addEventListener('click', async () => {
            const code = codeEditor.getValue();
            const language = document.getElementById('language-select').value;
            const outputContent = document.getElementById('output-content');
            const loadingIndicator = document.getElementById('loading');
            const executeButton = document.getElementById('execute-button');

            if (!code.trim()) {
                outputContent.innerHTML = '<div class="output-error">Please write some code first!</div>';
                return;
            }

            // Show loading
            loadingIndicator.style.display = 'block';
            outputContent.innerHTML = '';
            executeButton.disabled = true;
            executeButton.textContent = 'Running...';

            try {
                await new Promise(resolve => setTimeout(resolve, 500)); // Simulate delay

                if (language === 'javascript') {
                    const originalLog = console.log;
                    const logs = [];
                    console.log = (...args) => {
                        logs.push(args.join(' '));
                        originalLog.apply(console, args);
                    };

                    try {
                        eval(code);
                        const output = logs.join('\n') || 'Code executed successfully (no output)';
                        outputContent.innerHTML =
                            `<div class="output-success"><strong>✅ Success:</strong></div><div class="output-info">${output}</div>`;
                    } catch (error) {
                        outputContent.innerHTML =
                            `<div class="output-error"><strong>❌ Error:</strong><br>${error.message}</div>`;
                    }

                    console.log = originalLog;
                } else {
                    outputContent.innerHTML =
                        `<div class="output-success"><strong>✅ Simulated execution for ${language}</strong></div><div class="output-info">In a real implementation, this would connect to a backend service.</div>`;
                }
            } catch (error) {
                outputContent.innerHTML =
                    `<div class="output-error"><strong>❌ Error:</strong><br>${error.message}</div>`;
            } finally {
                loadingIndicator.style.display = 'none';
                executeButton.disabled = false;
                executeButton.textContent = '▶ Run';
            }
        });

        // Clear buttons
        document.getElementById('clear-editor').addEventListener('click', () => {
            codeEditor.setValue('');
        });

        document.getElementById('clear-output').addEventListener('click', () => {
            document.getElementById('output-content').innerHTML =
                '<div class="output-success"><strong>🚀 Output cleared!</strong></div>';
        });

        // Format code (basic)
        document.getElementById('format-code').addEventListener('click', () => {
            const code = codeEditor.getValue();
            // Basic formatting - in a real app, you'd use a proper formatter
            const formatted = code.replace(/;\s*}/g, ';\n}').replace(/{\s*/g, ' {\n    ');
            codeEditor.setValue(formatted);
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'Enter') {
                document.getElementById('execute-button').click();
            }
        });

        // Initialize
        updateQuestion('array-sum');
        codeEditor.focus();
    </script>
</body>

</html>
