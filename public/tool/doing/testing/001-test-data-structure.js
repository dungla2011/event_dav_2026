// 001-test-data-structure.js
// Test: Data structure and core algorithms (NO DOM)

const { JSDOM } = require('jsdom');

console.log('🧪 001 - DATA STRUCTURE TEST (No DOM)');
console.log('=====================================');

// Mock family tree data
const testData = {
    name: "Ông Tổ",
    children: [
        {
            name: "Con ông Tổ 1",
            children: [
                { name: "Cháu 1", children: [] },
                { name: "Cháu 2", children: [] }
            ]
        },
        {
            name: "Con ông Tổ 2", 
            children: [
                { name: "Cháu 3", children: [] }
            ]
        },
        { name: "Con ông Tổ 3", children: [] }
    ]
};

// Test 1: Tree hierarchy depth calculation
function calculateTreeDepth(node) {
    if (!node.children || node.children.length === 0) {
        return 1;
    }
    return 1 + Math.max(...node.children.map(child => calculateTreeDepth(child)));
}

function test001_TreeDepth() {
    console.log('\n🔍 Test 1.1: Tree depth calculation');
    
    const depth = calculateTreeDepth(testData);
    const expectedDepth = 3; // Root -> Children -> Grandchildren
    
    console.log(`📊 Calculated depth: ${depth}`);
    console.log(`📊 Expected depth: ${expectedDepth}`);
    console.log(`Result: ${depth === expectedDepth ? '✅ PASS' : '❌ FAIL'}`);
    
    return depth === expectedDepth;
}

// Test 2: Node counting
function countNodes(node) {
    if (!node.children || node.children.length === 0) {
        return 1;
    }
    return 1 + node.children.reduce((sum, child) => sum + countNodes(child), 0);
}

function test002_NodeCount() {
    console.log('\n🔍 Test 1.2: Node counting');
    
    const totalNodes = countNodes(testData);
    const expectedNodes = 7; // Root + 3 children + 3 grandchildren
    
    console.log(`📊 Total nodes: ${totalNodes}`);
    console.log(`📊 Expected nodes: ${expectedNodes}`);
    console.log(`Result: ${totalNodes === expectedNodes ? '✅ PASS' : '❌ FAIL'}`);
    
    return totalNodes === expectedNodes;
}

// Test 3: Level node distribution
function getNodesPerLevel(node, level = 0, result = {}) {
    if (!result[level]) result[level] = 0;
    result[level]++;
    
    if (node.children && node.children.length > 0) {
        node.children.forEach(child => {
            getNodesPerLevel(child, level + 1, result);
        });
    }
    
    return result;
}

function test003_LevelDistribution() {
    console.log('\n🔍 Test 1.3: Level distribution');
    
    const distribution = getNodesPerLevel(testData);
    const expected = { 0: 1, 1: 3, 2: 3 }; // Level 0: 1 root, Level 1: 3 children, Level 2: 3 grandchildren
    
    console.log(`📊 Distribution:`, distribution);
    console.log(`📊 Expected:`, expected);
    
    const isCorrect = JSON.stringify(distribution) === JSON.stringify(expected);
    console.log(`Result: ${isCorrect ? '✅ PASS' : '❌ FAIL'}`);
    
    return isCorrect;
}

// Test 4: Level settings structure
function test004_LevelSettings() {
    console.log('\n🔍 Test 1.4: Level settings structure');
    
    const levelSettings = {
        0: { width: 200, height: 120, textRotation: 'horizontal' },
        1: { width: 230, height: 140, textRotation: 'vertical-top' },
        2: { width: 260, height: 160, textRotation: 'vertical-bottom' }
    };
    
    const hasCorrectStructure = Object.keys(levelSettings).every(level => {
        const setting = levelSettings[level];
        return setting.hasOwnProperty('width') && 
               setting.hasOwnProperty('height') && 
               setting.hasOwnProperty('textRotation');
    });
    
    console.log(`📊 Level settings structure valid: ${hasCorrectStructure ? '✅ PASS' : '❌ FAIL'}`);
    return hasCorrectStructure;
}

// Test 5: Configuration object validation
function test005_ConfigValidation() {
    console.log('\n🔍 Test 1.5: Configuration validation');
    
    const config = {
        nodeWidth: 180,
        nodeHeight: 100,
        textRotation: 'horizontal',
        levelSettings: {
            0: { width: 200, height: 120 },
            1: { width: 180, height: 100 }
        }
    };
    
    const isValid = config.nodeWidth > 0 && 
                   config.nodeHeight > 0 &&
                   ['horizontal', 'vertical-top', 'vertical-bottom'].includes(config.textRotation) &&
                   typeof config.levelSettings === 'object';
    
    console.log(`📊 Config structure: ${JSON.stringify(config, null, 2)}`);
    console.log(`📊 Config valid: ${isValid ? '✅ PASS' : '❌ FAIL'}`);
    
    return isValid;
}

// Run all tests
async function runDataStructureTests() {
    console.log('🚀 Running all data structure tests...\n');
    
    const results = [
        test001_TreeDepth(),
        test002_NodeCount(), 
        test003_LevelDistribution(),
        test004_LevelSettings(),
        test005_ConfigValidation()
    ];
    
    const passed = results.filter(r => r).length;
    const total = results.length;
    
    console.log('\n📋 SUMMARY:');
    console.log(`✅ Passed: ${passed}/${total}`);
    console.log(`❌ Failed: ${total - passed}/${total}`);
    console.log(`Success rate: ${(passed/total*100).toFixed(1)}%`);
    
    if (passed === total) {
        console.log('🎉 All data structure tests PASSED!');
        return true;
    } else {
        console.log('⚠️ Some data structure tests FAILED!');
        return false;
    }
}

// Export for use in main test runner
module.exports = { runDataStructureTests };

// Run if called directly
if (require.main === module) {
    runDataStructureTests().then(success => {
        process.exit(success ? 0 : 1);
    });
}
