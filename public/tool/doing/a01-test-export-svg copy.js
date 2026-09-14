const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

// Cấu hình test cases
const testCases = [
    {
        name: "horizontal-center-name-only",
        config: {
            showTitle: false,
            showBirthday: false,
            showImage: false,
            showDeathDate: false,
            textRotation: "horizontal",
            textAlignment: "center",
            nodeWidth: 200,
            nodeHeight: 100,
            textAlignmentOffset: 20,
            textAlignmentOffsetY: 0
        }
    },
    {
        name: "horizontal-center-with-title-birthday",
        config: {
            showTitle: true,
            showBirthday: true,
            showImage: false,
            showDeathDate: false,
            textRotation: "horizontal",
            textAlignment: "center",
            nodeWidth: 200,
            nodeHeight: 100,
            textAlignmentOffset: 20,
            textAlignmentOffsetY: 0
        }
    },
    {
        name: "vertical-bottom-center-name-only",
        config: {
            showTitle: false,
            showBirthday: false,
            showImage: false,
            showDeathDate: false,
            textRotation: "vertical-bottom",
            textAlignment: "center",
            nodeWidth: 100,
            nodeHeight: 200,
            textAlignmentOffset: 20,
            textAlignmentOffsetY: 0
        }
    },
    {
        name: "vertical-bottom-center-with-title-birthday",
        config: {
            showTitle: true,
            showBirthday: true,
            showImage: false,
            showDeathDate: false,
            textRotation: "vertical-bottom",
            textAlignment: "center",
            nodeWidth: 100,
            nodeHeight: 200,
            textAlignmentOffset: 20,
            textAlignmentOffsetY: 0
        }
    },
    {
        name: "vertical-bottom-left-alignment",
        config: {
            showTitle: true,
            showBirthday: true,
            showImage: false,
            showDeathDate: false,
            textRotation: "vertical-bottom",
            textAlignment: "left",
            nodeWidth: 100,
            nodeHeight: 200,
            textAlignmentOffset: 46,
            textAlignmentOffsetY: -33
        }
    },
    {
        name: "horizontal-with-image-top",
        config: {
            showTitle: true,
            showBirthday: true,
            showImage: true,
            showDeathDate: false,
            textRotation: "horizontal",
            textAlignment: "center",
            nodeWidth: 200,
            nodeHeight: 100,
            imagePosition: "top",
            textAlignmentOffset: 20,
            textAlignmentOffsetY: 0
        }
    },
    {
        name: "vertical-with-image-left",
        config: {
            showTitle: true,
            showBirthday: true,
            showImage: true,
            showDeathDate: false,
            textRotation: "vertical-bottom",
            textAlignment: "center",
            nodeWidth: 100,
            nodeHeight: 200,
            imagePosition: "left",
            textAlignmentOffset: 20,
            textAlignmentOffsetY: 0
        }
    }
];

class SVGExportTester {
    constructor() {
        this.browser = null;
        this.page = null;
        this.testResults = [];
    }

    async init() {
        console.log('🚀 Khởi tạo Puppeteer browser...');
        this.browser = await puppeteer.launch({
            headless: false, // Để debug dễ hơn
            devtools: true,
            defaultViewport: { width: 1920, height: 1080 }
        });
        this.page = await this.browser.newPage();
        
        // Load trang web
        const url = 'http://127.0.0.1:5500/doing.html';
        await this.page.goto(url);
        
        // Đợi trang load xong
        await this.page.waitForSelector('#tree-svg');
        await this.page.waitForFunction(() => document.readyState === 'complete');
        await new Promise(resolve => setTimeout(resolve, 2000)); // Đợi data load
        
        console.log('✅ Trang web đã load xong');
    }

    async applyConfig(config) {
        console.log('🔧 Áp dụng cấu hình:', config);
        
        // Apply từng setting
        for (const [key, value] of Object.entries(config)) {
            await this.applySpecificSetting(key, value);
        }
        
        // Đợi render xong
        await new Promise(resolve => setTimeout(resolve, 1000));
    }

