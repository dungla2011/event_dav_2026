// 002-test-config-system.js
// Test: Configuration system with localStorage (JSDOM)

const { JSDOM } = require('jsdom');

console.log('🧪 002 - CONFIG SYSTEM TEST (JSDOM)');
console.log('===================================');

// Setup JSDOM environment
function setupJSDOM() {
    const dom = new JSDOM(`<!DOCTYPE html>
        <html>
        <head><title>Config Test</title></head>
        <body>
            <select id="textRotation">
                <option value="horizontal">Nằm ngang</option>
                <option value="vertical-top">Dọc xuống</option>
                <option value="vertical-bottom">Dọc lên</option>
            </select>
            <input id="nodeWidth" type="range" min="100" max="300" value="180">
            <input id="nodeHeight" type="range" min="80" max="200" value="100">
        </body>
        </html>`, {
        url: 'http://localhost',
        storageQuota: 10000000
    });
    
    global.window = dom.window;
    global.document = dom.window.document;
    global.localStorage = dom.window.localStorage;
    
    return dom;
}

// Mock configuration functions
function mockConfigFunctions() {
    global.saveConfig = function() {
        const config = {
            nodeWidth: parseInt(document.getElementById('nodeWidth')?.value || 180),
            nodeHeight: parseInt(document.getElementById('nodeHeight')?.value || 100),
            textRotation: document.getElementById('textRotation')?.value || 'horizontal',
            levelSettings: global.levelSettings || {}
        };
        
        localStorage.setItem('familyTreeConfig', JSON.stringify(config));
        return config;
    };
    
    global.loadConfig = function() {
        const config = localStorage.getItem('familyTreeConfig');
        return config ? JSON.parse(config) : null;
    };
    
    global.getLevelSettings = function(level) {
        const levelSettings = global.levelSettings || {};
        return levelSettings[level] || { 
            width: 180, 
            height: 100, 
            textRotation: 'horizontal' 
        };
    };
    
    global.setLevelSettings = function(level, settings) {
        if (!global.levelSettings) global.levelSettings = {};
        global.levelSettings[level] = settings;
    };
}

// Test 1: Save/Load basic config
function test001_BasicSaveLoad() {
    console.log('\n🔍 Test 2.1: Basic save/load config');
    
    // Set some values
    document.getElementById('nodeWidth').value = '250';
    document.getElementById('nodeHeight').value = '150';
    document.getElementById('textRotation').value = 'vertical-top';
    
    // Save config
    const savedConfig = saveConfig();
    console.log(`📊 Saved config:`, savedConfig);
    
    // Load config
    const loadedConfig = loadConfig();
    console.log(`📊 Loaded config:`, loadedConfig);
    
    const isEqual = JSON.stringify(savedConfig) === JSON.stringify(loadedConfig);
    console.log(`Result: ${isEqual ? '✅ PASS' : '❌ FAIL'}`);
    
    return isEqual;
}

// Test 2: Level settings save/load
function test002_LevelSettings() {
    console.log('\n🔍 Test 2.2: Level settings save/load');
    
    // Set level settings
    setLevelSettings(0, { width: 200, height: 120, textRotation: 'horizontal' });
    setLevelSettings(1, { width: 230, height: 140, textRotation: 'vertical-top' });
    setLevelSettings(2, { width: 260, height: 160, textRotation: 'vertical-bottom' });
    
    // Save with level settings
    const config = saveConfig();
    console.log(`📊 Config with levels:`, config);
    
    // Test getLevelSettings
    const level0 = getLevelSettings(0);
    const level1 = getLevelSettings(1);
    const level2 = getLevelSettings(2);
    
    console.log(`📊 Level 0:`, level0);
    console.log(`📊 Level 1:`, level1);
    console.log(`📊 Level 2:`, level2);
    
    const isCorrect = level0.width === 200 && level1.width === 230 && level2.width === 260;
    console.log(`Result: ${isCorrect ? '✅ PASS' : '❌ FAIL'}`);
    
    return isCorrect;
}

