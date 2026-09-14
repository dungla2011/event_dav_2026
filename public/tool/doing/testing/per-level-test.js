const puppeteer = require('puppeteer');

async function testPerLevelDimensions() {
    console.log('🚀 Starting Per-Level Dimension Test...');
    
    const browser = await puppeteer.launch({ 
        headless: false,
        slowMo: 100
    });
    
    const page = await browser.newPage();
    
    try {
        // Navigate to the app
        await page.goto('http://127.0.0.1:5500/doing.html');
        console.log('📱 Navigated to app');
        
        // Wait for elements to load
        await page.waitForSelector('#levelSelector');
        await page.waitForSelector('rect:not(.link-line)');
        console.log('✅ Elements loaded');
        
        // Get all levels available
        const levels = await page.$$eval('#levelSelector option', options => 
            options.filter(opt => opt.value !== '-1').map(opt => ({
                value: opt.value,
                text: opt.textContent
            }))
        );
        
        console.log(`📊 Found ${levels.length} levels: ${levels.map(l => l.text).join(', ')}`);
        
        // Limit to max 3 levels as requested
        const testLevels = levels.slice(0, 3);
        console.log(`🧪 Testing first ${testLevels.length} levels only`);
        
        // Test each level
        for (let i = 0; i < testLevels.length; i++) {
            const level = testLevels[i];
            const levelIndex = parseInt(level.value);
            
            console.log(`\n🧪 Testing Level ${levelIndex + 1} (${level.text})...`);
            
            // Select the level
            await page.select('#levelSelector', level.value);
            await new Promise(resolve => setTimeout(resolve, 300));
            
            // Check if level controls are visible
            const levelControlsVisible = await page.evaluate(() => {
                const levelControls = document.getElementById('levelControls');
                return levelControls && levelControls.style.display !== 'none';
            });
            
            if (!levelControlsVisible) {
                console.log(`⚠️ Level controls not visible for level ${levelIndex + 1}`);
                continue;
            }
            
            // Get nodes for this level
            const levelNodes = await page.$$eval('.person-node', nodes => {
                return nodes.map((node, idx) => {
                    const rect = node.querySelector('rect');
                    return {
                        index: idx,
                        width: rect ? rect.getAttribute('width') : null,
                        height: rect ? rect.getAttribute('height') : null,
                        transform: node.getAttribute('transform')
                    };
                });
            });
            
            console.log(`📊 Level ${levelIndex + 1} has ${levelNodes.length} total nodes`);
            
            // Get initial dimensions for this level
            const initialLevelSettings = await page.evaluate((level) => {
                return window.getLevelSettings ? window.getLevelSettings(level) : null;
            }, levelIndex);
            
            if (initialLevelSettings) {
                console.log(`📊 Initial Level ${levelIndex + 1} settings: ${initialLevelSettings.width}x${initialLevelSettings.height}`);
            }
            
            // Test level-specific width change
            const testWidth = 200 + (i * 30); // Different width for each level
            console.log(`🔧 Setting Level ${levelIndex + 1} width to ${testWidth}...`);
            
            // Check if level width control exists
            const levelWidthControl = await page.$('#levelWidth');
            if (levelWidthControl) {
                await page.evaluate((width) => {
                    const slider = document.getElementById('levelWidth');
                    if (slider) {
                        slider.value = width;
                        slider.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }, testWidth);
                
                await new Promise(resolve => setTimeout(resolve, 500));
                
                // Check if level settings updated
                const updatedLevelSettings = await page.evaluate((level) => {
                    return window.getLevelSettings ? window.getLevelSettings(level) : null;
                }, levelIndex);
                
                if (updatedLevelSettings) {
                    console.log(`📊 Updated Level ${levelIndex + 1} settings: ${updatedLevelSettings.width}x${updatedLevelSettings.height}`);
                    console.log(`Result: ${updatedLevelSettings.width === testWidth ? '✅ PASS' : '❌ FAIL'}`);
                } else {
                    console.log(`⚠️ Could not get updated settings for level ${levelIndex + 1}`);
                }
                
                // Check specific nodes for this level
                const updatedNodes = await page.$$eval('.person-node', nodes => {
                    return nodes.map((node, idx) => {
                        const rect = node.querySelector('rect');
                        return {
                            index: idx,
                            width: rect ? rect.getAttribute('width') : null,
                            height: rect ? rect.getAttribute('height') : null
                        };
                    });
                });
                
                // Show first few nodes of each level for verification
                console.log(`📊 Node dimensions after level ${levelIndex + 1} change:`);
                updatedNodes.slice(0, 3).forEach((node, idx) => {
                    console.log(`  Node ${idx + 1}: ${node.width}x${node.height}`);
                });
                
            } else {
                console.log(`⚠️ No level width control found for level ${levelIndex + 1}`);
            }
            
            // Test level-specific height change
            const testHeight = 120 + (i * 20); // Different height for each level
            console.log(`🔧 Setting Level ${levelIndex + 1} height to ${testHeight}...`);
            
            const levelHeightControl = await page.$('#levelHeight');
            if (levelHeightControl) {
                await page.evaluate((height) => {
                    const slider = document.getElementById('levelHeight');
                    if (slider) {
                        slider.value = height;
                        slider.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }, testHeight);
                
                await new Promise(resolve => setTimeout(resolve, 500));
                
                const finalLevelSettings = await page.evaluate((level) => {
                    return window.getLevelSettings ? window.getLevelSettings(level) : null;
                }, levelIndex);
                
                if (finalLevelSettings) {
                    console.log(`📊 Final Level ${levelIndex + 1} settings: ${finalLevelSettings.width}x${finalLevelSettings.height}`);
                    console.log(`Height result: ${finalLevelSettings.height === testHeight ? '✅ PASS' : '❌ FAIL'}`);
                }
            } else {
                console.log(`⚠️ No level height control found for level ${levelIndex + 1}`);
            }
        }
        
        // Summary: Check all levels have different dimensions
        console.log(`\n📋 SUMMARY - Level Dimension Differences:`);
        for (const level of testLevels) {
            const levelIndex = parseInt(level.value);
            const settings = await page.evaluate((level) => {
                return window.getLevelSettings ? window.getLevelSettings(level) : null;
            }, levelIndex);
            
            if (settings) {
                console.log(`Level ${levelIndex + 1}: ${settings.width}x${settings.height}`);
            }
        }
        
        // Test level selector reset
        console.log(`\n🔄 Testing level selector reset...`);
        await page.select('#levelSelector', '-1'); // Reset to "-- Chọn hàng --"
        await new Promise(resolve => setTimeout(resolve, 300));
        
        const controlsHidden = await page.evaluate(() => {
            const levelControls = document.getElementById('levelControls');
            return !levelControls || levelControls.style.display === 'none';
        });
        
        console.log(`Level controls hidden after reset: ${controlsHidden ? '✅ YES' : '❌ NO'}`);
        
        console.log('\n🎉 Per-level dimension tests completed!');
        
        // Keep open for inspection
        console.log('🔍 Browser will stay open for 15 seconds for manual inspection...');
        await new Promise(resolve => setTimeout(resolve, 15000));
        
    } catch (error) {
        console.error('❌ Per-level test error:', error);
    } finally {
        await browser.close();
    }
}

// Run the test
testPerLevelDimensions().catch(console.error);
