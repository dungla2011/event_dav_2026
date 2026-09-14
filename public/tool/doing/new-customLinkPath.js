function customLinkPath(d) {
    const sourceSettings = getLevelSettings(d.source.depth);
    const targetSettings = getLevelSettings(d.target.depth);
    
    const sourceX = d.source.x;
    const sourceY = d.source.y + sourceSettings.height / 2;
    const targetX = d.target.x;
    const targetY = d.target.y - targetSettings.height / 2;
    
    // Chỉ apply thuật toán nếu current parent có con
    if (!d.source.children || d.source.children.length === 0) {
        // Node không có con → đường đơn giản ở giữa
        const simpleHorizontalY = sourceY + (targetY - sourceY) * 0.7;
        return `M${sourceX},${sourceY}
                L${sourceX},${simpleHorizontalY}
                L${targetX},${simpleHorizontalY}
                L${targetX},${targetY}`;
    }
    
    // Thuật toán mới: Phân phối Y dựa trên hướng đường ngang
    const sourceLevel = d.source.depth;
    
    console.log(`🔧 Processing parent: ${d.source.data.name}, targetX: ${targetX}, sourceX: ${sourceX}`);
    
    // Tính Y bounds cho level này
    const lowBound = sourceY + 15;   // Thấp nhất (gần parent)
    const highBound = targetY - 15;  // Cao nhất (gần child)
    
    // Xác định hướng của đường ngang dựa trên trung bình X của children
    const avgChildX = d.source.children.reduce((sum, child) => sum + child.x, 0) / d.source.children.length;
    const direction = avgChildX - sourceX;
    
    // Tìm tất cả parent nodes ở cùng level VÀ CÓ CON, phân loại theo hướng
    const allParentsAtLevel = root.descendants()
        .filter(node => node.depth === sourceLevel && node.children && node.children.length > 0);
    
    const leftToRightParents = [];   // parent.x < avgChild.x (đường đi sang phải)
    const rightToLeftParents = [];   // parent.x > avgChild.x (đường đi sang trái)
    const straightParents = [];      // parent.x ≈ avgChild.x (đường thẳng)
    
    allParentsAtLevel.forEach(parent => {
        const parentAvgChildX = parent.children.reduce((sum, child) => sum + child.x, 0) / parent.children.length;
        const parentDirection = parentAvgChildX - parent.x;
        
        if (Math.abs(parentDirection) < 5) {
            straightParents.push(parent);
        } else if (parentDirection > 0) {
            leftToRightParents.push(parent);  // Đi sang phải
        } else {
            rightToLeftParents.push(parent);  // Đi sang trái
        }
    });
    
    // Sắp xếp từng nhóm theo X của parent
    leftToRightParents.sort((a, b) => a.x - b.x);
    rightToLeftParents.sort((a, b) => a.x - b.x);
    straightParents.sort((a, b) => a.x - b.x);
    
    console.log(`📊 Direction groups: L→R: ${leftToRightParents.length}, R→L: ${rightToLeftParents.length}, Straight: ${straightParents.length}`);
    
    // Xác định vị trí Y cho current parent
    let horizontalY;
    
    if (Math.abs(direction) < 5) {
        // Straight: ở giữa
        const parentPosition = straightParents.findIndex(p => p === d.source);
        const totalStraight = straightParents.length;
        
        if (totalStraight === 1) {
            horizontalY = lowBound + (highBound - lowBound) / 2;
        } else {
            const spacing = (highBound - lowBound) / (totalStraight + 1);
            horizontalY = lowBound + ((parentPosition + 1) * spacing);
        }
        console.log(`⬇️ Straight parent ${parentPosition + 1}/${totalStraight}: Y=${horizontalY.toFixed(1)}`);
        
    } else if (direction < 0) {
        // Right-to-left: Y thấp nhất (gần parent)
        const parentPosition = rightToLeftParents.findIndex(p => p === d.source);
        const totalRightToLeft = rightToLeftParents.length;
        
        if (totalRightToLeft === 1) {
            horizontalY = lowBound + 10;  // Gần parent nhất
        } else {
            const availableSpace = (highBound - lowBound) / 2;  // Chia 1/2 không gian cho R→L
            const spacing = availableSpace / totalRightToLeft;
            horizontalY = lowBound + ((parentPosition + 0.5) * spacing);
        }
        console.log(`➡️ R→L parent ${parentPosition + 1}/${totalRightToLeft}: Y=${horizontalY.toFixed(1)} (thấp)`);
        
    } else {
        // Left-to-right: Y cao nhất (gần child)
        const parentPosition = leftToRightParents.findIndex(p => p === d.source);
        const totalLeftToRight = leftToRightParents.length;
        
        if (totalLeftToRight === 1) {
            horizontalY = highBound - 10;  // Gần child nhất
        } else {
            const availableSpace = (highBound - lowBound) / 2;  // Chia 1/2 không gian cho L→R
            const spacing = availableSpace / totalLeftToRight;
            horizontalY = highBound - ((parentPosition + 0.5) * spacing);
        }
        console.log(`⬅️ L→R parent ${parentPosition + 1}/${totalLeftToRight}: Y=${horizontalY.toFixed(1)} (cao)`);
    }
    
    // Bounds checking cuối cùng
    horizontalY = Math.max(horizontalY, lowBound);
    horizontalY = Math.min(horizontalY, highBound);
    
    console.log(`✅ Final Y for ${d.source.data.name}: ${horizontalY.toFixed(1)}`);
    
    return `M${sourceX},${sourceY}
            L${sourceX},${horizontalY}
            L${targetX},${horizontalY}
            L${targetX},${targetY}`;
}
