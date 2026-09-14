#!/usr/bin/env node

/**
 * Screenshot Service v2 - Full Page Automation
 * 
 * Thay vì gửi HTML, service này:
 * 1. Truy cập URL thật với cookies/auth
 * 2. Chờ page load xong
 * 3. Click nút download (trigger domtoimage code gốc)
 * 4. Intercept download và return về
 * 
 * Puppeteer headless KHÔNG bị giới hạn canvas size như browser thật!
 */

const puppeteer = require('puppeteer');
const express = require('express');
const bodyParser = require('body-parser');
const fs = require('fs');

const app = express();
const PORT = 3001; // Port khác để không conflict

app.use(bodyParser.json({ limit: '10mb' }));
app.use(bodyParser.urlencoded({ limit: '10mb', extended: true }));

// CORS
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    next();
});

let browser = null;

// Init browser
async function initBrowser() {
    const chromiumPath = process.env.PUPPETEER_EXECUTABLE_PATH || 
                         '/snap/bin/chromium' || 
                         '/usr/bin/chromium';
    
    const launchOptions = {
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--disable-gpu',
            '--disable-web-security', // Allow cross-origin
            '--max-old-space-size=4096',
            '--js-flags=--max-old-space-size=4096',
            // QUAN TRỌNG: Không giới hạn canvas size
            '--unlimited-storage',
            '--full-memory-crash-report'
        ],
        // Tăng timeout
        defaultViewport: null
    };
    
    if (chromiumPath && fs.existsSync(chromiumPath)) {
        launchOptions.executablePath = chromiumPath;
        console.log('✅ Using system Chromium:', chromiumPath);
    }
    
    browser = await puppeteer.launch(launchOptions);
    console.log('✅ Puppeteer browser initialized (headless mode)');
}

/**
 * POST /screenshot-url
 * 
 * Body:
 * {
 *   url: string,              // URL to visit (e.g., https://mytree.vn/my-tree?pid=123)
 *   cookies: array,           // Cookies for auth (optional)
 *   selector: string,         // Selector to click (e.g., .btn_ctrl_svg1)
 *   waitForDownload: boolean, // Wait for download trigger (default: true)
 *   width: number,            // Viewport width (default: 1920)
 *   height: number,           // Viewport height (default: 1080)
 *   timeout: number           // Max timeout in seconds (default: 60)
 * }
 */
