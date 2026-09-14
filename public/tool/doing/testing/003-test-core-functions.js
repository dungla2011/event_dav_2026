// 003-test-core-functions.js  
// Test: Core utility functions (JSDOM + Mock D3)

const { JSDOM } = require('jsdom');

console.log('🧪 003 - CORE FUNCTIONS TEST (JSDOM + Mock D3)');
console.log('===============================================');

// Setup JSDOM with mock D3
function setupMockEnvironment() {
    const dom = new JSDOM(`<!DOCTYPE html>
        <html>
        <head><title>Core Functions Test</title></head>
        <body>
            <svg id="familyTree" width="800" height="600">
                <g class="container"></g>
            </svg>
        </body>
        </html>`);
    
    global.window = dom.window;
    global.document = dom.window.document;
    
    // Mock D3 functions
    global.d3 = {
        hierarchy: (data) => ({
            data: data,
            depth: 0,
            descendants: function() {
                const nodes = [this];
                if (data.children) {
                    data.children.forEach((child, i) => {
                        nodes.push({ data: child, depth: 1, parent: this });
                    });
                }
                return nodes;
            }
        }),
        tree: () => ({
            size: (size) => ({}),
            nodeSize: (size) => ({})
        })
    };
    
    return dom;
}

// Mock core functions
function setupCoreFunctions() {
    // Text rotation function
    global.getSVGTextTransform = function(rotation, x, y) {
        if (rotation === 0 || rotation === '0') {
            return `translate(${x}, ${y})`;
        } else {
            return `translate(${x}, ${y}) rotate(${rotation})`;
        }
    };
    
    // Text rotation getter
    global.getTextRotation = function(level) {
        const levelSettings = global.levelSettings || {};
        const setting = levelSettings[level];
        
        if (setting && setting.textRotation) {
            switch(setting.textRotation) {
                case 'vertical-top': return -90;
                case 'vertical-bottom': return 90;
                default: return 0;
            }
        }
        
        // Global rotation
        const globalRotation = global.globalTextRotation || 'horizontal';
        switch(globalRotation) {
            case 'vertical-top': return -90;
            case 'vertical-bottom': return 90;
            default: return 0;
        }
    };
    
    // Level settings getter
    global.getLevelSettings = function(level) {
        const levelSettings = global.levelSettings || {};
        return levelSettings[level] || {
            width: 180,
            height: 100, 
            textRotation: 'horizontal'
        };
    };
    
    // Node dimensions calculator
    global.getNodeDimensions = function(nodeData) {
        const level = nodeData.depth || 0;
        const levelSettings = getLevelSettings(level);
        
        return {
            width: levelSettings.width,
            height: levelSettings.height
        };
    };
    
    // Tree positioning calculator
    global.calculateTreeLayout = function(treeData, nodeWidth = 180, nodeHeight = 100) {
        const nodes = [];
        const links = [];
        
        // Simple positioning algorithm
        function positionNode(node, x = 0, y = 0, level = 0) {
            const positioned = {
                ...node,
                x: x,
                y: y * (nodeHeight + 50), // Vertical spacing
                level: level
            };
            nodes.push(positioned);
            
            if (node.children) {
                const childSpacing = nodeWidth + 50;
                const startX = x - (node.children.length - 1) * childSpacing / 2;
                
                node.children.forEach((child, i) => {
                    const childX = startX + i * childSpacing;
                    const childY = y + 1;
                    
                    links.push({
                        source: positioned,
                        target: { x: childX, y: childY * (nodeHeight + 50) }
                    });
                    
                    positionNode(child, childX, childY, level + 1);
                });
            }
        }
        
        positionNode(treeData);
        return { nodes, links };
    };
}