    async applySpecificSetting(key, value) {
        const applyFunctions = {
            showTitle: async (val) => {
                await this.page.evaluate((checked) => {
                    document.getElementById('showTitle').checked = checked;
                    document.getElementById('showTitle').dispatchEvent(new Event('change'));
                }, val);
            },
            showBirthday: async (val) => {
                await this.page.evaluate((checked) => {
                    document.getElementById('showBirthday').checked = checked;
                    document.getElementById('showBirthday').dispatchEvent(new Event('change'));
                }, val);
            },
            showImage: async (val) => {
                await this.page.evaluate((checked) => {
                    document.getElementById('showImage').checked = checked;
                    document.getElementById('showImage').dispatchEvent(new Event('change'));
                }, val);
            },
            showDeathDate: async (val) => {
                await this.page.evaluate((checked) => {
                    document.getElementById('showDeathDate').checked = checked;
                    document.getElementById('showDeathDate').dispatchEvent(new Event('change'));
                }, val);
            },
            textRotation: async (val) => {
                await this.page.evaluate((rotation) => {
                    const radio = document.querySelector(`input[name="globalTextRotation"][value="${rotation}"]`);
                    if (radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change'));
                    }
                }, val);
            },
            textAlignment: async (val) => {
                await this.page.evaluate((alignment) => {
                    const radio = document.querySelector(`input[name="globalTextAlignment"][value="${alignment}"]`);
                    if (radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change'));
                    }
                }, val);
            },
            nodeWidth: async (val) => {
                await this.page.evaluate((width) => {
                    const slider = document.getElementById('nodeWidth');
                    if (slider) {
                        slider.value = width;
                        slider.dispatchEvent(new Event('input'));
                    }
                }, val);
            },
            nodeHeight: async (val) => {
                await this.page.evaluate((height) => {
                    const slider = document.getElementById('nodeHeight');
                    if (slider) {
                        slider.value = height;
                        slider.dispatchEvent(new Event('input'));
                    }
                }, val);
            },
            textAlignmentOffset: async (val) => {
                await this.page.evaluate((offset) => {
                    const slider = document.getElementById('textAlignmentOffset');
                    if (slider) {
                        slider.value = offset;
                        slider.dispatchEvent(new Event('input'));
                    }
                }, val);
            },
            textAlignmentOffsetY: async (val) => {
                await this.page.evaluate((offset) => {
                    const slider = document.getElementById('textAlignmentOffsetY');
                    if (slider) {
                        slider.value = offset;
                        slider.dispatchEvent(new Event('input'));
                    }
                }, val);
            },
            imagePosition: async (val) => {
                await this.page.evaluate((position) => {
                    const select = document.getElementById('globalImagePosition');
                    if (select) {
                        select.value = position;
                        select.dispatchEvent(new Event('change'));
                    }
                }, val);
            }
        };

        if (applyFunctions[key]) {
            await applyFunctions[key](value);
        }
    }

