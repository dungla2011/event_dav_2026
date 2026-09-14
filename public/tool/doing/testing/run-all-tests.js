// run-all-tests.js
// Main Test Runner - Automatically runs all numbered tests

const path = require('path');
const { spawn } = require('child_process');

console.log('🚀 FAMILY TREE - AUTOMATED TEST SUITE');
console.log('=====================================');
console.log('Running comprehensive test suite from 001 to 010...\n');

// Test configuration
const tests = [
    {
        number: '001',
        name: 'Data Structure Test',
        file: '001-test-data-structure.js',
        type: 'No DOM',
        description: 'Core data structures and algorithms'
    },
    {
        number: '002', 
        name: 'Config System Test',
        file: '002-test-config-system.js',
        type: 'JSDOM',
        description: 'Configuration and localStorage functionality'
    },
    {
        number: '003',
        name: 'Core Functions Test',
        file: '003-test-core-functions.js',
        type: 'JSDOM + Mock D3',
        description: 'Utility functions and calculations'
    },
    {
        number: '004',
        name: 'Dimension Changes Test',
        file: '004-test-dimension-changes.js',
        type: 'Puppeteer DOM',
        description: 'Global and per-level dimension controls'
    },
    {
        number: '005',
        name: 'Text Rotation Test',
        file: '005-test-text-rotation.js',
        type: 'Puppeteer DOM', 
        description: 'Text rotation for global and per-level'
    },
    {
        number: '006',
        name: 'Per-Level Features Test',
        file: '006-test-per-level-features.js',
        type: 'Puppeteer DOM',
        description: 'Per-level dimension and rotation features'
    },
    {
        number: '007',
        name: 'UI Interactions Test',
        file: '007-test-ui-interactions.js',
        type: 'Puppeteer DOM',
        description: 'User interface interactions and UX'
    },
    {
        number: '008',
        name: 'SVG Export Test',
        file: '008-test-svg-export.js',
        type: 'Puppeteer DOM',
        description: 'SVG export functionality and content'
    },
    {
        number: '009',
        name: 'Performance Test',
        file: '009-test-performance.js',
        type: 'Puppeteer DOM',
        description: 'Performance and stress testing'
    },
    {
        number: '010',
        name: 'Full Integration Test',
        file: '010-test-full-integration.js',
        type: 'Puppeteer DOM',
        description: 'Complete workflow integration testing'
    }
];

// Track results
const results = {
    total: tests.length,
    passed: 0,
    failed: 0,
    details: []
};

// Run a single test
function runTest(test) {
    return new Promise((resolve, reject) => {
        console.log(`\n🧪 Running ${test.number} - ${test.name} (${test.type})`);
        console.log(`📋 ${test.description}`);
        console.log(`📄 File: ${test.file}`);
        console.log('─'.repeat(60));
        
        const startTime = Date.now();
        const child = spawn('node', [test.file], {
            stdio: 'inherit',
            cwd: __dirname
        });
        
        child.on('close', (code) => {
            const endTime = Date.now();
            const duration = endTime - startTime;
            
            const result = {
                test: test,
                success: code === 0,
                duration: duration,
                code: code
            };
            
            console.log(`\n⏱️  Duration: ${duration}ms`);
            
            if (code === 0) {
                console.log(`✅ ${test.number} - ${test.name} PASSED`);
                results.passed++;
            } else {
                console.log(`❌ ${test.number} - ${test.name} FAILED (exit code: ${code})`);
                results.failed++;
            }
            
            results.details.push(result);
            resolve(result);
        });
        
        child.on('error', (error) => {
            console.error(`❌ Error running ${test.number}: ${error.message}`);
            results.failed++;
            results.details.push({
                test: test,
                success: false,
                duration: 0,
                error: error.message
            });
            resolve({ success: false, error: error.message });
        });
    });
}

// Check if Live Server is running (for Puppeteer tests)
async function checkLiveServer() {
    try {
        const http = require('http');
        return new Promise((resolve) => {
            const req = http.request({
                hostname: '127.0.0.1',
                port: 5500,
                path: '/doing.html',
                method: 'HEAD',
                timeout: 2000
            }, (res) => {
                resolve(res.statusCode === 200);
            });
            
            req.on('error', () => resolve(false));
            req.on('timeout', () => {
                req.destroy();
                resolve(false);
            });
            req.end();
        });
    } catch (error) {
        return false;
    }
}