// Test 1: Text rotation calculation
function test001_TextRotation() {
    console.log('\n🔍 Test 3.1: Text rotation calculation');
    
    // Test global rotation
    global.globalTextRotation = 'vertical-top';
    const globalRotation = getTextRotation(0);
    
    // Test level-specific rotation
    global.levelSettings = {
        1: { textRotation: 'vertical-bottom' },
        2: { textRotation: 'horizontal' }
    };
    
    const level1Rotation = getTextRotation(1);
    const level2Rotation = getTextRotation(2);
    
    console.log(`📊 Global rotation: ${globalRotation}° (expected: -90°)`);
    console.log(`📊 Level 1 rotation: ${level1Rotation}° (expected: 90°)`);
    console.log(`📊 Level 2 rotation: ${level2Rotation}° (expected: 0°)`);
    
    const isCorrect = globalRotation === -90 && level1Rotation === 90 && level2Rotation === 0;
    console.log(`Result: ${isCorrect ? '✅ PASS' : '❌ FAIL'}`);
    
    return isCorrect;
}

// Test 2: SVG transform generation
function test002_SVGTransform() {
    console.log('\n🔍 Test 3.2: SVG transform generation');
    
    const tests = [
        { rotation: 0, x: 100, y: 50, expected: 'translate(100, 50)' },
        { rotation: 90, x: 100, y: 50, expected: 'translate(100, 50) rotate(90)' },
        { rotation: -90, x: 150, y: 75, expected: 'translate(150, 75) rotate(-90)' }
    ];
    
    let allPassed = true;
    
    tests.forEach((test, i) => {
        const result = getSVGTextTransform(test.rotation, test.x, test.y);
        const passed = result === test.expected;
        
        console.log(`📊 Test ${i+1}: rotation=${test.rotation}°, pos=(${test.x},${test.y})`);
        console.log(`  Result: "${result}"`);
        console.log(`  Expected: "${test.expected}"`);
        console.log(`  ${passed ? '✅ PASS' : '❌ FAIL'}`);
        
        if (!passed) allPassed = false;
    });
    
    console.log(`Overall result: ${allPassed ? '✅ PASS' : '❌ FAIL'}`);
    return allPassed;
}

// Test 3: Node dimensions calculation
function test003_NodeDimensions() {
    console.log('\n🔍 Test 3.3: Node dimensions calculation');
    
    // Setup level settings
    global.levelSettings = {
        0: { width: 200, height: 120 },
        1: { width: 230, height: 140 },
        2: { width: 260, height: 160 }
    };
    
    const testNodes = [
        { depth: 0 },
        { depth: 1 },
        { depth: 2 },
        { depth: 3 } // No level settings, should use default
    ];
    
    const expectedDimensions = [
        { width: 200, height: 120 },
        { width: 230, height: 140 },
        { width: 260, height: 160 },
        { width: 180, height: 100 } // Default
    ];
    
    let allCorrect = true;
    
    testNodes.forEach((node, i) => {
        const dimensions = getNodeDimensions(node);
        const expected = expectedDimensions[i];
        
        const isCorrect = dimensions.width === expected.width && 
                         dimensions.height === expected.height;
        
        console.log(`📊 Level ${node.depth}: ${dimensions.width}x${dimensions.height} (expected: ${expected.width}x${expected.height}) ${isCorrect ? '✅' : '❌'}`);
        
        if (!isCorrect) allCorrect = false;
    });
    
    console.log(`Result: ${allCorrect ? '✅ PASS' : '❌ FAIL'}`);
    return allCorrect;
}

// Test 4: Tree layout calculation
function test004_TreeLayout() {
    console.log('\n🔍 Test 3.4: Tree layout calculation');
    
    const testData = {
        name: "Root",
        children: [
            { name: "Child 1", children: [] },
            { name: "Child 2", children: [] }
        ]
    };
    
    const layout = calculateTreeLayout(testData, 180, 100);
    
    console.log(`📊 Generated ${layout.nodes.length} nodes and ${layout.links.length} links`);
    console.log(`📊 Nodes:`, layout.nodes.map(n => ({ name: n.name, x: n.x, y: n.y, level: n.level })));
    console.log(`📊 Links:`, layout.links.map(l => ({ sx: l.source.x, sy: l.source.y, tx: l.target.x, ty: l.target.y })));
    
    const hasCorrectNodeCount = layout.nodes.length === 3; // Root + 2 children
    const hasCorrectLinkCount = layout.links.length === 2; // 2 parent-child links
    const rootAtOrigin = layout.nodes[0].x === 0 && layout.nodes[0].y === 0;
    
    console.log(`📊 Node count correct: ${hasCorrectNodeCount ? '✅' : '❌'}`);
    console.log(`📊 Link count correct: ${hasCorrectLinkCount ? '✅' : '❌'}`);
    console.log(`📊 Root positioned at origin: ${rootAtOrigin ? '✅' : '❌'}`);
    
    const result = hasCorrectNodeCount && hasCorrectLinkCount && rootAtOrigin;
    console.log(`Result: ${result ? '✅ PASS' : '❌ FAIL'}`);
    
    return result;
}

