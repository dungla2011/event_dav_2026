// tooltip-test.js
// Quick test for tooltip functionality

const puppeteer = require('puppeteer');

async function testTooltip() {
    console.log('🧪 Testing Tooltip Functionality...');
    
    const browser = await puppeteer.launch({ 
        headless: false,
        slowMo: 200
    });
    
    const page = await browser.newPage();
    
    try {
        await page.goto('http://127.0.0.1:5500/doing.html');
        await page.waitForSelector('#tree-svg');
        console.log('✅ Page loaded');
        
        // Wait for nodes to appear
        await page.waitForSelector('.person-node', { timeout: 5000 });
        console.log('✅ Nodes detected');
        
        // Test tooltip visibility
        console.log('\n🔍 Testing tooltip on hover...');
        
        // Hover over first node
        await page.hover('.person-node');
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Check if tooltip is visible
        const tooltipVisible = await page.evaluate(() => {
            const tooltip = document.getElementById('tooltip');
            return tooltip && tooltip.classList.contains('visible');
        });
        
        console.log(`📊 Tooltip visible on hover: ${tooltipVisible ? '✅ YES' : '❌ NO'}`);
        
        // Get tooltip content
        const tooltipContent = await page.evaluate(() => {
            const header = document.getElementById('tooltip-header');
            const content = document.getElementById('tooltip-content');
            return {
                header: header ? header.textContent : null,
                content: content ? content.innerHTML : null
            };
        });
        
        console.log(`📊 Tooltip header: "${tooltipContent.header}"`);
        console.log(`📊 Tooltip has content: ${tooltipContent.content ? '✅ YES' : '❌ NO'}`);
        
        // Move mouse away to test hide
        await page.mouse.move(100, 100);
        await new Promise(resolve => setTimeout(resolve, 500));
        
        const tooltipHidden = await page.evaluate(() => {
            const tooltip = document.getElementById('tooltip');
            return tooltip && !tooltip.classList.contains('visible');
        });
        
        console.log(`📊 Tooltip hidden on mouse out: ${tooltipHidden ? '✅ YES' : '❌ NO'}`);
        
        // Test multiple nodes
        console.log('\n🔍 Testing multiple nodes...');
        
        const nodeCount = await page.$$eval('.person-node', nodes => nodes.length);
        console.log(`📊 Found ${nodeCount} nodes to test`);
        
        for (let i = 0; i < Math.min(nodeCount, 3); i++) {
            console.log(`\n🎯 Testing node ${i + 1}...`);
            
            await page.evaluate((index) => {
                const nodes = document.querySelectorAll('.person-node');
                if (nodes[index]) {
                    // Trigger mouseover event
                    const event = new MouseEvent('mouseover', { bubbles: true });
                    nodes[index].dispatchEvent(event);
                }
            }, i);
            
            await new Promise(resolve => setTimeout(resolve, 300));
            
            const nodeTooltipData = await page.evaluate(() => {
                const tooltip = document.getElementById('tooltip');
                const header = document.getElementById('tooltip-header');
                return {
                    visible: tooltip && tooltip.classList.contains('visible'),
                    name: header ? header.textContent : null
                };
            });
            
            console.log(`  Name: "${nodeTooltipData.name}"`);
            console.log(`  Visible: ${nodeTooltipData.visible ? '✅' : '❌'}`);
        }
        
        // Summary
        console.log('\n📋 TOOLTIP TEST SUMMARY:');
        console.log(`✅ Tooltip shows on hover: ${tooltipVisible ? 'PASS' : 'FAIL'}`);
        console.log(`✅ Tooltip hides on mouse out: ${tooltipHidden ? 'PASS' : 'FAIL'}`);
        console.log(`✅ Content populated: ${tooltipContent.content ? 'PASS' : 'FAIL'}`);
        
        const overallSuccess = tooltipVisible && tooltipHidden && tooltipContent.content;
        console.log(`\n🎉 Overall tooltip test: ${overallSuccess ? '✅ PASS' : '❌ FAIL'}`);
        
        // Keep open for manual inspection
        console.log('\n🔍 Browser stays open for 10 seconds for manual testing...');
        await new Promise(resolve => setTimeout(resolve, 10000));
        
        return overallSuccess;
        
    } catch (error) {
        console.error('❌ Tooltip test error:', error);
        return false;
    } finally {
        await browser.close();
    }
}

// Run test
testTooltip().then(success => {
    console.log(`\nTooltip test ${success ? 'completed successfully' : 'failed'}`);
    process.exit(success ? 0 : 1);
});
