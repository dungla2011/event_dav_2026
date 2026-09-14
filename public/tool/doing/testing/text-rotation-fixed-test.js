const puppeteer = require('puppeteer');

async function testTextRotationFixed() {
    console.log('🚀 Starting FIXED Text Rotation Test...');
    
    const browser = await puppeteer.launch({ 
        headless: false,
        slowMo: 150
    });
    
    const page = await browser.newPage();
    
    try {
        await page.goto('http://127.0.0.1:5500/doing.html');
        console.log('📱 Navigated to app');
        
        await page.waitForSelector('#levelSelector');
        await page.waitForSelector('text.person-name');
        console.log('✅ Elements loaded');
        
        // Helper functions
        const getTextRotations = async () => {
            return await page.$$eval('text.person-name', texts => {
                return texts.map((text, idx) => {
                    const transform = text.getAttribute('transform');
                    const rotation = transform ? transform.match(/rotate\(([^)]+)\)/) : null;
                    return {
                        index: idx,
                        text: text.textContent.trim(),
                        rotation: rotation ? rotation[1] : '0',
                        transform: transform
                    };
                });
            });
        };
        
        console.log('\n🧪 === GLOBAL TEXT ROTATION TEST ===');
        
        // Test each global option
        const globalOptions = [
            { value: 'horizontal', name: 'Nằm ngang', expectedRotation: '0' },
            { value: 'vertical-top', name: 'Dọc xuống', expectedRotation: '-90' },
            { value: 'vertical-bottom', name: 'Dọc lên', expectedRotation: '90' }
        ];
        
        for (const option of globalOptions) {
            console.log(`\n🔧 Testing global "${option.name}" (${option.value})...`);
            
            await page.select('#textRotation', option.value);
            await new Promise(resolve => setTimeout(resolve, 800)); // More wait time
            
            const rotations = await getTextRotations();
            const sampleText = rotations.find(r => r.text.length > 0) || rotations[0];
            
            console.log(`📊 Sample: "${sampleText.text}" = ${sampleText.rotation}°`);
            console.log(`Expected: ${option.expectedRotation}°`);
            console.log(`Result: ${sampleText.rotation === option.expectedRotation ? '✅ PASS' : '❌ FAIL'}`);
            
            // Check localStorage after more delay
            await new Promise(resolve => setTimeout(resolve, 500));
            const config = await page.evaluate(() => {
                const cfg = localStorage.getItem('familyTreeConfig');
                return cfg ? JSON.parse(cfg) : null;
            });
            
            console.log(`💾 Config saved: ${config?.textRotation || 'none'} | ${config?.textRotation === option.value ? '✅ PASS' : '❌ FAIL'}`);
        }
        
        console.log('\n🧪 === PER-LEVEL TEXT ROTATION TEST ===');
        
        // Reset to horizontal first
        await page.select('#textRotation', 'horizontal');
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Get levels
        const levels = await page.$$eval('#levelSelector option', options => 
            options.filter(opt => opt.value !== '-1').map(opt => ({
                value: opt.value,
                text: opt.textContent
            }))
        );
        
        console.log(`📊 Testing ${Math.min(levels.length, 3)} levels...`);
        
        // Test per-level options with correct values
        const levelTestOptions = [
            { value: 'horizontal', name: 'Nằm ngang' },
            { value: 'vertical-top', name: 'Dọc xuống' },
            { value: 'vertical-bottom', name: 'Dọc lên' }
        ];
        
        for (let i = 0; i < Math.min(levels.length, 3); i++) {
            const level = levels[i];
            const levelIndex = parseInt(level.value);
            const testOption = levelTestOptions[i];
            
            console.log(`\n🧪 Level ${levelIndex + 1}: "${testOption.name}" (${testOption.value})...`);
            
            // Select level
            await page.select('#levelSelector', level.value);
            await new Promise(resolve => setTimeout(resolve, 400));
            
            // Check if level control exists
            const hasLevelControl = await page.$('#levelTextRotation');
            if (!hasLevelControl) {
                console.log(`⚠️ No level text rotation control`);
                continue;
            }
            
            // Get level options to verify
            const levelOptions = await page.$$eval('#levelTextRotation option', opts => 
                opts.map(opt => ({ value: opt.value, text: opt.textContent }))
            );
            console.log(`📋 Available: ${levelOptions.map(opt => opt.text).join(', ')}`);
            
            // Set level rotation
            await page.select('#levelTextRotation', testOption.value);
            await new Promise(resolve => setTimeout(resolve, 800));
            
            // Check level settings
            const levelSettings = await page.evaluate((level) => {
                return window.getLevelSettings ? window.getLevelSettings(level) : null;
            }, levelIndex);
            
            if (levelSettings) {
                console.log(`📊 Level settings: ${levelSettings.textRotation || 'none'}`);
                console.log(`Settings result: ${levelSettings.textRotation === testOption.value ? '✅ PASS' : '❌ FAIL'}`);
            }
            
            // Check actual text rotation
            const currentRotations = await getTextRotations();
            const sampleText = currentRotations.find(r => r.text.length > 0) || currentRotations[0];
            console.log(`📊 Current sample: "${sampleText.text}" = ${sampleText.rotation}°`);
        }
        
        console.log('\n🧪 === MIXED LEVELS TEST ===');
        
        // Set different rotation for each level
        const mixedTests = [
            { levelIndex: 0, option: 'horizontal', name: 'Nằm ngang' },
            { levelIndex: 1, option: 'vertical-top', name: 'Dọc xuống' },
            { levelIndex: 2, option: 'vertical-bottom', name: 'Dọc lên' }
        ];
        
        for (const test of mixedTests) {
            if (test.levelIndex >= levels.length) continue;
            
            console.log(`🔧 Setting Level ${test.levelIndex + 1} to "${test.name}"...`);
            
            await page.select('#levelSelector', levels[test.levelIndex].value);
            await new Promise(resolve => setTimeout(resolve, 300));
            await page.select('#levelTextRotation', test.option);
            await new Promise(resolve => setTimeout(resolve, 500));
        }
        
        // Check final state
        console.log(`\n📋 FINAL LEVEL SETTINGS:`);
        for (let i = 0; i < Math.min(levels.length, 3); i++) {
            const levelIndex = parseInt(levels[i].value);
            const settings = await page.evaluate((level) => {
                return window.getLevelSettings ? window.getLevelSettings(level) : null;
            }, levelIndex);
            
            if (settings) {
                console.log(`Level ${levelIndex + 1}: ${settings.textRotation || 'default'} | size: ${settings.width}x${settings.height}`);
            }
        }
        
        // Test global override
        console.log(`\n🔄 Testing global override...`);
        await page.select('#levelSelector', '-1');
        await new Promise(resolve => setTimeout(resolve, 300));
        await page.select('#textRotation', 'vertical-top');
        await new Promise(resolve => setTimeout(resolve, 800));
        
        const finalRotations = await getTextRotations();
        const finalSample = finalRotations.find(r => r.text.length > 0) || finalRotations[0];
        console.log(`📊 Global override: "${finalSample.text}" = ${finalSample.rotation}°`);
        console.log(`Global override result: ${finalSample.rotation === '-90' ? '✅ PASS' : '❌ FAIL'}`);
        
        console.log('\n🎉 Fixed text rotation test completed!');
        
        // Keep open longer for inspection
        console.log('🔍 Browser stays open 15 seconds for inspection...');
        await new Promise(resolve => setTimeout(resolve, 15000));
        
    } catch (error) {
        console.error('❌ Test error:', error);
    } finally {
        await browser.close();
    }
}

testTextRotationFixed().catch(console.error);