// Test 3: localStorage persistence
function test003_LocalStoragePersistence() {
    console.log('\n🔍 Test 2.3: localStorage persistence');
    
    // Clear localStorage
    localStorage.clear();
    
    // Save a config
    const testConfig = {
        nodeWidth: 300,
        nodeHeight: 180,
        textRotation: 'vertical-bottom',
        levelSettings: {
            0: { width: 280, height: 160 }
        }
    };
    
    localStorage.setItem('familyTreeConfig', JSON.stringify(testConfig));
    
    // Retrieve directly from localStorage
    const retrieved = localStorage.getItem('familyTreeConfig');
    const parsed = JSON.parse(retrieved);
    
    console.log(`📊 Stored:`, testConfig);
    console.log(`📊 Retrieved:`, parsed);
    
    const isPersistent = parsed.nodeWidth === 300 && parsed.textRotation === 'vertical-bottom';
    console.log(`Result: ${isPersistent ? '✅ PASS' : '❌ FAIL'}`);
    
    return isPersistent;
}

// Test 4: Config validation
function test004_ConfigValidation() {
    console.log('\n🔍 Test 2.4: Config validation');
    
    const validConfigs = [
        { nodeWidth: 180, nodeHeight: 100, textRotation: 'horizontal' },
        { nodeWidth: 250, nodeHeight: 150, textRotation: 'vertical-top' },
        { nodeWidth: 300, nodeHeight: 200, textRotation: 'vertical-bottom' }
    ];
    
    const invalidConfigs = [
        { nodeWidth: -10, nodeHeight: 100, textRotation: 'horizontal' }, // Negative width
        { nodeWidth: 180, nodeHeight: 0, textRotation: 'horizontal' }, // Zero height
        { nodeWidth: 180, nodeHeight: 100, textRotation: 'invalid' } // Invalid rotation
    ];
    
    function validateConfig(config) {
        return config.nodeWidth > 0 && 
               config.nodeHeight > 0 && 
               ['horizontal', 'vertical-top', 'vertical-bottom'].includes(config.textRotation);
    }
    
    const validResults = validConfigs.map(validateConfig);
    const invalidResults = invalidConfigs.map(validateConfig);
    
    const allValidPass = validResults.every(r => r === true);
    const allInvalidFail = invalidResults.every(r => r === false);
    
    console.log(`📊 Valid configs pass: ${allValidPass ? '✅' : '❌'}`);
    console.log(`📊 Invalid configs fail: ${allInvalidFail ? '✅' : '❌'}`);
    
    const result = allValidPass && allInvalidFail;
    console.log(`Result: ${result ? '✅ PASS' : '❌ FAIL'}`);
    
    return result;
}

// Test 5: Config merging
function test005_ConfigMerging() {
    console.log('\n🔍 Test 2.5: Config merging');
    
    const defaultConfig = {
        nodeWidth: 180,
        nodeHeight: 100,
        textRotation: 'horizontal',
        levelSettings: {}
    };
    
    const userConfig = {
        nodeWidth: 250,
        textRotation: 'vertical-top',
        levelSettings: {
            0: { width: 200 }
        }
    };
    
    const mergedConfig = { ...defaultConfig, ...userConfig };
    
    console.log(`📊 Default:`, defaultConfig);
    console.log(`📊 User:`, userConfig);
    console.log(`📊 Merged:`, mergedConfig);
    
    const isCorrect = mergedConfig.nodeWidth === 250 && 
                     mergedConfig.nodeHeight === 100 && 
                     mergedConfig.textRotation === 'vertical-top';
    
    console.log(`Result: ${isCorrect ? '✅ PASS' : '❌ FAIL'}`);
    
    return isCorrect;
}

// Run all config tests
async function runConfigTests() {
    console.log('🚀 Running all config system tests...\n');
    
    const dom = setupJSDOM();
    mockConfigFunctions();
    
    const results = [
        test001_BasicSaveLoad(),
        test002_LevelSettings(),
        test003_LocalStoragePersistence(), 
        test004_ConfigValidation(),
        test005_ConfigMerging()
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
        console.log('🎉 All config system tests PASSED!');
        return true;
    } else {
        console.log('⚠️ Some config system tests FAILED!');
        return false;
    }
}

// Export for main test runner
module.exports = { runConfigTests };

// Run if called directly
if (require.main === module) {
    runConfigTests().then(success => {
        process.exit(success ? 0 : 1);
    });
}
