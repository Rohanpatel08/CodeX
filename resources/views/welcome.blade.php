<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        .title-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .tagline {
            font-size: 1.2rem;
            font-weight: 300;
            margin-top: 5px;
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
            height: 50px;
            background: rgba(0, 0, 0, 0.3);
            padding: 8px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 50px;
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

        .generate-question {
            padding: 10px 15px;
            background: linear-gradient(50deg, #fc4b4b, #4ecdc4);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 15px;
            cursor: pointer;
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

        .question-selector option {
            color: #000;
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
        <h1>CodeX - Where Code Meets Challenge</h1>
        <div class="header-controls">
            <select class="language-selector" id="language-select">
                @foreach ($languages as $language)
                    <option id="{{ $language['id'] }}" style="color: #000;">{{ $language['name'] }}</option>
                @endforeach
                {{-- <option value="python">Python</option>
                <option value="java">Java</option>
                <option value="cpp">C++</option> --}}
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
                <button class="generate-question" id="generate-question">Generate Question</button>
                {{-- <select class="question-selector" id="question-select" >
                    <option value="array-sum">Array Sum</option>
                    <option value="palindrome">Palindrome Check</option>
                    <option value="fibonacci">Fibonacci</option>
                    <option value="sorting">Bubble Sort</option>
                    <option value="factorial">Factorial</option>
                </select> --}}
            </div>
            <div class="panel-content">
                <div class="question-content" id="question-content">
                    <div style="text-align: center; padding: 50px 20px; color: #4ecdc4;">
                        <h3>🎯 Ready to Challenge Yourself?</h3>
                        <p style="margin: 20px 0; font-size: 1.1rem;">Click the <strong>"Generate Question"</strong>
                            button to get a coding problem tailored for you!</p>
                        <p style="color: #e0e6ed; font-size: 0.9rem;">✨ Fresh questions • 🚀 Multiple difficulties • 💡
                            Learn by doing</p>
                    </div>
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
                <textarea id="code-editor">// Welcome to CodeX! 🚀
// 
// Steps to get started:
// 1. Generate a question using the "Generate Question" button
// 2. Read the problem statement carefully
// 3. Write your solution here
// 4. Click "Run" to test your code
//
// Happy coding! 💻✨

function solve() {
    // Your amazing solution goes here!
    
}

// Test your solution
console.log("Ready to code! Generate a question first.");</textarea>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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

        // Global variables
        let questions = {};
        let currentQuestionId = null;

        // API Configuration
        const API_BASE_URL = '/api'; // Adjust this to your Laravel API base URL

        // Fetch questions from backend
        async function fetchQuestions() {
            try {
                const response = await fetch(`/questions`);
                const data = await response.json();

                // Transform Laravel questions to frontend format
                questions = {};
                data.forEach(question => {
                    questions[question.id] = {
                        id: question.id,
                        difficulty: question.difficulty || 'easy',
                        title: question.title,
                        description: question.description,
                        inputFormat: question.input_format || '',
                        outputFormat: question.output_format || '',
                        constraints: question.constraints || '',
                        testCases: question.test_cases || [],
                        code: question.starter_code ||
                            `// Write your solution here\nfunction solve() {\n    // Your code here\n}\n\n// Test your solution\nconsole.log(solve());`
                    };
                });

                // Populate question selector
                populateQuestionSelector();

                // Load first question
                const firstQuestionId = Object.keys(questions)[0];
                if (firstQuestionId) {
                    updateQuestion(firstQuestionId);
                }

            } catch (error) {
                console.error('Error fetching questions:', error);
                // No fallback - user should generate questions
            }
        }

        // Populate question selector dropdown
        // function populateQuestionSelector() {
        //     const selector = document.getElementById('question-select');
        //     selector.innerHTML = '';

        //     Object.keys(questions).forEach(questionId => {
        //         const option = document.createElement('option');
        //         option.value = questionId;
        //         option.textContent = questions[questionId].title;
        //         selector.appendChild(option);
        //     });
        // }

        // Fallback static questions
        function loadStaticQuestions() {
            questions = {
                'static-1': {
                    id: 'static-1',
                    difficulty: 'easy',
                    title: 'Sum of Array Elements',
                    description: 'Write a function that takes an array of numbers and returns the sum of all elements.',
                    inputFormat: 'Array of integers',
                    outputFormat: 'Single integer (sum)',
                    constraints: 'Array length <= 1000',
                    testCases: [{
                            input: '[1, 2, 3, 4, 5]',
                            expected_output: '15'
                        },
                        {
                            input: '[]',
                            expected_output: '0'
                        },
                        {
                            input: '[-1, 1, -2, 2]',
                            expected_output: '0'
                        }
                    ],
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
                }
            };

            populateQuestionSelector();
            updateQuestion('static-1');
        }

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
            if (!question) return;

            currentQuestionId = questionId;
            const content = document.getElementById('question-content');

            const difficultyClass = `difficulty-${question.difficulty}`;

            // Format test cases for display
            let testCasesDisplay = '';
            if (question.testCases && question.testCases.length > 0) {
                testCasesDisplay = question.testCases.map(tc =>
                    `Input: ${tc.input}\nOutput: ${tc.expected_output}`
                ).join('\n\n');
            }

            content.innerHTML = `
                <div class="difficulty-badge ${difficultyClass}">${question.difficulty.charAt(0).toUpperCase() + question.difficulty.slice(1)}</div>
                <h3>${question.title}</h3>
                <p>${question.description}</p>
                
                ${question.inputFormat ? `<p><strong>Input Format:</strong></p><pre>${question.inputFormat}</pre>` : ''}
                
                ${question.outputFormat ? `<p><strong>Output Format:</strong></p><pre>${question.outputFormat}</pre>` : ''}
                
                ${question.constraints ? `<p><strong>Constraints:</strong></p><pre>${question.constraints}</pre>` : ''}
                
                ${testCasesDisplay ? `<p><strong>Test Cases:</strong></p><pre>${testCasesDisplay}</pre>` : ''}
            `;

            codeEditor.setValue(question.code);
        }

        // Question selector
        // document.getElementById('question-select').addEventListener('change', (e) => {
        //     updateQuestion(e.target.value);
        // });

        // Language selector
        document.getElementById('language-select').addEventListener('change', (e) => {
            const language = e.target.selectedOptions[0].textContent;
            const languageName = (language.trim().split(' ')[0]).toLowerCase();
            codeEditor.setOption('mode', languageModes[languageName]);
        });

        // Execute code via backend
        document.getElementById('execute-button').addEventListener('click', async () => {
            const code = codeEditor.getValue();
            const languageId = document.getElementById('language-select').selectedOptions[0].id;
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
                // Send code to backend for evaluation
                // currentQuestionId is already set as global variable
                console.log('Language ID:', languageId);
                console.log('Current Question ID:', currentQuestionId, 'Type:', typeof currentQuestionId);
                console.log('About to send request to:', `/api/submit`);

                $.ajax({
                    url: "{{ route('api.submit') }}",
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    contentType: "application/json",
                    data: JSON.stringify({
                        code: code,
                        language: languageId,
                        question_id: currentQuestionId
                    }),
                    success: function(response) {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers);

                        const result = response;
                        console.log('Parsed JSON result:', result);
                        if (response.success === true) {
                            if (result.success) {
                                let outputHtml =
                                    '<div class="output-success"><strong>✅ Success:</strong></div>';

                                if (result.output) {
                                    outputHtml += `<div class="output-info">${result.output}</div>`;
                                }

                                if (result.test_results) {
                                    outputHtml +=
                                        '<div class="output-info"><strong>Test Results:</strong></div>';
                                    result.test_results.forEach((test, index) => {
                                        const status = test.passed ? '✅' : '❌';
                                        outputHtml +=
                                            `<div class="output-info">${status} Test ${index + 1}: ${test.passed ? 'PASSED' : 'FAILED'}</div>`;
                                        if (!test.passed) {
                                            outputHtml +=
                                                `<div class="output-error">Expected: ${test.expected}<br>Got: ${test.actual}</div>`;
                                        }
                                    });
                                }

                                outputContent.innerHTML = outputHtml;
                            } else {
                                outputContent.innerHTML =
                                    `<div class="output-error"><strong>❌ Error:</strong><br>${result.error || 'Execution failed'}</div>`;
                            }
                        } else {
                            outputContent.innerHTML =
                                `<div class="output-error"><strong>❌ Server Error:</strong><br>${result.message || 'Failed to execute code'}</div>`;
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                        // handle error
                    }
                });

            } catch (error) {
                console.error('Execution error:', error);
                outputContent.innerHTML =
                    `<div class="output-error"><strong>❌ Connection Error:</strong><br>Unable to connect to server. Please try again.<br><br><strong>Error Details:</strong><br>${error.message}</div>`;
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
        // Don't fetch questions initially - let user generate them
        codeEditor.focus();

        document.getElementById('generate-question').addEventListener('click', () => {
            const generateButton = document.getElementById('generate-question');
            
            // Show loading state
            generateButton.disabled = true;
            generateButton.textContent = 'Generating...';
            generateButton.style.opacity = '0.7';
            
            $.ajax({
                url: '/api/generate-questions',
                method: 'POST',
                success: function(response) {
                    testCasesDisplay = response.testCases.map(tc =>
                        `Input: ${tc.input}\nOutput: ${tc.expected_output}`
                    ).join('\n\n');
                    console.log(response);
                    if (response.status == 'success') {
                        // Store question ID in global variable
                        currentQuestionId = response.question.id;

                        const content = document.getElementById('question-content');
                        content.innerHTML = `
                            <p hidden>${response.question.id}</p>
                            <h3>${response.question.title}</h3>
                            <p>${response.question.description}</p>
                            
                            ${response.question.input_format ? `<p><strong>Input Format:</strong></p><pre>${response.question.input_format}</pre>` : ''}
                            
                            ${response.question.output_format ? `<p><strong>Output Format:</strong></p><pre>${response.question.output_format}</pre>` : ''}
                            
                            ${response.question.constraints ? `<p><strong>Constraints:</strong></p><pre>${response.question.constraints}</pre>` : ''}
                            
                            ${testCasesDisplay ? `<p><strong>Test Cases:</strong></p><pre>${testCasesDisplay}</pre>` : ''}
                        `;

                        // Update code editor with starter code
                        const starterCode = response.question.starter_code || `// ${response.question.title}
// ${response.question.description}

function solve() {
    // Write your solution here
    
}

// Test your solution
console.log(solve());`;
                        codeEditor.setValue(starterCode);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Question generation error:', error);
                    // You can add error handling here if needed
                },
                complete: function() {
                    // Hide loading state
                    generateButton.disabled = false;
                    generateButton.textContent = 'Generate Question';
                    generateButton.style.opacity = '1';
                }
            })
        });
    </script>
</body>

</html>