// Main test runner
async function runAllTests() {
    console.log('🔍 Pre-flight checks...');
    
    // Check if Live Server is needed and running
    const hasPuppeteerTests = tests.some(t => t.type.includes('Puppeteer'));
    
    if (hasPuppeteerTests) {
        const liveServerRunning = await checkLiveServer();
        
        if (!liveServerRunning) {
            console.log('⚠️  WARNING: Live Server not detected on http://127.0.0.1:5500');
            console.log('   Puppeteer tests (004-010) may fail.');
            console.log('   Please start Live Server and try again.');
            console.log('   Continuing with available tests...\n');
        } else {
            console.log('✅ Live Server detected on http://127.0.0.1:5500');
        }
    }
    
    const startTime = Date.now();
    
    // Run tests sequentially to avoid conflicts
    for (const test of tests) {
        // Skip Puppeteer tests if Live Server not available
        if (test.type.includes('Puppeteer')) {
            const serverAvailable = await checkLiveServer();
            if (!serverAvailable) {
                console.log(`\n⏭️  Skipping ${test.number} - ${test.name} (Live Server not available)`);
                results.details.push({
                    test: test,
                    success: false,
                    skipped: true,
                    reason: 'Live Server not available'
                });
                continue;
            }
        }
        
        await runTest(test);
        
        // Brief pause between tests
        await new Promise(resolve => setTimeout(resolve, 1000));
    }
    
    const totalTime = Date.now() - startTime;
    
    // Final summary
    console.log('\n' + '='.repeat(80));
    console.log('🎯 FINAL TEST SUMMARY');
    console.log('='.repeat(80));
    
    results.details.forEach((result, index) => {
        const status = result.skipped ? '⏭️  SKIPPED' : 
                      result.success ? '✅ PASSED' : '❌ FAILED';
        const duration = result.skipped ? '' : ` (${result.duration}ms)`;
        const reason = result.skipped ? ` - ${result.reason}` : '';
        
        console.log(`${result.test.number}. ${result.test.name}: ${status}${duration}${reason}`);
    });
    
    console.log('\n📊 STATISTICS:');
    console.log(`✅ Passed: ${results.passed}/${results.total}`);
    console.log(`❌ Failed: ${results.failed}/${results.total}`);
    const skipped = results.details.filter(r => r.skipped).length;
    if (skipped > 0) {
        console.log(`⏭️  Skipped: ${skipped}/${results.total}`);
    }
    console.log(`⏱️  Total time: ${(totalTime / 1000).toFixed(1)}s`);
    console.log(`📈 Success rate: ${((results.passed / results.total) * 100).toFixed(1)}%`);
    
    // Performance summary
    console.log('\n⚡ PERFORMANCE BREAKDOWN:');
    results.details.filter(r => !r.skipped).forEach(result => {
        const timeStr = `${(result.duration / 1000).toFixed(1)}s`;
        console.log(`  ${result.test.number} (${result.test.type}): ${timeStr}`);
    });
    
    if (results.passed === results.total) {
        console.log('\n🎉 ALL TESTS PASSED! The family tree app is working perfectly! 🎉');
        process.exit(0);
    } else {
        console.log('\n⚠️  Some tests failed. Please check the logs above for details.');
        process.exit(1);
    }
}

// Handle command line arguments
const args = process.argv.slice(2);

if (args.length > 0) {
    // Run specific test by number
    const testNumber = args[0];
    const specificTest = tests.find(t => t.number === testNumber);
    
    if (specificTest) {
        console.log(`🎯 Running specific test: ${testNumber}\n`);
        runTest(specificTest).then(() => process.exit(0));
    } else {
        console.log(`❌ Test ${testNumber} not found. Available tests: ${tests.map(t => t.number).join(', ')}`);
        process.exit(1);
    }
} else {
    // Run all tests
    runAllTests();
}

// Export for programmatic use
module.exports = { runAllTests, runTest, tests };