// Test 5: Level settings integration
function test005_LevelSettingsIntegration() {
    console.log('\n🔍 Test 3.5: Level settings integration');
    
    // Setup complex level settings
    global.levelSettings = {
        0: { width: 250, height: 150, textRotation: 'horizontal' },
        1: { width: 200, height: 120, textRotation: 'vertical-top' },
        2: { width: 180, height: 100, textRotation: 'vertical-bottom' }
    };
    
    const testCases = [
        { level: 0, expectedWidth: 250, expectedHeight: 150, expectedRotation: 0 },
        { level: 1, expectedWidth: 200, expectedHeight: 120, expectedRotation: -90 },
        { level: 2, expectedWidth: 180, expectedHeight: 100, expectedRotation: 90 },
        { level: 3, expectedWidth: 180, expectedHeight: 100, expectedRotation: 0 } // Default
    ];
    
    let allPassed = true;
    
    testCases.forEach(testCase => {
        const settings = getLevelSettings(testCase.level);
        const nodeData = { depth: testCase.level };
        const dimensions = getNodeDimensions(nodeData);
        const rotation = getTextRotation(testCase.level);
        
        const widthCorrect = dimensions.width === testCase.expectedWidth;
        const heightCorrect = dimensions.height === testCase.expectedHeight;
        const rotationCorrect = rotation === testCase.expectedRotation;
        
        console.log(`📊 Level ${testCase.level}:`);
        console.log(`  Width: ${dimensions.width} (expected: ${testCase.expectedWidth}) ${widthCorrect ? '✅' : '❌'}`);
        console.log(`  Height: ${dimensions.height} (expected: ${testCase.expectedHeight}) ${heightCorrect ? '✅' : '❌'}`);
        console.log(`  Rotation: ${rotation}° (expected: ${testCase.expectedRotation}°) ${rotationCorrect ? '✅' : '❌'}`);
        
        if (!widthCorrect || !heightCorrect || !rotationCorrect) {
            allPassed = false;
        }
    });
    
    console.log(`Result: ${allPassed ? '✅ PASS' : '❌ FAIL'}`);
    return allPassed;
}

// Run all core function tests
async function runCoreFunctionTests() {
    console.log('🚀 Running all core function tests...\n');
    
    const dom = setupMockEnvironment();
    setupCoreFunctions();
    
    const results = [
        test001_TextRotation(),
        test002_SVGTransform(),
        test003_NodeDimensions(),
        test004_TreeLayout(),
        test005_LevelSettingsIntegration()
    ];
    
    const passed = results.filter(r => r).length;
    const total = results.length;
    
    console.log('\n📋 SUMMARY:');
    console.log(`✅ Passed: ${passed}/${total}`);
    console.log(`❌ Failed: ${total - passed}/${total}`);
    console.log(`Success rate: ${(passed/total*100).toFixed(1)}%`);
    
    // Cleanup
    dom.window.close();
    
    if (passed === total) {
        console.log('🎉 All core function tests PASSED!');
        return true;
    } else {
        console.log('⚠️ Some core function tests FAILED!');
        return false;
    }
}

// Export for main test runner
module.exports = { runCoreFunctionTests };

// Run if called directly
if (require.main === module) {
    runCoreFunctionTests().then(success => {
        process.exit(success ? 0 : 1);
    });
}
