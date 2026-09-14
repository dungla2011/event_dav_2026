// Debug script để test dimension changes
// Copy và paste vào console của doing.html

function debugDimensions() {
    console.log('🔍 Debug Dimension Changes...');
    
    // Check if controls exist
    const widthSlider = document.getElementById('nodeWidth');
    const heightSlider = document.getElementById('nodeHeight');
    const firstRect = document.querySelector('.person-rect');
    
    console.log('Controls found:');
    console.log('- widthSlider:', !!widthSlider, widthSlider?.value);
    console.log('- heightSlider:', !!heightSlider, heightSlider?.value);
    console.log('- firstRect:', !!firstRect);
    
    if (firstRect) {
        console.log('- current rect dimensions:', 
            firstRect.getAttribute('width'), 'x', firstRect.getAttribute('height'));
    }
    
    // Check global variables
    console.log('\nGlobal variables:');
    console.log('- defaultNodeWidth:', typeof defaultNodeWidth !== 'undefined' ? defaultNodeWidth : 'undefined');
    console.log('- defaultNodeHeight:', typeof defaultNodeHeight !== 'undefined' ? defaultNodeHeight : 'undefined');
    console.log('- window.defaultNodeWidth:', typeof window.defaultNodeWidth !== 'undefined' ? window.defaultNodeWidth : 'undefined');
    console.log('- window.defaultNodeHeight:', typeof window.defaultNodeHeight !== 'undefined' ? window.defaultNodeHeight : 'undefined');
    
    // Check functions
    console.log('\nFunctions available:');
    console.log('- updateDisplay:', typeof updateDisplay);
    console.log('- getLevelSettings:', typeof getLevelSettings);
    
    return {
        widthSlider,
        heightSlider,
        firstRect,
        defaultNodeWidth: typeof defaultNodeWidth !== 'undefined' ? defaultNodeWidth : undefined,
        defaultNodeHeight: typeof defaultNodeHeight !== 'undefined' ? defaultNodeHeight : undefined
    };
}

async function testWidthChange() {
    console.log('🧪 Testing Width Change...');
    
    const widthSlider = document.getElementById('nodeWidth');
    const firstRect = document.querySelector('.person-rect');
    
    if (!widthSlider || !firstRect) {
        console.error('❌ Required elements not found');
        return;
    }
    
    const originalWidth = parseInt(widthSlider.value);
    const originalRectWidth = parseFloat(firstRect.getAttribute('width'));
    const newWidth = originalWidth + 50;
    
    console.log(`Original slider: ${originalWidth}, rect: ${originalRectWidth}`);
    
    // Method 1: Just change slider and trigger event
    console.log('Method 1: Slider + Event');
    widthSlider.value = newWidth;
    widthSlider.dispatchEvent(new Event('input', { bubbles: true }));
    
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    let updatedWidth = parseFloat(firstRect.getAttribute('width'));
    console.log(`After slider change: ${updatedWidth} (expected: ${newWidth})`);
    
    // Method 2: Change global variable + updateDisplay
    console.log('Method 2: Global Variable + updateDisplay');
    if (typeof defaultNodeWidth !== 'undefined') {
        defaultNodeWidth = newWidth;
    }
    if (typeof window.defaultNodeWidth !== 'undefined') {
        window.defaultNodeWidth = newWidth;
    }
    
    if (typeof updateDisplay === 'function') {
        updateDisplay();
    }
    
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    updatedWidth = parseFloat(firstRect.getAttribute('width'));
    console.log(`After updateDisplay: ${updatedWidth} (expected: ${newWidth})`);
    
    // Method 3: Force all nodes to update
    console.log('Method 3: Force all rect updates');
    document.querySelectorAll('.person-rect').forEach(rect => {
        rect.setAttribute('width', newWidth);
    });
    
    updatedWidth = parseFloat(firstRect.getAttribute('width'));
    console.log(`After force update: ${updatedWidth} (expected: ${newWidth})`);
    
    // Restore original
    widthSlider.value = originalWidth;
    if (typeof defaultNodeWidth !== 'undefined') {
        defaultNodeWidth = originalWidth;
    }
    widthSlider.dispatchEvent(new Event('input', { bubbles: true }));
    if (typeof updateDisplay === 'function') {
        updateDisplay();
    }
    
    console.log('✅ Test completed');
}

// Auto-load message
console.log('🔧 Debug Dimensions Script Loaded!');
console.log('📝 Run debugDimensions() to check current state');
console.log('📝 Run testWidthChange() to test width change methods');
