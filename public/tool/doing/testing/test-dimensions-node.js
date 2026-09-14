const { JSDOM } = require('jsdom');
const fs = require('fs');
const path = require('path');

async function testDimensionChanges() {
    console.log('🔧 Starting Node.js Dimension Test...');
    
    try {
        // Read the HTML file
        const htmlPath = path.join(__dirname, 'doing.html');
        const htmlContent = fs.readFileSync(htmlPath, 'utf8');
        
        // Create JSDOM environment
        const dom = new JSDOM(htmlContent, {
            url: 'http://localhost/',
            pretendToBeVisual: true,
            resources: 'usable',
            runScripts: 'dangerously'
        });
        
        const { window } = dom;
        const { document } = window;
        
        // Wait for DOM to be ready
        await new Promise(resolve => {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', resolve);
            } else {
                resolve();
            }
        });
        
        // Wait a bit more for scripts to initialize
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        console.log('✅ DOM loaded, testing dimension changes...');
        
        // Test 1: Check if elements exist
        const widthSlider = document.getElementById('nodeWidth');
        const heightSlider = document.getElementById('nodeHeight');
        const firstRect = document.querySelector('.person-rect');
        
        console.log('🔍 Elements found:');
        console.log('  - widthSlider:', !!widthSlider);
        console.log('  - heightSlider:', !!heightSlider);
        console.log('  - firstRect:', !!firstRect);
        
        if (!widthSlider || !heightSlider) {
            console.log('❌ Required sliders not found');
            return;
        }
        
        // Test 2: Check initial values
        console.log('🔍 Initial values:');
        console.log('  - widthSlider.value:', widthSlider.value);
        console.log('  - heightSlider.value:', heightSlider.value);
        console.log('  - window.defaultNodeWidth:', window.defaultNodeWidth);
        console.log('  - window.defaultNodeHeight:', window.defaultNodeHeight);
        
        if (firstRect) {
            console.log('  - firstRect.width:', firstRect.getAttribute('width'));
            console.log('  - firstRect.height:', firstRect.getAttribute('height'));
        }
        
        // Test 3: Change dimensions
        const originalWidth = parseInt(widthSlider.value) || 180;
        const newWidth = originalWidth + 50;
        
        console.log(`🧪 Testing width change: ${originalWidth} → ${newWidth}`);
        
        // Set slider value
        widthSlider.value = newWidth;
        console.log('  - Slider value set to:', widthSlider.value);
        
        // Trigger input event
        const inputEvent = new window.Event('input', { bubbles: true });
        widthSlider.dispatchEvent(inputEvent);
        console.log('  - Input event dispatched');
        
        // Check if global variable was updated
        console.log('  - window.defaultNodeWidth after event:', window.defaultNodeWidth);
        
        // Call updateDisplay if available
        if (typeof window.updateDisplay === 'function') {
            console.log('  - Calling updateDisplay()...');
            window.updateDisplay();
            
            // Check rect after update
            if (firstRect) {
                const newRectWidth = firstRect.getAttribute('width');
                console.log('  - firstRect.width after updateDisplay:', newRectWidth);
                
                const success = Math.abs(parseFloat(newRectWidth) - newWidth) < 10;
                console.log(`  - Test result: ${success ? '✅ PASS' : '❌ FAIL'}`);
            }
        } else {
            console.log('  - updateDisplay function not available');
        }
        
        // Test 4: Check getLevelSettings
        if (typeof window.getLevelSettings === 'function') {
            console.log('🔍 Testing getLevelSettings:');
            const level0Settings = window.getLevelSettings(0);
            console.log('  - Level 0 settings:', {
                width: level0Settings.width,
                height: level0Settings.height
            });
            
            const level1Settings = window.getLevelSettings(1);
            console.log('  - Level 1 settings:', {
                width: level1Settings.width,
                height: level1Settings.height
            });
        }
        
    } catch (error) {
        console.error('❌ Test error:', error.message);
        console.error(error.stack);
    }
}

// Run the test
testDimensionChanges().then(() => {
    console.log('🏁 Test completed');
}).catch(error => {
    console.error('💥 Test failed:', error);
});
