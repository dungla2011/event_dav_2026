const puppeteer = require('puppeteer');

async function testDimensionChanges() {
    console.log('🚀 Starting Puppeteer DOM test...');
    
    const browser = await puppeteer.launch({ 
        headless: false, // Set to true for headless mode
        slowMo: 100 // Slow down for visibility
    });
    
    const page = await browser.newPage();
    
    try {
        // Navigate to the app
        await page.goto('http://127.0.0.1:5500/doing.html');
        console.log('📱 Navigated to app');
        
        // Wait for elements to load
        await page.waitForSelector('#nodeWidth');
        await page.waitForSelector('rect:not(.link-line)');
        console.log('✅ Elements loaded');
        
        // Get initial state
        const initialWidth = await page.$eval('rect:not(.link-line)', el => el.getAttribute('width'));
        const initialSliderValue = await page.$eval('#nodeWidth', el => el.value);
        
        console.log(`📊 Initial state: rect=${initialWidth}, slider=${initialSliderValue}`);
        
        // Test 1: Change width to 250
        console.log('🧪 Test 1: Changing width to 250...');
        await page.evaluate(() => {
            const slider = document.getElementById('nodeWidth');
            slider.value = '250';
            slider.dispatchEvent(new Event('input', { bubbles: true }));
        });
        
        // Wait for update
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Check result
        const newWidth = await page.$eval('rect:not(.link-line)', el => el.getAttribute('width'));
        const newSliderValue = await page.$eval('#nodeWidth', el => el.value);
        
        console.log(`📊 After test 1: rect=${newWidth}, slider=${newSliderValue}`);
        console.log(`Result: ${newWidth === '250' ? '✅ PASS' : '❌ FAIL'}`);
        
        // Test 2: Change height to 150
        console.log('🧪 Test 2: Changing height to 150...');
        await page.evaluate(() => {
            const slider = document.getElementById('nodeHeight');
            slider.value = '150';
            slider.dispatchEvent(new Event('input', { bubbles: true }));
        });
        
        await new Promise(resolve => setTimeout(resolve, 500));
        
        const newHeight = await page.$eval('rect:not(.link-line)', el => el.getAttribute('height'));
        const newHeightSliderValue = await page.$eval('#nodeHeight', el => el.value);
        
        console.log(`📊 After test 2: rect=${newHeight}, slider=${newHeightSliderValue}`);
        console.log(`Result: ${newHeight === '150' ? '✅ PASS' : '❌ FAIL'}`);
        
        // Test 3: Check multiple nodes
        console.log('🧪 Test 3: Checking all nodes...');
        const allWidths = await page.$$eval('rect:not(.link-line)', rects => 
            rects.map(rect => rect.getAttribute('width'))
        );
        
        console.log(`📊 All rect widths: ${allWidths.join(', ')}`);
        
        // Test 4: Check localStorage persistence
        console.log('🧪 Test 4: Checking localStorage...');
        const savedConfig = await page.evaluate(() => {
            const config = localStorage.getItem('familyTree_globalConfig');
            return config ? JSON.parse(config) : null;
        });
        
        console.log(`💾 Saved config: nodeWidth=${savedConfig?.nodeWidth}, nodeHeight=${savedConfig?.nodeHeight}`);
        
        console.log('🎉 All tests completed!');
        
        // Keep browser open for manual inspection
        console.log('🔍 Browser will stay open for 10 seconds for manual inspection...');
        await new Promise(resolve => setTimeout(resolve, 10000));
        
    } catch (error) {
        console.error('❌ Test error:', error);
    } finally {
        await browser.close();
    }
}

// Run the test
testDimensionChanges().catch(console.error);