app.post('/screenshot-url', async (req, res) => {
    const startTime = Date.now();
    let page = null;
    
    try {
        const {
            url,
            cookies = [],
            selector = '.btn_ctrl_svg1',
            waitForDownload = true,
            width = 1920,
            height = 1080,
            timeout = 60000
        } = req.body;
        
        if (!url) {
            return res.status(400).json({ error: 'URL is required' });
        }
        
        console.log(`🌐 Opening URL: ${url}`);
        console.log(`🎯 Selector to click: ${selector}`);
        
        // Create new page
        page = await browser.newPage();
        
        // Set viewport
        await page.setViewport({ width, height });
        
        // Set cookies if provided
        if (cookies && cookies.length > 0) {
            await page.setCookie(...cookies);
            console.log(`🍪 Set ${cookies.length} cookies`);
        }
        
        // Variable to store download
        let downloadedBlob = null;
        
        // Intercept fetch/xhr requests to catch blob creation
        await page.setRequestInterception(true);
        
        page.on('request', request => {
            request.continue();
        });
        
        // Listen for console logs from page (for debugging)
        page.on('console', msg => {
            console.log(`📄 PAGE LOG:`, msg.text());
        });
        
        // Navigate to URL
        await page.goto(url, {
            waitUntil: 'networkidle0',
            timeout: timeout
        });
        
        console.log('✅ Page loaded');
        
        // Wait for selector to be visible
        await page.waitForSelector(selector, { timeout: 10000 });
        console.log(`✅ Found selector: ${selector}`);
        
        // Setup download listener BEFORE clicking
        const client = await page.target().createCDPSession();
        await client.send('Page.setDownloadBehavior', {
            behavior: 'allow',
            downloadPath: '/tmp'
        });
        
        // Click the button
        console.log(`🖱️  Clicking button...`);
        await page.click(selector);
        
        // Wait for download to complete (monitor network activity)
        await page.waitForTimeout(5000); // Give time for domtoimage to process
        
        // Alternative: Take screenshot of entire page after clicking
        console.log('📸 Taking screenshot...');
        
        // Execute JavaScript to get the canvas/image that domtoimage creates
        const imageDataUrl = await page.evaluate(() => {
            return new Promise((resolve, reject) => {
                // Wait a bit for domtoimage to finish
                setTimeout(() => {
                    // Try to find the generated image/canvas
                    const canvas = document.querySelector('canvas');
                    if (canvas) {
                        resolve(canvas.toDataURL('image/png'));
                    } else {
                        // If no canvas, take screenshot of SVG directly
                        const svg = document.getElementById('svg_grid');
                        if (svg) {
                            // Let domtoimage do its job
                            if (typeof domtoimage !== 'undefined') {
                                domtoimage.toPng(svg)
                                    .then(dataUrl => resolve(dataUrl))
                                    .catch(err => reject(err));
                            } else {
                                reject(new Error('domtoimage not found'));
                            }
                        } else {
                            reject(new Error('No SVG found'));
                        }
                    }
                }, 3000);
            });
        });
        
        if (!imageDataUrl) {
            throw new Error('Failed to get image data');
        }
        
        // Convert data URL to buffer
        const base64Data = imageDataUrl.replace(/^data:image\/\w+;base64,/, '');
        const imageBuffer = Buffer.from(base64Data, 'base64');
        
        const duration = Date.now() - startTime;
        console.log(`✅ Screenshot completed in ${duration}ms, size: ${imageBuffer.length} bytes`);
        
        // Return image
        res.set('Content-Type', 'image/png');
        res.set('Content-Length', imageBuffer.length);
        res.send(imageBuffer);
        
    } catch (error) {
        console.error('❌ Error:', error);
        res.status(500).json({
            error: error.message,
            stack: process.env.NODE_ENV === 'development' ? error.stack : undefined
        });
    } finally {
        if (page) {
            await page.close();
        }
    }
});

/**
 * GET /health
 */
app.get('/health', (req, res) => {
    res.json({
        status: 'ok',
        browser: browser ? 'connected' : 'disconnected',
        service: 'screenshot-url-service',
        port: PORT,
        uptime: process.uptime(),
        memory: process.memoryUsage()
    });
});

/**
 * GET /
 */
app.get('/', (req, res) => {
    res.json({
        service: 'Screenshot URL Service v2',
        version: '2.0.0',
        description: 'Visit real page with cookies and trigger download',
        endpoints: {
            'POST /screenshot-url': 'Visit URL and trigger screenshot',
            'GET /health': 'Health check'
        },
        example: {
            url: 'POST http://localhost:3001/screenshot-url',
            body: {
                url: 'https://mytree.vn/my-tree?pid=11461493758623744',
                selector: '.btn_ctrl_svg1',
                cookies: [
                    { name: 'session', value: 'xxx', domain: 'mytree.vn' }
                ]
            }
        }
    });
});

// Graceful shutdown
process.on('SIGINT', async () => {
    console.log('\n🛑 Shutting down...');
    if (browser) {
        await browser.close();
    }
    process.exit(0);
});

// Start
(async () => {
    try {
        await initBrowser();
        app.listen(PORT, '127.0.0.1', () => {
            console.log(`🚀 Screenshot URL service running on http://127.0.0.1:${PORT}`);
            console.log(`📖 API docs: http://127.0.0.1:${PORT}/`);
        });
    } catch (error) {
        console.error('❌ Failed to start:', error);
        process.exit(1);
    }
})();
