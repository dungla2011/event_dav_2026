// 008-test-svg-export.js
// Test: SVG export functionality (Puppeteer DOM)

const puppeteer = require('puppeteer');

console.log('🧪 008 - SVG EXPORT TEST (Puppeteer DOM)');
console.log('========================================');

async function testSVGExport() {
    console.log('🚀 Starting SVG Export Test...');
    
    const browser = await puppeteer.launch({ 
        headless: false,
        slowMo: 100
    });
    
    const page = await browser.newPage();
    
    try {
        await page.goto('http://127.0.0.1:5500/doing.html');
        await page.waitForSelector('#familyTree');
        console.log('✅ Family tree loaded');
        
        // Test 1: Check SVG structure exists
        console.log('\n🔍 Test 8.1: SVG structure validation');
        
        const svgExists = await page.$('#familyTree');
        const nodesExist = await page.$$('.person-node');
        const linksExist = await page.$$('.link-line');
        
        console.log(`📊 SVG element exists: ${svgExists ? '✅' : '❌'}`);
        console.log(`📊 Nodes exist: ${nodesExist.length} found`);
        console.log(`📊 Links exist: ${linksExist.length} found`);
        
        // Test 2: Export function availability
        console.log('\n🔍 Test 8.2: Export function availability');
        
        const exportFunctionExists = await page.evaluate(() => {
            return typeof window.exportSVG === 'function';
        });
        
        console.log(`📊 Export function exists: ${exportFunctionExists ? '✅ PASS' : '❌ FAIL'}`);
        
        // Test 3: SVG content generation
        console.log('\n🔍 Test 8.3: SVG content generation');
        
        const svgContent = await page.evaluate(() => {
            const svg = document.getElementById('familyTree');
            if (!svg) return null;
            
            // Get SVG dimensions
            const rect = svg.getBoundingClientRect();
            const viewBox = svg.getAttribute('viewBox');
            
            return {
                width: svg.getAttribute('width'),
                height: svg.getAttribute('height'),
                viewBox: viewBox,
                rectWidth: rect.width,
                rectHeight: rect.height,
                childrenCount: svg.children.length
            };
        });
        
        console.log(`📊 SVG dimensions: ${svgContent?.width}x${svgContent?.height}`);
        console.log(`📊 ViewBox: ${svgContent?.viewBox || 'not set'}`);
        console.log(`📊 Children count: ${svgContent?.childrenCount || 0}`);
        
        const hasValidDimensions = svgContent && 
                                  parseInt(svgContent.width) > 0 && 
                                  parseInt(svgContent.height) > 0;
        
        console.log(`SVG content test: ${hasValidDimensions ? '✅ PASS' : '❌ FAIL'}`);
        
        // Test 4: Text rotation in export
        console.log('\n🔍 Test 8.4: Text rotation in export');
        
        // Set text rotation
        await page.select('#textRotation', 'vertical-top');
        await new Promise(resolve => setTimeout(resolve, 500));
        
        const textRotations = await page.$$eval('text.person-name', texts => {
            return texts.map(text => {
                const transform = text.getAttribute('transform');
                return {
                    text: text.textContent,
                    transform: transform,
                    hasRotation: transform && transform.includes('rotate')
                };
            });
        });
        
        const rotatedTexts = textRotations.filter(t => t.hasRotation);
        console.log(`📊 Rotated texts: ${rotatedTexts.length}/${textRotations.length}`);
        console.log(`Text rotation export test: ${rotatedTexts.length > 0 ? '✅ PASS' : '❌ FAIL'}`);
        
        // Test 5: Dynamic sizing in export
        console.log('\n🔍 Test 8.5: Dynamic sizing in export');
        
        // Change node dimensions
        await page.evaluate(() => {
            const widthSlider = document.getElementById('nodeWidth');
            const heightSlider = document.getElementById('nodeHeight');
            if (widthSlider) {
                widthSlider.value = 250;
                widthSlider.dispatchEvent(new Event('input', { bubbles: true }));
            }
            if (heightSlider) {
                heightSlider.value = 150;
                heightSlider.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });
        
        await new Promise(resolve => setTimeout(resolve, 800));
        
        const updatedDimensions = await page.$$eval('rect:not(.link-line)', rects => {
            return rects.map(rect => ({
                width: rect.getAttribute('width'),
                height: rect.getAttribute('height')
            }));
        });
        
        const hasUpdatedSizes = updatedDimensions.some(dim => 
            dim.width === '250' && dim.height === '150'
        );
        
        console.log(`📊 Updated node dimensions found: ${hasUpdatedSizes ? 'yes' : 'no'}`);
        console.log(`Dynamic sizing export test: ${hasUpdatedSizes ? '✅ PASS' : '❌ FAIL'}`);
        
        // Test 6: Export trigger simulation
        console.log('\n🔍 Test 8.6: Export trigger simulation');
        
        let exportTriggered = false;
        
        // Override download function to detect export
        await page.evaluate(() => {
            const originalCreateElement = document.createElement;
            document.createElement = function(tagName) {
                if (tagName.toLowerCase() === 'a') {
                    const element = originalCreateElement.call(this, tagName);
                    const originalClick = element.click;
                    element.click = function() {
                        window.exportTriggered = true;
                        console.log('Export download triggered');
                    };
                    return element;
                }
                return originalCreateElement.call(this, tagName);
            };
        });
        
        // Trigger export
        const exportButton = await page.$('#exportSVG');
        if (exportButton) {
            await page.click('#exportSVG');
            await new Promise(resolve => setTimeout(resolve, 500));
            
            exportTriggered = await page.evaluate(() => {
                return window.exportTriggered === true;
            });
        }
        
        console.log(`📊 Export triggered: ${exportTriggered ? 'yes' : 'no'}`);
        console.log(`Export trigger test: ${exportTriggered ? '✅ PASS' : '❌ FAIL'}`);
        
        console.log('\n🎉 SVG export tests completed!');
        
        await new Promise(resolve => setTimeout(resolve, 3000));
        
        return true;
        
    } catch (error) {
        console.error('❌ SVG export test error:', error);
        return false;
    } finally {
        await browser.close();
    }
}

// Export for main test runner
module.exports = { testSVGExport };

// Run if called directly
if (require.main === module) {
    testSVGExport().then(success => {
        process.exit(success ? 0 : 1);
    });
}
