const { Builder, By, until } = require('selenium-webdriver');

async function testWithSelenium() {
    console.log('🚀 Starting Selenium DOM test...');
    
    const driver = await new Builder().forBrowser('chrome').build();
    
    try {
        // Navigate to app
        await driver.get('http://127.0.0.1:5500/doing.html');
        console.log('📱 Navigated to app');
        
        // Wait for elements
        await driver.wait(until.elementLocated(By.id('nodeWidth')), 5000);
        await driver.wait(until.elementLocated(By.css('rect:not(.link-line)')), 5000);
        console.log('✅ Elements located');
        
        // Get elements
        const widthSlider = await driver.findElement(By.id('nodeWidth'));
        const firstRect = await driver.findElement(By.css('rect:not(.link-line)'));
        
        // Get initial state
        const initialWidth = await firstRect.getAttribute('width');
        const initialSliderValue = await widthSlider.getAttribute('value');
        
        console.log(`📊 Initial: rect=${initialWidth}, slider=${initialSliderValue}`);
        
        // Test width change
        console.log('🧪 Testing width change to 275...');
        
        await widthSlider.clear();
        await widthSlider.sendKeys('275');
        
        // Trigger input event
        await driver.executeScript(`
            const slider = document.getElementById('nodeWidth');
            slider.dispatchEvent(new Event('input', { bubbles: true }));
        `);
        
        // Wait for update
        await driver.sleep(500);
        
        // Check results
        const newWidth = await firstRect.getAttribute('width');
        const newSliderValue = await widthSlider.getAttribute('value');
        
        console.log(`📊 After change: rect=${newWidth}, slider=${newSliderValue}`);
        console.log(`Width test: ${newWidth === '275' ? '✅ PASS' : '❌ FAIL'}`);
        
        // Test height change
        console.log('🧪 Testing height change to 125...');
        
        const heightSlider = await driver.findElement(By.id('nodeHeight'));
        await heightSlider.clear();
        await heightSlider.sendKeys('125');
        
        await driver.executeScript(`
            const slider = document.getElementById('nodeHeight');
            slider.dispatchEvent(new Event('input', { bubbles: true }));
        `);
        
        await driver.sleep(500);
        
        const newHeight = await firstRect.getAttribute('height');
        console.log(`📊 New height: ${newHeight}`);
        console.log(`Height test: ${newHeight === '125' ? '✅ PASS' : '❌ FAIL'}`);
        
        // Check global variables
        const globalWidth = await driver.executeScript('return window.defaultNodeWidth;');
        const globalHeight = await driver.executeScript('return window.defaultNodeHeight;');
        
        console.log(`🌐 Global variables: width=${globalWidth}, height=${globalHeight}`);
        
        console.log('🎉 Selenium tests completed!');
        
        // Keep open for inspection
        await driver.sleep(5000);
        
    } catch (error) {
        console.error('❌ Selenium test error:', error);
    } finally {
        await driver.quit();
    }
}

module.exports = testWithSelenium;

// Run if called directly
if (require.main === module) {
    testWithSelenium().catch(console.error);
}
