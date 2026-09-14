/**
 * Numerology Calculator V1
 * Class tính toán các con số thần số học theo phương pháp Pythagoras
 */
class NumerologyV1 {
    constructor(numerologyData) {
        this.data = numerologyData;
        
        // Cấu hình tập trung cho tất cả các loại số (Single Source of Truth)
        this.numberTypes = [
            {
                icon: '🌟',
                text: 'Đường Đời',
                property: 'lifePath',
                method: 'calculateLifePath',
                badge: 'bg-orange',
                useBirthDate: true,
                hint: 'Sứ mệnh cốt lõi'
            },
            {
                icon: '🌈',
                text: 'Trưởng Thành',
                property: 'maturityNumber',
                method: 'calculateMaturityNumber',
                badge: 'bg-orange',
                useBirthDate: false,
                hint: 'Mục tiêu sau 40 tuổi'
            },
            {
                icon: '💫',
                text: 'Linh Hồn',
                property: 'soulNumber',
                method: 'calculateSoulNumber',
                badge: 'bg-purple',
                useBirthDate: false,
                hint: 'Động lực nội tâm'
            },
            {
                icon: '⚖️',
                text: 'Cân Bằng',
                property: 'balanceNumber',
                method: 'calculateBalanceNumber',
                badge: 'bg-purple',
                useBirthDate: false,
                hint: 'Cách giải quyết vấn đề'
            },
            {
                icon: '🎭',
                text: 'Tương Tác',
                property: 'personalityNumber',
                method: 'calculatePersonalityNumber',
                badge: 'bg-royalblue',
                useBirthDate: false,
                hint: 'Ấn tượng bên ngoài'
            },
            {
                icon: '😊',
                text: 'Thái Độ',
                property: 'attitudeNumber',
                method: 'calculateAttitudeNumber',
                badge: 'bg-royalblue',
                useBirthDate: true,
                hint: 'Cách nhìn cuộc sống'
            },
            {
                icon: '📅',
                text: 'Ngày Sinh',
                property: 'birthDayNumber',
                method: 'calculateBirthDayNumber',
                badge: 'bg-secondary',
                useBirthDate: true,
                hint: 'Ngày trong tháng'
            },
            {
                icon: '❌',
                text: 'Thiếu Vắng',
                property: 'missingNumbers',
                method: 'findMissingNumbers',
                badge: 'bg-secondary',
                useBirthDate: false,
                hint: 'Cần phát triển'
            }
        ];
        
        // Tạo các map từ numberTypes (generated từ source duy nhất)
        this.iconMap = {};
        this.numberTypeMap = {};
        this.propertyMap = {};
        this.badgeColors = {};
        this.tableColumns = [];
        
        this.numberTypes.forEach(type => {
            const displayText = `${type.icon} ${type.text}`;
            this.iconMap[type.icon] = type.text;
            this.numberTypeMap[type.text] = type.method;
            this.propertyMap[type.property] = displayText;
            this.badgeColors[displayText] = type.badge;
            this.tableColumns.push(displayText);
        });
    }
    
    /**
     * Lấy method name từ text hiển thị hoặc icon
     */
    getMethodName(displayText) {
        // Nếu là icon, chuyển sang text
        if (this.iconMap[displayText]) {
            displayText = this.iconMap[displayText];
        }
        return this.numberTypeMap[displayText];
    }
    
    /**
     * Tính số theo tên hiển thị
     */
    calculateByDisplayName(displayText, fullName, birthDate) {
        // Tìm type config
        const type = this.numberTypes.find(t => 
            displayText.includes(t.icon) || displayText.includes(t.text)
        );
        
        if (type && typeof this[type.method] === 'function') {
            // Dùng useBirthDate để xác định tham số
            return type.useBirthDate ? this[type.method](birthDate) : this[type.method](fullName);
        }
        return null;
    }

    /**
     * Chuyển đổi chữ cái thành số
     */
    letterToNumber(letter) {
        const upperLetter = letter.toUpperCase();
        if (this.data && this.data.bangChuCai[upperLetter]) {
            return this.data.bangChuCai[upperLetter];
        }
        return 0;
    }

    /**
     * Kiểm tra nguyên âm
     */
    isVowel(letter) {
        const upperLetter = letter.toUpperCase();
        return this.data && this.data.nguyenAm.includes(upperLetter);
    }