    async extractTextPositions() {
        const displayPositions = await this.page.evaluate(() => {
            const textElements = document.querySelectorAll('#tree-svg text.person-name, #tree-svg text.person-title, #tree-svg text.person-birthday');
            const positions = [];
            
            textElements.forEach((element, index) => {
                if (index < 9) { // Chỉ lấy 3 nodes đầu tiên (3 loại text * 3 nodes)
                    const transform = element.getAttribute('transform');
                    const className = element.getAttribute('class');
                    const content = element.textContent;
                    
                    // Parse transform để lấy translate values
                    const translateMatch = transform.match(/translate\(([^,]+),\s*([^)]+)\)/);
                    const rotateMatch = transform.match(/rotate\(([^)]+)\)/);
                    
                    if (translateMatch) {
                        positions.push({
                            type: 'display',
                            class: className,
                            content: content,
                            x: parseFloat(translateMatch[1]),
                            y: parseFloat(translateMatch[2]),
                            rotation: rotateMatch ? parseFloat(rotateMatch[1]) : 0,
                            transform: transform
                        });
                    }
                }
            });
            
            return positions;
        });

        return displayPositions;
    }

    async exportSVGAndExtractPositions() {
        // Trigger export
        await this.page.evaluate(() => {
            exportCorelDrawSVGLegacy(); // Gọi hàm export
        });

        // Đợi export xong
        await new Promise(resolve => setTimeout(resolve, 1000));

        // Extract positions từ exported SVG
        const exportPositions = await this.page.evaluate(() => {
            // Tìm exported SVG content trong DOM hoặc download area
            // Giả sử exported SVG được append vào body hoặc có thể access được
            const exportedTexts = [];
            
            // Try to find the exported SVG
            const exportSVG = document.querySelector('svg[data-export="true"]') || 
                             document.querySelector('.export-result svg') ||
                             document.querySelector('#export-container svg');
            
            if (exportSVG) {
                const textElements = exportSVG.querySelectorAll('text');
                textElements.forEach((element, index) => {
                    if (index < 9) { // Chỉ lấy 3 nodes đầu tiên
                        const transform = element.getAttribute('transform');
                        const content = element.textContent;
                        
                        const translateMatch = transform.match(/translate\(([^,]+),\s*([^)]+)\)/);
                        const rotateMatch = transform.match(/rotate\(([^)]+)\)/);
                        
                        if (translateMatch) {
                            exportedTexts.push({
                                type: 'export',
                                content: content,
                                x: parseFloat(translateMatch[1]),
                                y: parseFloat(translateMatch[2]),
                                rotation: rotateMatch ? parseFloat(rotateMatch[1]) : 0,
                                transform: transform
                            });
                        }
                    }
                });
            }
            
            return exportedTexts;
        });

        return exportPositions;
    }

    comparePositions(displayPositions, exportPositions, tolerance = 1) {
        const results = [];
        
        // Group by content để match display vs export
        const displayByContent = displayPositions.reduce((acc, pos) => {
            acc[pos.content] = pos;
            return acc;
        }, {});
        
        const exportByContent = exportPositions.reduce((acc, pos) => {
            acc[pos.content] = pos;
            return acc;
        }, {});
        
        // Compare positions
        for (const content of Object.keys(displayByContent)) {
            const displayPos = displayByContent[content];
            const exportPos = exportByContent[content];
            
            if (!exportPos) {
                results.push({
                    content,
                    status: 'MISSING',
                    message: 'Export position not found'
                });
                continue;
            }
            
            const xDiff = Math.abs(displayPos.x - exportPos.x);
            const yDiff = Math.abs(displayPos.y - exportPos.y);
            const rotationDiff = Math.abs(displayPos.rotation - exportPos.rotation);
            
            const isMatch = xDiff <= tolerance && yDiff <= tolerance && rotationDiff <= tolerance;
            
            results.push({
                content,
                status: isMatch ? 'PASS' : 'FAIL',
                display: displayPos,
                export: exportPos,
                differences: {
                    x: xDiff,
                    y: yDiff,
                    rotation: rotationDiff
                },
                message: isMatch ? 'Positions match' : `Position mismatch: ΔX=${xDiff.toFixed(2)}, ΔY=${yDiff.toFixed(2)}, ΔRot=${rotationDiff.toFixed(2)}`
            });
        }
        
        return results;
    }

    async runSingleTest(testCase) {
        console.log(`\n🧪 Đang test: ${testCase.name}`);
        
        try {
            // Apply config
            await this.applyConfig(testCase.config);
            
            // Extract display positions
            const displayPositions = await this.extractTextPositions();
            console.log(`📊 Tìm thấy ${displayPositions.length} text elements trong display SVG`);
            
            // Export and extract export positions
            const exportPositions = await this.exportSVGAndExtractPositions();
            console.log(`📊 Tìm thấy ${exportPositions.length} text elements trong export SVG`);
            
            // Compare positions
            const comparison = this.comparePositions(displayPositions, exportPositions);
            
            const passCount = comparison.filter(c => c.status === 'PASS').length;
            const failCount = comparison.filter(c => c.status === 'FAIL').length;
            const missingCount = comparison.filter(c => c.status === 'MISSING').length;
            
            const result = {
                testName: testCase.name,
                config: testCase.config,
                displayPositions,
                exportPositions,
                comparison,
                summary: {
                    total: comparison.length,
                    pass: passCount,
                    fail: failCount,
                    missing: missingCount,
                    success: failCount === 0 && missingCount === 0
                }
            };
            
            console.log(`✅ Test ${testCase.name}: ${passCount} PASS, ${failCount} FAIL, ${missingCount} MISSING`);
            
            return result;
            
        } catch (error) {
            console.error(`❌ Test ${testCase.name} failed:`, error);
            return {
                testName: testCase.name,
                config: testCase.config,
                error: error.message,
                summary: { success: false }
            };
        }
    }

    async runAllTests() {
        console.log(`🏁 Bắt đầu chạy ${testCases.length} test cases...`);
        
        for (const testCase of testCases) {
            const result = await this.runSingleTest(testCase);
            this.testResults.push(result);
            
            // Pause between tests
            await new Promise(resolve => setTimeout(resolve, 2000));
        }
        
        return this.testResults;
    }

    generateReport() {
        const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
        const reportPath = path.join(__dirname, `test-report-${timestamp}.json`);
        
        const report = {
            timestamp: new Date().toISOString(),
            summary: {
                totalTests: this.testResults.length,
                passedTests: this.testResults.filter(r => r.summary?.success).length,
                failedTests: this.testResults.filter(r => !r.summary?.success).length
            },
            results: this.testResults
        };
        
        fs.writeFileSync(reportPath, JSON.stringify(report, null, 2));
        
        console.log(`\n📋 Test Report Generated: ${reportPath}`);
        console.log(`📊 Summary: ${report.summary.passedTests}/${report.summary.totalTests} tests passed`);
        
        // Generate human-readable report
        const humanReportPath = path.join(__dirname, `test-summary-${timestamp}.txt`);
        let humanReport = `SVG Export Test Report - ${new Date().toLocaleString()}\n`;
        humanReport += `${'='.repeat(60)}\n\n`;
        
        humanReport += `SUMMARY:\n`;
        humanReport += `Total Tests: ${report.summary.totalTests}\n`;
        humanReport += `Passed: ${report.summary.passedTests}\n`;
        humanReport += `Failed: ${report.summary.failedTests}\n\n`;
        
        this.testResults.forEach(result => {
            humanReport += `TEST: ${result.testName}\n`;
            humanReport += `Status: ${result.summary?.success ? '✅ PASS' : '❌ FAIL'}\n`;
            
            if (result.comparison) {
                result.comparison.forEach(comp => {
                    humanReport += `  - ${comp.content}: ${comp.status} - ${comp.message}\n`;
                });
            }
            
            humanReport += `\n`;
        });
        
        fs.writeFileSync(humanReportPath, humanReport);
        console.log(`📋 Human Report Generated: ${humanReportPath}`);
        
        return report;
    }

    async cleanup() {
        if (this.browser) {
            await this.browser.close();
        }
    }
}

// Chạy tests
async function main() {
    const tester = new SVGExportTester();
    
    try {
        await tester.init();
        await tester.runAllTests();
        const report = tester.generateReport();
        
        console.log('\n🎉 Tất cả tests đã hoàn thành!');
        console.log(`📊 Kết quả: ${report.summary.passedTests}/${report.summary.totalTests} tests passed`);
        
    } catch (error) {
        console.error('❌ Test suite failed:', error);
    } finally {
        await tester.cleanup();
    }
}

// Check if this script is run directly
if (require.main === module) {
    main();
}

module.exports = { SVGExportTester, testCases };
