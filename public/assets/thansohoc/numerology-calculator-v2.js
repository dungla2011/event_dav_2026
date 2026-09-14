class NumerologyV2 {
    constructor(fullname, birthdateStr, numerologyData = null) {
        // fullname: "Lương Trường Sinh"
        // birthdateStr: "19/01/2001" hoặc "2001-01-19"
        this.fullname = fullname || "";
        this.birth = this._parseDate(birthdateStr);
        this.data = numerologyData;
        
        // Cấu hình tập trung cho V2 (công thức khác V1)
        this.numberTypes = [
            {
                icon: '🌟',
                text: 'Đường Đời',
                textEn: 'Life Path',
                property: 'lifePath',
                method: 'getLifePath',
                badge: 'bg-orange',
                useBirthDate: true,
                hint: 'Sứ mệnh cốt lõi'
            },
            
            {
                icon: '✨',
                text: 'Sứ Mệnh',
                textEn: 'Expression/Destiny',
                property: 'expression',
                method: 'getExpression',
                badge: 'bg-orange',
                useBirthDate: false,
                hint: 'Tài năng bẩm sinh'
            },
            {
                icon: '🌈',
                text: 'Trưởng Thành',
                textEn: 'Maturity',
                property: 'maturity',
                method: 'getMaturity',
                badge: 'bg-orange',
                useBirthDate: false,
                hint: 'Mục tiêu sau 40 tuổi'
            },
            {
                icon: '🎭',
                text: 'Tương Tác (Nhân Cách)',
                textEn: 'Personality',
                property: 'personality',
                method: 'getPersonality',
                badge: 'bg-royalblue',
                useBirthDate: false,
                hint: 'Ấn tượng bên ngoài'
            },
            {
                icon: '�',
                text: 'Thái Độ',
                textEn: 'Attitude',
                property: 'attitude',
                method: 'getAttitude',
                badge: 'bg-royalblue',
                useBirthDate: true,
                hint: 'Cách nhìn cuộc sống'
            },
            {
                icon: '💫',
                text: 'Nội Tâm (Linh Hồn)',
                textEn: 'Soul Urge',
                property: 'soulUrge',
                method: 'getSoulUrge',
                badge: 'bg-purple',
                useBirthDate: false,
                hint: 'Khát khao bên trong'
            },
            {
                icon: '🔢',
                text: 'Nội Cảm',
                textEn: 'Inner Self',
                property: 'innerSelf',
                method: 'getInnerSelf',
                badge: 'bg-purple',
                useBirthDate: false,
                hint: 'Số xuất hiện nhiều nhất'
            },
            {
                icon: '🚪',
                text: 'Năng Lực Tiếp Cận',
                textEn: 'Approach',
                property: 'approach',
                method: 'getApproach',
                badge: 'bg-purple',
                useBirthDate: false,
                hint: 'Tiếp cận người/việc mới'
            },
            {
                icon: '🧠',
                text: 'Trí Tuệ',
                textEn: 'Intelligence',
                property: 'intelligence',
                method: 'getIntelligence',
                badge: 'bg-purple',
                useBirthDate: false,
                hint: 'Cách phân tích vấn đề'
            },
            {
                icon: '⚖️',
                text: 'Cân Bằng',
                textEn: 'Balance',
                property: 'balance',
                method: 'getBalance',
                badge: 'bg-purple',
                useBirthDate: false,
                hint: 'Cách giải quyết vấn đề'
            },
            {
                icon: '❌',
                text: 'Thiếu Vắng',
                textEn: 'Missing Numbers',
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
            return this[type.method]();
        }
        return null;
    }

    // ========== PUBLIC ==========
    getLifePath() {
        const { day, month, year } = this.birth;
        const digits = `${this._pad(day)}${this._pad(month)}${year}`;
        return this._reduceNumber(this._sumDigits(digits));
    }

    getExpression() { // Sứ mệnh / Destiny
        const txt = this._normalize(this.fullname);
        const total = this._lettersToNumbers(txt)
            .reduce((a, b) => a + b, 0);
        return this._reduceNumber(total);
    }

    getSoulUrge() { // Nội tâm (Linh Hồn): nguyên âm toàn bộ HỌ TÊN
        const txt = this._normalize(this.fullname);
        const cleanTxt = txt.replace(/[^A-Z]/g, "");
        
        console.log('=== DEBUG NỘI TÂM ===');
        console.log('Họ tên gốc:', this.fullname);
        console.log('Sau chuẩn hóa:', cleanTxt);
        
        let total = 0;
        const vowelDetails = [];
        
        for (let i = 0; i < cleanTxt.length; i++) {
            const char = cleanTxt[i];
            const prevChar = i > 0 ? cleanTxt[i - 1] : null;
            const nextChar = i < cleanTxt.length - 1 ? cleanTxt[i + 1] : null;
            
            if (this._isVowel(char, prevChar, nextChar)) {
                const num = this._letterToNumber(char);
                total += num;
                vowelDetails.push(`${char} = ${num}`);
            }
        }
        
        console.log('Các chữ nguyên âm:', vowelDetails.join(', '));
        console.log('Tổng:', total);
        
        const result = this._reduceNumber(total);
        console.log('Kết quả sau rút gọn:', result);
        console.log('===================');
        
        return result;
    }

    getInnerSelf() { // Nội Cảm: Số xuất hiện nhiều nhất trong biểu đồ tên
        const txt = this._normalize(this.fullname);
        const frequency = {};
        
        // Đếm tần suất xuất hiện của mỗi số
        for (let char of txt) {
            const num = this._letterToNumber(char);
            if (num > 0) {
                frequency[num] = (frequency[num] || 0) + 1;
            }
        }
        
        // Tìm số xuất hiện nhiều nhất
        let maxCount = 0;
        let innerSelf = 1;
        
        for (let num in frequency) {
            if (frequency[num] > maxCount) {
                maxCount = frequency[num];
                innerSelf = parseInt(num);
            }
        }
        
        return innerSelf;
    }

    getApproach() { // Năng Lực Tiếp Cận: Tất cả chữ cái trong TÊN GỌI (từ cuối)
        const txt = this._normalize(this.fullname);
        const parts = txt.split(/\s+/).filter(Boolean);
        
        // Lấy tên gọi (từ cuối cùng)
        if (parts.length === 0) return 1;
        const firstName = parts[parts.length - 1];
        
        const nums = this._lettersToNumbers(firstName);
        const total = nums.reduce((a, b) => a + b, 0);
        return this._reduceNumber(total);
    }

    getIntelligence() { // Trí Tuệ: Nguyên âm trong TÊN GỌI
        const txt = this._normalize(this.fullname);
        const parts = txt.split(/\s+/).filter(Boolean);
        
        // Lấy tên gọi (từ cuối cùng)
        if (parts.length === 0) return 1;
        const firstName = parts[parts.length - 1];
        
        let total = 0;
        for (let i = 0; i < firstName.length; i++) {
            const char = firstName[i];
            const prevChar = i > 0 ? firstName[i - 1] : null;
            const nextChar = i < firstName.length - 1 ? firstName[i + 1] : null;
            
            if (this._isVowel(char, prevChar, nextChar)) {
                total += this._letterToNumber(char);
            }
        }
        
        return this._reduceNumber(total);
    }

    getPersonality() { // Tương Tác (Nhân Cách): phụ âm toàn bộ HỌ TÊN
        const txt = this._normalize(this.fullname);
        const cleanTxt = txt.replace(/[^A-Z]/g, "");
        
        let total = 0;
        for (let i = 0; i < cleanTxt.length; i++) {
            const char = cleanTxt[i];
            const prevChar = i > 0 ? cleanTxt[i - 1] : null;
            const nextChar = i < cleanTxt.length - 1 ? cleanTxt[i + 1] : null;
            
            // Nếu KHÔNG phải nguyên âm thì là phụ âm
            if (!this._isVowel(char, prevChar, nextChar)) {
                total += this._letterToNumber(char);
            }
        }
        
        return this._reduceNumber(total);
    }

    getMaturity() { // Trưởng thành = Đường đời + Sứ mệnh
        const lp = this.getLifePath();
        const ex = this.getExpression();
        return this._reduceNumber(lp + ex);
    }

    getBalance() { // Cân bằng: chữ cái đầu của mỗi từ
        // Lương Trường Sinh -> L, T, S
        const name = this._normalize(this.fullname);
        const parts = name.split(/\s+/).filter(Boolean);
        const letters = parts.map(p => p[0]);
        const total = letters
            .map(ch => this._letterToNumber(ch))
            .reduce((a, b) => a + b, 0);
        return this._reduceNumber(total);
    }

    getAttitude() { 
        // Thái độ = ngày + tháng sinh, giữ master
        const { day, month } = this.birth;
        
        console.log('=== DEBUG THÁI ĐỘ ===');
        console.log('Ngày sinh:', day);
        console.log('Tháng sinh:', month);
        console.log('Tổng:', day + month);
        
        const result = this._reduceNumber(day + month);
        console.log('Kết quả sau rút gọn:', result);
        console.log('===================');
        
        return result;
    }

    getPersonalYear(currentYear) {
        const { day, month } = this.birth;
        const digits = `${this._pad(day)}${this._pad(month)}${currentYear}`;
        return this._reduceNumber(this._sumDigits(digits));
    }

    getPersonalMonth(currentYear, month) {
        const py = this.getPersonalYear(currentYear);
        return this._reduceNumber(py + month);
    }

    // ========== HÀNH TRÌNH CUỘC ĐỜI ==========
    
    /**
     * 1. BA GIAI ĐOẠN PHÁT TRIỂN
     */
    getLifeStages() {
        const lifePath = this.getLifePath();
        const endYouth = 36 - lifePath;
        const endAdult = endYouth + 27;
        
        const monthReduced = this._reduceToSingleDigit(this.birth.month);
        const dayReduced = this._reduceToSingleDigit(this.birth.day);
        const yearReduced = this._reduceToSingleDigit(this._sumDigits(this.birth.year.toString()));
        
        return {
            youth: {
                startAge: 0,
                endAge: endYouth,
                number: monthReduced,
                label: 'Thiếu Niên'
            },
            adult: {
                startAge: endYouth + 1,
                endAge: endAdult,
                number: dayReduced,
                label: 'Trưởng Thành'
            },
            mature: {
                startAge: endAdult + 1,
                number: yearReduced,
                label: 'Viên Mãn'
            }
        };
    }
    
    /**
     * 2. BỐN ĐỈNH CAO (4 PINNACLES)
     */
    getPinnacles() {
        const lifePath = this.getLifePath();
        const agePinnacle1 = 36 - lifePath;
        const agePinnacle2 = agePinnacle1 + 9;
        const agePinnacle3 = agePinnacle2 + 9;
        const agePinnacle4 = agePinnacle3 + 9;
        
        const dayReduced = this._reduceToSingleDigit(this.birth.day);
        const monthReduced = this._reduceToSingleDigit(this.birth.month);
        const yearReduced = this._reduceToSingleDigit(this._sumDigits(this.birth.year.toString()));
        
        // Đỉnh 1 và 2: rút gọn về 1 chữ số
        const pinnacle1 = this._reduceToSingleDigit(monthReduced + dayReduced);
        const pinnacle2 = this._reduceToSingleDigit(dayReduced + yearReduced);
        
        // Đỉnh 3 và 4: giữ nguyên nếu ≤ 12, nếu > 12 thì rút gọn (nhưng tối đa là 11)
        let pinnacle3 = pinnacle1 + pinnacle2;
        if (pinnacle3 > 12) {
            pinnacle3 = this._reduceToSingleDigit(pinnacle3);
        }
        
        let pinnacle4 = monthReduced + yearReduced;
        if (pinnacle4 > 12) {
            pinnacle4 = this._reduceToSingleDigit(pinnacle4);
        }
        
        return [
            {
                number: pinnacle1,
                age: agePinnacle1,
                year: this.birth.year + agePinnacle1,
                label: 'Đỉnh 1'
            },
            {
                number: pinnacle2,
                age: agePinnacle2,
                year: this.birth.year + agePinnacle2,
                label: 'Đỉnh 2'
            },
            {
                number: pinnacle3,
                age: agePinnacle3,
                year: this.birth.year + agePinnacle3,
                label: 'Đỉnh 3'
            },
            {
                number: pinnacle4,
                age: agePinnacle4,
                year: this.birth.year + agePinnacle4,
                label: 'Đỉnh 4'
            }
        ];
    }
    
    /**
     * 3. BỐN THỬ THÁCH (4 CHALLENGES)
     */
    getChallenges() {
        const dayReduced = this._reduceToSingleDigit(this.birth.day);
        const monthReduced = this._reduceToSingleDigit(this.birth.month);
        const yearReduced = this._reduceToSingleDigit(this._sumDigits(this.birth.year.toString()));
        
        const challenge1 = Math.abs(dayReduced - monthReduced);
        const challenge2 = Math.abs(dayReduced - yearReduced);
        const challenge3 = Math.abs(challenge1 - challenge2);
        const challenge4 = Math.abs(monthReduced - yearReduced);
        
        const pinnacles = this.getPinnacles();
        
        return [
            {
                number: challenge1,
                period: `Từ sinh đến ${pinnacles[0].age} tuổi`,
                label: 'Thử thách 1'
            },
            {
                number: challenge2,
                period: `Từ ${pinnacles[0].age} đến ${pinnacles[1].age} tuổi`,
                label: 'Thử thách 2'
            },
            {
                number: challenge3,
                period: `Từ ${pinnacles[1].age} đến ${pinnacles[2].age} tuổi`,
                label: 'Thử thách 3'
            },
            {
                number: challenge4,
                period: `Từ ${pinnacles[2].age} tuổi trở đi`,
                label: 'Thử thách 4'
            }
        ];
    }


    getAll(currentYear = (new Date()).getFullYear()) {
        const result = {
            fullName: this.fullname,
            birthDate: `${this.birth.day}/${this.birth.month}/${this.birth.year}`
        };
        
        // Tính toán tất cả các số chính
        this.numberTypes.forEach(type => {
            if (typeof this[type.method] === 'function') {
                result[type.property] = this[type.method]();
            }
        });
        
        // Thêm Personal Year và Personal Months
        result.personalYear = this.getPersonalYear(currentYear);
        result.personalMonths = Array.from({ length: 12 }, (_, i) => ({
            month: i + 1,
            value: this.getPersonalMonth(currentYear, i + 1)
        }));
        
        // Thêm Hành Trình Cuộc Đời (Life Journey)
        result.lifeStages = this.getLifeStages();
        result.pinnacles = this.getPinnacles();
        result.challenges = this.getChallenges();
        
        return result;
    }

    // ========== PRIVATE ==========

    _parseDate(str) {
        // cho phép "19/01/2001" hoặc "2001-01-19"
        if (!str) {
            const d = new Date();
            return { day: d.getDate(), month: d.getMonth() + 1, year: d.getFullYear() };
        }
        if (str.includes("/")) {
            const [d, m, y] = str.split("/").map(Number);
            return { day: d, month: m, year: y };
        } else if (str.includes("-")) {
            const [y, m, d] = str.split("-").map(Number);
            return { day: d, month: m, year: y };
        }
        throw new Error("Định dạng ngày không hợp lệ");
    }

    _pad(n) {
        return n < 10 ? "0" + n : "" + n;
    }

    _sumDigits(str) {
        return str.split("").reduce((a, c) => a + (parseInt(c, 10) || 0), 0);
    }

    _reduceNumber(n) {
        // giữ 11, 22, 33
        while (n > 9 && n !== 11 && n !== 22 && n !== 33) {
            n = this._sumDigits(String(n));
        }
        return n;
    }

    _normalize(txt) {
        // bỏ dấu tiếng Việt, chuyển hoa
        return txt
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/đ/gi, "d")
            .toUpperCase();
    }

    _isVowel(char, prevChar, nextChar) {
        // Quy tắc nguyên âm đặc biệt cho Y:
        // - Y là nguyên âm khi: đứng 1 mình HOẶC trước và sau Y là phụ âm
        // - Y là phụ âm khi: trước hoặc sau Y có nguyên âm
        const basicVowels = ["A", "E", "I", "O", "U"];
        
        if (basicVowels.includes(char)) {
            return true;
        }
        
        if (char === "Y") {
            // Kiểm tra ký tự trước và sau
            const prevIsVowel = prevChar && basicVowels.includes(prevChar);
            const nextIsVowel = nextChar && basicVowels.includes(nextChar);
            
            // Y là phụ âm nếu trước HOẶC sau là nguyên âm
            if (prevIsVowel || nextIsVowel) {
                return false; // Y là phụ âm
            }
            
            // Ngược lại, Y là nguyên âm
            return true;
        }
        
        return false;
    }
    
    /**
     * Rút gọn về 1 chữ số (KHÔNG giữ Master Numbers)
     */
    _reduceToSingleDigit(num) {
        while (num > 9) {
            num = this._sumDigits(num.toString());
        }
        return num;
    }

    _letterToNumber(char) {
        // Bảng Pitago
        const map = {
            1: ["A", "J", "S"],
            2: ["B", "K", "T"],
            3: ["C", "L", "U"],
            4: ["D", "M", "V"],
            5: ["E", "N", "W"],
            6: ["F", "O", "X"],
            7: ["G", "P", "Y"],
            8: ["H", "Q", "Z"],
            9: ["I", "R"]
        };
        for (const [num, arr] of Object.entries(map)) {
            if (arr.includes(char)) return parseInt(num, 10);
        }
        return 0;
    }

    _lettersToNumbers(txt, filterFn = null) {
        const letters = txt.replace(/[^A-Z]/g, "").split("");
        return letters
            .filter(ch => (filterFn ? filterFn(ch) : true))
            .map(ch => this._letterToNumber(ch));
    }
    
    /**
     * Tìm Số Thiếu Vắng - Các số từ 1-9 KHÔNG có trong họ tên
     */
    findMissingNumbers() {
        const numbersPresent = new Set();
        const txt = this._normalize(this.fullname);
        
        console.log('=== DEBUG THIẾU VẮNG ===');
        console.log('Họ tên:', this.fullname);
        console.log('Sau chuẩn hóa:', txt);
        
        for (let char of txt) {
            const num = this._letterToNumber(char);
            if (num > 0) {
                numbersPresent.add(num);
                console.log(`${char} → ${num}`);
            }
        }
        
        console.log('Các số có trong tên:', Array.from(numbersPresent).sort());
        
        const missing = [];
        for (let i = 1; i <= 9; i++) {
            if (!numbersPresent.has(i)) {
                missing.push(i);
            }
        }
        
        console.log('Các số THIẾU VẮNG:', missing);
        
        return missing;
    }
}

// ====== DEMO ======
// const calc = new NumerologyV2("Lương Trường Sinh", "19/01/2001");
// console.log(calc.getAll(2025));