    /**
     * Rút gọn số (giữ Master Numbers: 11, 22, 33)
     */
    reduceNumber(num) {
        while (num > 9 && num !== 11 && num !== 22 && num !== 33) {
            num = num.toString().split('').reduce((sum, digit) => sum + parseInt(digit), 0);
        }
        return num;
    }

    /**
     * Tính Số Đường Đời (Cách 2 - Chuẩn quốc tế, giữ Master Number)
     */
    calculateLifePath(birthDate) {
        const date = new Date(birthDate);
        let day = date.getDate();
        let month = date.getMonth() + 1;
        let year = date.getFullYear();
        
        // Rút gọn ngày (giữ Master Number)
        day = this.reduceNumber(day);
        
        // Rút gọn tháng (giữ Master Number)
        month = this.reduceNumber(month);
        
        // Rút gọn năm (giữ Master Number)
        year = this.reduceNumber(year);
        
        // Cộng tổng và rút gọn lần cuối
        const total = day + month + year;
        return this.reduceNumber(total);
    }

    /**
     * Tính Số Linh Hồn (nguyên âm)
     */
    calculateSoulNumber(name) {
        let total = 0;
        for (let char of name) {
            if (this.isVowel(char)) {
                total += this.letterToNumber(char);
            }
        }
        return this.reduceNumber(total);
    }

    /**
     * Tính Số Tương Tác (phụ âm)
     */
    calculatePersonalityNumber(name) {
        let total = 0;
        for (let char of name) {
            const num = this.letterToNumber(char);
            if (num > 0 && !this.isVowel(char)) {
                total += num;
            }
        }
        return this.reduceNumber(total);
    }

    /**
     * Tính Số Trưởng Thành (tất cả chữ cái)
     */
    calculateMaturityNumber(name) {
        let total = 0;
        for (let char of name) {
            total += this.letterToNumber(char);
        }
        return this.reduceNumber(total);
    }

    /**
     * Tính Số Thái Độ (không giữ Master Number)
     */
    calculateAttitudeNumber(birthDate) {
        const date = new Date(birthDate);
        const day = date.getDate();
        const month = date.getMonth() + 1;
        
        let total = day + month;
        // Số thái độ không giữ Master Number
        while (total > 9) {
            total = total.toString().split('').reduce((sum, digit) => sum + parseInt(digit), 0);
        }
        return total;
    }

    /**
     * Tính Số Ngày Sinh
     */
    calculateBirthDayNumber(birthDate) {
        const date = new Date(birthDate);
        return date.getDate();
    }

    /**
     * Tính Số Cân Bằng
     */
    calculateBalanceNumber(name) {
        // Lấy tên (first name)
        const firstName = name.trim().split(' ').pop();
        return firstName.length;
    }

    /**
     * Tìm Số Thiếu Vắng
     */
    findMissingNumbers(name) {
        const numbersPresent = new Set();
        
        for (let char of name) {
            const num = this.letterToNumber(char);
            if (num > 0) {
                numbersPresent.add(num);
            }
        }
        
        const missing = [];
        for (let i = 1; i <= 9; i++) {
            if (!numbersPresent.has(i)) {
                missing.push(i);
            }
        }
        
        return missing;
    }

    /**
     * Tạo biểu đồ kim tự tháp
     */
    createPyramidChart(name) {
        const numbers = [];
        for (let char of name) {
            const num = this.letterToNumber(char);
            if (num > 0) {
                numbers.push(num);
            }
        }
        
        // Đếm số lần xuất hiện
        const frequency = {};
        for (let i = 1; i <= 9; i++) {
            frequency[i] = numbers.filter(n => n === i).length;
        }
        
        return frequency;
    }

    /**
     * Tính toán tất cả các số từ tên và ngày sinh
     */
    calculateAllNumbers(fullName, birthDate) {
        return {
            fullName,
            birthDate,
            lifePath: this.calculateLifePath(birthDate),
            soulNumber: this.calculateSoulNumber(fullName),
            personalityNumber: this.calculatePersonalityNumber(fullName),
            maturityNumber: this.calculateMaturityNumber(fullName),
            attitudeNumber: this.calculateAttitudeNumber(birthDate),
            birthDayNumber: this.calculateBirthDayNumber(birthDate),
            balanceNumber: this.calculateBalanceNumber(fullName),
            missingNumbers: this.findMissingNumbers(fullName),
            pyramidChart: this.createPyramidChart(fullName)
        };
    }
}
