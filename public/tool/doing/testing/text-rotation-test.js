const puppeteer = require('puppeteer');

async function testTextRotation() {
    console.log('🚀 Starting Text Rotation Test...');
    
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
        await page.waitForSelector('text.person-name');
        console.log('✅ Elements loaded');
        
        // Get initial text elements and their rotation
        const getTextRotations = async () => {
            return await page.$$eval('text.person-name', texts => {
                return texts.map((text, idx) => {
                    const transform = text.getAttribute('transform');
                    const rotation = transform ? transform.match(/rotate\(([^)]+)\)/) : null;
                    return {
                        index: idx,
                        text: text.textContent,
                        rotation: rotation ? rotation[1] : '0',
                        transform: transform
                    };
                });
            });
        };
        
        // Get text rotation options available
        const getRotationOptions = async () => {
            return await page.$$eval('#textRotation option', options => 
                options.map(opt => ({
                    value: opt.value,
                    text: opt.textContent
                }))
            );
        };
        
        console.log('\n🧪 === GLOBAL TEXT ROTATION TEST ===');
        
        // Get available rotation options
        const rotationOptions = await getRotationOptions();
        console.log(`📊 Available rotation options: ${rotationOptions.map(opt => opt.text).join(', ')}`);
        
        // Test global text rotation
        const initialRotations = await getTextRotations();
        console.log(`📊 Initial text rotations (showing first 3):`);
        initialRotations.slice(0, 3).forEach((item, idx) => {
            console.log(`  Text ${idx + 1}: "${item.text}" rotation=${item.rotation}°`);
        });
        
        // Test different rotation options
        for (const option of rotationOptions) {
            console.log(`\n🔧 Setting global rotation to "${option.text}" (${option.value})...`);
            
            // Set global rotation via dropdown
            await page.select('#textRotation', option.value);
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Check updated rotations
            const updatedRotations = await getTextRotations();
            console.log(`📊 Global rotation "${option.text}" result:`);
            console.log(`  Sample: "${updatedRotations[0]?.text || 'N/A'}" = ${updatedRotations[0]?.rotation || '0'}°`);
            console.log(`  Transform: ${updatedRotations[0]?.transform || 'none'}`);
            
            // Check localStorage save
            const savedConfig = await page.evaluate(() => {
                const config = localStorage.getItem('familyTreeConfig');
                return config ? JSON.parse(config) : null;
            });
            
            if (savedConfig && savedConfig.textRotation === option.value) {
                console.log(`💾 localStorage saved: ✅ PASS (${savedConfig.textRotation})`);
            } else {
                console.log(`💾 localStorage saved: ❌ FAIL (saved: ${savedConfig?.textRotation})`);
            }
        }
        
        console.log('\n🧪 === PER-LEVEL TEXT ROTATION TEST ===');
        
        // Reset global rotation to horizontal
        await page.select('#textRotation', 'horizontal');
        await new Promise(resolve => setTimeout(resolve, 300));
        
        // Get available levels
        const levels = await page.$$eval('#levelSelector option', options => 
            options.filter(opt => opt.value !== '-1').map(opt => ({
                value: opt.value,
                text: opt.textContent
            }))
        );
        
        console.log(`📊 Found ${levels.length} levels for per-level testing`);
        
        // Test each level with different rotation
        const testLevels = levels.slice(0, 3); // Max 3 levels
        const levelRotationOptions = ['horizontal', 'vertical', '45deg'];
        
        for (let i = 0; i < testLevels.length; i++) {
            const level = testLevels[i];
            const levelIndex = parseInt(level.value);
            const testRotation = levelRotationOptions[i] || 'horizontal';
            
            console.log(`\n🧪 Testing Level ${levelIndex + 1} rotation = "${testRotation}"...`);
            
            // Select the level
            await page.select('#levelSelector', level.value);
            await new Promise(resolve => setTimeout(resolve, 300));
            
            // Check if level rotation control exists
            const levelRotationControl = await page.$('#levelTextRotation');
            if (!levelRotationControl) {
                console.log(`⚠️ No level text rotation control found for level ${levelIndex + 1}`);
                continue;
            }
            
            // Get available options for this level
            const levelOptions = await page.$$eval('#levelTextRotation option', options => 
                options.map(opt => ({
                    value: opt.value,
                    text: opt.textContent
                }))
            );
            console.log(`📋 Level rotation options: ${levelOptions.map(opt => opt.text).join(', ')}`);
            
            // Set level rotation
            await page.select('#levelTextRotation', testRotation);
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Get current rotations
            const currentRotations = await getTextRotations();
            
            // Check level settings
            const levelSettings = await page.evaluate((level) => {
                return window.getLevelSettings ? window.getLevelSettings(level) : null;
            }, levelIndex);
            
            if (levelSettings) {
                console.log(`📊 Level ${levelIndex + 1} settings rotation: ${levelSettings.textRotation || 'horizontal'}`);
                console.log(`Settings result: ${(levelSettings.textRotation || 'horizontal') === testRotation ? '✅ PASS' : '❌ FAIL'}`);
            }
            
            // Show sample text rotations (might be mixed if multiple levels)
            console.log(`📊 Current text rotations (first 3):`);
            currentRotations.slice(0, 3).forEach((item, idx) => {
                console.log(`  Text ${idx + 1}: "${item.text}" = ${item.rotation}° | transform: ${item.transform || 'none'}`);
            });
        }
        
        console.log('\n🧪 === MIXED ROTATION TEST ===');
        
        // Test that different levels can have different rotations
        console.log('🔧 Setting multiple levels with different rotations...');
        
        // Set Level 1 to vertical
        if (testLevels[0]) {
            await page.select('#levelSelector', testLevels[0].value);
            await new Promise(resolve => setTimeout(resolve, 200));
            await page.select('#levelTextRotation', 'vertical');
            await new Promise(resolve => setTimeout(resolve, 300));
        }
        
        // Set Level 2 to 45deg
        if (testLevels[1]) {
            await page.select('#levelSelector', testLevels[1].value);
            await new Promise(resolve => setTimeout(resolve, 200));
            await page.select('#levelTextRotation', '45deg');
            await new Promise(resolve => setTimeout(resolve, 300));
        }
        
        // Set Level 3 to -45deg (if available)
        if (testLevels[2]) {
            await page.select('#levelSelector', testLevels[2].value);
            await new Promise(resolve => setTimeout(resolve, 200));
            const hasNeg45 = await page.$eval('#levelTextRotation', select => {
                return Array.from(select.options).some(opt => opt.value === '-45deg');
            });
            if (hasNeg45) {
                await page.select('#levelTextRotation', '-45deg');
            } else {
                await page.select('#levelTextRotation', 'horizontal');
            }
            await new Promise(resolve => setTimeout(resolve, 300));
        }
        
        // Check final mixed state
        const finalRotations = await getTextRotations();
        console.log(`📊 Final mixed rotations:`);
        finalRotations.forEach((item, idx) => {
            console.log(`  Text ${idx + 1}: "${item.text}" = ${item.rotation}° | ${item.transform || 'none'}`);
        });
        
        // Summary of level settings
        console.log(`\n📋 LEVEL SETTINGS SUMMARY:`);
        for (const level of testLevels) {
            const levelIndex = parseInt(level.value);
            const settings = await page.evaluate((level) => {
                return window.getLevelSettings ? window.getLevelSettings(level) : null;
            }, levelIndex);
            
            if (settings) {
                console.log(`Level ${levelIndex + 1}: rotation=${settings.textRotation || 'horizontal'}, size=${settings.width}x${settings.height}`);
            }
        }
        
        // Test reset to global
        console.log(`\n🔄 Testing reset to global rotation...`);
        await page.select('#levelSelector', '-1'); // Reset to global
        await new Promise(resolve => setTimeout(resolve, 300));
        
        await page.select('#textRotation', 'vertical');
        await new Promise(resolve => setTimeout(resolve, 500));
        
        const globalResetRotations = await getTextRotations();
        console.log(`📊 Global reset to vertical:`);
        globalResetRotations.slice(0, 3).forEach((item, idx) => {
            console.log(`  Text ${idx + 1}: "${item.text}" = ${item.rotation}° | ${item.transform || 'none'}`);
        });
        
        // Check if all text has consistent rotation after global reset
        const globalResetConfig = await page.evaluate(() => {
            const config = localStorage.getItem('familyTreeConfig');
            return config ? JSON.parse(config) : null;
        });
        
        console.log(`Global reset config: ${globalResetConfig?.textRotation || 'not saved'}`);
        console.log(`Global reset result: ${globalResetConfig?.textRotation === 'vertical' ? '✅ PASS' : '❌ FAIL'}`);
        
        console.log('\n🎉 Text rotation tests completed!');
        
        // Keep open for inspection
        console.log('🔍 Browser will stay open for 10 seconds for manual inspection...');
        await new Promise(resolve => setTimeout(resolve, 10000));
        
    } catch (error) {
        console.error('❌ Text rotation test error:', error);
    } finally {
        await browser.close();
    }
}

// Run the test
testTextRotation().catch(console.error);
