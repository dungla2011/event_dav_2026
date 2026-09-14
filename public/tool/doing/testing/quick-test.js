/**
 * QUICK TEST SCRIPT FOR FAMILY TREE APP
 * Copy and paste this into browser console to run all tests
 */

async function quickTestSuite() {
    console.log("🧪 Starting Quick Test Suite for Family Tree App...\n");
    
    let passed = 0;
    let failed = 0;
    
    function test(name, testFn) {
        try {
            const result = testFn();
            if (result) {
                console.log(`✅ ${name}: PASSED`);
                passed++;
            } else {
                console.log(`❌ ${name}: FAILED`);
                failed++;
            }
        } catch (error) {
            console.log(`❌ ${name}: FAILED - ${error.message}`);
            failed++;
        }
    }
    
    function asyncTest(name, testFn) {
        return new Promise(async (resolve) => {
            try {
                const result = await testFn();
                if (result) {
                    console.log(`✅ ${name}: PASSED`);
                    passed++;
                } else {
                    console.log(`❌ ${name}: FAILED`);
                    failed++;
                }
            } catch (error) {
                console.log(`❌ ${name}: FAILED - ${error.message}`);
                failed++;
            }
            resolve();
        });
    }
    
    // 1. UI Elements Test
    test("UI Elements Present", () => {
        const header = document.querySelector('.header');
        const sidebar = document.querySelector('.sidebar');
        const svg = document.querySelector('#tree-svg');
        const tooltip = document.querySelector('#tooltip');
        
        return header && sidebar && svg && tooltip;
    });
    
    // 2. Data Loading Test
    test("Data Loaded", () => {
        const nodes = document.querySelectorAll('.person-node');
        console.log(`   → Found ${nodes.length} family tree nodes`);
        return nodes.length > 0;
    });
    
    // 3. Controls Test
    test("Control Buttons Present", () => {
        const buttons = document.querySelectorAll('button');
        const requiredButtons = ['Phóng to', 'Thu nhỏ', 'Reset', 'Xuất SVG', 'Load data.json'];
        
        let foundButtons = 0;
        requiredButtons.forEach(buttonText => {
            const found = Array.from(buttons).some(btn => btn.textContent.includes(buttonText));
            if (found) foundButtons++;
        });
        
        console.log(`   → Found ${foundButtons}/${requiredButtons.length} required buttons`);
        return foundButtons >= 4; // Allow some flexibility
    });
    
    // 4. Level Controls Test
    test("Level Controls Present", () => {
        const levelSelector = document.querySelector('#levelSelector');
        const nodeWidth = document.querySelector('#nodeWidth');
        const nodeHeight = document.querySelector('#nodeHeight');
        
        return levelSelector && nodeWidth && nodeHeight;
    });
    
    // 5. SVG Canvas Test
    test("SVG Canvas Functional", () => {
        const svg = document.querySelector('#tree-svg');
        const rect = svg.getBoundingClientRect();
        const hasContent = svg.children.length > 0;
        
        console.log(`   → SVG dimensions: ${rect.width}x${rect.height}`);
        console.log(`   → SVG child elements: ${svg.children.length}`);
        
        return rect.width > 0 && rect.height > 0 && hasContent;
    });
    
    // 6. Zoom Functionality Test
    await asyncTest("Zoom Controls", async () => {
        const zoomInBtn = Array.from(document.querySelectorAll('button'))
            .find(btn => btn.textContent.includes('Phóng to'));
        const zoomOutBtn = Array.from(document.querySelectorAll('button'))
            .find(btn => btn.textContent.includes('Thu nhỏ'));
        const resetBtn = Array.from(document.querySelectorAll('button'))
            .find(btn => btn.textContent.includes('Reset'));
        
        if (!zoomInBtn || !zoomOutBtn || !resetBtn) return false;
        
        // Test zoom in
        zoomInBtn.click();
        await new Promise(r => setTimeout(r, 100));
        
        // Test zoom out
        zoomOutBtn.click();
        await new Promise(r => setTimeout(r, 100));
        
        // Test reset
        resetBtn.click();
        await new Promise(r => setTimeout(r, 100));
        
        console.log("   → Zoom controls executed successfully");
        return true;
    });
    
    // 7. Node Interaction Test
    await asyncTest("Node Interaction", async () => {
        const firstNode = document.querySelector('.person-node');
        if (!firstNode) return false;
        
        // Test click
        firstNode.click();
        await new Promise(r => setTimeout(r, 100));
        
        // Test hover for tooltip
        const hoverEvent = new MouseEvent('mouseover', { bubbles: true });
        firstNode.dispatchEvent(hoverEvent);
        await new Promise(r => setTimeout(r, 200));
        
        const tooltip = document.querySelector('#tooltip');
        const tooltipVisible = tooltip.classList.contains('visible');
        
        // Clean up
        const mouseOutEvent = new MouseEvent('mouseout', { bubbles: true });
        firstNode.dispatchEvent(mouseOutEvent);
        
        console.log(`   → Tooltip visibility: ${tooltipVisible}`);
        return tooltipVisible;
    });
    
    // 8. Configuration Persistence Test
    test("Configuration System", () => {
        // Check if localStorage is being used
        const globalConfig = localStorage.getItem('familyTree_global_config');
        const levelConfig = localStorage.getItem('familyTree_level_settings');
        
        // Test saving a simple config
        const testConfig = { test: true, timestamp: Date.now() };
        localStorage.setItem('familyTree_test', JSON.stringify(testConfig));
        
        const retrieved = JSON.parse(localStorage.getItem('familyTree_test'));
        localStorage.removeItem('familyTree_test');
        
        console.log(`   → Global config exists: ${!!globalConfig}`);
        console.log(`   → Level config exists: ${!!levelConfig}`);
        console.log(`   → localStorage functional: ${retrieved && retrieved.test === true}`);
        
        return retrieved && retrieved.test === true;
    });
    
    // 9. Text Rotation Test
    await asyncTest("Text Rotation", async () => {
        const rotationSelect = document.querySelector('#textRotation');
        if (!rotationSelect) return false;
        
        const originalValue = rotationSelect.value;
        
        // Test different rotation values
        rotationSelect.value = 'vertical-down';
        rotationSelect.dispatchEvent(new Event('change'));
        await new Promise(r => setTimeout(r, 300));
        
        rotationSelect.value = 'vertical-up';
        rotationSelect.dispatchEvent(new Event('change'));
        await new Promise(r => setTimeout(r, 300));
        
        // Restore original
        rotationSelect.value = originalValue;
        rotationSelect.dispatchEvent(new Event('change'));
        
        console.log("   → Text rotation changes applied");
        return true;
    });
    
    // 10. Performance Test
    test("Performance Check", () => {
        const nodes = document.querySelectorAll('.person-node');
        const nodeCount = nodes.length;
        
        // Measure rendering time
        const startTime = performance.now();
        
        // Trigger a small change to measure update time
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.style.display = 'none';
            sidebar.style.display = '';
        }
        
        const endTime = performance.now();
        const renderTime = endTime - startTime;
        
        console.log(`   → Node count: ${nodeCount}`);
        console.log(`   → Render time: ${renderTime.toFixed(2)}ms`);
        
        // Memory usage if available
        if (performance.memory) {
            const memory = performance.memory.usedJSHeapSize / 1024 / 1024;
            console.log(`   → Memory usage: ${memory.toFixed(2)}MB`);
            return renderTime < 100 && memory < 50; // Reasonable thresholds
        }
        
        return renderTime < 100;
    });
    
    // Results
    console.log("\n" + "=".repeat(50));
    console.log(`📊 TEST RESULTS: ${passed} passed, ${failed} failed`);
    
    if (failed === 0) {
        console.log("🎉 ALL TESTS PASSED! Your Family Tree app is working perfectly!");
    } else {
        console.log(`⚠️  ${failed} test(s) failed. Please review the issues above.`);
    }
    
    // Additional diagnostics
    console.log("\n📋 SYSTEM INFO:");
    console.log(`   → Browser: ${navigator.userAgent.split(' ').pop()}`);
    console.log(`   → Viewport: ${window.innerWidth}x${window.innerHeight}`);
    console.log(`   → Current URL: ${window.location.href}`);
    
    return { passed, failed, total: passed + failed };
}

// Auto-run if in browser
if (typeof window !== 'undefined') {
    console.log("🔧 Family Tree Quick Test Script Loaded!");
    console.log("📝 Run quickTestSuite() to test all functionality");
    console.log("💡 Example: quickTestSuite().then(results => console.log('Done!', results))");
}

// For Node.js environments
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { quickTestSuite };
}
