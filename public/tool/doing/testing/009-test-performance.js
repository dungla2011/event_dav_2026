// 009-test-performance.js
// Test: Performance and stress testing (Puppeteer DOM)

const puppeteer = require('puppeteer');

console.log('🧪 009 - PERFORMANCE TEST (Puppeteer DOM)');
console.log('==========================================');

async function testPerformance() {
    console.log('🚀 Starting Performance Test...');
    
    const browser = await puppeteer.launch({ 
        headless: false,
        slowMo: 50
    });
    
    const page = await browser.newPage();
    
    try {
        // Test 1: Initial load performance
        console.log('\n🔍 Test 9.1: Initial load performance');
        
        const startTime = Date.now();
        await page.goto('http://127.0.0.1:5500/doing.html');
        await page.waitForSelector('#familyTree');
        const loadTime = Date.now() - startTime;
        
        console.log(`📊 Initial load time: ${loadTime}ms`);
        console.log(`Load performance: ${loadTime < 3000 ? '✅ PASS' : '❌ FAIL'} (< 3000ms)`);
        
        // Test 2: Rapid dimension changes
        console.log('\n🔍 Test 9.2: Rapid dimension changes');
        
        const rapidChangeStart = Date.now();
        
        for (let i = 0; i < 10; i++) {
            const width = 150 + (i * 10); // 150, 160, 170, ..., 240
            const height = 80 + (i * 5);  // 80, 85, 90, ..., 125
            
            await page.evaluate((w, h) => {
                const widthSlider = document.getElementById('nodeWidth');
                const heightSlider = document.getElementById('nodeHeight');
                if (widthSlider) {
                    widthSlider.value = w;
                    widthSlider.dispatchEvent(new Event('input', { bubbles: true }));
                }
                if (heightSlider) {
                    heightSlider.value = h;
                    heightSlider.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }, width, height);
            
            await new Promise(resolve => setTimeout(resolve, 50)); // Small delay
        }
        
        const rapidChangeTime = Date.now() - rapidChangeStart;
        console.log(`📊 Rapid changes time: ${rapidChangeTime}ms (10 changes)`);
        console.log(`Rapid changes performance: ${rapidChangeTime < 2000 ? '✅ PASS' : '❌ FAIL'} (< 2000ms)`);
        
        // Test 3: Level switching performance
        console.log('\n🔍 Test 9.3: Level switching performance');
        
        const levelSwitchStart = Date.now();
        
        const levels = await page.$$eval('#levelSelector option', options => 
            options.filter(opt => opt.value !== '-1').map(opt => opt.value)
        );
        
        for (const level of levels) {
            await page.select('#levelSelector', level);
            await new Promise(resolve => setTimeout(resolve, 100));
            
            // Make a small change
            await page.evaluate(() => {
                const levelWidth = document.getElementById('levelWidth');
                if (levelWidth) {
                    levelWidth.value = parseInt(levelWidth.value) + 5;
                    levelWidth.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
            
            await new Promise(resolve => setTimeout(resolve, 100));
        }
        
        const levelSwitchTime = Date.now() - levelSwitchStart;
        console.log(`📊 Level switching time: ${levelSwitchTime}ms (${levels.length} levels)`);
        console.log(`Level switching performance: ${levelSwitchTime < 3000 ? '✅ PASS' : '❌ FAIL'} (< 3000ms)`);
        
        // Test 4: Text rotation performance
        console.log('\n🔍 Test 9.4: Text rotation performance');
        
        const rotationStart = Date.now();
        
        const rotations = ['horizontal', 'vertical-top', 'vertical-bottom'];
        
        for (let i = 0; i < 5; i++) { // 5 cycles
            for (const rotation of rotations) {
                await page.select('#textRotation', rotation);
                await new Promise(resolve => setTimeout(resolve, 100));
            }
        }
        
        const rotationTime = Date.now() - rotationStart;
        console.log(`📊 Text rotation time: ${rotationTime}ms (15 changes)`);
        console.log(`Text rotation performance: ${rotationTime < 2000 ? '✅ PASS' : '❌ FAIL'} (< 2000ms)`);
        
        // Test 5: Memory usage estimation
        console.log('\n🔍 Test 9.5: Memory usage estimation');
        
        const memoryInfo = await page.evaluate(() => {
            if (performance.memory) {
                return {
                    usedJSHeapSize: performance.memory.usedJSHeapSize,
                    totalJSHeapSize: performance.memory.totalJSHeapSize,
                    jsHeapSizeLimit: performance.memory.jsHeapSizeLimit
                };
            }
            return null;
        });
        
        if (memoryInfo) {
            const usedMB = (memoryInfo.usedJSHeapSize / 1024 / 1024).toFixed(2);
            const totalMB = (memoryInfo.totalJSHeapSize / 1024 / 1024).toFixed(2);
            
            console.log(`📊 Used JS heap: ${usedMB} MB`);
            console.log(`📊 Total JS heap: ${totalMB} MB`);
            console.log(`Memory usage: ${usedMB < 50 ? '✅ PASS' : '❌ FAIL'} (< 50 MB)`);
        } else {
            console.log(`⚠️ Memory info not available in this browser`);
        }
        
        // Test 6: DOM node count
        console.log('\n🔍 Test 9.6: DOM node count');
        
        const domStats = await page.evaluate(() => {
            const allElements = document.querySelectorAll('*');
            const svgElements = document.querySelectorAll('svg *');
            const personNodes = document.querySelectorAll('.person-node');
            const textElements = document.querySelectorAll('text');
            
            return {
                totalElements: allElements.length,
                svgElements: svgElements.length,
                personNodes: personNodes.length,
                textElements: textElements.length
            };
        });
        
        console.log(`📊 Total DOM elements: ${domStats.totalElements}`);
        console.log(`📊 SVG elements: ${domStats.svgElements}`);
        console.log(`📊 Person nodes: ${domStats.personNodes}`);
        console.log(`📊 Text elements: ${domStats.textElements}`);
        
        const reasonableDOMSize = domStats.totalElements < 1000;
        console.log(`DOM size: ${reasonableDOMSize ? '✅ PASS' : '❌ FAIL'} (< 1000 elements)`);
        
        console.log('\n🎉 Performance tests completed!');
        
        await new Promise(resolve => setTimeout(resolve, 3000));
        
        return true;
        
    } catch (error) {
        console.error('❌ Performance test error:', error);
        return false;
    } finally {
        await browser.close();
    }
}

// Export for main test runner
module.exports = { testPerformance };

// Run if called directly
if (require.main === module) {
    testPerformance().then(success => {
        process.exit(success ? 0 : 1);
    });
}
