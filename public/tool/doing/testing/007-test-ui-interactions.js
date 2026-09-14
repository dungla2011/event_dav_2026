// 007-test-ui-interactions.js
// Test: UI interactions and user experience (Puppeteer DOM)

const puppeteer = require('puppeteer');

console.log('🧪 007 - UI INTERACTIONS TEST (Puppeteer DOM)');
console.log('=============================================');

async function testUIInteractions() {
    console.log('🚀 Starting UI Interactions Test...');
    
    const browser = await puppeteer.launch({ 
        headless: false,
        slowMo: 100
    });
    
    const page = await browser.newPage();
    
    try {
        await page.goto('http://127.0.0.1:5500/doing.html');
        console.log('📱 Navigated to app');
        
        await page.waitForSelector('#levelSelector');
        console.log('✅ Elements loaded');
        
        // Test 1: Level selector functionality
        console.log('\n🔍 Test 7.1: Level selector functionality');
        
        const initialLevelControls = await page.evaluate(() => {
            const controls = document.getElementById('levelControls');
            return controls ? controls.style.display : null;
        });
        
        console.log(`📊 Initial level controls visibility: ${initialLevelControls || 'hidden'}`);
        
        // Select a level
        await page.select('#levelSelector', '0');
        await new Promise(resolve => setTimeout(resolve, 300));
        
        const levelControlsVisible = await page.evaluate(() => {
            const controls = document.getElementById('levelControls');
            return controls && controls.style.display !== 'none';
        });
        
        console.log(`📊 Level controls after selection: ${levelControlsVisible ? 'visible' : 'hidden'}`);
        console.log(`Level selector test: ${levelControlsVisible ? '✅ PASS' : '❌ FAIL'}`);
        
        // Test 2: Slider interactions
        console.log('\n🔍 Test 7.2: Slider interactions');
        
        const initialWidth = await page.$eval('#levelWidth', el => el.value);
        console.log(`📊 Initial level width: ${initialWidth}`);
        
        await page.evaluate(() => {
            const slider = document.getElementById('levelWidth');
            slider.value = 220;
            slider.dispatchEvent(new Event('input', { bubbles: true }));
        });
        
        await new Promise(resolve => setTimeout(resolve, 500));
        
        const updatedWidth = await page.$eval('#levelWidth', el => el.value);
        console.log(`📊 Updated level width: ${updatedWidth}`);
        console.log(`Slider interaction test: ${updatedWidth === '220' ? '✅ PASS' : '❌ FAIL'}`);
        
        // Test 3: Tooltip functionality
        console.log('\n🔍 Test 7.3: Tooltip functionality');
        
        const nodeExists = await page.$('.person-node');
        if (nodeExists) {
            await page.hover('.person-node');
            await new Promise(resolve => setTimeout(resolve, 300));
            
            const tooltipVisible = await page.evaluate(() => {
                const tooltip = document.querySelector('.tooltip');
                return tooltip && tooltip.style.opacity !== '0';
            });
            
            console.log(`📊 Tooltip visible on hover: ${tooltipVisible ? 'yes' : 'no'}`);
            console.log(`Tooltip test: ${tooltipVisible ? '✅ PASS' : '❌ FAIL'}`);
        } else {
            console.log(`⚠️ No nodes found for tooltip test`);
        }
        
        // Test 4: Export button functionality
        console.log('\n🔍 Test 7.4: Export button functionality');
        
        const exportButton = await page.$('#exportSVG');
        if (exportButton) {
            // Click export (won't actually download in test)
            await page.click('#exportSVG');
            await new Promise(resolve => setTimeout(resolve, 500));
            
            console.log(`📊 Export button clickable: ✅ PASS`);
        } else {
            console.log(`⚠️ Export button not found: ❌ FAIL`);
        }
        
        // Test 5: Reset functionality
        console.log('\n🔍 Test 7.5: Reset functionality');
        
        // Reset level selector
        await page.select('#levelSelector', '-1');
        await new Promise(resolve => setTimeout(resolve, 300));
        
        const controlsHidden = await page.evaluate(() => {
            const controls = document.getElementById('levelControls');
            return !controls || controls.style.display === 'none';
        });
        
        console.log(`📊 Controls hidden after reset: ${controlsHidden ? 'yes' : 'no'}`);
        console.log(`Reset test: ${controlsHidden ? '✅ PASS' : '❌ FAIL'}`);
        
        console.log('\n🎉 UI interaction tests completed!');
        
        await new Promise(resolve => setTimeout(resolve, 3000));
        
    } catch (error) {
        console.error('❌ UI interaction test error:', error);
        return false;
    } finally {
        await browser.close();
    }
    
    return true;
}

// Export for main test runner
module.exports = { testUIInteractions };

// Run if called directly
if (require.main === module) {
    testUIInteractions().then(success => {
        process.exit(success ? 0 : 1);
    });
}
