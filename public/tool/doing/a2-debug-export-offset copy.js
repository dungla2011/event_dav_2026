// Debug script: Compare Display vs Export coordinates
// Simulate exact logic from both functions

console.log('🔍 Debug: Display vs Export coordinate comparison');
console.log('==================================================');

// Test data for "Con ông Tổ 4" node at level 2
const nodeData = {
    name: 'Con ông Tổ 4',
    title: 'Trưởng Nam', 
    birthday: '4/1/2001',
    date_of_death: null,
    depth: 2
};

// Settings for level 2 (100x200)
const nodeSettings = { width: 100, height: 200 };
const centerX = nodeSettings.width / 2; // 50

// Level 2 has vertical-bottom text rotation
const textRotation = 'vertical-bottom';

// Margins
const nameMargin = 16;
const titleMargin = 16;

// Display options (level 2 might have these settings)
const displayOptions = {
    showTitle: true,
    showBirthday: true, 
    showDeathDate: false // No death date for this node
};

console.log('📊 Node data:', nodeData);
console.log('📊 Settings:', nodeSettings);
console.log('📊 Display options:', displayOptions);

// Calculate actualNumLines (same for both display and export)
let actualNumLines = 1; // Always have name
if (displayOptions.showTitle) actualNumLines++;
if (displayOptions.showBirthday) actualNumLines++;  
if (displayOptions.showDeathDate) actualNumLines++;

console.log(`📊 Calculated actualNumLines: ${actualNumLines}`);

// Calculate totalHeight (same for both display and export)
let totalHeight = 0;
if (actualNumLines > 1) totalHeight += nameMargin;   // 16
if (actualNumLines > 2) totalHeight += titleMargin;  // 16
if (actualNumLines > 3) totalHeight += nameMargin;   // 0 (not applied)

console.log(`📏 Total Height: ${totalHeight}px`);

// getLinePosition function (same for both)
const getLinePosition = (index) => {
    let position = 0;
    for (let i = 0; i < index; i++) {
        if (i === 0) { // After name
            position += nameMargin;
        } else if (i === 1) { // After title  
            position += titleMargin;
        } else if (i === 2) { // After birthday
            position += nameMargin;
        }
    }
    return position;
};

// Test for Name text (index 0)
console.log('\n🎯 NAME TEXT CALCULATION:');
console.log('========================');

const textIndex = 0; // Name
const effectiveIndex = (actualNumLines - 1) - textIndex; // 2 - 0 = 2
const linePos = getLinePosition(effectiveIndex); // getLinePosition(2) = 32

console.log(`textIndex: ${textIndex}`);
console.log(`effectiveIndex: ${effectiveIndex}`);
console.log(`linePos (getLinePosition(${effectiveIndex})): ${linePos}px`);

// Base X calculation (same for both display and export)
const baseTextX = centerX - (totalHeight / 2) + linePos;
console.log(`baseTextX: ${centerX} - ${totalHeight/2} + ${linePos} = ${baseTextX}px`);

// Check if there are any additional offsets in export
console.log('\n📋 EXPECTED RESULTS:');
console.log('===================');
console.log(`Display SVG: translate(66, 202.5) rotate(90) ✅`);
console.log(`Our calculation: translate(${baseTextX}, Y) rotate(90)`);
console.log(`Export SVG: translate(74, 202.5) rotate(90) ❌ (+8px difference)`);

console.log('\n🔍 ANALYSIS:');
console.log('============');
if (baseTextX === 66) {
    console.log('✅ Our calculation matches display SVG exactly!');
    console.log('❌ Export function must have additional +8px offset somewhere');
    console.log('🔧 Need to check: alignmentOffsetY or other offsets in export');
} else {
    console.log(`❌ Our calculation (${baseTextX}) doesn't match display (66)`);
    console.log('🔧 Need to verify margin values or display logic');
}

console.log('\n🎯 NEXT STEPS:');
console.log('==============');
console.log('1. Check alignmentOffsetY value in export function');
console.log('2. Check if there are level-specific text offsets');
console.log('3. Compare actual margin values between display and export');
