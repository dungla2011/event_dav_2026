import { test, expect } from '@playwright/test';

test.describe('Family Tree Dimension Changes', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to the family tree app
    await page.goto('http://127.0.0.1:5500/doing.html');
    
    // Wait for app to load
    await page.waitForSelector('#nodeWidth');
    await page.waitForSelector('rect:not(.link-line)');
  });

  test('should update node width when slider changes', async ({ page }) => {
    // Get initial state
    const initialWidth = await page.getAttribute('rect:not(.link-line)', 'width');
    const initialSliderValue = await page.inputValue('#nodeWidth');
    
    console.log(`Initial: width=${initialWidth}, slider=${initialSliderValue}`);
    
    // Change width slider to 250
    await page.fill('#nodeWidth', '250');
    await page.dispatchEvent('#nodeWidth', 'input');
    
    // Wait for update
    await page.waitForTimeout(500);
    
    // Check if rect width changed
    const newWidth = await page.getAttribute('rect:not(.link-line)', 'width');
    const newSliderValue = await page.inputValue('#nodeWidth');
    
    console.log(`After change: width=${newWidth}, slider=${newSliderValue}`);
    
    // Assertions
    expect(newSliderValue).toBe('250');
    expect(newWidth).toBe('250');
    expect(newWidth).not.toBe(initialWidth);
  });

  test('should update node height when slider changes', async ({ page }) => {
    // Get initial state
    const initialHeight = await page.getAttribute('rect:not(.link-line)', 'height');
    const initialSliderValue = await page.inputValue('#nodeHeight');
    
    console.log(`Initial: height=${initialHeight}, slider=${initialSliderValue}`);
    
    // Change height slider to 150
    await page.fill('#nodeHeight', '150');
    await page.dispatchEvent('#nodeHeight', 'input');
    
    // Wait for update
    await page.waitForTimeout(500);
    
    // Check if rect height changed
    const newHeight = await page.getAttribute('rect:not(.link-line)', 'height');
    const newSliderValue = await page.inputValue('#nodeHeight');
    
    console.log(`After change: height=${newHeight}, slider=${newSliderValue}`);
    
    // Assertions
    expect(newSliderValue).toBe('150');
    expect(newHeight).toBe('150');
    expect(newHeight).not.toBe(initialHeight);
  });

  test('should update multiple nodes consistently', async ({ page }) => {
    // Get all rect elements
    const rects = await page.locator('rect:not(.link-line)').all();
    
    // Get initial widths
    const initialWidths = await Promise.all(
      rects.map(rect => rect.getAttribute('width'))
    );
    
    console.log(`Initial widths: ${initialWidths.join(', ')}`);
    
    // Change width to 300
    await page.fill('#nodeWidth', '300');
    await page.dispatchEvent('#nodeWidth', 'input');
    await page.waitForTimeout(500);
    
    // Get new widths
    const newWidths = await Promise.all(
      rects.map(rect => rect.getAttribute('width'))
    );
    
    console.log(`New widths: ${newWidths.join(', ')}`);
    
    // All level 0 nodes should have width 300
    const level0Rects = await page.locator('.person-node').all();
    for (const nodeGroup of level0Rects) {
      const rect = nodeGroup.locator('rect');
      const width = await rect.getAttribute('width');
      // At least some should be 300 (level 0 nodes)
    }
    
    // At least one rect should have changed to 300
    expect(newWidths.some(w => w === '300')).toBeTruthy();
  });

  test('should persist changes in localStorage', async ({ page }) => {
    // Change width
    await page.fill('#nodeWidth', '280');
    await page.dispatchEvent('#nodeWidth', 'input');
    await page.waitForTimeout(500);
    
    // Check localStorage
    const globalConfig = await page.evaluate(() => {
      const config = localStorage.getItem('familyTree_globalConfig');
      return config ? JSON.parse(config) : null;
    });
    
    expect(globalConfig).toBeTruthy();
    expect(globalConfig.nodeWidth).toBe(280);
    
    // Reload page and check if setting persists
    await page.reload();
    await page.waitForSelector('#nodeWidth');
    
    const persistedValue = await page.inputValue('#nodeWidth');
    expect(persistedValue).toBe('280');
  });

  test('should handle edge cases', async ({ page }) => {
    // Test minimum value
    await page.fill('#nodeWidth', '100');
    await page.dispatchEvent('#nodeWidth', 'input');
    await page.waitForTimeout(300);
    
    const minWidth = await page.getAttribute('rect:not(.link-line)', 'width');
    expect(minWidth).toBe('100');
    
    // Test maximum value
    await page.fill('#nodeWidth', '400');
    await page.dispatchEvent('#nodeWidth', 'input');
    await page.waitForTimeout(300);
    
    const maxWidth = await page.getAttribute('rect:not(.link-line)', 'width');
    expect(maxWidth).toBe('400');
  });
});
