@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
Số hoá Dự án Kinh Doanh
@endsection

@section("content")

    <style>
        footer {
            display: none;
        }
        .tbl_sum1 td {
            padding: 10px;
        }
        .tbl_sum1 tr {
            display: none;
        }
        .table_01 input{
            color: #007bff;
        }
        .table_01 td{
            padding-right: 10px!important;
        }
        .row-san-luong-ban-du-kien-thang input {
            color: #007bff;
        }
        /*  Not readonly ? */
        #tableBody-cost  input:not([readonly]) {
            color: #007bff;
        }
        .input-marketing-metrics, .input-sales-metrics{
            width: 95%;
            border: 1px solid #ccc;
            /* background-color: lavenderblush; */
        }

        .input-marketing-metrics[readonly], .input-sales-metrics[readonly]{
            border: 0px;
            /* background-color: transparent; */
        }




        /* Multi-scenario table styling */
        .project_detail.mt-5 {
            border-collapse: collapse;
        }
        .project_detail.mt-5 td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .project_detail.mt-5 td:first-child {
            text-align: left;
            font-weight: bold;
            background-color: #f8f9fa;
            width: 250px;
        }
        .project_detail.mt-5 input {
            width: 80px;
            text-align: center;
            border: 1px solid #ccc;
            padding: 4px;
        }

        .project_detail.table_03 td{
            padding-right: 10px;
        }

        .col-cost.x1{}
        .col-cost input,
        .col-quantity input,
        .col-total div ,
        .col-monthly-allocation div,
        .col-yearly-allocation div,
        .col-depreciation input {
            text-align: right;
        }

        input, select, textarea {
            -webkit-appearance: auto!important;
        }
        .col-check.new-row input {
            /*display: none;*/
        }
        .col-check {
            text-align: center;
            width: 50px;
        }
        .col-stt {
            width: 60px;
        }
        .col-stt div{
            text-align: center;
        }

        .toolbar {
            background: #f8f9fa;
            padding: 15px 20px;
            border: 1px solid #dee2e6;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .table-container {
            /*overflow-x: auto;*/
            /*max-height: 80vh;*/
        }

        .excel-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .excel-table th {
            background: #e9ecef;
            border: 1px solid #dee2e6;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .excel-table td {
            border: 1px solid #dee2e6;
            padding: 0;
            /*height: 35px;*/
        }

        .excel-table tr:nth-child(even) {
            /*background-color: #f8f9fa;*/
        }

        .excel-table tr:hover td{
            /*background-color: #e3f2fd !important;*/
            border-bottom-color: gray;
            border-top-color: gray;
        }

        .cell-input {
            width: 100%;
            height: 100%;
            border: none;
            outline: none;
            padding: 8px;
            background: white; /* Editable cells - màu trắng như Google Sheets */
            font-size: 14px;
        }

        .cell-input:focus {
            background: #fff3cd;
            border: 2px solid #ffc107;
            box-shadow: 0 0 0 1px #ffc107;
        }

        .readonly {
            background: #e9ecef !important;
            color: #6c757d;
        }

        /* Calculated/Display cells - không nhập được */
        .calculated-cell {
            /*background: #f8f9fa !important;*/
            /*border: 1px solid #e9ecef;*/
            text-align: right;
            padding: 8px;
            font-weight: 500;
            border-radius: 3px;
        }

        .total-cell {
            /*background: #e8f5e9 !important; !* Light green cho total *!*/
            /* color: brown; */
        }

        .monthly-allocation-cell {
            /* background: #e3f2fd !important;  */
            /* color: #1976d2; */
        }

        .new-row {
            background-color: #e8f5e8 !important;
        }

        .new-row:hover {
            background-color: white !important;
        }

        /* Hide category column for specific tabs */
        .hide-category-column .col-category {
            display: none !important;
        }

        /* Excel-like cell selection styles */
        .cell-selected {
            background-color: #cce7ff !important;
            border: 2px solid #0066cc !important;
        }

        .cell-editing {
            background-color: #fff3cd !important;
            border: 2px solid #ffc107 !important;
        }

        .row-actions {
            display: flex;
            gap: 5px;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .action-btn {
            padding: 4px 8px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }
        .col-name {
            min-width: 200px;
        }

        .save-btn {
            background: #28a745;
        }

        .project_detail.tbl02 td{
            padding-right: 10px!important;
            text-align: right;
        }


        /* Project Detail Table Styles */
        .project_detail {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .project_detail tr {
            border-bottom: 1px solid #dee2e6;
        }

        .project_detail tr.section-header {
            background: #f8f9fa;
        }

        .project_detail tr.summary-row {
            background: #e9ecef;
        }

        .project_detail tr.highlight-row {
            background: #e8f5e9;
        }

        /* Monthly Input Controls */
        .monthly-input-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }

        .monthly-input-container input {
            width: 100%;
            text-align: right;
            padding: 5px;
            border: 1px solid #ccc;
            font-family: 'Courier New', Consolas, monospace;
            transition: all 0.2s;
        }

        .monthly-input-container input.locked {
            background: #fff9c4;
            border: 2px solid #f9a825;
            font-weight: bold;
        }

        .phan_bo_tung_thang td {
            font-family: 'Courier New', Consolas, monospace;
        }

        .monthly-btn-group {
            display: flex;
            gap: 3px;
            justify-content: center;
        }

        .project_detail.table_06 tbody td{
            font-size: 12px;
            padding: 5px;;
        }

        .monthly-btn {
            width: 22px;
            height: 20px;
            border: 1px solid #999;
            background: #e0e0e0;
            color: #333;
            cursor: pointer;
            border-radius: 2px;
            font-size: 12px;
            font-weight: normal;
        }

        .monthly-btn:hover {
            background: #d0d0d0;
        }

        .project_detail tr.thick-border {
            border-bottom: 2px solid #dee2e6;
        }

        .project_detail td {
            padding: 8px 10px!important;
            border-right: 1px solid #dee2e6;
        }
        .project_detail.table_06 tbody td {
            padding: 8px 5px!important;
        }

        .project_detail tr[class*="row-"] input{
            width: 100%;
            height: 100%;
            border: 0px solid #eee;
            text-align: right;
            /* padding-right: 10px; */
        }

        .project_detail td:last-child {
            border-right: none;
            text-align: right;
            /* width: 250px; */
            padding: 0;
        }

        .project_detail.table_06 {
            text-align: right;
        }

        .project_detail td.section-title {
            padding: 12px 15px;
            font-weight: bold;
            color: #495057;
            font-size: 16px;
        }

        .project_detail td.summary-cell {
            font-weight: bold;
        }

        .project_detail td.highlight-cell {
            color: #dc3545;
            font-weight: 500;
        }

        .project_detail td.success-cell {
            font-weight: bold;
            color: #28a745;
        }

        .project_detail input {
            padding-left: 10px;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 350px;
        }

        .toast {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(100%);
            transition: all 0.3s ease;
            border-left: 4px solid #007bff;
            max-width: 100%;
            word-wrap: break-word;
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast.success {
            border-left-color: #28a745;
        }

        .toast.error {
            border-left-color: #dc3545;
        }

        .toast.info {
            border-left-color: #17a2b8;
        }

        .toast-icon {
            font-size: 20px;
            min-width: 20px;
        }

        .toast.success .toast-icon::before {
            content: '✅';
        }

        .toast.error .toast-icon::before {
            content: '❌';
        }

        .toast.info .toast-icon::before {
            content: 'ℹ️';
        }

        .toast-content {
            flex: 1;
            font-size: 14px;
            line-height: 1.4;
        }

        .toast-close {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #6c757d;
            padding: 0;
            margin-left: 8px;
            min-width: 20px;
        }

        .toast-close:hover {
            color: #495057;
        }

        .col-id { width: 40px; display: none; } /* Hidden ID column */
        .col-stt { width: 60px; text-align: center; }

        .col-category { width: 150px; display:auto; }
        .col-cost {
            width: 180px;
            padding: 4px 8px;
            position: relative;
        }

        .col-cost .cell-input {
            margin-bottom: 0;
            display: none; /* Hide the original input */
        }

        /* Hide spinner arrows for number input in cost column */
        .col-cost input[type="number"]::-webkit-outer-spin-button,
        .col-cost input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .col-cost input[type="number"] {
            -moz-appearance: textfield; /* Firefox */
        }

        /* Make cost-display editable */
        .cost-display-editable {
            width: 100%;
            height: 100%;
            border: none;
            outline: none;
            padding: 8px;
            background: white;
            font-size: 14px;
            text-align: right;
            cursor: text;
            min-height: 35px;
            box-sizing: border-box;
        }

        .cost-display-editable:focus {
            background: #fff3cd;
            border: 2px solid #ffc107;
            box-shadow: 0 0 0 1px #ffc107;
        }

        /* Readonly cost input styling for depreciated items */
        .cost-display-editable.readonly-depreciated {
            /*background-color: #ccc !important;*/
            font-style: italic !important;
            /*color: #666 !important;*/
            color: #eee;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }

        .cost-display-editable.readonly-depreciated:focus {
            /*background-color: #ccc !important;*/
            /*border: 1px solid #999 !important;*/
            box-shadow: none !important;
        }

        /* Additional specificity for readonly styling */
        .col-cost .cost-display-editable.readonly-depreciated {
            background-color: #eee !important;
            font-style: italic !important;
            /*color: #666 !important;*/
            color: #eee;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }

        /* Excel-like navigation styles */
        .cell-selected {
            background: #e3f2fd !important;
            border: 2px solid #2196f3 !important;
            box-shadow: 0 0 0 1px #2196f3 !important;
        }

        .cell-editing {
            background: #fff3cd !important;
            border: 2px solid #ffc107 !important;
            box-shadow: 0 0 0 1px #ffc107 !important;
        }

        /* Hide focus outline when not editing */
        .cell-input:not(.cell-editing):focus,
        .cost-display-editable:not(.cell-editing):focus {
            outline: none;
        }

        tr.new-row {
            background-color: white!important;
        }

        #tableBody-fixed tr {
            background-color: white;
        }


        .col-quantity { width: 100px; }
        .col-depreciation { width: 120px; }
        .col-unit-depreciation { width: 140px; }
        .col-monthly-allocation { width: 140px; }
        .col-yearly-allocation { width: 140px; }
        .col-total { width: 120px; }
        .col-note { width: 200px; }
        .col-actions { width: 120px; }

        /* Tab-specific column visibility */
        #tab-variable .col-depreciation { display: none; }
        #tab-variable .col-monthly-allocation { display: none; }

        /* Hide option1 column for all tabs except variable-cost */
        .col-option1 { display: none;
        min-width: 100px;
        }

        /* Show option1 column only in variable-cost tab */
        body[data-current-tab="variable-cost"] .col-option1 { display: table-cell; }



                 /* Dynamic container height */
         .cls_contain {
             transition: height 0.3s ease;
             overflow: visible;
             margin-bottom: 20px; /* Giảm xuống để next_content gần hơn */
             /*min-height: 600px;*/
         }

                 /* Tab System */
         .tab-container {
             margin: 20px 0px;
            margin-bottom: 20px; /* Giảm khoảng cách để next_content gần hơn */
            /* Bỏ min-height để tự động điều chỉnh theo content */
            display: flex;
            flex-direction: column;
         }

        .tab-nav {
            display: flex;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 0;
            align-items: center;
            position: relative;
        }

        .tab-btn {
            padding: 12px 24px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-bottom: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #6c757d;
            transition: all 0.3s ease;
            border-radius: 8px 8px 0 0;
            margin-right: 4px;
        }

        .tab-btn:hover {
            background: #e9ecef;
            color: #495057;
        }

        .tab-btn.active {
            background: #fff;
            color: #007bff;
            border-color: #007bff;
            border-bottom: 2px solid #fff;
            margin-bottom: -2px;
            position: relative;
            z-index: 1;
        }

        .phan_bo_tung_thang td {
            font-size: 10px;
        }
        .tab-btn:disabled {
            background: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .tab-btn:disabled:hover {
            background: #e9ecef;
            color: #6c757d;
        }

        /* Save All Button */
        .save-all-btn {
            margin-left: auto;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);
        }

        .save-all-btn:hover:not(:disabled) {
            background: #218838;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }

        .save-all-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #6c757d;
            transform: none;
            box-shadow: none;
        }

        /* Delete Selected Button */
        .delete-selected-btn {
            margin-left: 10px;
            padding: 6px 10px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
        }

        .delete-selected-btn:hover:not(:disabled) {
            background: #c82333;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }

        .delete-selected-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #6c757d;
            transform: none;
            box-shadow: none;
        }

        /* Auto-save indicator styles */
        .auto-save-indicator {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            z-index: 10;
            pointer-events: none;
            animation: fadeIn 0.3s ease-in;
        }

                 @keyframes fadeIn {
             from { opacity: 0; }
             to { opacity: 1; }
         }

         /* Global Auto-save indicator styles */
         .global-auto-save-indicator {
             position: absolute;
             right: 10px;
             top: 50%;
             transform: translateY(-50%);
             padding: 4px 8px;
             border-radius: 4px;
             background: #f8f9fa;
             border: 1px solid #dee2e6;
             display: flex;
             align-items: center;
             gap: 3px;
             transition: all 0.3s ease;
             z-index: 10;
         }

         .global-auto-save-icon {
             font-size: 12px; /* Size bình thường như indicator ở hàng */
             animation: fadeIn 0.3s ease-in;
         }

         /* Different states for global auto-save */
         .global-auto-save-indicator.pending {
             background: #fff3cd;
             border-color: #ffeaa7;
         }

         .global-auto-save-indicator.saving {
             background: #d1ecf1;
             border-color: #bee5eb;
         }

         .global-auto-save-indicator.success {
             background: #d4edda;
             border-color: #c3e6cb;
         }

         .global-auto-save-indicator.error {
             background: #f8d7da;
             border-color: #f5c6cb;
         }

                 .tab-content {
             display: none;
             background: white;
             /*border-radius: 0 8px 8px 8px;*/
             margin-top:0px!important;
             /* Đảm bảo tab content không overflow */
             overflow: visible;
             position: relative;
         }

         .tab-content.active {
             display: block;
         }

                 .tab-table-container {
             background: white;
             /*border-radius: 8px;*/
             box-shadow: 0 2px 10px rgba(0,0,0,0.1);
             overflow: visible;
             /* Bỏ max-height để không bị đè lên content bên dưới */
        }

                .table-container {
            /* Không dùng max-height và overflow để tránh scroll */
            overflow: visible;
            /* Đảm bảo table không đè lên content bên dưới */
            position: relative;
            z-index: 1;
         }

        /* New Plan Button Styling */
        .new-plan-btn {
            padding: 8px 16px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            white-space: nowrap;
            transition: background-color 0.3s ease;
        }

        .new-plan-btn:hover {
            background: #218838;
        }

        .new-plan-btn:active {
            background: #1e7e34;
        }

        .col-category { width: 150px; }

        /* Select styling for category column */
        .col-category select.cell-input {
            width: 100%;
            height: 100%;
            border: none;
            outline: none;
            padding: 8px;
            background: white;
            font-size: 14px;
            cursor: pointer;
        }

        .col-category select.cell-input:focus {
            background: #fff3cd;
            border: 2px solid #ffc107;
            box-shadow: 0 0 0 1px #ffc107;
        }
    </style>

    <div class="container-fluid cls_contain" style="margin-bottom: 100px;">

        <div class="plan-selector" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <!-- Left Side: Existing Plan Selection -->
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label for="planSelect" style="font-weight: 600; color: #495057; white-space: nowrap;">Chọn Dự án:</label>
                    <select id="planSelect" onchange="handlePlanChangeWrapper()" style="padding: 8px 12px; border: 2px solid #007bff; border-radius: 6px; font-size: 14px; min-width: 200px; background: white;">
                        <option value="">-- Chọn Dự án --</option>
                    </select>
                    <a href="/admin/" style="color: white">.</a>
                </div>

                <!-- Right Side: New Plan Creation -->
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label for="newPlanName" style="font-weight: 600; color: #495057; white-space: nowrap;">Tạo Dự án mới:</label>
                    <input type="text" id="newPlanName" placeholder="Nhập tên Dự án..." style="padding: 8px 12px; border: 2px solid #28a745; border-radius: 6px; font-size: 14px; min-width: 200px;">
                    <button onclick="addNewPlan()" class="new-plan-btn">
                        ➕ Tạo
                    </button>
                </div>
            </div>

            <div style="margin-top: 10px; display: none">
                <span id="planStatus" style="color: #6c757d; font-style: italic;">Đang tải danh sách Dự án...</span>
            </div>
        </div>

        {{--        <div class="toolbar" style="display: none">--}}
        {{--            <button class="btn btn-primary" onclick="loadAllData()" disabled id="refresh-btn">--}}
        {{--                🔄 Refresh All Data--}}
        {{--            </button>--}}
        {{--            <button class="btn btn-success" onclick="saveAllTabs()" disabled id="save-btn">--}}
        {{--                💾 Save All Changes--}}
        {{--            </button>--}}
        {{--            <span style="margin-left: auto; color: #6c757d;">--}}
        {{--                Total Items: <span id="totalItemCount">0</span>--}}
        {{--            </span>--}}
        {{--        </div>--}}

        <!-- Toast Container -->
        <div class="toast-container" id="toastContainer"></div>

        <!-- Tab Navigation -->
        <div class="tab-container">
            <div class="tab-nav">

                <div class="tab-btn active" onclick="switchTab('depreciation')" id="depreciation-tab">
                    📊 Khấu Hao
                </div>

                <div class="tab-btn" onclick="switchTab('fixed-cost')" id="fixed-cost-tab">
                    🏢 Chi phí cố định
                </div>

                <div class="tab-btn" onclick="switchTab('variable-cost')" id="variable-cost-tab">
                    📈 Chi phí biến đổi
                </div>

                <div class="tab-btn" onclick="switchTab('cost')" id="cost-tab">
                    💰 Chi phí Tổng hợp
                </div>

                <!-- Save All Button -->
                <button class="save-all-btn" onclick="saveAllChanges()" disabled id="saveAllBtn" style="display: none">
                    💾 Save All
                </button>

                <!-- Delete Selected Button -->
                <button class="delete-selected-btn" onclick="deleteSelectedRows()" disabled id="deleteSelectedBtn" style="display: none;">
                    🗑️ Delete Selected
                </button>

                <!-- Global Auto-save Status Icon -->
                <div class="global-auto-save-indicator" id="globalAutoSaveIndicator" style="display: none;">
                    <span class="global-auto-save-icon" id="globalAutoSaveIcon">💾</span>
                </div>

{{--                <!-- Test Button for Container Height Adjustment -->--}}
{{--                <button onclick="adjustContainerHeight()" style="margin-left: 10px; padding: 6px 12px; background: #17a2b8; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;" title="Test container height adjustment">--}}
{{--                    🔧 Test--}}
{{--                </button>--}}
            </div>

            <!-- Cost Tab -->
            <div class="tab-content active" id="tab-cost">
                <div class="tab-table-container">
                    <div class="table-container">
                        <table class="excel-table">
                            <thead>
                            <tr>
                                <th class="col-check">
                                    <input type="checkbox" id="selectAllCost" title="Chọn tất cả" onchange="toggleSelectAll('cost')">
                                </th>
                                <th class="col-id">ID</th>
                                <th class="col-stt">STT</th>
                                <th class="col-name">Các chi phí</th>
                                <th class="col-category">Thể loại</th>
                                <th class="col-option1">Lựa chọn</th>
                                <th class="col-cost x0">Giá </th>
                                <th class="col-quantity">Số lượng</th>
                                <th class="col-total">Tổng số </th>
                                <th class="col-depreciation">Số tháng khấu hao</th>
                                <th class="col-unit-depreciation">$ Khấu hao 1 đơn vị</th>
                                <th class="col-monthly-allocation">$ Khấu hao 1 tháng</th>
                                <th class="col-yearly-allocation">$ Khấu hao 1 năm</th>
                                <th class="col-note">Ghi chú</th>
                                <th class="col-actions">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="tableBody-cost">
                            <tr class="loading">
                                <td colspan="14">Loading data...</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped table-hover tbl_sum1">

                    <tbody>

                        <tr class="">
                            <td scope="col" class="text-left">Tổng chi phí khấu hao</td>
                            <td id="tong_chi_phi_khau_hao" class="text-center fw-bold text-info">0</td>
                        </tr>
                        <tr class="">
                            <td scope="col" class="text-left">Chi phí khấu hao Hàng tháng</td>
                            <td id="chi_phi_khau_hao_hang_thang" class="text-center fw-bold text-warning">0</td>
                        </tr>
                        <tr class="show_in_chi_phi_co_dinh1">
                            <td scope="col" class="text-left">Chi phí khấu hao Hàng năm</td>
                            <td id="chi_phi_khau_hao_hang_nam" class="text-center fw-bold text-secondary">0</td>
                        </tr>
                        <tr class="show_in_chi_phi_co_dinh">
                            <td scope="col" class="text-left">Tổng CPCĐ trực tiếp hàng tháng</td>
                            <td id="tong_cpcd_truc_tiep_hang_thang" class="text-center fw-bold text-primary">0</td>
                        </tr>
                        <tr class="show_in_chi_phi_co_dinh">
                            <td scope="col" class="text-left">Tổng CPCĐ trực tiếp hàng năm</td>
                            <td id="tong_cpcd_truc_tiep_hang_nam" class="text-center fw-bold text-success">0</td>
                        </tr>
                        <tr class="">
                            <td scope="col" class="text-left">Tổng chi phí biến đổi hàng tháng</td>
                            <td id="tong_chi_phi_bien_doi_hang_thang" class="text-center fw-bold text-primary">0</td>
                        </tr>
                        <tr class="">
                            <td scope="col" class="text-left">Tổng chi phí biến đổi hàng năm</td>
                            <td id="tong_chi_phi_bien_doi_hang_nam" class="text-center fw-bold text-success">0</td>
                        </tr>
                        <tr class="">
                            <td scope="col" class="text-left">Tổng CPCĐ hàng tháng</td>
                            <td id="tong_cpcd_hang_thang" class="text-center fw-bold text-danger">0</td>
                        </tr>
                        <tr class="">
                            <td scope="col" class="text-left">Tổng CPCĐ hàng năm</td>
                            <td id="tong_cpcd_hang_nam" class="text-center fw-bold text-dark">0</td>
                        </tr>

                        <tr class="show_in_chi_phi_bien_doi">
                            <td scope="col" class="text-left">Tổng CP Bán hàng hàng Tháng</td>
                            <td id="tong_cp_ban_hang_hang_thang" class="text-center fw-bold text-dark">0</td>
                        </tr>
                        <tr class="show_in_chi_phi_bien_doi">
                            <td scope="col" class="text-left">Tổng CP Bán hàng hàng Năm</td>
                            <td id="tong_cp_ban_hang_hang_nam" class="text-center fw-bold text-dark">0</td>
                        </tr>
                        <tr class="show_in_chi_phi_bien_doi">
                            <td scope="col" class="text-left">Tổng CP giá vốn hàng tháng</td>
                            <td id="tong_cp_gia_von_hang_thang" class="text-center fw-bold text-dark">0</td>
                        </tr>
                        <tr class="show_in_chi_phi_bien_doi">
                            <td scope="col" class="text-left">Tổng CP giá vốn hàng năm</td>
                            <td id="tong_cp_gia_von_hang_nam" class="text-center fw-bold text-dark">0</td>
                        </tr>






                    </tbody>
                </table>
            </div>


            <div style="margin-top: 10px; padding: 15px; background: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6; position: relative; z-index: 10; clear: both;">

            <div>
            <h4 style="margin-bottom: 20px; color: #495057;">📊 Phân tích Dự án</h4>
                <div class="project_info">
                    <table class="project_detail table_01">


                        <tr class="row-gia-ban-du-kien-thang">
                            <td>Giá bán dự kiến</td>
                            <td>
                            <input type="text" style=""
                            class="input_gia_ban_du_kien" id='input_gia_ban_du_kien' placeholder="Nhập giá bán dự kiến" value="" >
                            </td>
                        </tr>

                        <tr class="row-luong-ban-du-kien-thang">
                            <td>Lượng bán dự kiến/tháng</td>
                            <td>
                            <input type="text" style=""
                            class="input_luong_ban_du_kien_thang" id='input_luong_ban_du_kien_thang' placeholder="Nhập lượng bán dự kiến hàng tháng" value="" >
                            </td>
                        </tr>

                        <tr class="thick-border row-doanh-thu-du-kien">
                            <td class="">Doanh thu dự kiến/tháng</td>
                            <td class="" id='doanh-thu-du-kien-thang'>
                            </td>
                        </tr>


                        <tr class="row-chi-phi-co-dinh-thang">
                            <td>Chi phí cố định hàng tháng</td>
                            <td id="chi_phi_co_dinh_thang"></td>
                        </tr>

                        <tr class="row-chi-phi-bien-doi-thang">
                            <td>Chi phí biến đổi hàng tháng</td>
                            <td id="chi_phi_bien_doi_thang"></td>
                        </tr>

                        <tr class="row-bien-phi-don-vi-thang">
                            <td>Biến phí đơn vị hàng tháng</td>
                            <td id="bien_phi_don_vi_thang"></td>
                        </tr>

                        <tr class="row-san-luong-hoa-von-thang">
                            <td>Sản lượng hoà vốn hàng tháng</td>
                            <td id="san_luong_hoa_von_thang"></td>
                        </tr>



                        <tr class="row-chi-phi-co-dinh-nam">
                            <td>Chi phí cố định hàng năm</td>
                            <td id="chi_phi_co_dinh_nam"></td>
                        </tr>

                        <tr class="row-chi-phi-bien-doi-nam">
                            <td>Chi phí biến đổi hàng năm</td>
                            <td id="chi_phi_bien_doi_nam"></td>
                        </tr>

                        <tr class="row-bien-phi-don-vi-nam">
                            <td>Biến phí đơn vị hàng năm</td>
                            <td id="bien_phi_don_vi_nam"></td>
                        </tr>

                        <tr class="row-san-luong-hoa-von-nam">
                            <td>Sản lượng hoà vốn hàng năm</td>
                            <td id="san_luong_hoa_von_nam"></td>
                        </tr>


                    </table>
                    <table class="project_detail tbl02 mt-5">
                                         <tr class="row-san-luong-ban-du-kien-thang">
                         <td>Sản lượng dự kiến bán hàng tháng</td>
                         <td>
                         <input type="text" style="width: 100px; margin-right: 5px;"
                         class="input_san_luong_ban_du_kien_thang_1" id='input_san_luong_ban_du_kien_thang_1' placeholder="Kịch bản 1" value="" >
                         </td>
                         <td>
                         <input type="text" style="width: 100px; margin-right: 5px;"
                         class="input_san_luong_ban_du_kien_thang_2" id='input_san_luong_ban_du_kien_thang_2' placeholder="Kịch bản 2" value="" >
                         </td>
                         <td>
                         <input type="text" style="width: 100px; margin-right: 5px;"
                         class="input_san_luong_ban_du_kien_thang_3" id='input_san_luong_ban_du_kien_thang_3' placeholder="Kịch bản 3" value="" >
                         </td>
                         <td>
                         <input type="text" style="width: 100px;"
                         class="input_san_luong_ban_du_kien_thang_4" id='input_san_luong_ban_du_kien_thang_4' placeholder="Kịch bản 4" value="" >
                         </td>
                     </tr>
                                         <tr>
                         <td>Chi phí cố định hàng tháng</td>
                         <td id="chi_phi_co_dinh_thang_1"></td>
                         <td id="chi_phi_co_dinh_thang_2"></td>
                         <td id="chi_phi_co_dinh_thang_3"></td>
                         <td id="chi_phi_co_dinh_thang_4"></td>
                     </tr>
                     <tr>
                         <td>Chi phí biến đổi hàng tháng</td>
                         <td id="chi_phi_bien_doi_thang_1"></td>
                         <td id="chi_phi_bien_doi_thang_2"></td>
                         <td id="chi_phi_bien_doi_thang_3"></td>
                         <td id="chi_phi_bien_doi_thang_4"></td>
                     </tr>
                     <tr>
                         <td>Tổng phí hàng tháng</td>
                         <td id="tong_phi_thang_1"></td>
                         <td id="tong_phi_thang_2"></td>
                         <td id="tong_phi_thang_3"></td>
                         <td id="tong_phi_thang_4"></td>
                     </tr>
                     <tr>
                         <td>Doanh thu thuần</td>
                         <td id="doanh_thu_thuan_1"></td>
                         <td id="doanh_thu_thuan_2"></td>
                         <td id="doanh_thu_thuan_3"></td>
                         <td id="doanh_thu_thuan_4"></td>
                     </tr>
                     <tr>
                         <td>Lợi nhuận thuần</td>
                         <td id="loi_nhuan_thuan_1"></td>
                         <td id="loi_nhuan_thuan_2"></td>
                         <td id="loi_nhuan_thuan_3"></td>
                         <td id="loi_nhuan_thuan_4"></td>
                     </tr>
                     <tr>
                         <td>Thuế phải đóng cho NN (20%)</td>
                         <td id="thue_phai_dong_cho_nn_1"></td>
                         <td id="thue_phai_dong_cho_nn_2"></td>
                         <td id="thue_phai_dong_cho_nn_3"></td>
                         <td id="thue_phai_dong_cho_nn_4"></td>
                     </tr>
                     <tr>
                         <td>Lợi nhuận net</td>
                         <td id="loi_nhuan_net_1"></td>
                         <td id="loi_nhuan_net_2"></td>
                         <td id="loi_nhuan_net_3"></td>
                         <td id="loi_nhuan_net_4"></td>
                     </tr>
                     <tr>
                         <td>Thu hồi khấu hao dần (rút tiền về)</td>
                         <td id="thu_hoi_khau_hao_dan_1"></td>
                         <td id="thu_hoi_khau_hao_dan_2"></td>
                         <td id="thu_hoi_khau_hao_dan_3"></td>
                         <td id="thu_hoi_khau_hao_dan_4"></td>
                     </tr>

                     <tr>
                        <!-- Tỷ lệ chi phí cố định hàng tháng/ Doanh thu thuần = Chi phí cố định hàng tháng/Doanh thu thuần -->
                         <td>Tỷ lệ chi phí cố định hàng tháng/ Doanh thu thuần</td>
                        <td id="ty_le_chi_phi_co_dinh_hang_thang_doanh_thu_thuan_1"></td>
                        <td id="ty_le_chi_phi_co_dinh_hang_thang_doanh_thu_thuan_2"></td>
                        <td id="ty_le_chi_phi_co_dinh_hang_thang_doanh_thu_thuan_3"></td>
                        <td id="ty_le_chi_phi_co_dinh_hang_thang_doanh_thu_thuan_4"></td>

                     </tr>
                     <tr>
                        <!-- Tỷ lệ chi phí biến đổi hàng tháng/Doanh thu thuần = Chi phí biến đổi hàng tháng/Doanh thu thuần -->
                        <td>Tỷ lệ chi phí biến đổi hàng tháng/Doanh thu thuần</td>
                        <td id="ty_le_chi_phi_bien_doi_hang_thang_doanh_thu_thuan_1"></td>
                        <td id="ty_le_chi_phi_bien_doi_hang_thang_doanh_thu_thuan_2"></td>
                        <td id="ty_le_chi_phi_bien_doi_hang_thang_doanh_thu_thuan_3"></td>
                        <td id="ty_le_chi_phi_bien_doi_hang_thang_doanh_thu_thuan_4"></td>
                     </tr>
                     <tr>
                        <!-- Dòng 3: Tỷ lệ lợi nhuận net/ Doanh thu thuần = Lợi nhuận net/Doanh thu thuần-->
                        <td>Tỷ lệ lợi nhuận net/ Doanh thu thuần</td>
                        <td id="ty_le_loi_nhuan_net_hang_thang_doanh_thu_thuan_1"></td>
                        <td id="ty_le_loi_nhuan_net_hang_thang_doanh_thu_thuan_2"></td>
                        <td id="ty_le_loi_nhuan_net_hang_thang_doanh_thu_thuan_3"></td>
                        <td id="ty_le_loi_nhuan_net_hang_thang_doanh_thu_thuan_4"></td>
                     </tr>
                    </table>



                </div>
            </div>

            <div>
                <h4 style="margin: 20px 0px; color: #495057;">📊 Tổng hợp kết quả dự kiến của dự án </h4>
                <div class="project_info">
                    <table class="project_detail table_04">
                        <tr class="row-tong-chi-phi-dau-tu">
                            <td>Tổng chi phí đầu tư ban đầu</td>
                            <td id="tong_chi_phi_dau_tu_ban_dau">
                                0
                            </td>
                            <td>
                                Tổng giá trị khấu hao + Chi phí CĐ trực tiếp+CF BĐ
                            </td>
                        </tr>

                        <tr class="row-chi-phi-hoat-dong-hoa-von">
                            <td>Chi phí hoạt động hàng tháng để đạt hòa vốn</td>
                            <td id="chi_phi_hoat_dong_hang_thang_hoa_von">
                                0
                            </td>
                            <td>
                                Chi phí cố định + chi phí biến đổi tại điểm hòa vốn
                            </td>
                        </tr>

                        <tr class="row-chi-phi-hoat-dong-san-luong-du-kien">
                            <td>Chi phí hoạt động hàng tháng để đạt sản lượng dự kiến</td>
                            <td id="chi_phi_hoat_dong_hang_thang_san_luong_du_kien">
                                0
                            </td>
                            <td>
                                Chi phí cố định + chi phí biến đổi theo sản lượng dự kiến
                            </td>
                        </tr>



                    </table>
                </div>
            </div>

            <div>
                <h4 style="margin: 20px 0px; color: #495057;">📊 Bảng dự kiến kết quả đầu tư tại mức sản lượng xác định </h4>


                <table class="project_detail table_03">
                    <tr class="row-san-luong-ban-copy">
                        <td>Sản lượng bán</td>
                        <td id="san_luong_ban_1">
                        </td>
                        <td id="san_luong_ban_2">
                        </td>
                        <td id="san_luong_ban_3">
                        </td>
                        <td id="san_luong_ban_4">
                        </td>
                    </tr>

                    <tr class="row-tong-loi-nhuan-net-hang-nam">
                        <td>Tổng lợi nhuận net hàng năm</td>
                        <td id="tong_loi_nhuan_net_hang_nam_1">
                            0
                        </td>
                        <td id="tong_loi_nhuan_net_hang_nam_2">
                            0
                        </td>
                        <td id="tong_loi_nhuan_net_hang_nam_3">
                            0
                        </td>
                        <td id="tong_loi_nhuan_net_hang_nam_4">
                            0
                        </td>

                    </tr>

                    <tr class="row-tong-thu-khau-hao-hang-nam">
                        <td>Tổng thu khấu hao hàng năm</td>
                        <td id="tong_thu_khau_hao_hang_nam_1">
                            0
                        </td>
                        <td id="tong_thu_khau_hao_hang_nam_2">
                            0
                        </td>
                        <td id="tong_thu_khau_hao_hang_nam_3">
                            0
                        </td>
                        <td id="tong_thu_khau_hao_hang_nam_4">
                            0
                        </td>

                    </tr>

                    <tr class="row-tong-thu-hoi-tien-dau-tu-mot-nam">
                        <td>Tổng thu hồi tiền đầu tư một năm</td>
                        <td id="tong_thu_hoi_tien_dau_tu_mot_nam_1">
                            0
                        </td>
                        <td id="tong_thu_hoi_tien_dau_tu_mot_nam_2">
                            0
                        </td>
                        <td id="tong_thu_hoi_tien_dau_tu_mot_nam_3">
                            0
                        </td>
                        <td id="tong_thu_hoi_tien_dau_tu_mot_nam_4">
                            0
                        </td>

                    </tr>

                    <tr class="row-tong-thoi-gian-thu-hoi-von">
                        <td>Tổng thời gian thu hồi vốn</td>
                        <td id="tong_thoi_gian_thu_hoi_von_1">
                            0
                        </td>
                        <td id="tong_thoi_gian_thu_hoi_von_2">
                            0
                        </td>
                        <td id="tong_thoi_gian_thu_hoi_von_3">
                            0
                        </td>
                        <td id="tong_thoi_gian_thu_hoi_von_4">
                            0
                        </td>

                    </tr>

                    <tr class="row-ty-suat-loi-nhuan-roi-hang-nam">
                        <td>Tỷ suất lợi nhuận (ROI) hàng năm</td>
                        <td id="ty_suat_loi_nhuan_roi_hang_nam_1">
                            0%
                        </td>
                        <td id="ty_suat_loi_nhuan_roi_hang_nam_2">
                            0%
                        </td>
                        <td id="ty_suat_loi_nhuan_roi_hang_nam_3">
                            0%
                        </td>
                        <td id="ty_suat_loi_nhuan_roi_hang_nam_4">
                            0%
                        </td>

                    </tr>

                    <tr class="row-tong-loi-nhuan-mot-nam-sau-khi-hoan-von">
                        <td>Tổng lợi nhuận một năm sau khi hoàn vốn</td>
                        <td id="tong_loi_nhuan_mot_nam_sau_khi_hoan_von_1">
                            0
                        </td>
                        <td id="tong_loi_nhuan_mot_nam_sau_khi_hoan_von_2">
                            0
                        </td>
                        <td id="tong_loi_nhuan_mot_nam_sau_khi_hoan_von_3">
                            0
                        </td>
                        <td id="tong_loi_nhuan_mot_nam_sau_khi_hoan_von_4">
                            0
                        </td>

                    </tr>

                    <tr class="row-ty-suat-loi-nhuan-sau-khi-hoan-von">
                        <td>Tỷ suất lợi nhuận sau khi hoàn vốn</td>
                        <td id="ty_suat_loi_nhuan_sau_khi_hoan_von_1">
                            0%
                        </td>
                        <td id="ty_suat_loi_nhuan_sau_khi_hoan_von_2">
                            0%
                        </td>
                        <td id="ty_suat_loi_nhuan_sau_khi_hoan_von_3">
                            0%
                        </td>
                        <td id="ty_suat_loi_nhuan_sau_khi_hoan_von_4">
                            0%
                        </td>

                    </tr>

                </table>


            </div>

            <div class="dau_tu_khac">
                <h4 style="margin: 20px 0px; color: #495057;">📊 So sánh các hoạt động đầu tư khác </h4>
                <div class="project_info">
                    <table class="project_detail table_05">
                        <tr class="row-ten-loai-hinh-dau-tu">
                            <td>
                                <input type="text" id="ten_loai_hinh_dau_tu_so_sanh" placeholder="VD: Đầu tư vàng"
                                       style="text-align: left; width: 100%; padding: 5px; border: 1px solid #ccc; border-radius: 4px;"
                                       value="">
                            </td>
                            <td>
                                <input type="number" id="phan_tram_lai_xuat_so_sanh" placeholder="% Lãi suất"
                                       style="text-align: right; width: 80%; padding: 5px; border: 1px solid #ccc; border-radius: 4px;"
                                       value=""> %
                            </td>
                            <td>
                                Loại hình đầu tư so sánh
                            </td>
                        </tr>

                        <tr class="row-gia-tri-loi-nhuan-hang-nam">
                            <td>Giá trị lợi nhuận hàng năm</td>
                            <td>

                            </td>
                            <td>
                                Lợi nhuận hàng năm của loại hình đầu tư trên
                            </td>
                        </tr>

                        <tr class="row-so-sanh-ty-suat-loi-nhuan">
                            <td>So sánh tỷ suất lợi nhuận của hoạt động startup và loại hình đầu tư trên</td>
                            <td id="so_sanh_ty_suat_loi_nhuan">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span id="ty_suat_startup">0%</span>
                                    <span>vs</span>
                                    <span id="ty_suat_dau_tu_khac">0%</span>
                                    <span id="ket_qua_so_sanh" style="font-weight: bold; margin-left: 10px;"></span>
                                </div>
                            </td>
                            <td>
                                So sánh hiệu quả đầu tư giữa startup và đầu tư khác
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="phan_bo_tung_thang">
                <h4 style="margin: 20px 0px; color: #495057;">📊 Phân bổ dự kiến theo từng tháng (Kịch bản 4) </h4>
                <div class="project_info">
                    <table class="project_detail table_06">
                        <thead>
                            <tr>
                                <th style="text-align: left; padding: 10px; background-color: #e9ecef;">Chỉ tiêu</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 1</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 2</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 3</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 4</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 5</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 6</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 7</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 8</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 9</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 10</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 11</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 12</th>
                                <th style="text-align: center; padding: 10px; background-color: #d4edda; font-weight: bold;">Tổng năm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="row-san-luong-ban-du-kien-tung-thang">
                                <td>Sản lượng bán dự kiến</td>
                                <td>
                                    <div class="monthly-input-container">
                                        <input type="number" id="san_luong_thang_1" class="input-san-luong-thang" min="0" value="">
                                        <div class="monthly-btn-group">
                                            <button type="button" class="monthly-btn decrease" data-target="san_luong_thang_1">−</button>
                                            <button type="button" class="monthly-btn increase" data-target="san_luong_thang_1">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="monthly-input-container">
                                        <input type="number" id="san_luong_thang_2" class="input-san-luong-thang" min="0" value="">
                                        <div class="monthly-btn-group">
                                            <button type="button" class="monthly-btn decrease" data-target="san_luong_thang_2">−</button>
                                            <button type="button" class="monthly-btn increase" data-target="san_luong_thang_2">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="monthly-input-container">
                                        <input type="number" id="san_luong_thang_3" class="input-san-luong-thang" min="0" value="">
                                        <div class="monthly-btn-group">
                                            <button type="button" class="monthly-btn decrease" data-target="san_luong_thang_3">−</button>
                                            <button type="button" class="monthly-btn increase" data-target="san_luong_thang_3">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="monthly-input-container">
                                        <input type="number" id="san_luong_thang_4" class="input-san-luong-thang" min="0" value="">
                                        <div class="monthly-btn-group">
                                            <button type="button" class="monthly-btn decrease" data-target="san_luong_thang_4">−</button>
                                            <button type="button" class="monthly-btn increase" data-target="san_luong_thang_4">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="monthly-input-container">
                                        <input type="number" id="san_luong_thang_5" class="input-san-luong-thang" min="0" value="">
                                        <div class="monthly-btn-group">
                                            <button type="button" class="monthly-btn decrease" data-target="san_luong_thang_5">−</button>
                                            <button type="button" class="monthly-btn increase" data-target="san_luong_thang_5">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="monthly-input-container">
                                        <input type="number" id="san_luong_thang_6" class="input-san-luong-thang" min="0" value="">
                                        <div class="monthly-btn-group">
                                            <button type="button" class="monthly-btn decrease" data-target="san_luong_thang_6">−</button>
                                            <button type="button" class="monthly-btn increase" data-target="san_luong_thang_6">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="monthly-input-container">
                                        <input type="number" id="san_luong_thang_7" class="input-san-luong-thang" min="0" value="">
                                        <div class="monthly-btn-group">
                                            <button type="button" class="monthly-btn decrease" data-target="san_luong_thang_7">−</button>
                                            <button type="button" class="monthly-btn increase" data-target="san_luong_thang_7">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="monthly-input-container">
                                        <input type="number" id="san_luong_thang_8" class="input-san-luong-thang" min="0" value="">
                                        <div class="monthly-btn-group">
                                            <button type="button" class="monthly-btn decrease" data-target="san_luong_thang_8">−</button>
                                            <button type="button" class="monthly-btn increase" data-target="san_luong_thang_8">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="monthly-input-container">
                                        <input type="number" id="san_luong_thang_9" class="input-san-luong-thang" min="0" value="">
                                        <div class="monthly-btn-group">
                                            <button type="button" class="monthly-btn decrease" data-target="san_luong_thang_9">−</button>
                                            <button type="button" class="monthly-btn increase" data-target="san_luong_thang_9">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="monthly-input-container">
                                        <input type="number" id="san_luong_thang_10" class="input-san-luong-thang" min="0" value="">
                                        <div class="monthly-btn-group">
                                            <button type="button" class="monthly-btn decrease" data-target="san_luong_thang_10">−</button>
                                            <button type="button" class="monthly-btn increase" data-target="san_luong_thang_10">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="monthly-input-container">
                                        <input type="number" id="san_luong_thang_11" class="input-san-luong-thang" min="0" value="">
                                        <div class="monthly-btn-group">
                                            <button type="button" class="monthly-btn decrease" data-target="san_luong_thang_11">−</button>
                                            <button type="button" class="monthly-btn increase" data-target="san_luong_thang_11">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="monthly-input-container">
                                        <input type="number" id="san_luong_thang_12" class="input-san-luong-thang" min="0" value="">
                                        <div class="monthly-btn-group">
                                            <button type="button" class="monthly-btn decrease" data-target="san_luong_thang_12">−</button>
                                            <button type="button" class="monthly-btn increase" data-target="san_luong_thang_12">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td id="san_luong_tong_nam" style="font-weight: bold; background-color: #d4edda;"></td>
                            </tr>
                            <tr>
                                <td>Chi phí cố định</td>
                                <td id="cp_co_dinh_thang_1"></td>
                                <td id="cp_co_dinh_thang_2"></td>
                                <td id="cp_co_dinh_thang_3"></td>
                                <td id="cp_co_dinh_thang_4"></td>
                                <td id="cp_co_dinh_thang_5"></td>
                                <td id="cp_co_dinh_thang_6"></td>
                                <td id="cp_co_dinh_thang_7"></td>
                                <td id="cp_co_dinh_thang_8"></td>
                                <td id="cp_co_dinh_thang_9"></td>
                                <td id="cp_co_dinh_thang_10"></td>
                                <td id="cp_co_dinh_thang_11"></td>
                                <td id="cp_co_dinh_thang_12"></td>
                                <td id="cp_co_dinh_tong_nam" style="font-weight: bold; background-color: #d4edda;"></td>
                            </tr>
                            <tr>
                                <td>Chi phí biến đổi</td>
                                <td id="cp_bien_doi_thang_1"></td>
                                <td id="cp_bien_doi_thang_2"></td>
                                <td id="cp_bien_doi_thang_3"></td>
                                <td id="cp_bien_doi_thang_4"></td>
                                <td id="cp_bien_doi_thang_5"></td>
                                <td id="cp_bien_doi_thang_6"></td>
                                <td id="cp_bien_doi_thang_7"></td>
                                <td id="cp_bien_doi_thang_8"></td>
                                <td id="cp_bien_doi_thang_9"></td>
                                <td id="cp_bien_doi_thang_10"></td>
                                <td id="cp_bien_doi_thang_11"></td>
                                <td id="cp_bien_doi_thang_12"></td>
                                <td id="cp_bien_doi_tong_nam" style="font-weight: bold; background-color: #d4edda;"></td>
                            </tr>
                            <tr>
                                <td>Tổng chi phí</td>
                                <td id="tong_cp_thang_1"></td>
                                <td id="tong_cp_thang_2"></td>
                                <td id="tong_cp_thang_3"></td>
                                <td id="tong_cp_thang_4"></td>
                                <td id="tong_cp_thang_5"></td>
                                <td id="tong_cp_thang_6"></td>
                                <td id="tong_cp_thang_7"></td>
                                <td id="tong_cp_thang_8"></td>
                                <td id="tong_cp_thang_9"></td>
                                <td id="tong_cp_thang_10"></td>
                                <td id="tong_cp_thang_11"></td>
                                <td id="tong_cp_thang_12"></td>
                                <td id="tong_cp_tong_nam" style="font-weight: bold; background-color: #d4edda;"></td>
                            </tr>
                            <tr>
                                <td>Doanh thu thuần</td>
                                <td id="doanh_thu_thang_1"></td>
                                <td id="doanh_thu_thang_2"></td>
                                <td id="doanh_thu_thang_3"></td>
                                <td id="doanh_thu_thang_4"></td>
                                <td id="doanh_thu_thang_5"></td>
                                <td id="doanh_thu_thang_6"></td>
                                <td id="doanh_thu_thang_7"></td>
                                <td id="doanh_thu_thang_8"></td>
                                <td id="doanh_thu_thang_9"></td>
                                <td id="doanh_thu_thang_10"></td>
                                <td id="doanh_thu_thang_11"></td>
                                <td id="doanh_thu_thang_12"></td>
                                <td id="doanh_thu_tong_nam" style="font-weight: bold; background-color: #d4edda;"></td>
                            </tr>
                            <tr>
                                <td>Lợi nhuận thuần</td>
                                <td id="loi_nhuan_thuan_thang_1"></td>
                                <td id="loi_nhuan_thuan_thang_2"></td>
                                <td id="loi_nhuan_thuan_thang_3"></td>
                                <td id="loi_nhuan_thuan_thang_4"></td>
                                <td id="loi_nhuan_thuan_thang_5"></td>
                                <td id="loi_nhuan_thuan_thang_6"></td>
                                <td id="loi_nhuan_thuan_thang_7"></td>
                                <td id="loi_nhuan_thuan_thang_8"></td>
                                <td id="loi_nhuan_thuan_thang_9"></td>
                                <td id="loi_nhuan_thuan_thang_10"></td>
                                <td id="loi_nhuan_thuan_thang_11"></td>
                                <td id="loi_nhuan_thuan_thang_12"></td>
                                <td id="loi_nhuan_thuan_tong_nam" style="font-weight: bold; background-color: #d4edda;"></td>
                            </tr>
                            <tr>
                                <td>Thuế (20%)</td>
                                <td id="thue_thang_1"></td>
                                <td id="thue_thang_2"></td>
                                <td id="thue_thang_3"></td>
                                <td id="thue_thang_4"></td>
                                <td id="thue_thang_5"></td>
                                <td id="thue_thang_6"></td>
                                <td id="thue_thang_7"></td>
                                <td id="thue_thang_8"></td>
                                <td id="thue_thang_9"></td>
                                <td id="thue_thang_10"></td>
                                <td id="thue_thang_11"></td>
                                <td id="thue_thang_12"></td>
                                <td id="thue_tong_nam" style="font-weight: bold; background-color: #d4edda;"></td>
                            </tr>
                            <tr style="background-color: #e8f5e9;">
                                <td style="font-weight: bold;">Lợi nhuận net</td>
                                <td id="loi_nhuan_net_thang_1"></td>
                                <td id="loi_nhuan_net_thang_2"></td>
                                <td id="loi_nhuan_net_thang_3"></td>
                                <td id="loi_nhuan_net_thang_4"></td>
                                <td id="loi_nhuan_net_thang_5"></td>
                                <td id="loi_nhuan_net_thang_6"></td>
                                <td id="loi_nhuan_net_thang_7"></td>
                                <td id="loi_nhuan_net_thang_8"></td>
                                <td id="loi_nhuan_net_thang_9"></td>
                                <td id="loi_nhuan_net_thang_10"></td>
                                <td id="loi_nhuan_net_thang_11"></td>
                                <td id="loi_nhuan_net_thang_12"></td>
                                <td id="loi_nhuan_net_tong_nam" style="font-weight: bold; background-color: #c3e6cb;"></td>
                            </tr>
                            <tr>
                                <td>Thu hồi khấu hao</td>
                                <td id="thu_hoi_khau_hao_thang_1"></td>
                                <td id="thu_hoi_khau_hao_thang_2"></td>
                                <td id="thu_hoi_khau_hao_thang_3"></td>
                                <td id="thu_hoi_khau_hao_thang_4"></td>
                                <td id="thu_hoi_khau_hao_thang_5"></td>
                                <td id="thu_hoi_khau_hao_thang_6"></td>
                                <td id="thu_hoi_khau_hao_thang_7"></td>
                                <td id="thu_hoi_khau_hao_thang_8"></td>
                                <td id="thu_hoi_khau_hao_thang_9"></td>
                                <td id="thu_hoi_khau_hao_thang_10"></td>
                                <td id="thu_hoi_khau_hao_thang_11"></td>
                                <td id="thu_hoi_khau_hao_thang_12"></td>
                                <td id="thu_hoi_khau_hao_tong_nam" style="font-weight: bold; background-color: #d4edda;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tinh_chi_so_ban_hang">
                <h4 style="margin: 20px 0px; color: #495057;">📊 Tính các chỉ số bán hàng</h4>
                <div class="project_info">
                    <table class="project_detail table_06">
                        <thead>
                            <tr>
                                <th style="text-align: left; padding: 10px; background-color: #e9ecef;">Chỉ tiêu</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 1</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 2</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 3</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 4</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 5</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 6</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 7</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 8</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 9</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 10</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 11</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 12</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Năng lực bán: Số khách/1NV/Tháng</td>
                                <td><input type="number" id="nang_luc_nv_ban_thang_1" class="input-sales-metrics" min="0" value=""></td>
                                <td><input type="number" id="nang_luc_nv_ban_thang_2" class="input-sales-metrics" min="0" value=""></td>
                                <td><input type="number" id="nang_luc_nv_ban_thang_3" class="input-sales-metrics" min="0" value=""></td>
                                <td><input type="number" id="nang_luc_nv_ban_thang_4" class="input-sales-metrics" min="0" value=""></td>
                                <td><input type="number" id="nang_luc_nv_ban_thang_5" class="input-sales-metrics" min="0" value=""></td>
                                <td><input type="number" id="nang_luc_nv_ban_thang_6" class="input-sales-metrics" min="0" value=""></td>
                                <td><input type="number" id="nang_luc_nv_ban_thang_7" class="input-sales-metrics" min="0" value=""></td>
                                <td><input type="number" id="nang_luc_nv_ban_thang_8" class="input-sales-metrics" min="0" value=""></td>
                                <td><input type="number" id="nang_luc_nv_ban_thang_9" class="input-sales-metrics" min="0" value=""></td>
                                <td><input type="number" id="nang_luc_nv_ban_thang_10" class="input-sales-metrics" min="0" value=""></td>
                                <td><input type="number" id="nang_luc_nv_ban_thang_11" class="input-sales-metrics" min="0" value=""></td>
                                <td><input type="number" id="nang_luc_nv_ban_thang_12" class="input-sales-metrics" min="0" value=""></td>
                            </tr>
                            <tr>
                                <td>Cần số lượng NV bán</td>
                                <td><input type="number" id="so_luong_nv_thang_1" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_luong_nv_thang_2" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_luong_nv_thang_3" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_luong_nv_thang_4" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_luong_nv_thang_5" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_luong_nv_thang_6" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_luong_nv_thang_7" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_luong_nv_thang_8" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_luong_nv_thang_9" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_luong_nv_thang_10" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_luong_nv_thang_11" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_luong_nv_thang_12" class="input-sales-metrics" min="0" value="" readonly></td>
                            </tr>
                            <tr>
                                <td>Tỷ lệ KH tiềm năng  <br> (VD: 5 người có 1 khách hàng)</td>
                                <td><input type="text" id="ty_le_chuyen_doi_thang_1" class="input-sales-metrics" value=""></td>
                                <td><input type="text" id="ty_le_chuyen_doi_thang_2" class="input-sales-metrics" value=""></td>
                                <td><input type="text" id="ty_le_chuyen_doi_thang_3" class="input-sales-metrics" value=""></td>
                                <td><input type="text" id="ty_le_chuyen_doi_thang_4" class="input-sales-metrics" value=""></td>
                                <td><input type="text" id="ty_le_chuyen_doi_thang_5" class="input-sales-metrics" value=""></td>
                                <td><input type="text" id="ty_le_chuyen_doi_thang_6" class="input-sales-metrics" value=""></td>
                                <td><input type="text" id="ty_le_chuyen_doi_thang_7" class="input-sales-metrics" value=""></td>
                                <td><input type="text" id="ty_le_chuyen_doi_thang_8" class="input-sales-metrics" value=""></td>
                                <td><input type="text" id="ty_le_chuyen_doi_thang_9" class="input-sales-metrics" value=""></td>
                                <td><input type="text" id="ty_le_chuyen_doi_thang_10" class="input-sales-metrics" value=""></td>
                                <td><input type="text" id="ty_le_chuyen_doi_thang_11" class="input-sales-metrics" value=""></td>
                                <td><input type="text" id="ty_le_chuyen_doi_thang_12" class="input-sales-metrics" value=""></td>
                            </tr>
                            <tr>
                                <td>Số KH tiềm năng cần</td>
                                <td><input type="number" id="so_kh_tiem_nang_thang_1" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_kh_tiem_nang_thang_2" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_kh_tiem_nang_thang_3" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_kh_tiem_nang_thang_4" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_kh_tiem_nang_thang_5" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_kh_tiem_nang_thang_6" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_kh_tiem_nang_thang_7" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_kh_tiem_nang_thang_8" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_kh_tiem_nang_thang_9" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_kh_tiem_nang_thang_10" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_kh_tiem_nang_thang_11" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="so_kh_tiem_nang_thang_12" class="input-sales-metrics" min="0" value="" readonly></td>
                            </tr>
                            <tr>
                                <td>Số KH tiềm năng/1NV</td>
                                <td><input type="number" id="kh_tiem_nang_per_nv_thang_1" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="kh_tiem_nang_per_nv_thang_2" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="kh_tiem_nang_per_nv_thang_3" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="kh_tiem_nang_per_nv_thang_4" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="kh_tiem_nang_per_nv_thang_5" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="kh_tiem_nang_per_nv_thang_6" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="kh_tiem_nang_per_nv_thang_7" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="kh_tiem_nang_per_nv_thang_8" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="kh_tiem_nang_per_nv_thang_9" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="kh_tiem_nang_per_nv_thang_10" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="kh_tiem_nang_per_nv_thang_11" class="input-sales-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="kh_tiem_nang_per_nv_thang_12" class="input-sales-metrics" min="0" value="" readonly></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tinh_chi_so_marketing">
                <h4 style="margin: 20px 0px; color: #495057;">📊 Tính các chỉ số Marketing online</h4>
                <div class="project_info">
                    <table class="project_detail table_06">
                        <thead>
                            <tr>
                                <th style="text-align: left; padding: 10px; background-color: #e9ecef;">Chỉ tiêu</th>
                                <th style="text-align: center; padding: 10px; background-color: #fff3cd;">Tỷ lệ chuyển đổi</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 1</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 2</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 3</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 4</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 5</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 6</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 7</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 8</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 9</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 10</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 11</th>
                                <th style="text-align: center; padding: 10px; background-color: #e9ecef;">Tháng 12</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Tổng lượng liên lạc (ĐT trực tiếp)</td>
                                <td><input type="text" id="ty_le_chuyen_doi_lien_lac" class="input-conversion-rate" value="" placeholder="VD: 10, là 10 có 1 chuyển đổi"></td>
                                <td><input type="number" id="lien_lac_thang_1" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="lien_lac_thang_2" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="lien_lac_thang_3" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="lien_lac_thang_4" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="lien_lac_thang_5" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="lien_lac_thang_6" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="lien_lac_thang_7" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="lien_lac_thang_8" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="lien_lac_thang_9" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="lien_lac_thang_10" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="lien_lac_thang_11" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="lien_lac_thang_12" class="input-marketing-metrics" min="0" value="" readonly></td>
                            </tr>
                            <tr>
                                <td>Lượng tương tác (chat, comments, share)</td>
                                <td><input type="text" id="ty_le_chuyen_doi_chat" class="input-conversion-rate" value="" placeholder="VD: 10"></td>
                                <td><input type="number" id="tuong_tac_chat_thang_1" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_chat_thang_2" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_chat_thang_3" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_chat_thang_4" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_chat_thang_5" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_chat_thang_6" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_chat_thang_7" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_chat_thang_8" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_chat_thang_9" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_chat_thang_10" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_chat_thang_11" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_chat_thang_12" class="input-marketing-metrics" min="0" value="" readonly></td>
                            </tr>
                            <tr>
                                <td>Lượng tương tác (Like, love)</td>
                                <td><input type="text" id="ty_le_chuyen_doi_like" class="input-conversion-rate" value="" placeholder="VD: 10"></td>
                                <td><input type="number" id="tuong_tac_like_thang_1" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_like_thang_2" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_like_thang_3" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_like_thang_4" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_like_thang_5" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_like_thang_6" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_like_thang_7" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_like_thang_8" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_like_thang_9" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_like_thang_10" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_like_thang_11" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_like_thang_12" class="input-marketing-metrics" min="0" value="" readonly></td>
                            </tr>
                            <tr>
                                <td>Lượng tương tác (Seen)</td>
                                <td><input type="text" id="ty_le_chuyen_doi_seen" class="input-conversion-rate" value="" placeholder="VD: 10"></td>
                                <td><input type="number" id="tuong_tac_seen_thang_1" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_seen_thang_2" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_seen_thang_3" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_seen_thang_4" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_seen_thang_5" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_seen_thang_6" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_seen_thang_7" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_seen_thang_8" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_seen_thang_9" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_seen_thang_10" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_seen_thang_11" class="input-marketing-metrics" min="0" value="" readonly></td>
                                <td><input type="number" id="tuong_tac_seen_thang_12" class="input-marketing-metrics" min="0" value="" readonly></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Column headers and visibility configuration for each tab
        const COLUMN_HEADERS = {
            'depreciation': {
                'col-id': { header: 'ID', visible: false },
                'col-stt': { header: 'STT', visible: true },
                'col-name': { header: 'Các chi phí', visible: true },
                'col-category': { header: 'Thể loại', visible: false },
                'col-cost': { header: 'Giá trị', visible: true },
                'col-quantity': { header: 'Số lượng', visible: true },
                'col-total': { header: 'Tổng giá trị khấu hao', visible: true },
                'col-depreciation': { header: 'Số tháng khấu hao', visible: true },
                'col-unit-depreciation': { header: '$ Khấu hao 1 đơn vị', visible: false },
                'col-monthly-allocation': { header: '$ Khấu hao 1 tháng', visible: true },
                'col-yearly-allocation': { header: '$ Khấu hao 1 năm', visible: true },
                'col-note': { header: 'Ghi chú', visible: true },
                'col-actions': { header: 'Actions', visible: true }
            },
            'fixed-cost': {
                'col-id': { header: 'ID', visible: false },
                'col-stt': { header: 'STT', visible: true },
                'col-name': { header: 'Các chi phí', visible: true },
                'col-category': { header: 'Thể loại', visible: true },
                'col-cost': { header: 'Chi phí 1 tháng', visible: true },
                'col-quantity': { header: 'Số lượng', visible: true },
                'col-total': { header: 'Tổng chi phí cố định/tháng', visible: false },
                'col-depreciation': { header: 'Số tháng khấu hao', visible: false },
                'col-unit-depreciation': { header: 'Chi phí đơn vị', visible: true },
                'col-monthly-allocation': { header: 'Chi phí cố định hàng tháng', visible: true },
                'col-yearly-allocation': { header: 'Chi phí cố định hàng năm', visible: true },
                'col-note': { header: 'Ghi chú', visible: true },
                'col-actions': { header: 'Actions', visible: true }
            },
                        'variable-cost': {
                'col-id': { header: 'ID', visible: false },
                'col-stt': { header: 'STT', visible: true },
                'col-name': { header: 'Các chi phí', visible: true },
                'col-category': { header: 'Thể loại', visible: false },
                'col-option1': { header: 'Lựa chọn', visible: true },
                'col-cost': { header: 'Giá trị', visible: true },
                'col-quantity': { header: 'Số lượng', visible: true },
                'col-total': { header: 'Tổng giá trị hàng tháng', visible: true },
                'col-depreciation': { header: 'Số tháng khấu hao', visible: false },
                'col-unit-depreciation': { header: '$ Khấu hao 1 đơn vị', visible: false },
                'col-monthly-allocation': { header: '$ Khấu hao 1 tháng', visible: false },
                'col-yearly-allocation': { header: 'Tổng giá trị hàng năm', visible: true },
                'col-note': { header: 'Ghi chú', visible: true },
                'col-actions': { header: 'Actions', visible: true }
            },
            'cost': {
                'col-id': { header: 'ID', visible: false },
                'col-stt': { header: 'STT', visible: true },
                'col-name': { header: 'Các chi phí', visible: true },
                'col-category': { header: 'Thể loại', visible: true },
                'col-cost': { header: 'Giá trị', visible: true },
                'col-quantity': { header: 'Số lượng', visible: true },
                'col-total': { header: 'Tổng giá trị', visible: true },
                'col-depreciation': { header: 'Số tháng khấu hao', visible: true },
                'col-unit-depreciation': { header: '$ Khấu hao 1 đơn vị', visible: false },
                'col-monthly-allocation': { header: '$ Khấu hao 1 tháng', visible: false },
                'col-yearly-allocation': { header: '$ Khấu hao 1 năm', visible: false },
                'col-note': { header: 'Ghi chú', visible: true },
                'col-actions': { header: 'Actions', visible: true }
            }
        };

        // Function to update column headers and visibility based on current tab
        function updateColumnHeaders(tabId) {
            const headers = COLUMN_HEADERS[tabId];
            if (!headers) return;

            // Update all column headers and visibility
            Object.keys(headers).forEach(colClass => {
                const config = headers[colClass];

                // Update header text
                const headerElement = document.querySelector(`th.${colClass}`);
                if (headerElement) {
                    headerElement.textContent = config.header;

                    // Update header visibility
                    headerElement.style.display = config.visible ? '' : 'none';
                }

                // Update corresponding cells visibility
                const cells = document.querySelectorAll(`td.${colClass}`);
                cells.forEach(cell => {
                    cell.style.display = config.visible ? '' : 'none';
                });
            });
        }

        // Tab Configuration
        const TAB_CONFIG = {
            cost: {
                name: 'Chi phí',
                data: [],
                changes: new Map(),
                nextTempId: -1,
                tableBodyId: 'tableBody-cost',
                apiEndpoint: '/api/member-cost-item',
                // apiEndpointList: '/list?seoby_s10=eq&seby_s10=fixed_cost',
                apiEndpointList: '/list?limit=200',
                apiEndpointAdd: '/add',
                apiEndpointUpdate: '/update',
                updateVariableOption: '',
                hasDepreciation: true
            }
        };

        // Current active tab (legacy - always 'cost' for table operations)
        let currentTab = 'cost';

        // Helper function to get actual current active tab from URL
        function getCurrentActiveTab() {
            const params = getURLParams();
            return params.tabId || 'cost';
        }

        let currentPlanId = '';

        // ========== DATA MODEL & MANAGER ==========

        /**
         * Class đại diện cho một item chi phí
         */
        class CostItem {
            constructor(data = {}) {
                this.id = data.id || null;
                this.user_id = data.user_id || null;
                this.plan_id = data.plan_id || null;
                this.item_name = data.item_name || '';
                this.category = data.category || '';
                this.cost = parseFloat(data.cost) || 0;
                this.quantity = parseFloat(data.quantity) || 0;
                this.depreciation = data.depreciation ? parseFloat(data.depreciation) : null;
                this.note = data.note || '';
                this.option1 = data.option1 || '';
                this.status = data.status || null;
                this.type = data.type || null;
                this.created_at = data.created_at || null;
                this.updated_at = data.updated_at || null;
                this.deleted_at = data.deleted_at || null;

                // Computed properties
                this.isNew = !this.id || this.id < 0;
                this.isEmpty = !this.item_name && !this.category && this.cost === 0 && this.quantity === 0;
            }

            /**
             * Tính tổng giá trị (cost * quantity)
             */
            getTotal() {
                return this.cost * this.quantity;
            }

            /**
             * Tính khấu hao đơn vị
             */
            getUnitDepreciation() {
                if (this.category === 'fixed' || this.category === 'fixed_direct') {
                    return this.cost;
                } else if (this.depreciation && this.depreciation > 0) {
                    return Math.round(this.cost / this.depreciation);
                }
                return 0;
            }

            /**
             * Tính phân bổ hàng tháng
             */
            getMonthlyAllocation() {
                if (this.category === 'fixed' || this.category === 'fixed_direct') {
                    return this.getTotal();
                } else if (this.depreciation && this.depreciation > 0) {
                    return Math.round(this.getTotal() / this.depreciation);
                }
                return 0;
            }

            /**
             * Tính phân bổ hàng năm (yearly allocation)
             */
            getYearlyAllocation() {
                if (this.category === 'variable') {
                    // For variable cost: yearly = monthly total * 12
                    return this.getTotal() * 12;
                } else {
                    // For fixed and depreciation: yearly = monthly allocation * 12
                    return this.getMonthlyAllocation() * 12;
                }
            }

            /**
             * Cập nhật dữ liệu từ form
             */
            updateFromForm(formData) {
                this.item_name = formData.item_name || '';
                this.category = formData.category || '';
                this.cost = parseFloat(formData.cost) || 0;
                this.quantity = parseFloat(formData.quantity) || 0;
                this.depreciation = formData.depreciation ? parseFloat(formData.depreciation) : null;
                this.note = formData.note || '';
                this.option1 = formData.option1 || '';
                this.updated_at = new Date().toISOString();

                // Update computed properties
                this.isEmpty = !this.item_name && !this.category && this.cost === 0 && this.quantity === 0;
            }

            /**
             * Tạo object để gửi API
             */
            toAPIData() {
                return {
                    id: this.id,
                    plan_id: this.plan_id,
                    item_name: this.item_name,
                    category: this.category,
                    cost: this.cost,
                    quantity: this.quantity,
                    depreciation: this.depreciation,
                    note: this.note,
                    option1: this.option1,
                    status: this.status
                };
            }

            /**
             * Kiểm tra item có khớp với filter của tab không
             */
            matchesTabFilter(tabId) {
                switch(tabId) {
                    case 'depreciation':
                        return this.category === 'depreciated';
                    case 'fixed-cost':
                        return this.category === 'fixed' || this.category === 'fixed_direct' || this.category === 'depreciated';
                    case 'variable-cost':
                        return this.category === 'variable';
                    case 'cost':
                    default:
                        return true;
                }
            }

            /**
             * Clone object
             */
            clone() {
                return new CostItem(this.toAPIData());
            }
        }

        /**
         * Class quản lý dữ liệu
         */
        class DataManager {
            constructor() {
                this.items = []; // Mảng CostItem objects
                this.nextTempId = -1; // ID tạm cho items mới
                this.changes = new Map(); // Track changes for auto-save
            }

            /**
             * Load dữ liệu từ API
             */
            loadFromAPI(apiData) {
                this.items = apiData.map(item => new CostItem(item));
                this.changes.clear();
                console.log(`📊 DataManager: Loaded ${this.items.length} items from API`);
                return this.items;
            }

            /**
             * Thêm item mới
             */
            addItem(data) {
                const item = new CostItem({
                    ...data,
                    id: data.id || this.nextTempId--,
                    plan_id: currentPlanId
                });
                this.items.push(item);
                console.log(`➕ DataManager: Added new item`, item);
                return item;
            }

            /**
             * Cập nhật item theo ID
             */
            updateItem(id, data) {
                const index = this.items.findIndex(item => item.id == id);
                if (index !== -1) {
                    this.items[index].updateFromForm(data);
                    console.log(`🔄 DataManager: Updated item ${id}`, this.items[index]);
                    return this.items[index];
                }
                return null;
            }

            /**
             * Xóa item theo ID
             */
            deleteItem(id) {
                const index = this.items.findIndex(item => item.id == id);
                if (index !== -1) {
                    const deletedItem = this.items.splice(index, 1)[0];
                    console.log(`🗑️ DataManager: Deleted item ${id}`, deletedItem);
                    return deletedItem;
                }
                return null;
            }

            /**
             * Lấy item theo ID
             */
            getItem(id) {
                return this.items.find(item => item.id == id);
            }

            /**
             * Lấy tất cả items
             */
            getAllItems() {
                return this.items;
            }

            /**
             * Lấy items theo filter của tab
             */
            getItemsByTab(tabId) {
                return this.items.filter(item => item.matchesTabFilter(tabId));
            }

            /**
             * Lấy items không rỗng
             */
            getNonEmptyItems() {
                return this.items.filter(item => !item.isEmpty);
            }

            /**
             * Tính tổng theo tab
             */
            calculateTotalsByTab(tabId) {
                const filteredItems = this.getItemsByTab(tabId).filter(item => !item.isEmpty);

                const totals = {
                    totalCost: 0,
                    totalQuantity: 0,
                    totalAmount: 0,
                    totalMonthlyAllocation: 0,
                    giaVonSum: 0, // Cho variable-cost tab
                    fixedSum: 0   // Cho fixed-cost tab
                };

                filteredItems.forEach(item => {
                    totals.totalCost += item.cost;
                    totals.totalQuantity += item.quantity;
                    totals.totalAmount += item.getTotal();
                    totals.totalMonthlyAllocation += item.getMonthlyAllocation();
                });

                // Tính tổng đặc biệt cho từng tab - tính từ TẤT CẢ items, không chỉ filteredItems
                const allItems = this.getAllItems().filter(item => !item.isEmpty);
                allItems.forEach(item => {
                    // Tính giaVonSum: tổng cost của tất cả items có option1 = 'gia_von'
                    if (item.option1 === 'gia_von') {
                        totals.giaVonSum += item.cost;
                    }

                    // Tính fixedSum: tổng cost*quantity của tất cả items có category = 'fixed'
                    if (item.category === 'fixed' || item.category === 'fixed_direct') {
                        totals.fixedSum += item.getTotal(); // cost * quantity
                    }
                });

                console.log(`🧮 DataManager: Calculated totals for ${tabId}:`, totals);
                console.log(`🎯 Special totals: giaVonSum=${totals.giaVonSum}, fixedSum=${totals.fixedSum}`);
                return totals;
            }

            /**
             * Sắp xếp items theo tab
             */
            sortItemsByTab(tabId) {
                if (tabId === 'fixed-cost') {
                    // Sắp xếp: depreciated trước, fixed sau
                    this.items.sort((a, b) => {
                        if (a.category === 'depreciated' && b.category !== 'depreciated') return -1;
                        if (a.category !== 'depreciated' && b.category === 'depreciated') return 1;
                        return 0;
                    });
                } else if (tabId === 'variable-cost') {
                    // Sắp xếp: ban_hang trước, gia_von sau
                    this.items.sort((a, b) => {
                        if (a.option1 === 'ban_hang' && b.option1 !== 'ban_hang') return -1;
                        if (a.option1 !== 'ban_hang' && b.option1 === 'ban_hang') return 1;
                        if (a.option1 === 'gia_von' && b.option1 !== 'gia_von') return -1;
                        if (a.option1 !== 'gia_von' && b.option1 === 'gia_von') return 1;
                        return 0;
                    });
                }
            }

            /**
             * Tạo items rỗng cho nhập liệu
             */
            ensureEmptyRows(minCount = 2) {
                const emptyItems = this.items.filter(item => item.isEmpty);
                const needed = minCount - emptyItems.length;

                for (let i = 0; i < needed; i++) {
                    this.addItem({});
                }
            }

            /**
             * Lấy số lượng items mới
             */
            getNewItemsCount() {
                return this.items.filter(item => item.isNew).length;
            }

            /**
             * Clear all data
             */
            clear() {
                this.items = [];
                this.changes.clear();
                this.nextTempId = -1;
            }
        }

        // Khởi tạo DataManager global
        const dataManager = new DataManager();

        // ========== SUMMARY ROW CALCULATION FUNCTIONS ==========

        /**
         * Tính tổng cho cột "Tổng cộng" (cost × quantity)
         */
        function calculateTotalForColTotal(tabId) {
            // Use DataManager for consistency
            const items = dataManager.getItemsByTab(tabId).filter(item => !item.isEmpty);
            const total = items.reduce((sum, item) => sum + item.getTotal(), 0);
            console.log(`💰 calculateTotalForColTotal(${tabId}) - using DataManager: ${formatNumberWithDots(total)}`);
            console.log(`📋 Items found:`, items.map(item => `${item.item_name}: ${item.getTotal()}`));
            return total;
        }

        /**
         * Tính tổng cho cột "Khấu hao" (tổng số tháng khấu hao)
         */
        function calculateTotalForColDepreciation(tabId) {
            // Use DataManager for consistency
            const items = dataManager.getItemsByTab(tabId).filter(item => !item.isEmpty);
            const total = items.reduce((sum, item) => sum + (item.depreciation || 0), 0);
            console.log(`📅 calculateTotalForColDepreciation(${tabId}) - using DataManager: ${total}`);
            return total;
        }

        /**
         * Tính tổng cho cột "Khấu hao đơn vị"
         */
        function calculateTotalForColUnitDepreciation(tabId) {
            // Use DataManager for consistency
            const items = dataManager.getItemsByTab(tabId).filter(item => !item.isEmpty);
            const total = items.reduce((sum, item) => sum + item.getUnitDepreciation(), 0);
            console.log(`🔢 calculateTotalForColUnitDepreciation(${tabId}) - using DataManager: ${formatNumberWithDots(total)}`);
            return total;
        }

        /**
         * Tính tổng cho cột "Phân bổ hàng tháng"
         */
        function calculateTotalForColMonthlyAllocation(tabId) {
            // Use DataManager for consistency
            const items = dataManager.getItemsByTab(tabId).filter(item => !item.isEmpty);
            const total = items.reduce((sum, item) => sum + item.getMonthlyAllocation(), 0);
            console.log(`📊 calculateTotalForColMonthlyAllocation(${tabId}) - using DataManager: ${formatNumberWithDots(total)}`);
            return total;
        }

        /**
         * Helper function: Lấy items đã filter theo tab
         */
        function getFilteredItemsByTab(tabId) {
            const allItems = dataManager.getAllItems().filter(item => !item.isEmpty);

            switch(tabId) {
                case 'depreciation':
                    return allItems.filter(item => item.category === 'depreciated');
                case 'fixed-cost':
                    return allItems.filter(item => item.category === 'depreciated' || item.category === 'fixed' || item.category === 'fixed_direct');
                case 'variable-cost':
                    return allItems.filter(item => item.category === 'variable');
                case 'cost':
                default:
                    return allItems;
            }
        }

        // Debounce mechanism for updateSummaryRowByTab
        let summaryUpdateTimeout = null;

        /**
         * Cập nhật tất cả 4 cột trong summary row theo tab (with debounce)
         */
        function updateSummaryRowByTab(tabId) {
            // Debug: Log who is calling this function with 'cost'
            if (tabId === 'cost') {
                const currentActiveTab = getCurrentActiveTab();
                if (currentActiveTab !== 'cost') {
                    console.warn(`⚠️ WARNING: updateSummaryRowByTab('cost') called while on tab '${currentActiveTab}'`);
                    console.trace('Call stack:');
                }
            }

            // Clear existing timeout to prevent multiple rapid calls
            if (summaryUpdateTimeout) {
                clearTimeout(summaryUpdateTimeout);
            }

            // Debounce the actual update
            summaryUpdateTimeout = setTimeout(() => {
                console.log(`🔄 updateSummaryRowByTab(${tabId}) - EXECUTING`);

                // Tính tổng cho 4 cột
                const totalAmount = calculateTotalForColTotal(tabId);
                const totalDepreciation = calculateTotalForColDepreciation(tabId);
                const totalUnitDepreciation = calculateTotalForColUnitDepreciation(tabId);
                const totalMonthlyAllocation = calculateTotalForColMonthlyAllocation(tabId);

            // ✅ FIX: ID elements are always 'cost', not dynamic tabId
            const summaryTotalElement = document.getElementById('summary-total-cost');
            const summaryDepreciationElement = document.getElementById('summary-depreciation-cost');
            const summaryUnitDepreciationElement = document.getElementById('summary-unit-depreciation-cost');
            const summaryMonthlyElement = document.getElementById('summary-monthly-cost');
            const summaryYearlyElement = document.getElementById('summary-yearly-cost');

            if (summaryTotalElement) {
                summaryTotalElement.textContent = formatNumberWithDots(totalAmount);
                console.log(`✅ Updated summary-total-cost: ${formatNumberWithDots(totalAmount)}`);
            } else {
                console.log(`❌ Element summary-total-cost not found`);
            }

            if (summaryDepreciationElement) {
                summaryDepreciationElement.textContent = totalDepreciation;
                console.log(`✅ Updated summary-depreciation-cost: ${totalDepreciation}`);
            } else {
                console.log(`❌ Element summary-depreciation-cost not found`);
            }

            if (summaryUnitDepreciationElement) {
                summaryUnitDepreciationElement.textContent = formatNumberWithDots(totalUnitDepreciation);
                console.log(`✅ Updated summary-unit-depreciation-cost: ${formatNumberWithDots(totalUnitDepreciation)}`);
            } else {
                console.log(`❌ Element summary-unit-depreciation-cost not found`);
            }

            if (summaryMonthlyElement) {
                summaryMonthlyElement.textContent = formatNumberWithDots(totalMonthlyAllocation);
                console.log(`✅ Updated summary-monthly-cost: ${formatNumberWithDots(totalMonthlyAllocation)}`);
            } else {
                console.log(`❌ Element summary-monthly-cost not found`);
            }

            // Calculate total yearly allocation based on current tab's filtered items
            const totalYearlyAllocation = dataManager.getItemsByTab(tabId)
                .filter(item => !item.isEmpty)
                .reduce((sum, item) => sum + item.getYearlyAllocation(), 0);

            if (summaryYearlyElement) {
                summaryYearlyElement.textContent = formatNumberWithDots(totalYearlyAllocation);
                console.log(`✅ Updated summary-yearly-cost for tab ${tabId}: ${formatNumberWithDots(totalYearlyAllocation)}`);
            } else {
                console.log(`❌ Element summary-yearly-cost not found`);
            }

            // Cập nhật text summary (số tiền bằng chữ) với null safety
            const totalTextElement = document.getElementById('summary-total-text-cost');
            const monthlyTextElement = document.getElementById('summary-monthly-text-cost');

            if (totalTextElement) {
                totalTextElement.textContent = numberToVietnameseWords(totalAmount);
                console.log(`✅ Updated summary-total-text-cost`);
            } else {
                console.log(`❌ Element summary-total-text-cost not found`);
            }

            if (monthlyTextElement) {
                monthlyTextElement.textContent = numberToVietnameseWords(totalMonthlyAllocation);
                console.log(`✅ Updated summary-monthly-text-cost`);
            } else {
                console.log(`❌ Element summary-monthly-text-cost not found`);
            }

            const yearlyTextElement = document.getElementById('summary-yearly-text-cost');
            if (yearlyTextElement) {
                yearlyTextElement.textContent = numberToVietnameseWords(totalYearlyAllocation);
                console.log(`✅ Updated summary-yearly-text-cost`);
            } else {
                console.log(`❌ Element summary-yearly-text-cost not found`);
            }

            // Cập nhật tổng chi phí cố định trực tiếp hàng tháng
            const totals = dataManager.calculateTotalsByTab('cost'); // Tính từ tất cả items
            const tongCpcdTrucTiepHangThangElement = document.getElementById('tong_cpcd_truc_tiep_hang_thang');
            if (tongCpcdTrucTiepHangThangElement) {
                tongCpcdTrucTiepHangThangElement.textContent = formatNumberWithDots(totals.fixedSum);
                console.log(`✅ Updated tong_cpcd_truc_tiep_hang_thang: ${formatNumberWithDots(totals.fixedSum)}`);
            } else {
                console.log(`❌ Element tong_cpcd_truc_tiep_hang_thang not found`);
            }

            // Cập nhật tổng chi phí cố định trực tiếp hàng năm
            const tongCpcdTrucTiepHangNamElement = document.getElementById('tong_cpcd_truc_tiep_hang_nam');
            if (tongCpcdTrucTiepHangNamElement) {
                const yearlyFixedSum = totals.fixedSum * 12;
                tongCpcdTrucTiepHangNamElement.textContent = formatNumberWithDots(yearlyFixedSum);
                console.log(`✅ Updated tong_cpcd_truc_tiep_hang_nam: ${formatNumberWithDots(yearlyFixedSum)}`);
            } else {
                console.log(`❌ Element tong_cpcd_truc_tiep_hang_nam not found`);
            }

            // Cập nhật tổng chi phí biến đổi hàng tháng
            const variableCostMonthlyTotal = dataManager.getItemsByTab('variable-cost')
                .filter(item => !item.isEmpty)
                .reduce((sum, item) => sum + item.getTotal(), 0);

            const tongChiPhiBienDoiHangThangElement = document.getElementById('tong_chi_phi_bien_doi_hang_thang');
            if (tongChiPhiBienDoiHangThangElement) {
                tongChiPhiBienDoiHangThangElement.textContent = formatNumberWithDots(variableCostMonthlyTotal);
                console.log(`✅ Updated tong_chi_phi_bien_doi_hang_thang: ${formatNumberWithDots(variableCostMonthlyTotal)}`);
            } else {
                console.log(`❌ Element tong_chi_phi_bien_doi_hang_thang not found`);
            }

            // Cập nhật tổng chi phí biến đổi hàng năm
            const variableCostYearlyTotal = dataManager.getItemsByTab('variable-cost')
                .filter(item => !item.isEmpty)
                .reduce((sum, item) => sum + item.getYearlyAllocation(), 0);

            const tongChiPhiBienDoiHangNamElement = document.getElementById('tong_chi_phi_bien_doi_hang_nam');
            if (tongChiPhiBienDoiHangNamElement) {
                tongChiPhiBienDoiHangNamElement.textContent = formatNumberWithDots(variableCostYearlyTotal);
                console.log(`✅ Updated tong_chi_phi_bien_doi_hang_nam: ${formatNumberWithDots(variableCostYearlyTotal)}`);
            } else {
                console.log(`❌ Element tong_chi_phi_bien_doi_hang_nam not found`);
            }

            // Cập nhật tổng CP Bán hàng hàng tháng
            const tongCpBanHangHangThang = dataManager.getItemsByTab('variable-cost')
                .filter(item => !item.isEmpty && item.option1 === 'ban_hang')
                .reduce((sum, item) => sum + item.getTotal(), 0);

            const tongCpBanHangHangThangElement = document.getElementById('tong_cp_ban_hang_hang_thang');
            if (tongCpBanHangHangThangElement) {
                tongCpBanHangHangThangElement.textContent = formatNumberWithDots(tongCpBanHangHangThang);
                console.log(`✅ Updated tong_cp_ban_hang_hang_thang: ${formatNumberWithDots(tongCpBanHangHangThang)}`);
            } else {
                console.log(`❌ Element tong_cp_ban_hang_hang_thang not found`);
            }

            // Cập nhật tổng CP Bán hàng hàng năm
            const tongCpBanHangHangNam = tongCpBanHangHangThang * 12;
            const tongCpBanHangHangNamElement = document.getElementById('tong_cp_ban_hang_hang_nam');
            if (tongCpBanHangHangNamElement) {
                tongCpBanHangHangNamElement.textContent = formatNumberWithDots(tongCpBanHangHangNam);
                console.log(`✅ Updated tong_cp_ban_hang_hang_nam: ${formatNumberWithDots(tongCpBanHangHangNam)}`);
            } else {
                console.log(`❌ Element tong_cp_ban_hang_hang_nam not found`);
            }

            // Cập nhật tổng CP Giá vốn hàng tháng
            const tongCpGiaVonHangThang = dataManager.getItemsByTab('variable-cost')
                .filter(item => !item.isEmpty && item.option1 === 'gia_von')
                .reduce((sum, item) => sum + item.getTotal(), 0);

            const tongCpGiaVonHangThangElement = document.getElementById('tong_cp_gia_von_hang_thang');
            if (tongCpGiaVonHangThangElement) {
                tongCpGiaVonHangThangElement.textContent = formatNumberWithDots(tongCpGiaVonHangThang);
                console.log(`✅ Updated tong_cp_gia_von_hang_thang: ${formatNumberWithDots(tongCpGiaVonHangThang)}`);
            } else {
                console.log(`❌ Element tong_cp_gia_von_hang_thang not found`);
            }

            // Cập nhật tổng CP Giá vốn hàng năm
            const tongCpGiaVonHangNam = tongCpGiaVonHangThang * 12;
            const tongCpGiaVonHangNamElement = document.getElementById('tong_cp_gia_von_hang_nam');
            if (tongCpGiaVonHangNamElement) {
                tongCpGiaVonHangNamElement.textContent = formatNumberWithDots(tongCpGiaVonHangNam);
                console.log(`✅ Updated tong_cp_gia_von_hang_nam: ${formatNumberWithDots(tongCpGiaVonHangNam)}`);
            } else {
                console.log(`❌ Element tong_cp_gia_von_hang_nam not found`);
            }

            // Cập nhật các tỷ lệ
            updateRatioCalculations();

            // Cập nhật so sánh đầu tư
            updateInvestmentComparison();

            // Cập nhật tổng hợp dự án
            updateProjectSummaryCalculations();

                console.log(`✅ updateSummaryRowByTab(${tabId}) completed:`, {
                    totalAmount: formatNumberWithDots(totalAmount),
                    totalDepreciation,
                    totalUnitDepreciation: formatNumberWithDots(totalUnitDepreciation),
                    totalMonthlyAllocation: formatNumberWithDots(totalMonthlyAllocation),
                    totalYearlyAllocation: formatNumberWithDots(totalYearlyAllocation),
                    variableCostMonthlyTotal: formatNumberWithDots(variableCostMonthlyTotal),
                    variableCostYearlyTotal: formatNumberWithDots(variableCostYearlyTotal),
                    tongCpBanHangHangThang: formatNumberWithDots(tongCpBanHangHangThang),
                    tongCpBanHangHangNam: formatNumberWithDots(tongCpBanHangHangNam),
                    tongCpGiaVonHangThang: formatNumberWithDots(tongCpGiaVonHangThang),
                    tongCpGiaVonHangNam: formatNumberWithDots(tongCpGiaVonHangNam)
                });
            }, 100); // 100ms debounce delay
        }

        /**
         * Tính toán và cập nhật các tỷ lệ
         */
        function updateRatioCalculations() {
            // Lấy giá trị giá bán dự kiến
            const giaBanDuKien = parseFloat(document.getElementById('input_gia_ban_du_kien')?.value) || 0;

            // Lấy tổng chi phí cố định hàng tháng
            const chiPhiCoDinhHangThang = parseFloat(document.getElementById('chi_phi_co_dinh_thang')?.textContent?.replace(/\./g, '')) || 0;

            // Lấy tổng chi phí biến đổi hàng tháng
            const chiPhiBienDoiHangThang = parseFloat(document.getElementById('chi_phi_bien_doi_thang')?.textContent?.replace(/\./g, '')) || 0;

            // Tính toán cho 4 kịch bản
            for (let i = 1; i <= 4; i++) {
                // Lấy sản lượng bán dự kiến cho kịch bản i
                const sanLuongBan = parseFloat(document.getElementById(`input_san_luong_ban_du_kien_thang_${i}`)?.value) || 0;

                // Tính doanh thu thuần = Giá bán * Sản lượng bán
                const doanhThuThuan = giaBanDuKien * sanLuongBan;

                // Lấy chi phí cố định hàng tháng cho kịch bản i
                const chiPhiCoDinhKichBan = parseFloat(document.getElementById(`chi_phi_co_dinh_thang_${i}`)?.textContent?.replace(/\./g, '')) || chiPhiCoDinhHangThang;

                // Lấy chi phí biến đổi hàng tháng cho kịch bản i
                const chiPhiBienDoiKichBan = parseFloat(document.getElementById(`chi_phi_bien_doi_thang_${i}`)?.textContent?.replace(/\./g, '')) || (chiPhiBienDoiHangThang * sanLuongBan);

                // Lấy lợi nhuận net cho kịch bản i
                const loiNhuanNet = parseFloat(document.getElementById(`loi_nhuan_net_${i}`)?.textContent?.replace(/\./g, '')) || 0;

                // Tính tỷ lệ chi phí cố định hàng tháng / Doanh thu thuần
                const tyLeChiPhiCoDinh = doanhThuThuan > 0 ? (chiPhiCoDinhKichBan / doanhThuThuan * 100) : 0;

                // Tính tỷ lệ chi phí biến đổi hàng tháng / Doanh thu thuần
                const tyLeChiPhiBienDoi = doanhThuThuan > 0 ? (chiPhiBienDoiKichBan / doanhThuThuan * 100) : 0;

                // Tính tỷ lệ lợi nhuận net / Doanh thu thuần
                const tyLeLoiNhuanNet = doanhThuThuan > 0 ? (loiNhuanNet / doanhThuThuan * 100) : 0;

                // Cập nhật vào DOM
                const tyLeChiPhiCoDinhElement = document.getElementById(`ty_le_chi_phi_co_dinh_hang_thang_doanh_thu_thuan_${i}`);
                if (tyLeChiPhiCoDinhElement) {
                    tyLeChiPhiCoDinhElement.textContent = tyLeChiPhiCoDinh.toFixed(2) + '%';
                }

                const tyLeChiPhiBienDoiElement = document.getElementById(`ty_le_chi_phi_bien_doi_hang_thang_doanh_thu_thuan_${i}`);
                if (tyLeChiPhiBienDoiElement) {
                    tyLeChiPhiBienDoiElement.textContent = tyLeChiPhiBienDoi.toFixed(2) + '%';
                }

                const tyLeLoiNhuanNetElement = document.getElementById(`ty_le_loi_nhuan_net_hang_thang_doanh_thu_thuan_${i}`);
                if (tyLeLoiNhuanNetElement) {
                    tyLeLoiNhuanNetElement.textContent = tyLeLoiNhuanNet.toFixed(2) + '%';
                }

                console.log(`📊 Kịch bản ${i}:`, {
                    sanLuongBan,
                    doanhThuThuan: formatNumberWithDots(doanhThuThuan),
                    chiPhiCoDinhKichBan: formatNumberWithDots(chiPhiCoDinhKichBan),
                    chiPhiBienDoiKichBan: formatNumberWithDots(chiPhiBienDoiKichBan),
                    loiNhuanNet: formatNumberWithDots(loiNhuanNet),
                    tyLeChiPhiCoDinh: tyLeChiPhiCoDinh.toFixed(2) + '%',
                    tyLeChiPhiBienDoi: tyLeChiPhiBienDoi.toFixed(2) + '%',
                    tyLeLoiNhuanNet: tyLeLoiNhuanNet.toFixed(2) + '%'
                });
            }
        }

        /**
         * Tính toán so sánh tỷ suất lợi nhuận với đầu tư khác
         */
        function updateInvestmentComparison() {
            // Lấy tỷ suất lợi nhuận của startup
            const tyKuatStartupElement = document.getElementById('ty_suat_loi_nhuan_roi_hang_nam');
            const tySuatStartupText = tyKuatStartupElement?.textContent || '0%';
            const tyKuatStartup = parseFloat(tySuatStartupText.replace('%', '')) || 0;

            // Lấy giá trị lợi nhuận của đầu tư khác
            const giaTriLoiNhuanKhac = parseFloat(document.getElementById('gia_tri_loi_nhuan_hang_nam_dau_tu_khac')?.value) || 0;

            // Giả sử vốn đầu tư khác tương đương với vốn startup (có thể điều chỉnh)
            const vonDauTuStartup = parseFloat(document.getElementById('tong_chi_phi_dau_tu_ban_dau')?.textContent?.replace(/\./g, '')) || 0;

            // Tính tỷ suất lợi nhuận của đầu tư khác
            const tyKuatDauTuKhac = vonDauTuStartup > 0 ? (giaTriLoiNhuanKhac / vonDauTuStartup * 100) : 0;

            // Cập nhật hiển thị
            const tyKuatStartupDisplay = document.getElementById('ty_suat_startup');
            const tyKuatDauTuKhacDisplay = document.getElementById('ty_suat_dau_tu_khac');
            const ketQuaSoKanhElement = document.getElementById('ket_qua_so_sanh');

            if (tyKuatStartupDisplay) {
                tyKuatStartupDisplay.textContent = tyKuatStartup.toFixed(2) + '%';
            }

            if (tyKuatDauTuKhacDisplay) {
                tyKuatDauTuKhacDisplay.textContent = tyKuatDauTuKhac.toFixed(2) + '%';
            }

            // So sánh và hiển thị kết quả
            if (ketQuaSoKanhElement) {
                const chenhLech = tyKuatStartup - tyKuatDauTuKhac;
                if (chenhLech > 0) {
                    ketQuaSoKanhElement.textContent = `(Startup tốt hơn +${chenhLech.toFixed(2)}%)`;
                    ketQuaSoKanhElement.style.color = '#28a745'; // Màu xanh lá
                } else if (chenhLech < 0) {
                    ketQuaSoKanhElement.textContent = `(Đầu tư khác tốt hơn ${chenhLech.toFixed(2)}%)`;
                    ketQuaSoKanhElement.style.color = '#dc3545'; // Màu đỏ
                } else {
                    ketQuaSoKanhElement.textContent = '(Bằng nhau)';
                    ketQuaSoKanhElement.style.color = '#6c757d'; // Màu xám
                }
            }

            console.log(`📈 So sánh đầu tư:`, {
                tyKuatStartup: tyKuatStartup.toFixed(2) + '%',
                tyKuatDauTuKhac: tyKuatDauTuKhac.toFixed(2) + '%',
                chenhLech: (tyKuatStartup - tyKuatDauTuKhac).toFixed(2) + '%'
            });
        }

        /**
         * Tính toán 3 chỉ số đầu tư quan trọng
         */
        function updateProjectSummaryCalculations() {
            // 1. Tổng chi phí đầu tư ban đầu = Tổng giá trị khấu hao + Chi phí cố định trực tiếp + Chi phí biến đổi ban đầu

            // Lấy tổng giá trị khấu hao (từ depreciation items)
            const depreciationTotal = dataManager.getItemsByTab('depreciation')
                .filter(item => !item.isEmpty)
                .reduce((sum, item) => sum + item.getTotal(), 0);

            // Lấy tổng chi phí cố định trực tiếp
            const fixedDirectTotal = dataManager.getAllItems()
                .filter(item => !item.isEmpty && (item.category === 'fixed' || item.category === 'fixed_direct'))
                .reduce((sum, item) => sum + item.getTotal(), 0);

            // Lấy chi phí biến đổi ban đầu (có thể tính theo 1 tháng hoặc theo setup ban đầu)
            const variableInitialTotal = dataManager.getItemsByTab('variable-cost')
                .filter(item => !item.isEmpty)
                .reduce((sum, item) => sum + item.getTotal(), 0); // 1 tháng đầu

            const tongChiPhiDauTuBanDau = depreciationTotal + fixedDirectTotal + variableInitialTotal;

            // Cập nhật vào DOM
            const tongChiPhiDauTuElement = document.getElementById('tong_chi_phi_dau_tu_ban_dau');
            if (tongChiPhiDauTuElement) {
                tongChiPhiDauTuElement.textContent = formatNumberWithDots(tongChiPhiDauTuBanDau);
            }

            // 2. Chi phí hoạt động hàng tháng để đạt hòa vốn
            // Lấy sản lượng hòa vốn
            const sanLuongHoaVonElement = document.getElementById('san_luong_hoa_von_thang');
            const sanLuongHoaVon = parseFloat(sanLuongHoaVonElement?.textContent?.replace(/\./g, '')) || 0;

            // Lấy chi phí cố định hàng tháng
            const chiPhiCoDinhThang = dataManager.getAllItems()
                .filter(item => !item.isEmpty && (item.category === 'fixed' || item.category === 'fixed_direct' || item.category === 'depreciated'))
                .reduce((sum, item) => sum + item.getMonthlyAllocation(), 0);

            // Lấy biến phí đơn vị
            const bienPhiDonViElement = document.getElementById('bien_phi_don_vi_thang');
            const bienPhiDonVi = parseFloat(bienPhiDonViElement?.textContent?.replace(/\./g, '')) || 0;

            // Chi phí hoạt động để hòa vốn = Chi phí cố định + (Biến phí đơn vị × Sản lượng hòa vốn)
            const chiPhiHoatDongHoaVon = chiPhiCoDinhThang + (bienPhiDonVi * sanLuongHoaVon);

            const chiPhiHoatDongHoaVonElement = document.getElementById('chi_phi_hoat_dong_hang_thang_hoa_von');
            if (chiPhiHoatDongHoaVonElement) {
                chiPhiHoatDongHoaVonElement.textContent = formatNumberWithDots(chiPhiHoatDongHoaVon);
            }

            // 3. Chi phí hoạt động hàng tháng để đạt sản lượng dự kiến
            // Lấy lượng bán dự kiến
            const luongBanDuKienElement = document.getElementById('input_luong_ban_du_kien_thang');
            const luongBanDuKien = parseFloat(luongBanDuKienElement?.value) || 0;

            // Chi phí hoạt động để đạt sản lượng dự kiến = Chi phí cố định + (Biến phí đơn vị × Lượng bán dự kiến)
            const chiPhiHoatDongSanLuongDuKien = chiPhiCoDinhThang + (bienPhiDonVi * luongBanDuKien);

            const chiPhiHoatDongSanLuongDuKienElement = document.getElementById('chi_phi_hoat_dong_hang_thang_san_luong_du_kien');
            if (chiPhiHoatDongSanLuongDuKienElement) {
                chiPhiHoatDongSanLuongDuKienElement.textContent = formatNumberWithDots(chiPhiHoatDongSanLuongDuKien);
            }

            // 4. Tính toán các chỉ số tài chính bổ sung cho cả 4 cột

            // Tính toán cho từng cột (1-4)
            for (let i = 1; i <= 4; i++) {
                // Lấy lợi nhuận net từ bảng tính toán cột i
                const loiNhuanNetElement = document.getElementById(`loi_nhuan_net_${i}`);
                const loiNhuanNetThang = parseFloat(loiNhuanNetElement?.textContent?.replace(/\./g, '')) || 0;

                // Lấy thu hồi khấu hao từ bảng tính toán cột i
                const thuHoiKhauHaoElement = document.getElementById(`thu_hoi_khau_hao_dan_${i}`);
                const thuHoiKhauHaoThang = parseFloat(thuHoiKhauHaoElement?.textContent?.replace(/\./g, '')) || 0;

                // 4.1. Tổng lợi nhuận net hàng năm = Lợi nhuận net * 12
                const tongLoiNhuanNetHangNam = loiNhuanNetThang * 12;
                const tongLoiNhuanNetHangNamElement = document.getElementById(`tong_loi_nhuan_net_hang_nam_${i}`);
                if (tongLoiNhuanNetHangNamElement) {
                    tongLoiNhuanNetHangNamElement.textContent = formatNumberWithDots(tongLoiNhuanNetHangNam);
                }

                // 4.2. Tổng thu khấu hao hàng năm = Thu hồi khấu hao dần * 12
                const tongThuKhauHaoHangNam = thuHoiKhauHaoThang * 12;
                const tongThuKhauHaoHangNamElement = document.getElementById(`tong_thu_khau_hao_hang_nam_${i}`);
                if (tongThuKhauHaoHangNamElement) {
                    tongThuKhauHaoHangNamElement.textContent = formatNumberWithDots(tongThuKhauHaoHangNam);
                }

                // 4.3. Tổng thu hồi tiền đầu tư một năm = Tổng lợi nhuận net hàng năm + Tổng thu khấu hao hàng năm
                const tongThuHoiTienDauTuMotNam = tongLoiNhuanNetHangNam + tongThuKhauHaoHangNam;
                const tongThuHoiTienDauTuMotNamElement = document.getElementById(`tong_thu_hoi_tien_dau_tu_mot_nam_${i}`);
                if (tongThuHoiTienDauTuMotNamElement) {
                    tongThuHoiTienDauTuMotNamElement.textContent = formatNumberWithDots(tongThuHoiTienDauTuMotNam);
                }

                // 4.4. Tổng thời gian thu hồi vốn = Tổng chi phí đầu tư ban đầu / Tổng thu hồi tiền đầu tư một năm
                const tongThoiGianThuHoiVon = tongThuHoiTienDauTuMotNam > 0 ? (tongChiPhiDauTuBanDau / tongThuHoiTienDauTuMotNam) : 0;
                const tongThoiGianThuHoiVonElement = document.getElementById(`tong_thoi_gian_thu_hoi_von_${i}`);
                if (tongThoiGianThuHoiVonElement) {
                    tongThoiGianThuHoiVonElement.textContent = tongThoiGianThuHoiVon.toFixed(2) + ' năm';
                }

                // 4.5. Tỷ suất lợi nhuận (ROI) hàng năm = Tổng lợi nhuận net hàng năm / Tổng chi phí đầu tư ban đầu
                const tyKuatLoiNhuanROIHangNam = tongChiPhiDauTuBanDau > 0 ? (tongLoiNhuanNetHangNam / tongChiPhiDauTuBanDau * 100) : 0;
                const tyKuatLoiNhuanROIHangNamElement = document.getElementById(`ty_suat_loi_nhuan_roi_hang_nam_${i}`);
                if (tyKuatLoiNhuanROIHangNamElement) {
                    tyKuatLoiNhuanROIHangNamElement.textContent = tyKuatLoiNhuanROIHangNam.toFixed(2) + '%';
                }

                // 4.6. Tổng lợi nhuận một năm sau khi hoàn vốn = Tổng thu hồi tiền đầu tư một năm
                const tongLoiNhuanMotNamSauKhiHoanVon = tongThuHoiTienDauTuMotNam;
                const tongLoiNhuanMotNamSauKhiHoanVonElement = document.getElementById(`tong_loi_nhuan_mot_nam_sau_khi_hoan_von_${i}`);
                if (tongLoiNhuanMotNamSauKhiHoanVonElement) {
                    tongLoiNhuanMotNamSauKhiHoanVonElement.textContent = formatNumberWithDots(tongLoiNhuanMotNamSauKhiHoanVon);
                }

                // 4.7. Tỷ suất lợi nhuận sau khi hoàn vốn = Tổng lợi nhuận một năm sau khi hoàn vốn / Tổng chi phí đầu tư ban đầu
                const tyKuatLoiNhuanSauKhiHoanVon = tongChiPhiDauTuBanDau > 0 ? (tongLoiNhuanMotNamSauKhiHoanVon / tongChiPhiDauTuBanDau * 100) : 0;
                const tyKuatLoiNhuanSauKhiHoanVonElement = document.getElementById(`ty_suat_loi_nhuan_sau_khi_hoan_von_${i}`);
                if (tyKuatLoiNhuanSauKhiHoanVonElement) {
                    tyKuatLoiNhuanSauKhiHoanVonElement.textContent = tyKuatLoiNhuanSauKhiHoanVon.toFixed(2) + '%';
                }

                console.log(`💼 Tính toán cột ${i}:`, {
                    loiNhuanNetThang: formatNumberWithDots(loiNhuanNetThang),
                    thuHoiKhauHaoThang: formatNumberWithDots(thuHoiKhauHaoThang),
                    tongLoiNhuanNetHangNam: formatNumberWithDots(tongLoiNhuanNetHangNam),
                    tongThuKhauHaoHangNam: formatNumberWithDots(tongThuKhauHaoHangNam),
                    tongThuHoiTienDauTuMotNam: formatNumberWithDots(tongThuHoiTienDauTuMotNam),
                    tongThoiGianThuHoiVon: tongThoiGianThuHoiVon.toFixed(2) + ' năm',
                    tyKuatLoiNhuanROIHangNam: tyKuatLoiNhuanROIHangNam.toFixed(2) + '%',
                    tongLoiNhuanMotNamSauKhiHoanVon: formatNumberWithDots(tongLoiNhuanMotNamSauKhiHoanVon),
                    tyKuatLoiNhuanSauKhiHoanVon: tyKuatLoiNhuanSauKhiHoanVon.toFixed(2) + '%'
                });
            }

            console.log(`💼 Tính toán tổng hợp dự án hoàn tất:`, {
                depreciationTotal: formatNumberWithDots(depreciationTotal),
                fixedDirectTotal: formatNumberWithDots(fixedDirectTotal),
                variableInitialTotal: formatNumberWithDots(variableInitialTotal),
                tongChiPhiDauTuBanDau: formatNumberWithDots(tongChiPhiDauTuBanDau),
                sanLuongHoaVon: formatNumberWithDots(sanLuongHoaVon),
                chiPhiCoDinhThang: formatNumberWithDots(chiPhiCoDinhThang),
                bienPhiDonVi: formatNumberWithDots(bienPhiDonVi),
                chiPhiHoatDongHoaVon: formatNumberWithDots(chiPhiHoatDongHoaVon),
                luongBanDuKien: formatNumberWithDots(luongBanDuKien),
                chiPhiHoatDongSanLuongDuKien: formatNumberWithDots(chiPhiHoatDongSanLuongDuKien),
                message: "✅ Đã tính toán thành công cho cả 4 cột sản lượng"
            });

            // Copy sản lượng bán từ input xuống bảng hiển thị
            copySanLuongBanToDisplay();

            // Cập nhật bảng phân bổ 12 tháng
            updateMonthlyAllocationTable();
        }

        /**
         * Copy giá trị sản lượng bán từ 4 input xuống bảng hiển thị
         */
        function copySanLuongBanToDisplay() {
            for (let i = 1; i <= 4; i++) {
                const inputElement = document.getElementById(`input_san_luong_ban_du_kien_thang_${i}`);
                const displayElement = document.getElementById(`san_luong_ban_${i}`);

                if (inputElement && displayElement) {
                    const value = parseFloat(inputElement.value) || 0;
                    displayElement.textContent = formatNumberWithDots(value);
                }
            }

            console.log(`📋 Đã copy sản lượng bán từ input xuống bảng hiển thị`);
        }

        /**
         * Tính toán và cập nhật bảng phân bổ theo 12 tháng (dựa trên kịch bản 4)
         */
        function updateMonthlyAllocationTable() {
            console.log(`📅 Bắt đầu cập nhật bảng phân bổ 12 tháng...`);

            // Lấy giá trị từ kịch bản 4
            const giaBanDuKien = parseFloat(document.getElementById('input_gia_ban_du_kien')?.value) || 0;
            const sanLuongThang = parseFloat(document.getElementById('input_san_luong_ban_du_kien_thang_4')?.value) || 0;

            // Lấy chi phí cố định hàng tháng (từ kịch bản 4)
            const chiPhiCoDinhElement = document.getElementById('chi_phi_co_dinh_thang_4');
            const chiPhiCoDinh = parseFloat(chiPhiCoDinhElement?.textContent?.replace(/\./g, '')) || 0;

            // Lấy chi phí biến đổi hàng tháng (từ kịch bản 4)
            const chiPhiBienDoiElement = document.getElementById('chi_phi_bien_doi_thang_4');
            const chiPhiBienDoi = parseFloat(chiPhiBienDoiElement?.textContent?.replace(/\./g, '')) || 0;

            // Lấy khấu hao hàng tháng
            const khauHaoHangThangElement = document.getElementById('chi_phi_khau_hao_hang_thang');
            const khauHaoHangThang = parseFloat(khauHaoHangThangElement?.textContent?.replace(/\./g, '')) || 0;

            console.log(`📊 Dữ liệu đầu vào:`, {
                giaBanDuKien,
                sanLuongThang,
                chiPhiCoDinh,
                chiPhiBienDoi,
                khauHaoHangThang
            });

            // Tính toán cho 12 tháng
            let tongSanLuong = 0;
            let tongChiPhiCoDinh = 0;
            let tongChiPhiBienDoi = 0;
            let tongChiPhi = 0;
            let tongDoanhThu = 0;
            let tongLoiNhuanThuan = 0;
            let tongThue = 0;
            let tongLoiNhuanNet = 0;
            let tongThuHoiKhauHao = 0;

            for (let i = 1; i <= 12; i++) {
                // Sản lượng bán - đọc từ input (hoặc dùng default từ kịch bản 4)
                const inputElement = document.getElementById(`san_luong_thang_${i}`);
                let sanLuong = 0;

                if (inputElement && inputElement.tagName === 'INPUT') {
                    // Nếu là input, lấy giá trị từ input hoặc dùng default
                    sanLuong = parseFloat(inputElement.value) || sanLuongThang;
                    // Set giá trị mặc định nếu input rỗng hoặc lần đầu load
                    if (!inputElement.value || inputElement.value == '0') {
                        inputElement.value = sanLuongThang;
                        sanLuong = sanLuongThang;
                    }
                } else {
                    // Fallback: nếu là td, chỉ hiển thị
                    sanLuong = sanLuongThang;
                    if (inputElement) {
                        inputElement.textContent = formatNumberWithDots(sanLuong);
                    }
                }
                tongSanLuong += sanLuong;

                // Chi phí cố định (giống nhau mỗi tháng)
                const cpCoDinh = chiPhiCoDinh;
                document.getElementById(`cp_co_dinh_thang_${i}`).textContent = formatNumberWithDots(cpCoDinh);
                tongChiPhiCoDinh += cpCoDinh;

                // Chi phí biến đổi (tính theo sản lượng thực tế của tháng đó)
                // Chi phí biến đổi đơn vị = chi phí biến đổi kịch bản 4 / sản lượng kịch bản 4
                const chiPhiBienDoiDonVi = sanLuongThang > 0 ? chiPhiBienDoi / sanLuongThang : 0;
                const cpBienDoi = chiPhiBienDoiDonVi * sanLuong;
                document.getElementById(`cp_bien_doi_thang_${i}`).textContent = formatNumberWithDots(cpBienDoi);
                tongChiPhiBienDoi += cpBienDoi;

                // Tổng chi phí = Chi phí cố định + Chi phí biến đổi
                const tongCp = cpCoDinh + cpBienDoi;
                document.getElementById(`tong_cp_thang_${i}`).textContent = formatNumberWithDots(tongCp);
                tongChiPhi += tongCp;

                // Doanh thu thuần = Giá bán × Sản lượng
                const doanhThu = giaBanDuKien * sanLuong;
                document.getElementById(`doanh_thu_thang_${i}`).textContent = formatNumberWithDots(doanhThu);
                tongDoanhThu += doanhThu;

                // Lợi nhuận thuần = Doanh thu - Tổng chi phí
                const loiNhuanThuan = doanhThu - tongCp;
                document.getElementById(`loi_nhuan_thuan_thang_${i}`).textContent = formatNumberWithDots(loiNhuanThuan);
                tongLoiNhuanThuan += loiNhuanThuan;

                // Thuế 20%
                const thue = loiNhuanThuan * 0.2;
                document.getElementById(`thue_thang_${i}`).textContent = formatNumberWithDots(thue);
                tongThue += thue;

                // Lợi nhuận net = Lợi nhuận thuần - Thuế
                const loiNhuanNet = loiNhuanThuan - thue;
                document.getElementById(`loi_nhuan_net_thang_${i}`).textContent = formatNumberWithDots(loiNhuanNet);
                tongLoiNhuanNet += loiNhuanNet;

                // Thu hồi khấu hao (giống nhau mỗi tháng)
                const thuHoiKhauHao = khauHaoHangThang;
                document.getElementById(`thu_hoi_khau_hao_thang_${i}`).textContent = formatNumberWithDots(thuHoiKhauHao);
                tongThuHoiKhauHao += thuHoiKhauHao;
            }

            // Cập nhật cột tổng năm
            document.getElementById('san_luong_tong_nam').textContent = formatNumberWithDots(tongSanLuong);
            document.getElementById('cp_co_dinh_tong_nam').textContent = formatNumberWithDots(tongChiPhiCoDinh);
            document.getElementById('cp_bien_doi_tong_nam').textContent = formatNumberWithDots(tongChiPhiBienDoi);
            document.getElementById('tong_cp_tong_nam').textContent = formatNumberWithDots(tongChiPhi);
            document.getElementById('doanh_thu_tong_nam').textContent = formatNumberWithDots(tongDoanhThu);
            document.getElementById('loi_nhuan_thuan_tong_nam').textContent = formatNumberWithDots(tongLoiNhuanThuan);
            document.getElementById('thue_tong_nam').textContent = formatNumberWithDots(tongThue);
            document.getElementById('loi_nhuan_net_tong_nam').textContent = formatNumberWithDots(tongLoiNhuanNet);
            document.getElementById('thu_hoi_khau_hao_tong_nam').textContent = formatNumberWithDots(tongThuHoiKhauHao);

            console.log(`✅ Đã cập nhật bảng phân bổ 12 tháng thành công!`, {
                tongSanLuong,
                tongDoanhThu,
                tongLoiNhuanNet
            });

            // Tính số lượng nhân viên cần thiết
            calculateSoLuongNhanVien();
            
            // Tính số khách hàng tiềm năng
            calculateSoKhTiemNang();
            
            // Tính KH tiềm năng / nhân viên
            calculateKhTiemNangPerNv();
            
            // Tính Marketing Funnel
            calculateMarketingFunnel();
        }

        /**
         * Tính số lượng nhân viên cần thiết cho mỗi tháng
         * Công thức: so_luong_nv_thang_i = san_luong_thang_i / nang_luc_nv_ban_thang_i
         */
        function calculateSoLuongNhanVien() {
            console.log(`👥 Tính số lượng nhân viên cần thiết...`);

            for (let i = 1; i <= 12; i++) {
                const sanLuongInput = document.getElementById(`san_luong_thang_${i}`);
                const nangLucNvInput = document.getElementById(`nang_luc_nv_ban_thang_${i}`);
                const soLuongNvOutput = document.getElementById(`so_luong_nv_thang_${i}`);

                if (sanLuongInput && nangLucNvInput && soLuongNvOutput) {
                    const sanLuong = parseFloat(sanLuongInput.value) || 0;
                    const nangLucNv = parseFloat(nangLucNvInput.value) || 0;

                    let soLuongNv = 0;
                    if (nangLucNv > 0) {
                        soLuongNv = Math.ceil(sanLuong / nangLucNv); // Làm tròn lên
                    }

                    soLuongNvOutput.value = soLuongNv > 0 ? soLuongNv : '';
                    console.log(`   Tháng ${i}: ${sanLuong} / ${nangLucNv} = ${soLuongNv} NV`);
                }
            }

            console.log(`✅ Đã tính số lượng nhân viên xong`);
        }

        /**
         * Tính số khách hàng tiềm năng cho mỗi tháng
         * so_kh_tiem_nang_thang_i = san_luong_thang_i * ty_le_chuyen_doi_thang_i
         */
        function calculateSoKhTiemNang() {
            console.log(`🎯 Tính số khách hàng tiềm năng...`);

            for (let i = 1; i <= 12; i++) {
                const sanLuongInput = document.getElementById(`san_luong_thang_${i}`);
                const tyLeChuyenDoiInput = document.getElementById(`ty_le_chuyen_doi_thang_${i}`);
                const soKhTiemNangOutput = document.getElementById(`so_kh_tiem_nang_thang_${i}`);

                if (sanLuongInput && tyLeChuyenDoiInput && soKhTiemNangOutput) {
                    const sanLuong = parseFloat(sanLuongInput.value) || 0;
                    const tyLeChuyenDoi = parseFloat(tyLeChuyenDoiInput.value) || 0;

                    const soKhTiemNang = Math.round(sanLuong / tyLeChuyenDoi);

                    soKhTiemNangOutput.value = soKhTiemNang > 0 ? soKhTiemNang : '';
                    console.log(`   Tháng ${i}: ${sanLuong} / ${tyLeChuyenDoi} = ${soKhTiemNang} KH tiềm năng`);
                }
            }

            console.log(`✅ Đã tính số khách hàng tiềm năng xong`);
        }

        /**
         * Tính số KH tiềm năng / nhân viên cho mỗi tháng
         * kh_tiem_nang_per_nv_thang_i = so_kh_tiem_nang_thang_i / so_luong_nv_thang_i
         */
        function calculateKhTiemNangPerNv() {
            console.log(`👤 Tính KH tiềm năng / nhân viên...`);

            for (let i = 1; i <= 12; i++) {
                const soKhTiemNangInput = document.getElementById(`so_kh_tiem_nang_thang_${i}`);
                const soLuongNvInput = document.getElementById(`so_luong_nv_thang_${i}`);
                const khPerNvOutput = document.getElementById(`kh_tiem_nang_per_nv_thang_${i}`);

                if (soKhTiemNangInput && soLuongNvInput && khPerNvOutput) {
                    const soKhTiemNang = parseFloat(soKhTiemNangInput.value) || 0;
                    const soLuongNv = parseFloat(soLuongNvInput.value) || 0;

                    let khPerNv = 0;
                    if (soLuongNv > 0) {
                        khPerNv = Math.round(soKhTiemNang / soLuongNv);
                    }

                    khPerNvOutput.value = khPerNv > 0 ? khPerNv : '';
                    console.log(`   Tháng ${i}: ${soKhTiemNang} / ${soLuongNv} = ${khPerNv} KH/NV`);
                }
            }

            console.log(`✅ Đã tính KH tiềm năng / nhân viên xong`);
        }

        /**
         * Tính Marketing Funnel cho mỗi tháng
         * lien_lac_thang_i = san_luong_thang_i / ty_le_chuyen_doi_lien_lac
         * tuong_tac_chat_thang_i = lien_lac_thang_i / ty_le_chuyen_doi_chat
         * tuong_tac_like_thang_i = tuong_tac_chat_thang_i / ty_le_chuyen_doi_like
         * tuong_tac_seen_thang_i = tuong_tac_like_thang_i / ty_le_chuyen_doi_seen
         */
        function calculateMarketingFunnel() {
            console.log(`📊 Tính Marketing Funnel...`);

            // Lấy tỷ lệ chuyển đổi
            const tyLeLienLac = parseFloat(document.getElementById('ty_le_chuyen_doi_lien_lac')?.value) || 0;
            const tyLeChat = parseFloat(document.getElementById('ty_le_chuyen_doi_chat')?.value) || 0;
            const tyLeLike = parseFloat(document.getElementById('ty_le_chuyen_doi_like')?.value) || 0;
            const tyLeSeen = parseFloat(document.getElementById('ty_le_chuyen_doi_seen')?.value) || 0;

            console.log(`   Tỷ lệ: Liên lạc=${tyLeLienLac}, Chat=${tyLeChat}, Like=${tyLeLike}, Seen=${tyLeSeen}`);

            for (let i = 1; i <= 12; i++) {
                const sanLuongInput = document.getElementById(`san_luong_thang_${i}`);
                const lienLacOutput = document.getElementById(`lien_lac_thang_${i}`);
                const tuongTacChatOutput = document.getElementById(`tuong_tac_chat_thang_${i}`);
                const tuongTacLikeOutput = document.getElementById(`tuong_tac_like_thang_${i}`);
                const tuongTacSeenOutput = document.getElementById(`tuong_tac_seen_thang_${i}`);

                const sanLuong = parseFloat(sanLuongInput?.value) || 0;

                // Tính từng bước funnel
                let lienLac = 0;
                let tuongTacChat = 0;
                let tuongTacLike = 0;
                let tuongTacSeen = 0;

                if (tyLeLienLac > 0) {
                    lienLac = Math.round(sanLuong * tyLeLienLac);
                }
                if (tyLeChat > 0) {
                    tuongTacChat = Math.round(lienLac * tyLeChat);
                }
                if (tyLeLike > 0) {
                    tuongTacLike = Math.round(tuongTacChat * tyLeLike);
                }
                if (tyLeSeen > 0) {
                    tuongTacSeen = Math.round(tuongTacLike * tyLeSeen);
                }

                // Gán giá trị
                if (lienLacOutput) lienLacOutput.value = lienLac > 0 ? lienLac : '';
                if (tuongTacChatOutput) tuongTacChatOutput.value = tuongTacChat > 0 ? tuongTacChat : '';
                if (tuongTacLikeOutput) tuongTacLikeOutput.value = tuongTacLike > 0 ? tuongTacLike : '';
                if (tuongTacSeenOutput) tuongTacSeenOutput.value = tuongTacSeen > 0 ? tuongTacSeen : '';

                console.log(`   Tháng ${i}: SL=${sanLuong} → Liên lạc=${lienLac} → Chat=${tuongTacChat} → Like=${tuongTacLike} → Seen=${tuongTacSeen}`);
            }

            console.log(`✅ Đã tính Marketing Funnel xong`);
        }



        /**
         * Attach event listeners cho các nút +/- của sản lượng theo tháng
         */
        function attachMonthlyInputListeners() {
            console.log(`🎯 Đang attach event listeners cho các nút +/-...`);

            // Lấy tất cả các nút tăng/giảm
            const buttons = document.querySelectorAll('.monthly-btn');

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const inputElement = document.getElementById(targetId);

                    if (!inputElement) return;

                    // Đếm số ô chưa locked (không tính ô hiện tại)
                    const unlockedInputs = [];
                    for (let i = 1; i <= 12; i++) {
                        const otherId = `san_luong_thang_${i}`;
                        if (otherId !== targetId) {
                            const otherInput = document.getElementById(otherId);
                            if (otherInput && !otherInput.classList.contains('locked')) {
                                unlockedInputs.push(otherInput);
                            }
                        }
                    }

                    const unlockedCount = unlockedInputs.length;

                    if (unlockedCount === 0) {
                        console.log(`⚠️ Tất cả các ô khác đã bị lock, không thể điều chỉnh`);
                        return;
                    }

                    // Delta phụ thuộc vào số ô unlocked để đảm bảo chia đều được
                    // Delta = số ô unlocked, để mỗi ô điều chỉnh ±1
                    const isIncrease = this.classList.contains('increase');
                    const delta = isIncrease ? unlockedCount : -unlockedCount;

                    // Lấy giá trị hiện tại
                    const currentValue = parseFloat(inputElement.value) || 0;
                    const newValue = Math.max(0, currentValue + delta);

                    // Kiểm tra nếu giảm xuống âm thì không làm gì
                    if (newValue < 0) {
                        console.log(`⚠️ Không thể giảm xuống âm`);
                        return;
                    }

                    console.log(`🔄 ${isIncrease ? 'Tăng' : 'Giảm'} ${targetId}: ${currentValue} → ${newValue}, delta: ${delta} (${unlockedCount} ô unlocked)`);

                    // Cập nhật giá trị input
                    inputElement.value = newValue;

                    // Đánh dấu ô này là locked (đã được thay đổi thủ công)
                    inputElement.classList.add('locked');
                    inputElement.setAttribute('data-locked', 'true');

                    console.log(`🔒 Đã lock ${targetId}`);

                    // Điều chỉnh mỗi ô unlocked ±1 (ngược dấu với delta)
                    // delta = +N → mỗi ô -1
                    // delta = -N → mỗi ô +1
                    const adjustmentPerCell = isIncrease ? -1 : 1;

                    console.log(`📊 Điều chỉnh ${unlockedCount} ô chưa lock: ${adjustmentPerCell} mỗi ô`);

                    // Cập nhật các ô chưa locked
                    unlockedInputs.forEach(otherInput => {
                        const otherValue = parseFloat(otherInput.value) || 0;
                        const otherNewValue = otherValue + adjustmentPerCell;
                        otherInput.value = Math.max(0, otherNewValue); // Không cho âm
                    });

                    // Tính lại toàn bộ bảng
                    clearTimeout(window.monthlyInputTimeout);
                    window.monthlyInputTimeout = setTimeout(() => {
                        updateMonthlyAllocationTable();
                    }, 100);

                    // Kiểm tra tổng
                    let total = 0;
                    for (let i = 1; i <= 12; i++) {
                        const inp = document.getElementById(`san_luong_thang_${i}`);
                        total += parseFloat(inp?.value) || 0;
                    }
                    console.log(`✅ Tổng sau khi điều chỉnh: ${total}`);

                    // Auto-save tất cả các giá trị san_luong_thang vào API (debounce)
                    clearTimeout(window.sanLuongAutoSaveTimeout);
                    window.sanLuongAutoSaveTimeout = setTimeout(() => {
                        saveSanLuongThangToAPI();
                    }, 1000); // Đợi 1 giây sau khi dừng bấm
                });
            });

            console.log(`🎯 Đã attach event listeners cho ${buttons.length} nút`);
        }

        /**
         * Lưu tất cả giá trị san_luong_thang vào API
         */
        async function saveSanLuongThangToAPI() {
            if (!currentPlanId) {
                console.warn('⚠️ No plan selected, cannot save san_luong_thang');
                return;
            }

            console.log(`💾 Đang lưu san_luong_thang vào API...`);

            // Thu thập tất cả giá trị
            const params = new URLSearchParams();
            params.append('plan_id', currentPlanId);

            for (let i = 1; i <= 12; i++) {
                const input = document.getElementById(`san_luong_thang_${i}`);
                if (input && input.value) {
                    params.append(`san_luong_thang_${i}`, input.value);
                }
            }

            try {
                const url = `/api/member-plan-define-value/update_val?${params.toString()}`;
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                if (result.code === 1) {
                    console.log(`✅ Đã lưu san_luong_thang thành công`);
                    showStatus('Đã lưu sản lượng 12 tháng', 'success');
                } else {
                    throw new Error(result.message || 'Failed to save');
                }
            } catch (error) {
                console.error('❌ Error saving san_luong_thang:', error);
                showStatus('Lỗi khi lưu sản lượng: ' + error.message, 'error');
            }
        }

        /**
         * Force update summary row immediately (bypass debounce)
         */
        function updateSummaryRowByTabImmediate(tabId) {
            console.log(`🔄 updateSummaryRowByTabImmediate(${tabId}) - FORCE EXECUTING`);

            // Clear any pending debounced update
            if (summaryUpdateTimeout) {
                clearTimeout(summaryUpdateTimeout);
                summaryUpdateTimeout = null;
            }

            // Execute immediately
            const totalAmount = calculateTotalForColTotal(tabId);
            const totalDepreciation = calculateTotalForColDepreciation(tabId);
            const totalUnitDepreciation = calculateTotalForColUnitDepreciation(tabId);
            const totalMonthlyAllocation = calculateTotalForColMonthlyAllocation(tabId);

            // ✅ FIX: ID elements are always 'cost', not dynamic tabId
            const summaryTotalElement = document.getElementById('summary-total-cost');
            const summaryDepreciationElement = document.getElementById('summary-depreciation-cost');
            const summaryUnitDepreciationElement = document.getElementById('summary-unit-depreciation-cost');
            const summaryMonthlyElement = document.getElementById('summary-monthly-cost');
            const summaryYearlyElement = document.getElementById('summary-yearly-cost');

            if (summaryTotalElement) {
                summaryTotalElement.textContent = formatNumberWithDots(totalAmount);
                console.log(`✅ Updated summary-total-cost: ${formatNumberWithDots(totalAmount)}`);
            }

            if (summaryDepreciationElement) {
                summaryDepreciationElement.textContent = totalDepreciation;
                console.log(`✅ Updated summary-depreciation-cost: ${totalDepreciation}`);
            }

            if (summaryUnitDepreciationElement) {
                summaryUnitDepreciationElement.textContent = formatNumberWithDots(totalUnitDepreciation);
                console.log(`✅ Updated summary-unit-depreciation-cost: ${formatNumberWithDots(totalUnitDepreciation)}`);
            }

            if (summaryMonthlyElement) {
                summaryMonthlyElement.textContent = formatNumberWithDots(totalMonthlyAllocation);
                console.log(`✅ Updated summary-monthly-cost: ${formatNumberWithDots(totalMonthlyAllocation)}`);
            }

            // Calculate total yearly allocation based on current tab's filtered items
            const totalYearlyAllocation = dataManager.getItemsByTab(tabId)
                .filter(item => !item.isEmpty)
                .reduce((sum, item) => sum + item.getYearlyAllocation(), 0);

            if (summaryYearlyElement) {
                summaryYearlyElement.textContent = formatNumberWithDots(totalYearlyAllocation);
                console.log(`✅ Updated summary-yearly-cost for tab ${tabId}: ${formatNumberWithDots(totalYearlyAllocation)}`);
            }

            // Cập nhật text summary (số tiền bằng chữ) với null safety
            const totalTextElement = document.getElementById('summary-total-text-cost');
            const monthlyTextElement = document.getElementById('summary-monthly-text-cost');
            const yearlyTextElement = document.getElementById('summary-yearly-text-cost');

            if (totalTextElement) {
                totalTextElement.textContent = numberToVietnameseWords(totalAmount);
                console.log(`✅ Updated summary-total-text-cost`);
            }

            if (monthlyTextElement) {
                monthlyTextElement.textContent = numberToVietnameseWords(totalMonthlyAllocation);
                console.log(`✅ Updated summary-monthly-text-cost`);
            }

            if (yearlyTextElement) {
                yearlyTextElement.textContent = numberToVietnameseWords(totalYearlyAllocation);
                console.log(`✅ Updated summary-yearly-text-cost`);
            }

            // Cập nhật tổng chi phí cố định trực tiếp hàng tháng
            const totals = dataManager.calculateTotalsByTab('cost'); // Tính từ tất cả items
            const tongCpcdTrucTiepHangThangElement = document.getElementById('tong_cpcd_truc_tiep_hang_thang');
            if (tongCpcdTrucTiepHangThangElement) {
                tongCpcdTrucTiepHangThangElement.textContent = formatNumberWithDots(totals.fixedSum);
                console.log(`✅ Updated tong_cpcd_truc_tiep_hang_thang: ${formatNumberWithDots(totals.fixedSum)}`);
            }

            // Cập nhật tổng chi phí cố định trực tiếp hàng năm
            const tongCpcdTrucTiepHangNamElement = document.getElementById('tong_cpcd_truc_tiep_hang_nam');
            if (tongCpcdTrucTiepHangNamElement) {
                const yearlyFixedSum = totals.fixedSum * 12;
                tongCpcdTrucTiepHangNamElement.textContent = formatNumberWithDots(yearlyFixedSum);
                console.log(`✅ Updated tong_cpcd_truc_tiep_hang_nam: ${formatNumberWithDots(yearlyFixedSum)}`);
            }

            // Cập nhật tổng chi phí biến đổi hàng tháng
            const variableCostMonthlyTotalImmediate = dataManager.getItemsByTab('variable-cost')
                .filter(item => !item.isEmpty)
                .reduce((sum, item) => sum + item.getTotal(), 0);

            const tongChiPhiBienDoiHangThangElementImmediate = document.getElementById('tong_chi_phi_bien_doi_hang_thang');
            if (tongChiPhiBienDoiHangThangElementImmediate) {
                tongChiPhiBienDoiHangThangElementImmediate.textContent = formatNumberWithDots(variableCostMonthlyTotalImmediate);
                console.log(`✅ Updated tong_chi_phi_bien_doi_hang_thang: ${formatNumberWithDots(variableCostMonthlyTotalImmediate)}`);
            }

            // Cập nhật tổng chi phí biến đổi hàng năm
            const variableCostYearlyTotalImmediate = dataManager.getItemsByTab('variable-cost')
                .filter(item => !item.isEmpty)
                .reduce((sum, item) => sum + item.getYearlyAllocation(), 0);

            const tongChiPhiBienDoiHangNamElementImmediate = document.getElementById('tong_chi_phi_bien_doi_hang_nam');
            if (tongChiPhiBienDoiHangNamElementImmediate) {
                tongChiPhiBienDoiHangNamElementImmediate.textContent = formatNumberWithDots(variableCostYearlyTotalImmediate);
                console.log(`✅ Updated tong_chi_phi_bien_doi_hang_nam: ${formatNumberWithDots(variableCostYearlyTotalImmediate)}`);
            }

            // Cập nhật tổng CP Bán hàng hàng tháng (Immediate)
            const tongCpBanHangHangThangImmediate = dataManager.getItemsByTab('variable-cost')
                .filter(item => !item.isEmpty && item.option1 === 'ban_hang')
                .reduce((sum, item) => sum + item.getTotal(), 0);

            const tongCpBanHangHangThangElementImmediate = document.getElementById('tong_cp_ban_hang_hang_thang');
            if (tongCpBanHangHangThangElementImmediate) {
                tongCpBanHangHangThangElementImmediate.textContent = formatNumberWithDots(tongCpBanHangHangThangImmediate);
                console.log(`✅ Updated tong_cp_ban_hang_hang_thang (immediate): ${formatNumberWithDots(tongCpBanHangHangThangImmediate)}`);
            }

            // Cập nhật tổng CP Bán hàng hàng năm (Immediate)
            const tongCpBanHangHangNamImmediate = tongCpBanHangHangThangImmediate * 12;
            const tongCpBanHangHangNamElementImmediate = document.getElementById('tong_cp_ban_hang_hang_nam');
            if (tongCpBanHangHangNamElementImmediate) {
                tongCpBanHangHangNamElementImmediate.textContent = formatNumberWithDots(tongCpBanHangHangNamImmediate);
                console.log(`✅ Updated tong_cp_ban_hang_hang_nam (immediate): ${formatNumberWithDots(tongCpBanHangHangNamImmediate)}`);
            }

            // Cập nhật tổng CP Giá vốn hàng tháng (Immediate)
            const tongCpGiaVonHangThangImmediate = dataManager.getItemsByTab('variable-cost')
                .filter(item => !item.isEmpty && item.option1 === 'gia_von')
                .reduce((sum, item) => sum + item.getTotal(), 0);

            const tongCpGiaVonHangThangElementImmediate = document.getElementById('tong_cp_gia_von_hang_thang');
            if (tongCpGiaVonHangThangElementImmediate) {
                tongCpGiaVonHangThangElementImmediate.textContent = formatNumberWithDots(tongCpGiaVonHangThangImmediate);
                console.log(`✅ Updated tong_cp_gia_von_hang_thang (immediate): ${formatNumberWithDots(tongCpGiaVonHangThangImmediate)}`);
            }

            // Cập nhật tổng CP Giá vốn hàng năm (Immediate)
            const tongCpGiaVonHangNamImmediate = tongCpGiaVonHangThangImmediate * 12;
            const tongCpGiaVonHangNamElementImmediate = document.getElementById('tong_cp_gia_von_hang_nam');
            if (tongCpGiaVonHangNamElementImmediate) {
                tongCpGiaVonHangNamElementImmediate.textContent = formatNumberWithDots(tongCpGiaVonHangNamImmediate);
                console.log(`✅ Updated tong_cp_gia_von_hang_nam (immediate): ${formatNumberWithDots(tongCpGiaVonHangNamImmediate)}`);
            }

            // Cập nhật các tỷ lệ
            updateRatioCalculations();

            // Cập nhật so sánh đầu tư
            updateInvestmentComparison();

            // Cập nhật tổng hợp dự án
            updateProjectSummaryCalculations();

            console.log(`✅ updateSummaryRowByTabImmediate(${tabId}) completed:`, {
                totalAmount: formatNumberWithDots(totalAmount),
                totalDepreciation,
                totalUnitDepreciation: formatNumberWithDots(totalUnitDepreciation),
                totalMonthlyAllocation: formatNumberWithDots(totalMonthlyAllocation),
                totalYearlyAllocation: formatNumberWithDots(totalYearlyAllocation),
                variableCostMonthlyTotal: formatNumberWithDots(variableCostMonthlyTotalImmediate),
                variableCostYearlyTotal: formatNumberWithDots(variableCostYearlyTotalImmediate),
                tongCpBanHangHangThang: formatNumberWithDots(tongCpBanHangHangThangImmediate),
                tongCpBanHangHangNam: formatNumberWithDots(tongCpBanHangHangNamImmediate),
                tongCpGiaVonHangThang: formatNumberWithDots(tongCpGiaVonHangThangImmediate),
                tongCpGiaVonHangNam: formatNumberWithDots(tongCpGiaVonHangNamImmediate)
            });
        }

        // ========== HELPER FUNCTIONS FOR DATAMANAGER INTEGRATION ==========

        /**
         * Đồng bộ dữ liệu từ DOM vào DataManager khi có thay đổi
         */
        function syncRowToDataManager(row) {
            const rowId = row.dataset.id;
            const isNew = row.dataset.isNew === 'true';

            // Lấy dữ liệu từ row
            const rowData = getRowData(row);

            if (isNew) {
                // Nếu là row mới, chỉ cập nhật khi có dữ liệu thực sự
                if (rowData.item_name || rowData.category || parseFloat(rowData.cost) > 0 || parseFloat(rowData.quantity) > 0) {
                    const existingItem = dataManager.getItem(rowId);
                    if (existingItem) {
                        dataManager.updateItem(rowId, rowData);
                    } else {
                        dataManager.addItem({
                            ...rowData,
                            id: rowId,
                            plan_id: currentPlanId
                        });
                    }
                }
            } else {
                // Cập nhật item có sẵn
                dataManager.updateItem(rowId, rowData);
            }
        }

        /**
         * Đồng bộ dữ liệu từ DataManager vào DOM sau khi API thành công
         */
        function syncDataManagerToDOM(item) {
            // Tìm row tương ứng
            const row = document.querySelector(`tr[data-id="${item.id}"]`);
            if (!row) return;

            // Cập nhật các input fields
            const itemNameInput = row.querySelector('.col-name input');
            const categorySelect = row.querySelector('.col-category select');
            const option1Select = row.querySelector('.col-option1 select');
            const costInput = row.querySelector('.col-cost .cell-input');
            const costDisplay = row.querySelector('.col-cost .cost-display-editable');
            const quantityInput = row.querySelector('.col-quantity input');
            const depreciationInput = row.querySelector('.col-depreciation input');
            const noteInput = row.querySelector('.col-note input');

            if (itemNameInput) itemNameInput.value = item.item_name || '';
            if (categorySelect) categorySelect.value = item.category || '';
            if (option1Select) option1Select.value = item.option1 || '';
            if (costInput) costInput.value = item.cost || '';
            if (costDisplay) costDisplay.value = item.cost ? formatNumberWithDots(item.cost) : '';
            if (quantityInput) quantityInput.value = item.quantity || '';
            if (depreciationInput) depreciationInput.value = item.depreciation || '';
            if (noteInput) noteInput.value = item.note || '';

            // Tính toán lại các giá trị
            calculateTotal(costInput || quantityInput);
        }

        /**
         * Cập nhật tất cả tính toán dựa trên DataManager
         */
        function updateAllCalculationsFromDataManager() {
            // Get current active tab from URL
            const params = getURLParams();
            const currentActiveTab = params.tabId || 'cost';

            // Always use the current active tab - no hardcoded 'cost'
            if (currentActiveTab === 'depreciation' || currentActiveTab === 'fixed-cost' || currentActiveTab === 'variable-cost') {
                calculateFilteredTotals('cost'); // Only for legacy compatibility
            }
            updateSummaryRowByTab(currentActiveTab); // Always use current tab
        }

        /**
         * Tạo item mới trong DataManager và DOM
         */
        function createNewItemInDataManager(formData) {
            const newItem = dataManager.addItem(formData);

            // Cập nhật legacy config.data
            const config = TAB_CONFIG['cost'];
            const existingIndex = config.data.findIndex(item => item.id === newItem.id);
            if (existingIndex === -1) {
                config.data.push(newItem.toAPIData());
            }

            return newItem;
        }

        /**
         * Xóa item khỏi DataManager và cập nhật DOM
         */
        function removeItemFromDataManager(itemId) {
            const deletedItem = dataManager.deleteItem(itemId);

            if (deletedItem) {
                // Cập nhật legacy config.data
                const config = TAB_CONFIG['cost'];
                const index = config.data.findIndex(item => item.id == itemId);
                if (index !== -1) {
                    config.data.splice(index, 1);
                }
            }

            return deletedItem;
        }

        // Auto-save variables
        const autoSaveTimeouts = new Map(); // Store timeout IDs for each row
        const AUTO_SAVE_DELAY = 3000; // 3 seconds
        let globalAutoSaveCount = 0; // Track number of active auto-saves

        // Row height configuration
        const ROW_HEIGHT = 35; // Height of each table row in pixels

        // URL parameter management
        const URL_PARAMS = {
            PROJECT_ID: 'pro_id',
            TAB: 'tab'
        };

        const TAB_MAPPING = {
            'cost': '1',
            'depreciation': '2',
            'fixed-cost': '3',
            'variable-cost': '4'
        };

        const TAB_REVERSE_MAPPING = {
            '1': 'cost',
            '2': 'depreciation',
            '3': 'fixed-cost',
            '4': 'variable-cost'
        };

        // URL Management Functions
        function updateURL(projectId = null, tabId = null) {
            const url = new URL(window.location);

            if (projectId !== null) {
                if (projectId) {
                    url.searchParams.set(URL_PARAMS.PROJECT_ID, projectId);
                } else {
                    url.searchParams.delete(URL_PARAMS.PROJECT_ID);
                }
            }

            if (tabId !== null) {
                const tabNumber = TAB_MAPPING[tabId];
                if (tabNumber) {
                    url.searchParams.set(URL_PARAMS.TAB, tabNumber);
                } else {
                    url.searchParams.delete(URL_PARAMS.TAB);
                }
            }

            window.history.pushState({}, '', url);
        }

        function getURLParams() {
            const urlParams = new URLSearchParams(window.location.search);
            return {
                projectId: urlParams.get(URL_PARAMS.PROJECT_ID),
                tabId: TAB_REVERSE_MAPPING[urlParams.get(URL_PARAMS.TAB)] || null
            };
        }

        async function initializeFromURL() {
            const params = getURLParams();

            // Set initial tab (default to 'depreciation' if no tab in URL)
            const targetTabId = params.tabId || 'depreciation';
            currentTab = 'cost'; // Always use cost as the actual tab

            // Set body data attribute for CSS tab-specific styling
            document.body.setAttribute('data-current-tab', targetTabId);

            // Update tab UI
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            // Find the correct tab button to activate based on URL
            const targetTabBtn = document.getElementById(`${targetTabId}-tab`);
            const targetTabContent = document.getElementById(`tab-cost`); // Always show cost content

            if (targetTabBtn && targetTabContent) {
                targetTabBtn.classList.add('active');
                targetTabContent.classList.add('active');
            } else {
                // Fallback to depreciation tab if target tab not found
                const depreciationTabBtn = document.getElementById(`depreciation-tab`);
                const costTabContent = document.getElementById(`tab-cost`);
                if (depreciationTabBtn && costTabContent) {
                    depreciationTabBtn.classList.add('active');
                    costTabContent.classList.add('active');
                }
            }

            // Update column headers for initial tab
            updateColumnHeaders(targetTabId);

            // Apply row filtering based on initial tab (this will also handle category column visibility)
            applyTabFiltering(targetTabId);

            // Ensure calculations are updated after initial tab setup with a small delay
            setTimeout(() => {
                if (targetTabId === 'depreciation' || targetTabId === 'fixed-cost' || targetTabId === 'variable-cost') {
                    calculateFilteredTotals('cost'); // Only for legacy compatibility
                }
                updateSummaryRowByTab(targetTabId); // Always use the target tab
            }, 200);

            // Update URL if no tab was specified (set default to depreciation)
            if (!params.tabId) {
                updateURL(null, 'depreciation');
            }

            // Set project if specified in URL
            if (params.projectId) {
                console.log(`🔗 Setting project from URL: ${params.projectId}`);

                // Plans should already be loaded at this point
                const planSelect = document.getElementById('planSelect');
                if (planSelect) {
                    // Check if the project exists in the dropdown
                    const projectOption = planSelect.querySelector(`option[value="${params.projectId}"]`);
                    if (projectOption) {
                        console.log(`✅ Project found in dropdown, selecting: ${params.projectId}`);
                        planSelect.value = params.projectId;

                        // Load data for the selected project
                        await new Promise(resolve => {
                            // Use setTimeout to ensure DOM is ready
                            setTimeout(() => {
                                handlePlanChange();
                                resolve();
                            }, 100);
                        });

                        console.log(`✅ Project data loaded for: ${params.projectId}`);
                    } else {
                        console.log(`❌ Project not found in dropdown: ${params.projectId}`);
                        showStatus(`Project with ID ${params.projectId} not found`, 'warning');
                    }
                } else {
                    console.log(`❌ Plan select element not found`);
                }
            }
        }

        // Function to update summary rows visibility based on current tab
        function updateSummaryRowsVisibility(tabId) {
            // Hide all summary rows first
            document.querySelectorAll('.show_in_khau_hao1, .show_in_chi_phi_co_dinh, .show_in_chi_phi_bien_doi').forEach(row => {
                row.style.display = 'none';
            });

            // Show appropriate rows based on tab
            switch(tabId) {
                case 'depreciation':
                    document.querySelectorAll('.show_in_khau_hao1').forEach(row => {
                        row.style.display = 'table-row';
                    });
                    break;
                case 'fixed-cost':
                    document.querySelectorAll('.show_in_chi_phi_co_dinh').forEach(row => {
                        row.style.display = 'table-row';
                    });
                    break;
                case 'variable-cost':
                    document.querySelectorAll('.show_in_chi_phi_bien_doi').forEach(row => {
                        row.style.display = 'table-row';
                    });
                    break;
                case 'cost':
                    // Show all summary rows for cost tab
                    document.querySelectorAll('.show_in_khau_hao1, .show_in_chi_phi_co_dinh, .show_in_chi_phi_bien_doi').forEach(row => {
                        row.style.display = 'table-row';
                    });
                    break;
            }
        }

        // Tab Management
        function switchTab(tabId) {
            // Check if tab is disabled
            if (event.target.disabled) {
                return;
            }

            // Update tab buttons - remove active from all
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            // Activate clicked tab button
            event.target.classList.add('active');

            // Always show the cost tab content (since all fake tabs use the same content)
            document.getElementById(`tab-cost`).classList.add('active');

            // Set currentTab to 'cost' regardless of which tab was clicked
            currentTab = 'cost';

            // Set body data attribute for CSS tab-specific styling
            document.body.setAttribute('data-current-tab', tabId);

            // Update column headers for the selected tab
            updateColumnHeaders(tabId);

            // Apply row filtering based on tab (this will also handle category column visibility)
            applyTabFiltering(tabId);

            // Update summary rows visibility based on current tab
            updateSummaryRowsVisibility(tabId);

            // Ensure calculations are updated after tab switch with a small delay
            setTimeout(() => {
                if (tabId === 'depreciation' || tabId === 'fixed-cost' || tabId === 'variable-cost') {
                    calculateFilteredTotals('cost'); // Only for legacy compatibility
                }
                updateSummaryRowByTab(tabId); // Always use the switched tab
            }, 100);

            // Update URL with the clicked tab ID (not always 'cost')
            updateURL(null, tabId);

            // Update Save All button state for new tab
            updateSaveAllButton();

            // Update Delete button state for new tab
            updateDeleteButton();

            // Update cost input styling for new tab
            updateAllCostInputsStyling();

            // updateTotalItemCount();
        }

                // Sort rows by category for fixed-cost tab (depreciated first) and variable-cost tab (ban_hang first)
        function sortRowsByCategory(tbody, tabId) {
            if (tabId !== 'fixed-cost' && tabId !== 'variable-cost') return; // Only sort for fixed-cost and variable-cost tabs

            const rows = Array.from(tbody.querySelectorAll('tr:not(.loading):not(.summary-row):not(.summary-text-row)'));
            const summaryRow = tbody.querySelector('.summary-row');
            const textSummaryRow = tbody.querySelector('.summary-text-row');

            // Separate data rows and new rows
            const dataRows = rows.filter(row => row.dataset.isNew !== 'true');
            const newRows = rows.filter(row => row.dataset.isNew === 'true');

            // Sort data rows based on tab type
            dataRows.sort((a, b) => {
                if (tabId === 'fixed-cost') {
                    // For fixed-cost tab: depreciated first, then fixed, then others
                    const categoryA = a.querySelector('td.col-category select')?.value || '';
                    const categoryB = b.querySelector('td.col-category select')?.value || '';

                    // Priority order: depreciated (1), fixed (2), others (3)
                    const getPriority = (category) => {
                        if (category === 'depreciated') return 1;
                        if (category === 'fixed') return 2;
                        if (category === 'fixed_direct') return 2;
                        return 3;
                    };

                    const priorityA = getPriority(categoryA);
                    const priorityB = getPriority(categoryB);

                    if (priorityA !== priorityB) {
                        return priorityA - priorityB;
                    }

                    // If same priority, maintain original order by using dataset.index
                    const indexA = parseInt(a.dataset.index) || 0;
                    const indexB = parseInt(b.dataset.index) || 0;
                    return indexA - indexB;
                } else if (tabId === 'variable-cost') {
                    // For variable-cost tab: ban_hang first, then gia_von, then others
                    const option1A = a.querySelector('td.col-option1 select')?.value || '';
                    const option1B = b.querySelector('td.col-option1 select')?.value || '';

                    // Priority order: ban_hang (1), gia_von (2), others (3)
                    const getOption1Priority = (option1) => {
                        if (option1 === 'ban_hang') return 1;
                        if (option1 === 'gia_von') return 2;
                        return 3;
                    };

                    const priorityA = getOption1Priority(option1A);
                    const priorityB = getOption1Priority(option1B);

                    if (priorityA !== priorityB) {
                        return priorityA - priorityB;
                    }

                    // If same priority, maintain original order by using dataset.index
                    const indexA = parseInt(a.dataset.index) || 0;
                    const indexB = parseInt(b.dataset.index) || 0;
                    return indexA - indexB;
                }

                return 0; // No sorting for other cases
            });

            // Clear tbody and re-append in sorted order
            tbody.innerHTML = '';

            // Add sorted data rows
            dataRows.forEach(row => tbody.appendChild(row));

            // Add new rows
            newRows.forEach(row => tbody.appendChild(row));

            // Add summary rows at the end
            if (summaryRow) tbody.appendChild(summaryRow);
            if (textSummaryRow) tbody.appendChild(textSummaryRow);

            if (tabId === 'fixed-cost') {
                console.log(`🔄 Sorted ${dataRows.length} data rows for fixed-cost tab (depreciated first)`);
            } else if (tabId === 'variable-cost') {
                console.log(`🔄 Sorted ${dataRows.length} data rows for variable-cost tab (ban_hang first)`);
            }
        }

        // Apply row filtering based on selected tab
        function applyTabFiltering(tabId) {
            const tbody = document.getElementById('tableBody-cost');
            if (!tbody) return;

            const rows = tbody.querySelectorAll('tr:not(.loading):not(.summary-row)');

            rows.forEach(row => {
                // Always show new-rows (empty rows for input) regardless of tab
                if (row.dataset.isNew === 'true') {
                    row.style.display = '';
                    return;
                }

                if (tabId === 'depreciation') {
                    // For depreciation tab, show only rows with category = 'depreciated'
                    const categorySelect = row.querySelector('td.col-category select');
                    if (categorySelect) {
                        const categoryValue = categorySelect.value;

                        // Show only if category is 'depreciated'
                        if (categoryValue === 'depreciated') {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    } else {
                        // Hide rows without category select (shouldn't happen)
                        row.style.display = 'none';
                    }
                } else if (tabId === 'fixed-cost') {
                    // For fixed cost tab, show rows with category = 'fixed' or 'depreciated'
                    const categorySelect = row.querySelector('td.col-category select');
                    if (categorySelect) {
                        const categoryValue = categorySelect.value;

                        // Show if category is 'fixed' or 'depreciated'
                        if (categoryValue === 'fixed' || categoryValue === 'fixed_direct' || categoryValue === 'depreciated') {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    } else {
                        // Hide rows without category select (shouldn't happen)
                        row.style.display = 'none';
                    }
                } else if (tabId === 'variable-cost') {
                    // For variable cost tab, show only rows with category = 'variable'
                    const categorySelect = row.querySelector('td.col-category select');
                    if (categorySelect) {
                        const categoryValue = categorySelect.value;

                        // Show only if category is 'variable'
                        if (categoryValue === 'variable') {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    } else {
                        // Hide rows without category select (shouldn't happen)
                        row.style.display = 'none';
                    }
                } else {
                    // For other tabs (cost), show all rows
                    row.style.display = '';
                }
            });

            // Sort rows for fixed-cost tab (depreciated first)
            sortRowsByCategory(tbody, tabId);

            // Update STT for visible rows
            updateSTTForVisibleRows();

            // Recalculate totals based on visible rows only
            if (tabId === 'depreciation' || tabId === 'fixed-cost' || tabId === 'variable-cost') {
                calculateFilteredTotals('cost'); // Only for legacy compatibility
            }
            updateSummaryRowByTab(tabId); // Always use the passed tabId
        }

        // Calculate totals based on visible rows only (for filtering) - Using DataManager
        function calculateFilteredTotals(actualTabId) {
            // Get current active tab from URL
            const params = getURLParams();
            const currentActiveTab = params.tabId || 'cost';

            // Use DataManager to calculate totals
            const totals = dataManager.calculateTotalsByTab(currentActiveTab);

            console.log(`🧮 calculateFilteredTotals for tab: ${currentActiveTab}`, totals);
            console.log(`📊 Body data-current-tab: ${document.body.getAttribute('data-current-tab')}`);

            // ❌ REMOVED: Don't update summary-total here anymore, let updateSummaryRowByTab handle it
            // This was causing conflicts with updateSummaryRowByTab()

            // ❌ REMOVED: Don't update summary-monthly here anymore, let updateSummaryRowByTab handle it
            // This was causing conflicts with updateSummaryRowByTab()

            // Special handling for specific tabs: show filtered totals in cost column
            if (currentActiveTab === 'variable-cost') {
                console.log(`🎯 Final giaVonSum for variable-cost tab: ${totals.giaVonSum}`);
                const summaryCostElement = document.querySelector('#summary-row-cost .col-cost');
                if (summaryCostElement) {
                    // Clear existing content and add gia_von total
                    summaryCostElement.innerHTML = `<div style="text-align: right; font-weight: bold;; padding: 8px;">${formatNumberWithDots(totals.giaVonSum)}</div>`;
                }

                // Clear option1 summary for variable-cost tab
                const summaryOption1Element = document.querySelector('#summary-row-cost .col-option1 div');
                if (summaryOption1Element) {
                    summaryOption1Element.textContent = '';
                }
            } else if (currentActiveTab === 'fixed-cost') {
                console.log(`🎯 Final fixedSum for fixed-cost tab: ${totals.fixedSum}`);
                const summaryCostElement = document.querySelector('#summary-row-cost .col-cost');

                if (summaryCostElement) {
                    // Clear existing content and add fixed total
                    summaryCostElement.innerHTML = `<div style="text-align: right; font-weight: bold; padding: 8px;">${formatNumberWithDots(totals.fixedSum)}</div>`;
                }

                // Also update the external table if it exists
                const chiPhiCoSinhElement = document.getElementById('chi_phi_co_dinh_truc_tiep');
                if (chiPhiCoSinhElement) {
                    chiPhiCoSinhElement.innerHTML = formatNumberWithDots(totals.fixedSum);
                }

                // Clear option1 summary for fixed-cost tab
                const summaryOption1Element = document.querySelector('#summary-row-cost .col-option1 div');
                if (summaryOption1Element) {
                    summaryOption1Element.textContent = '';
                }
            } else {
                // Clear cost summary for other tabs (restore default empty)
                const summaryCostElement = document.querySelector('#summary-row-cost .col-cost');
                if (summaryCostElement) {
                    summaryCostElement.innerHTML = '';
                }

                // Clear option1 summary for other tabs
                const summaryOption1Element = document.querySelector('#summary-row-cost .col-option1 div');
                if (summaryOption1Element) {
                    summaryOption1Element.textContent = '';
                }
            }
        }

        // Update cost input styling based on category
        function updateCostInputStyling(row) {
            const categorySelect = row.querySelector('.col-category select');
            const costDisplayInput = row.querySelector('.col-cost .cost-display-editable');

            if (!categorySelect || !costDisplayInput) {
                console.log('❌ updateCostInputStyling: Missing elements', {
                    categorySelect: !!categorySelect,
                    costDisplayInput: !!costDisplayInput
                });
                return;
            }

            const category = categorySelect.value;

            // Check current active tab from URL or active tab button
            const params = getURLParams();
            const activeTab = params.tabId || 'cost';
            const isFixedCostTab = activeTab === 'fixed-cost';

            console.log('🔍 updateCostInputStyling:', {
                category: category,
                activeTab: activeTab,
                isFixedCostTab: isFixedCostTab,
                shouldBeReadonly: isFixedCostTab && category === 'depreciated'
            });

            if (isFixedCostTab && category === 'depreciated') {
                // Make readonly, gray, and italic for depreciated items in fixed-cost tab
                console.log('✅ Making cost input readonly for depreciated item in fixed-cost tab');
                costDisplayInput.classList.add('readonly-depreciated');
                costDisplayInput.readOnly = true;
                costDisplayInput.setAttribute('readonly', 'readonly');
            } else {
                // Remove readonly styling for other cases
                console.log('🔄 Making cost input editable');
                costDisplayInput.classList.remove('readonly-depreciated');
                costDisplayInput.readOnly = false;
                costDisplayInput.removeAttribute('readonly');
            }
        }

        // Update all cost inputs styling in current tab
        function updateAllCostInputsStyling() {
            const tbody = document.getElementById('tableBody-cost');
            if (!tbody) {
                console.log('❌ updateAllCostInputsStyling: tbody not found');
                return;
            }

            const rows = tbody.querySelectorAll('tr:not(.summary-row):not(.summary-text-row)');
            console.log(`🔄 updateAllCostInputsStyling: Processing ${rows.length} rows`);

            rows.forEach((row, index) => {
                console.log(`Processing row ${index + 1}:`);
                updateCostInputStyling(row);
            });
        }

        // Debug function to manually test cost input styling
        function debugCostInputStyling() {
            console.log('🐛 DEBUG: Testing cost input styling...');
            const tbody = document.getElementById('tableBody-cost');
            if (!tbody) {
                console.log('❌ tbody not found');
                return;
            }

            const rows = tbody.querySelectorAll('tr:not(.summary-row):not(.summary-text-row)');
            console.log(`Found ${rows.length} rows to check`);

            rows.forEach((row, index) => {
                const categorySelect = row.querySelector('.col-category select');
                const costDisplayInput = row.querySelector('.col-cost .cost-display-editable');

                if (categorySelect && costDisplayInput) {
                    console.log(`Row ${index + 1}:`, {
                        category: categorySelect.value,
                        hasReadonlyClass: costDisplayInput.classList.contains('readonly-depreciated'),
                        isReadonly: costDisplayInput.readOnly,
                        backgroundColor: window.getComputedStyle(costDisplayInput).backgroundColor
                    });
                }
            });
        }

        // Debug function to test row sorting
        function debugRowSorting() {
            console.log('🐛 DEBUG: Testing row sorting...');
            const tbody = document.getElementById('tableBody-cost');
            if (!tbody) {
                console.log('❌ tbody not found');
                return;
            }

            const rows = tbody.querySelectorAll('tr:not(.summary-row):not(.summary-text-row)');
            console.log(`Found ${rows.length} rows to check`);

            rows.forEach((row, index) => {
                const categorySelect = row.querySelector('.col-category select');
                const nameInput = row.querySelector('.col-name input');
                const isNew = row.dataset.isNew === 'true';

                if (categorySelect && nameInput) {
                    console.log(`Row ${index + 1}:`, {
                        name: nameInput.value || '(empty)',
                        category: categorySelect.value || '(no category)',
                        isNew: isNew,
                        dataIndex: row.dataset.index
                    });
                }
            });
        }



        // Sort rows after category change
        function sortRowsAfterCategoryChange() {
            const params = getURLParams();
            const currentActiveTab = params.tabId || 'cost';

            // Only sort if we're in fixed-cost tab
            if (currentActiveTab === 'fixed-cost') {
                const tbody = document.getElementById('tableBody-cost');
                if (tbody) {
                    setTimeout(() => {
                        sortRowsByCategory(tbody, currentActiveTab);
                        updateSTTForVisibleRows();
                    }, 100); // Small delay to ensure DOM is updated
                }
            }
        }

        // Sort rows after option1 change
        function sortRowsAfterOption1Change() {
            const params = getURLParams();
            const currentActiveTab = params.tabId || 'cost';

            // Only sort if we're in variable-cost tab
            if (currentActiveTab === 'variable-cost') {
                const tbody = document.getElementById('tableBody-cost');
                if (tbody) {
                    setTimeout(() => {
                        sortRowsByCategory(tbody, currentActiveTab);
                        updateSTTForVisibleRows();
                    }, 100); // Small delay to ensure DOM is updated
                }
            }
        }

        // Reapply current tab filtering (called when data changes)
        function reapplyCurrentTabFiltering() {
            const params = getURLParams();
            const currentActiveTab = params.tabId || 'cost';
            applyTabFiltering(currentActiveTab);
        }

        // Load data for specific tab
        async function loadData(tabId) {
            const config = TAB_CONFIG[tabId];
            try {
                showStatus(`Loading ${config.name}...`, 'info');
                const response = await fetch(`${config.apiEndpoint}${config.apiEndpointList}&seby_s14=${currentPlanId}`);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                // Check if API returned success response
                if (result.code === 1 && result.payload && result.payload.data) {
                    // Load data into DataManager
                    const apiData = result.payload.data.reverse(); // Đảo ngược mảng
                    dataManager.loadFromAPI(apiData);

                    // Update legacy config.data for backward compatibility
                    config.data = apiData;
                } else {
                    throw new Error('Invalid API response format');
                }

                renderTable(tabId);
                initializeContainerHeight(tabId);

                // Adjust container height after loading data with proper delay
                // setTimeout(() => {
                //     adjustContainerHeight();
                //     console.log('📊 Data loaded and container adjusted for tab:', tabId);
                // }, 300);

                showStatus(`${config.name} loaded successfully! (${config.data.length} items)`, 'success');
            } catch (error) {
                console.error(`Error loading ${config.name}:`, error);
                showStatus(`Error loading ${config.name}: ` + error.message, 'error');
                config.data = [];
                renderTable(tabId);
            }
        }

        // Load all tabs data
        async function loadAllData() {
            if (!currentPlanId) {
                showStatus('Vui lòng chọn dự án trước!', 'warning');
                return;
            }

            // Load cost data only
            await loadData('cost');

            // Final adjustment after all data is loaded
            setTimeout(() => {
                adjustContainerHeight();
                console.log('🎯 Data loaded - final container adjustment');
            }, 300);
        }



        // Load plans list
        async function loadPlans() {
            try {
                document.getElementById('planStatus').textContent = 'Đang tải danh sách dự án...';
                const response = await fetch('/api/member-plan-name/list');

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                // Check if API returned success response
                if (result.code === 1 && result.payload && result.payload.data) {
                    const plans = result.payload.data;
                    const planSelect = document.getElementById('planSelect');

                    // Clear existing options except the first one
                    planSelect.innerHTML = '<option value="">-- Chọn Dự án --</option>';

                    // Add plan options
                    plans.forEach(plan => {
                        const option = document.createElement('option');
                        option.value = plan.id;
                        option.textContent = plan.name;
                        planSelect.appendChild(option);
                    });

                    document.getElementById('planStatus').textContent = `Đã tải ${plans.length} Dự án`;
                } else {
                    throw new Error('Invalid API response format');
                }
            } catch (error) {
                console.error('Error loading plans:', error);
                document.getElementById('planStatus').textContent = 'Lỗi tải danh sách Dự án';
                showStatus('Error loading plans: ' + error.message, 'error');
            }
        }

        // Wrapper for onchange event (since HTML onchange can't handle async)
        function handlePlanChangeWrapper() {
            handlePlanChange().catch(error => {
                console.error('Error in handlePlanChange:', error);
                showStatus('Error loading project: ' + error.message, 'error');
            });
        }

        // Handle plan selection change
        async function handlePlanChange() {
            const planSelect = document.getElementById('planSelect');
            const selectedPlanId = planSelect.value;
            const selectedPlanName = planSelect.options[planSelect.selectedIndex].text;

            if (selectedPlanId) {
                currentPlanId = selectedPlanId;

                // Enable all tabs
                document.getElementById('depreciation-tab').disabled = false;
                document.getElementById('fixed-cost-tab').disabled = false;
                document.getElementById('variable-cost-tab').disabled = false;
                document.getElementById('cost-tab').disabled = false;

                // Update Save All button state
                updateSaveAllButton();
                // document.getElementById('refresh-btn').disabled = false;
                // document.getElementById('save-btn').disabled = false;

                // Check if there's already a tab in URL (from refresh/direct link)
                const params = getURLParams();
                const currentTabFromURL = params.tabId;

                if (currentTabFromURL) {
                    // If there's a tab in URL, keep it (for refresh scenarios)
                    console.log(`🔗 Keeping existing tab from URL: ${currentTabFromURL}`);

                    // Remove active from all tabs
                    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

                    // Activate the tab from URL
                    const targetTabBtn = document.getElementById(`${currentTabFromURL}-tab`);
                    if (targetTabBtn) {
                        targetTabBtn.classList.add('active');
                    }
                    document.getElementById('tab-cost').classList.add('active');

                    // Update column headers for the URL tab
                    updateColumnHeaders(currentTabFromURL);

                    // Apply filtering for the URL tab
                    applyTabFiltering(currentTabFromURL);

                    // Update URL with selected project but keep existing tab
                    updateURL(selectedPlanId, currentTabFromURL);
                } else {
                    // If no tab in URL, default to depreciation tab (for new project selection)
                    console.log(`🆕 No tab in URL, defaulting to depreciation tab`);

                    // Remove active from all tabs
                    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

                    // Activate depreciation tab
                    document.getElementById('depreciation-tab').classList.add('active');
                    document.getElementById('tab-cost').classList.add('active');

                    // Update column headers for depreciation tab
                    updateColumnHeaders('depreciation');

                    // Apply depreciation filtering
                    applyTabFiltering('depreciation');

                    // Update URL with selected project and depreciation tab
                    updateURL(selectedPlanId, 'depreciation');
                }

                // Update status
                document.getElementById('planStatus').textContent = `Đã chọn: ${selectedPlanName}`;

                // Load data for selected plan
                await loadAllData();

                // Load plan define values (from API/database)
                await loadPlanDefineValues();

                // Calculate multi-scenario analysis after loading all data
                setTimeout(() => {
                    calculateMultiScenarioAnalysis();
                }, 500);

                showStatus(`Đã chọn Dự án: ${selectedPlanName}`, 'success');
            } else {
                currentPlanId = '';

                // Disable all tabs
                document.getElementById('depreciation-tab').disabled = true;
                document.getElementById('fixed-cost-tab').disabled = true;
                document.getElementById('variable-cost-tab').disabled = true;
                document.getElementById('cost-tab').disabled = true;
                document.getElementById('saveAllBtn').disabled = true;
                // document.getElementById('refresh-btn').disabled = true;
                // document.getElementById('save-btn').disabled = true;

                // Update URL to remove project
                updateURL('', null);

                // Clear tables
                clearAllTables();

                // Clear plan define values
                clearPlanDefineValues();

                document.getElementById('planStatus').textContent = 'Chưa chọn Dự án';
            }
        }

        // Clear all tables
        function clearAllTables() {
            Object.keys(TAB_CONFIG).forEach(tabId => {
                const config = TAB_CONFIG[tabId];
                config.data = [];
                config.changes.clear();
                const tbody = document.getElementById(config.tableBodyId);
                tbody.innerHTML = '<tr class="loading"><td colspan="10">Chọn Dự án để xem dữ liệu...</td></tr>';
            });
            // updateTotalItemCount();
        }

        // Add new plan
        async function addNewPlan() {
            const newPlanNameInput = document.getElementById('newPlanName');
            const planName = newPlanNameInput.value.trim();

            if (!planName) {
                showStatus('Vui lòng nhập tên Dự án!', 'error');
                newPlanNameInput.focus();
                return;
            }

            try {
                document.getElementById('planStatus').textContent = 'Đang tạo Dự án mới...';

                const response = await fetch('/api/member-plan-name/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ name: planName })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                // Check if API returned success response
                if (result.code === 1 && result.payload) {
                    const newPlanId = result.payload;

                    // Clear input
                    newPlanNameInput.value = '';

                    // Reload plans and select the new one
                    await reloadAndSelectPlan(newPlanId);

                    showStatus(`Dự án "${planName}" đã được tạo thành công!`, 'success');
                } else {
                    throw new Error(result.message || 'Failed to create plan');
                }
            } catch (error) {
                console.error('Error creating new plan:', error);
                document.getElementById('planStatus').textContent = 'Lỗi tạo Dự án';
                showStatus('Error creating plan: ' + error.message, 'error');
            }
        }

        // Reload plans and select specific plan
        async function reloadAndSelectPlan(planIdToSelect) {
            try {
                // Reload plans list
                await loadPlans();

                // Select the specified plan
                const planSelect = document.getElementById('planSelect');
                planSelect.value = planIdToSelect;

                // Trigger change event to load data
                await handlePlanChange();

            } catch (error) {
                console.error('Error reloading and selecting plan:', error);
                showStatus('Error reloading plans: ' + error.message, 'error');
            }
        }

        // Handle Enter key in new plan input
        document.addEventListener('DOMContentLoaded', async function() {
            document.getElementById('newPlanName').addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    addNewPlan();
                }
            });

            // Add event listeners for plan define value inputs
            const giaBanDuKienThangInput = document.getElementById('input_gia_ban_du_kien');
            const luongBanDuKienThangInput = document.getElementById('input_luong_ban_du_kien_thang');

            if (giaBanDuKienThangInput) {
                giaBanDuKienThangInput.addEventListener('change', function() {
                    updatePlanDefineValue('input_gia_ban_du_kien', this.value);
                });
                giaBanDuKienThangInput.addEventListener('input', function() {
                    calculateProjectAnalysisValues();
                    calculateMultiScenarioAnalysis();
                    updateRatioCalculations();
                });
            }
            if (luongBanDuKienThangInput) {
                luongBanDuKienThangInput.addEventListener('change', function() {
                    updatePlanDefineValue('input_luong_ban_du_kien_thang', this.value);
                });
                luongBanDuKienThangInput.addEventListener('input', function() {
                    calculateProjectAnalysisValues();
                    calculateMultiScenarioAnalysis();
                    updateRatioCalculations();
                });
            }

            // Add event listeners for 4 scenario inputs
            for (let i = 1; i <= 4; i++) {
                const scenarioInput = document.getElementById(`input_san_luong_ban_du_kien_thang_${i}`);
                if (scenarioInput) {
                    scenarioInput.addEventListener('change', function() {
                        updatePlanDefineValue(`input_san_luong_ban_du_kien_thang_${i}`, this.value);
                    });
                    scenarioInput.addEventListener('input', function() {
                        calculateMultiScenarioAnalysis();
                        updateRatioCalculations();
                    });
                }
            }

            // Add event listener for investment comparison input
            const giaTriLoiNhuanKhacInput = document.getElementById('gia_tri_loi_nhuan_hang_nam_dau_tu_khac');
            if (giaTriLoiNhuanKhacInput) {
                giaTriLoiNhuanKhacInput.addEventListener('input', function() {
                    updateInvestmentComparison();
                });
            }

            // Add event listeners for 4 sản lượng bán inputs
            for (let i = 1; i <= 4; i++) {
                const inputElement = document.getElementById(`input_san_luong_ban_du_kien_thang_${i}`);
                if (inputElement) {
                    inputElement.addEventListener('input', function() {
                        copySanLuongBanToDisplay();
                    });
                    inputElement.addEventListener('change', function() {
                        copySanLuongBanToDisplay();
                    });
                }
            }

            // Add event listeners for sản lượng 12 tháng
            for (let i = 1; i <= 12; i++) {
                const sanLuongInput = document.getElementById(`san_luong_thang_${i}`);
                if (sanLuongInput) {
                    sanLuongInput.addEventListener('change', function() {
                        updatePlanDefineValue(`san_luong_thang_${i}`, this.value);
                    });
                }
            }

            // Add event listeners for năng lực bán 12 tháng
            for (let i = 1; i <= 12; i++) {
                const nangLucBanInput = document.getElementById(`nang_luc_nv_ban_thang_${i}`);
                if (nangLucBanInput) {
                    nangLucBanInput.addEventListener('change', function() {
                        updatePlanDefineValue(`nang_luc_nv_ban_thang_${i}`, this.value);
                    });
                }
            }

            // Add event listeners for tỷ lệ chuyển đổi 12 tháng
            for (let i = 1; i <= 12; i++) {
                const tyLeChuyenDoiInput = document.getElementById(`ty_le_chuyen_doi_thang_${i}`);
                if (tyLeChuyenDoiInput) {
                    tyLeChuyenDoiInput.addEventListener('change', function() {
                        updatePlanDefineValue(`ty_le_chuyen_doi_thang_${i}`, this.value);
                        calculateSoKhTiemNang(); // Tính lại số KH tiềm năng
                        calculateKhTiemNangPerNv(); // Tính lại KH/NV
                    });
                }
            }

            // Add event listeners for compare info inputs (compare_name and compare_rate)
            const compareNameInput = document.getElementById('ten_loai_hinh_dau_tu_so_sanh');
            const compareRateInput = document.getElementById('phan_tram_lai_xuat_so_sanh');

            if (compareNameInput) {
                compareNameInput.addEventListener('change', function() {
                    updatePlanDefineValue('compare_name', this.value);
                });
                compareNameInput.addEventListener('blur', function() {
                    updatePlanDefineValue('compare_name', this.value);
                });
            }

            if (compareRateInput) {
                compareRateInput.addEventListener('change', function() {
                    updatePlanDefineValue('compare_rate', this.value);
                });
                compareRateInput.addEventListener('blur', function() {
                    updatePlanDefineValue('compare_rate', this.value);
                });
            }

            // Add event listeners for tỷ lệ chuyển đổi Marketing (4 loại)
            const conversionFields = ['ty_le_chuyen_doi_lien_lac', 'ty_le_chuyen_doi_chat', 'ty_le_chuyen_doi_like', 'ty_le_chuyen_doi_seen'];
            conversionFields.forEach(fieldId => {
                const input = document.getElementById(fieldId);
                if (input) {
                    input.addEventListener('change', function() {
                        updatePlanDefineValue(fieldId, this.value);
                        calculateMarketingFunnel(); // Tính lại Marketing Funnel
                    });
                }
            });

            // Add event listeners for năng lực NV bán 12 tháng (tính lại số lượng NV khi thay đổi)
            for (let i = 1; i <= 12; i++) {
                const nangLucNvInput = document.getElementById(`nang_luc_nv_ban_thang_${i}`);
                if (nangLucNvInput) {
                    nangLucNvInput.addEventListener('change', function() {
                        updatePlanDefineValue(`nang_luc_nv_ban_thang_${i}`, this.value);
                        calculateSoLuongNhanVien();
                        calculateKhTiemNangPerNv(); // Tính lại KH/NV
                    });
                    nangLucNvInput.addEventListener('input', function() {
                        calculateSoLuongNhanVien();
                        calculateKhTiemNangPerNv(); // Tính lại KH/NV
                    });
                }
            }

            // Load plans first, then initialize from URL
            await initializeApp();
        });

        // Update plan define value via API
        async function updatePlanDefineValue(fieldName, value) {

            if (!currentPlanId) {
                console.warn('⚠️ No plan selected, cannot update plan define value');
                return;
            }

            try {
                const url = `/api/member-plan-define-value/update_val?plan_id=${currentPlanId}&${fieldName}=${encodeURIComponent(value)}`;
                console.log(`📡 Updating plan define value: ${fieldName} = ${value}`);
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();
                if (result.code === 1) {
                    console.log(`✅ Successfully updated ${fieldName}: ${value}`);
                    showStatus(`Đã cập nhật ${fieldName}`, 'success');
                    // Calculate and update derived values
                    calculateProjectAnalysisValues();
                } else {
                    throw new Error(result.message || 'Failed to update plan define value');
                }

            } catch (error) {
                console.error('❌ Error updating plan define value:', error);
                showStatus('Error updating value: ' + error.message, 'error');
            }
        }

        // Load plan define values from API
        async function loadPlanDefineValues() {
            if (!currentPlanId) {
                console.warn('⚠️ No plan selected, cannot load plan define values');
                return;
            }

            try {
                const url = `/api/member-plan-define-value/get_plan_info?plan_id=${currentPlanId}`;

                console.log(`📡 Loading plan define values for plan: ${currentPlanId}`);

                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                console.log('🔍 API response structure:', result);

                if (result.code === 1) {
                    // Check if payload exists and is an object
                    if (result.payload && typeof result.payload === 'object') {
                        console.log(`✅ Successfully loaded plan define values:`, result.payload);

                        // Helper function để set giá trị input
                        const setInputValue = (elementId, value) => {
                            const element = document.getElementById(elementId);
                            if (element && value !== null && value !== undefined) {
                                element.value = value;
                                console.log(`✅ Set ${elementId}: ${value}`);
                            } else if (element) {
                                element.value = '';
                            }
                        };

                        // Set values to main input fields
                        setInputValue('input_gia_ban_du_kien', result.payload['input_gia_ban_du_kien']);
                        setInputValue('input_luong_ban_du_kien_thang', result.payload['input_luong_ban_du_kien_thang']);

                        // Set values to 4 scenario inputs
                        for (let i = 1; i <= 4; i++) {
                            setInputValue(`input_san_luong_ban_du_kien_thang_${i}`, result.payload[`input_san_luong_ban_du_kien_thang_${i}`]);
                        }

                        // Set compare_name and compare_rate
                        setInputValue('ten_loai_hinh_dau_tu_so_sanh', result.payload['compare_name']);
                        setInputValue('phan_tram_lai_xuat_so_sanh', result.payload['compare_rate']);

                        // Set sản lượng 12 tháng với logic phân bổ
                        // Tong12 = san_luong_ban_4 * 12
                        // Nếu có một số tháng đã có giá trị, tính tổng đó
                        // Phần còn lại chia đều cho các tháng null
                        const sanLuongKichBan4 = parseFloat(result.payload['input_san_luong_ban_du_kien_thang_4']) || 0;
                        const tong12 = sanLuongKichBan4 * 12;

                        // Tìm các tháng đã có giá trị và các tháng null
                        let tongDaCo = 0;
                        let soThangNull = 0;
                        const thangValues = [];

                        for (let i = 1; i <= 12; i++) {
                            const value = result.payload[`san_luong_thang_${i}`];
                            if (value !== null && value !== undefined && value !== '') {
                                const numValue = parseFloat(value) || 0;
                                tongDaCo += numValue;
                                thangValues[i] = numValue;
                            } else {
                                soThangNull++;
                                thangValues[i] = null;
                            }
                        }

                        // Tính giá trị chia đều cho các tháng null
                        const conLai = tong12 - tongDaCo;
                        const giaTriChiaDeu = soThangNull > 0 ? Math.round(conLai / soThangNull) : 0;

                        console.log(`📊 Phân bổ sản lượng 12 tháng:`, {
                            sanLuongKichBan4,
                            tong12,
                            tongDaCo,
                            soThangNull,
                            conLai,
                            giaTriChiaDeu
                        });

                        // Set giá trị cho từng tháng
                        for (let i = 1; i <= 12; i++) {
                            if (thangValues[i] !== null) {
                                // Tháng đã có giá trị từ API
                                setInputValue(`san_luong_thang_${i}`, thangValues[i]);
                            } else {
                                // Tháng null - chia đều phần còn lại
                                setInputValue(`san_luong_thang_${i}`, giaTriChiaDeu > 0 ? giaTriChiaDeu : '');
                            }
                        }

                        // Set năng lực bán 12 tháng
                        for (let i = 1; i <= 12; i++) {
                            setInputValue(`nang_luc_nv_ban_thang_${i}`, result.payload[`nang_luc_nv_ban_thang_${i}`]);
                        }

                        // Set tỷ lệ chuyển đổi 12 tháng
                        for (let i = 1; i <= 12; i++) {
                            setInputValue(`ty_le_chuyen_doi_thang_${i}`, result.payload[`ty_le_chuyen_doi_thang_${i}`]);
                        }

                        // Set tỷ lệ chuyển đổi Marketing (4 loại)
                        setInputValue('ty_le_chuyen_doi_lien_lac', result.payload['ty_le_chuyen_doi_lien_lac']);
                        setInputValue('ty_le_chuyen_doi_chat', result.payload['ty_le_chuyen_doi_chat']);
                        setInputValue('ty_le_chuyen_doi_like', result.payload['ty_le_chuyen_doi_like']);
                        setInputValue('ty_le_chuyen_doi_seen', result.payload['ty_le_chuyen_doi_seen']);

                        // Calculate derived values
                        calculateProjectAnalysisValues();
                        calculateMultiScenarioAnalysis();
                        
                        // Tính số lượng nhân viên cần thiết
                        calculateSoLuongNhanVien();
                        
                        // Tính số khách hàng tiềm năng
                        calculateSoKhTiemNang();
                        
                        // Tính KH tiềm năng / nhân viên
                        calculateKhTiemNangPerNv();
                        
                        // Tính Marketing Funnel
                        calculateMarketingFunnel();

                    } else {
                        console.log('🔍 No plan define values found - payload is empty or invalid:', result.payload);
                    }
                } else {
                    console.log('🔍 API returned error or no data:', result);
                }

            } catch (error) {
                console.error('❌ Error loading plan define values:', error);
                // Don't show error message as this is optional data
            }
        }

                // Clear plan define values
        function clearPlanDefineValues() {
            const giaBanInput = document.getElementById('input_gia_ban_du_kien');
            const luongBanInput = document.getElementById('input_luong_ban_du_kien_thang');
            const doanhThuElement = document.getElementById('doanh-thu-du-kien-thang');

            if (giaBanInput) giaBanInput.value = '';
            if (luongBanInput) luongBanInput.value = '';
            if (doanhThuElement) doanhThuElement.textContent = '';

            // Clear 4 scenario inputs
            for (let i = 1; i <= 4; i++) {
                const scenarioInput = document.getElementById(`input_san_luong_ban_du_kien_thang_${i}`);
                if (scenarioInput) scenarioInput.value = '';
            }

            // Clear compare info inputs
            const compareNameInput = document.getElementById('ten_loai_hinh_dau_tu_so_sanh');
            const compareRateInput = document.getElementById('phan_tram_lai_xuat_so_sanh');
            if (compareNameInput) compareNameInput.value = '';
            if (compareRateInput) compareRateInput.value = '';

            // Clear sản lượng 12 tháng
            for (let i = 1; i <= 12; i++) {
                const el = document.getElementById(`san_luong_thang_${i}`);
                if (el) el.value = '';
            }

            // Clear năng lực bán 12 tháng
            for (let i = 1; i <= 12; i++) {
                const el = document.getElementById(`nang_luc_nv_ban_thang_${i}`);
                if (el) el.value = '';
            }

            // Clear số lượng nhân viên 12 tháng
            for (let i = 1; i <= 12; i++) {
                const el = document.getElementById(`so_luong_nv_thang_${i}`);
                if (el) el.value = '';
            }

            // Clear số khách hàng tiềm năng 12 tháng
            for (let i = 1; i <= 12; i++) {
                const el = document.getElementById(`so_kh_tiem_nang_thang_${i}`);
                if (el) el.value = '';
            }

            // Clear KH tiềm năng / nhân viên 12 tháng
            for (let i = 1; i <= 12; i++) {
                const el = document.getElementById(`kh_tiem_nang_per_nv_thang_${i}`);
                if (el) el.value = '';
            }

            // Clear tỷ lệ chuyển đổi 12 tháng
            for (let i = 1; i <= 12; i++) {
                const el = document.getElementById(`ty_le_chuyen_doi_thang_${i}`);
                if (el) el.value = '';
            }

            // Clear tỷ lệ chuyển đổi Marketing (4 loại)
            ['ty_le_chuyen_doi_lien_lac', 'ty_le_chuyen_doi_chat', 'ty_le_chuyen_doi_like', 'ty_le_chuyen_doi_seen'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });

            // Clear Marketing Funnel 12 tháng
            for (let i = 1; i <= 12; i++) {
                ['lien_lac_thang_', 'tuong_tac_chat_thang_', 'tuong_tac_like_thang_', 'tuong_tac_seen_thang_'].forEach(prefix => {
                    const el = document.getElementById(`${prefix}${i}`);
                    if (el) el.value = '';
                });
            }

            // Clear all project analysis values
            const analysisElements = [
                'chi_phi_co_dinh_thang', 'chi_phi_co_dinh_nam',
                'chi_phi_bien_doi_thang', 'chi_phi_bien_doi_nam',
                'bien_phi_don_vi_thang', 'bien_phi_don_vi_nam',
                'san_luong_hoa_von_thang', 'san_luong_hoa_von_nam'
            ];

            analysisElements.forEach(id => {
                const element = document.getElementById(id);
                if (element) element.textContent = '';
            });

            // Clear multi-scenario analysis values
            const multiScenarioElements = [
                'chi_phi_co_dinh_thang', 'chi_phi_bien_doi_thang', 'tong_phi_thang',
                'doanh_thu_thuan', 'loi_nhuan_thuan', 'thue_phai_dong_cho_nn',
                'loi_nhuan_net', 'thu_hoi_khau_hao_dan'
            ];

            for (let i = 1; i <= 4; i++) {
                multiScenarioElements.forEach(baseId => {
                    const element = document.getElementById(`${baseId}_${i}`);
                    if (element) element.textContent = '';
                });
            }

            console.log('🧹 Cleared plan define values, project analysis, and multi-scenario analysis');
        }

        /**
         * Gửi API update compare_name và compare_rate (deprecated - now using updatePlanDefineValue)
         */
        async function updateCompareInfo() {
            try {
                // Check if currentPlanId exists
                if (!currentPlanId) {
                    console.warn('⚠️ currentPlanId not set, cannot update compare info');
                    return;
                }

                // Get values from inputs
                const compareName = document.getElementById('ten_loai_hinh_dau_tu_so_sanh')?.value || '';
                const compareRate = document.getElementById('phan_tram_lai_xuat_so_sanh')?.value || '';

                console.log(`📤 Updating compare info for plan ${currentPlanId}:`, {
                    compare_name: compareName,
                    compare_rate: compareRate
                });

                // Send POST request to API
                const response = await fetch(`/api/member-plan-name/update/${currentPlanId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        compare_name: compareName,
                        compare_rate: compareRate
                    })
                });

                const result = await response.json();

                console.log('📥 API Response:', result);

                // Check for code === 1 which means success
                if (result.code === 1) {
                    console.log('✅ Compare info updated successfully:', result);
                    // showStatus('Cập nhật thông tin so sánh thành công', 'success');
                } else {
                    console.error('❌ API returned error:', result);
                    showStatus('Lỗi cập nhật thông tin so sánh: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('❌ Error updating compare info:', error);
                showStatus('Lỗi kết nối: ' + error.message, 'error');
            }
        }

        // Calculate and update project analysis values
        function calculateProjectAnalysisValues() {
            const giaBanDuKien = parseFloat(document.getElementById('input_gia_ban_du_kien')?.value) || 0;
            const luongBanDuKien = parseFloat(document.getElementById('input_luong_ban_du_kien_thang')?.value) || 0;

            // Calculate doanh thu dự kiến = giá bán × lượng bán
            const doanhThuDuKien = giaBanDuKien * luongBanDuKien;

            // Update doanh thu dự kiến display
            const doanhThuDuKienElement = document.getElementById('doanh-thu-du-kien-thang');
            if (doanhThuDuKienElement) {
                doanhThuDuKienElement.textContent = formatNumberWithDots(doanhThuDuKien);
            }

            // Get calculated values from data manager instead of HTML
            const chiPhiCoDinhThang = calculateTotalForColMonthlyAllocation('cost'); // Raw calculation
            const variableCostMonthlyTotal = dataManager.getItemsByTab('variable-cost')
                .filter(item => !item.isEmpty)
                .reduce((sum, item) => sum + item.getTotal(), 0);

            // Update chi phí cố định hàng tháng/năm
            const chiPhiCoDinhThangElement = document.getElementById('chi_phi_co_dinh_thang');
            const chiPhiCoDinhNamElement = document.getElementById('chi_phi_co_dinh_nam');
            if (chiPhiCoDinhThangElement) {
                chiPhiCoDinhThangElement.textContent = formatNumberWithDots(chiPhiCoDinhThang);
            }
            if (chiPhiCoDinhNamElement) {
                chiPhiCoDinhNamElement.textContent = formatNumberWithDots(chiPhiCoDinhThang * 12);
            }

            // Update chi phí biến đổi hàng tháng/năm
            const chiPhiBienDoiThangElement = document.getElementById('chi_phi_bien_doi_thang');
            const chiPhiBienDoiNamElement = document.getElementById('chi_phi_bien_doi_nam');
            if (chiPhiBienDoiThangElement) {
                chiPhiBienDoiThangElement.textContent = formatNumberWithDots(variableCostMonthlyTotal);
            }
            if (chiPhiBienDoiNamElement) {
                chiPhiBienDoiNamElement.textContent = formatNumberWithDots(variableCostMonthlyTotal * 12);
            }

            // Calculate biến phí đơn vị (chi phí biến đổi hàng tháng / lượng bán dự kiến)
            const bienPhiDonViThangElement = document.getElementById('bien_phi_don_vi_thang');
            const bienPhiDonViNamElement = document.getElementById('bien_phi_don_vi_nam');
            let bienPhiDonVi = 0;

            if (luongBanDuKien > 0) {
                bienPhiDonVi = variableCostMonthlyTotal / luongBanDuKien;

                if (bienPhiDonViThangElement) {
                    bienPhiDonViThangElement.textContent = formatNumberWithDots(bienPhiDonVi);
                }
                if (bienPhiDonViNamElement) {
                    bienPhiDonViNamElement.textContent = formatNumberWithDots(bienPhiDonVi * 12);
                }
            }

            // Calculate sản lượng hòa vốn sử dụng hàm chuyên dụng
            const sanLuongHoaVonThangElement = document.getElementById('san_luong_hoa_von_thang');
            const sanLuongHoaVonNamElement = document.getElementById('san_luong_hoa_von_nam');

            const sanLuongHoaVonThang = calculateSanLuongHoaVonThang(chiPhiCoDinhThang, giaBanDuKien, bienPhiDonVi);
            const sanLuongHoaVonNam = sanLuongHoaVonThang * 12;

            if (sanLuongHoaVonThangElement) {
                sanLuongHoaVonThangElement.textContent = formatNumberWithDots(Math.round(sanLuongHoaVonThang));
            }
            if (sanLuongHoaVonNamElement) {
                sanLuongHoaVonNamElement.textContent = formatNumberWithDots(Math.round(sanLuongHoaVonNam));
            }

            console.log(`📊 Calculated project analysis: Giá bán: ${giaBanDuKien}, Lượng bán: ${luongBanDuKien}, Doanh thu: ${doanhThuDuKien}, Chi phí cố định: ${chiPhiCoDinhThang}, Chi phí biến đổi: ${variableCostMonthlyTotal}, Biến phí đơn vị: ${bienPhiDonVi}`);
        }

        // Calculate multi-scenario analysis for 4 columns
        function calculateMultiScenarioAnalysis() {
            const giaBanDuKien = parseFloat(document.getElementById('input_gia_ban_du_kien')?.value) || 0;

            // Get calculated base values
            const chiPhiCoDinhThang = calculateTotalForColMonthlyAllocation('cost');
            const variableCostMonthlyTotal = dataManager.getItemsByTab('variable-cost')
                .filter(item => !item.isEmpty)
                .reduce((sum, item) => sum + item.getTotal(), 0);

            // Get depreciation total for thu hồi khấu hao
            const depreciationMonthlyTotal = dataManager.getItemsByTab('depreciation')
                .filter(item => !item.isEmpty)
                .reduce((sum, item) => sum + item.getMonthlyAllocation(), 0);

            // Calculate for each scenario (1-4)
            for (let i = 1; i <= 4; i++) {
                const sanLuongInput = document.getElementById(`input_san_luong_ban_du_kien_thang_${i}`);
                const sanLuong = parseFloat(sanLuongInput?.value) || 0;

                // Chi phí cố định hàng tháng (same for all scenarios)
                const chiPhiCoDinhElement = document.getElementById(`chi_phi_co_dinh_thang_${i}`);
                if (chiPhiCoDinhElement) {
                    chiPhiCoDinhElement.textContent = formatNumberWithDots(chiPhiCoDinhThang);
                }

                // Chi phí biến đổi hàng tháng = biến phí đơn vị × sản lượng
                let chiPhiBienDoiScenario = 0;
                if (sanLuong > 0) {
                    const bienPhiDonVi = variableCostMonthlyTotal / (parseFloat(document.getElementById('input_luong_ban_du_kien_thang')?.value) || 1);
                    chiPhiBienDoiScenario = bienPhiDonVi * sanLuong;
                }

                const chiPhiBienDoiElement = document.getElementById(`chi_phi_bien_doi_thang_${i}`);
                if (chiPhiBienDoiElement) {
                    chiPhiBienDoiElement.textContent = formatNumberWithDots(chiPhiBienDoiScenario);
                }

                // Tổng phí hàng tháng = chi phí cố định + chi phí biến đổi
                const tongPhiThang = chiPhiCoDinhThang + chiPhiBienDoiScenario;
                const tongPhiElement = document.getElementById(`tong_phi_thang_${i}`);
                if (tongPhiElement) {
                    tongPhiElement.textContent = formatNumberWithDots(tongPhiThang);
                }

                // Doanh thu thuần = giá bán × sản lượng
                const doanhThuThuan = giaBanDuKien * sanLuong;
                const doanhThuThuanElement = document.getElementById(`doanh_thu_thuan_${i}`);
                if (doanhThuThuanElement) {
                    doanhThuThuanElement.textContent = formatNumberWithDots(doanhThuThuan);
                }

                // Lợi nhuận thuần = doanh thu thuần - tổng phí
                const loiNhuanThuan = doanhThuThuan - tongPhiThang;
                const loiNhuanThuanElement = document.getElementById(`loi_nhuan_thuan_${i}`);
                if (loiNhuanThuanElement) {
                    loiNhuanThuanElement.textContent = formatNumberWithDots(loiNhuanThuan);
                }

                // Thuế phải đóng cho NN (20%) = lợi nhuận thuần × 20% (chỉ nếu lợi nhuận > 0)
                const thuePháiDong = loiNhuanThuan > 0 ? loiNhuanThuan * 0.2 : 0;
                const thueElement = document.getElementById(`thue_phai_dong_cho_nn_${i}`);
                if (thueElement) {
                    thueElement.textContent = formatNumberWithDots(thuePháiDong);
                }

                // Lợi nhuận net = lợi nhuận thuần - thuế
                const loiNhuanNet = loiNhuanThuan - thuePháiDong;
                const loiNhuanNetElement = document.getElementById(`loi_nhuan_net_${i}`);
                if (loiNhuanNetElement) {
                    loiNhuanNetElement.textContent = formatNumberWithDots(loiNhuanNet);
                }

                // Thu hồi khấu hao dần (rút tiền về) = depreciation monthly allocation
                const thuHoiKhauHaoElement = document.getElementById(`thu_hoi_khau_hao_dan_${i}`);
                if (thuHoiKhauHaoElement) {
                    thuHoiKhauHaoElement.textContent = formatNumberWithDots(depreciationMonthlyTotal);
                }

                console.log(`📊 Scenario ${i}: Sản lượng: ${sanLuong}, Doanh thu: ${doanhThuThuan}, Lợi nhuận thuần: ${loiNhuanThuan}, Lợi nhuận net: ${loiNhuanNet}`);
            }

            // Cập nhật các tỷ lệ sau khi tính toán xong các kịch bản
            updateRatioCalculations();

            // Cập nhật so sánh đầu tư
            updateInvestmentComparison();

            // Cập nhật tổng hợp dự án
            updateProjectSummaryCalculations();
        }

        // Initialize app with proper async handling
        async function initializeApp() {
            try {
                console.log('🚀 Starting app initialization...');

                // First load plans
                console.log('📋 Loading plans...');
                await loadPlans();
                console.log('✅ Plans loaded successfully');

                // Then initialize from URL parameters
                console.log('🔗 Initializing from URL...');
                await initializeFromURL();

                // Apply initial column headers for default tab
                const params = getURLParams();
                const initialTab = params.tabId || 'depreciation';
                updateColumnHeaders(initialTab);
                console.log(`✅ Applied initial column headers for tab: ${initialTab}`);

                // Initialize summary rows visibility for default tab
                updateSummaryRowsVisibility(initialTab);
                console.log(`✅ Applied initial summary rows visibility for tab: ${initialTab}`);

                // Calculate initial ratios
                updateRatioCalculations();
                console.log(`✅ Calculated initial ratios`);

                // Calculate initial investment comparison
                updateInvestmentComparison();
                console.log(`✅ Calculated initial investment comparison`);

                // Calculate initial project summary
                updateProjectSummaryCalculations();
                console.log(`✅ Calculated initial project summary`);

                console.log('✅ App initialization completed');

            } catch (error) {
                console.error('❌ Error during app initialization:', error);
                showStatus('Error initializing app: ' + error.message, 'error');
            }
        }

        // Debug function to check row visibility
        function debugRowVisibility(tabId = currentTab) {
            const config = TAB_CONFIG[tabId];
            const tbody = document.getElementById(config.tableBodyId);
            const allRows = tbody.querySelectorAll('tr');
            const newRows = tbody.querySelectorAll('tr[data-is-new="true"]');

            console.log(`=== Debug ${config.name} ===`);
            console.log(`Total rows: ${allRows.length}`);
            console.log(`New rows: ${newRows.length}`);
            console.log(`Data rows: ${config.data.length}`);
            console.log(`Container height: ${document.querySelector('.cls_contain').style.height}`);

            // Check if new rows are visible
            newRows.forEach((row, index) => {
                const rect = row.getBoundingClientRect();
                console.log(`New row ${index + 1}: ${rect.height > 0 ? 'Visible' : 'Hidden'} (height: ${rect.height}px)`);
            });
        }

        // Render table for specific tab
        function renderTable(tabId) {
            const config = TAB_CONFIG[tabId];
            const tbody = document.getElementById(config.tableBodyId);
            tbody.innerHTML = '';

            // 1. Render existing data rows first
            config.data.forEach((item, index) => {
                tbody.appendChild(createRow(item, index, false, tabId));
            });

            // 2. Add 2 empty rows for new entries (before summary row)
            if (getNewRowsCount(tabId) < 2) {
                const currentNewRows = getNewRowsCount(tabId);
                for (let i = currentNewRows; i < 2; i++) {
                    tbody.appendChild(createRow({
                        id: config.nextTempId--,
                        item_name: '',
                        category: '',
                        cost: '',
                        quantity: '',
                        depreciation: '',
                        monthly_allocation: '',
                        note: ''
                    }, config.data.length + i, true, tabId));
                }
            }

            // 3. Add summary row at the very end
            tbody.appendChild(createSummaryRow(tabId));

            // 4. Add text summary row (số tiền bằng chữ)
            tbody.appendChild(createTextSummaryRow(tabId));

            // updateTotalItemCount();

                            // Ensure minimum empty rows after rendering
                setTimeout(() => {
                    ensureMinimumEmptyRows(tabId);
                    calculateAllTotals(tabId);
                    updateSummaryRowByTab(tabId);
                    // updateAllSTT(tabId); // Remove this - we use updateSTTForVisibleRows instead

                // Apply filtering based on current active tab (this will also handle category column visibility)
                const params = getURLParams();
                const currentActiveTab = params.tabId || 'cost';
                updateColumnHeaders(currentActiveTab);
                applyTabFiltering(currentActiveTab); // This already includes sorting for fixed-cost tab

                // Update cost input styling after rendering and filtering
                updateAllCostInputsStyling();

                // Recalculate container height after adding empty rows
                initializeContainerHeight(tabId);

                // Debug check after 1 second
                setTimeout(() => {
                    debugRowVisibility(tabId);
                }, 1000);
            }, 200);
        }

        // Create table row
        function createRow(item, index, isNew = false, tabId = currentTab) {
            const config = TAB_CONFIG[tabId];
            const row = document.createElement('tr');
            row.className = isNew ? 'new-row' : '';
            row.dataset.index = index;
            row.dataset.isNew = isNew;
            row.dataset.id = item.id;
            row.dataset.tabId = tabId;

            let clsNameNew = row.className;

            // Calculate STT (1-based index for data rows only)
            const stt = isNew ? '' : (config.data.findIndex(d => d.id === item.id) + 1);

            // Base columns for all tabs
            let rowHTML = `
                                 <td class="col-check ${clsNameNew}">
                     <input type="checkbox" class="row-checkbox" data-row-id="${item.id}" onchange="updateDeleteButton()" ${isNew ? 'disabled' : ''}>
                 </td>
                <td class="col-id">
                    <input type="text" class="cell-input readonly" value="${item.id || ''}" readonly>
                </td>
                <td class="col-stt">
                    <div class="calculated-cell" style="color: #6c757d;">
                        ${stt}
                    </div>
                </td>
                <td class="col-name">
                    <input type="text" class="cell-input" value="${item.item_name || ''}"
                           onchange="trackChange(this, ${index}, 'item_name')"
                           onblur="checkNewRowCompletion(this)"
                           onclick="ensureMinimumEmptyRows()"
                           onfocus="ensureMinimumEmptyRows()">
                </td>
                <td class="col-category">
                    <select class="cell-input" onchange="trackChange(this, ${index}, 'category'); reapplyCurrentTabFiltering(); updateCostInputStyling(this.closest('tr')); calculateTotal(this.closest('tr').querySelector('.col-cost input')); sortRowsAfterCategoryChange()"
                           onblur="checkNewRowCompletion(this)"
                           onclick="ensureMinimumEmptyRows()"
                           onfocus="ensureMinimumEmptyRows()">
                        <option value="">-- Chọn --</option>
                        <option value="fixed" ${(item.category === 'fixed') ? 'selected' : ''}>Cố định</option>
                        <option value="fixed_direct" ${(item.category === 'fixed_direct') ? 'selected' : ''}>Cố định Trực tiếp</option>
                        <option value="variable" ${(item.category === 'variable') ? 'selected' : ''}>Biến đổi</option>
                        <option value="depreciated" ${(item.category === 'depreciated') ? 'selected' : ''}>Khấu hao</option>
                    </select>
                </td>
                <td class="col-option1">
                    <select class="cell-input" onchange="trackChange(this, ${index}, 'option1'); sortRowsAfterOption1Change()"
                           onblur="checkNewRowCompletion(this)"
                           onclick="ensureMinimumEmptyRows()"
                           onfocus="ensureMinimumEmptyRows()">
                        <option value="">-- Chọn --</option>
                        <option value="gia_von" ${(item.option1 === 'gia_von') ? 'selected' : ''}>Giá Vốn</option>
                        <option value="ban_hang" ${(item.option1 === 'ban_hang') ? 'selected' : ''}>Bán Hàng</option>
                    </select>
                </td>
                <td class="col-cost x4">
                    <input type="number" class="cell-input" value="${item.cost || ''}"
                           onchange="trackChange(this, ${index}, 'cost'); calculateTotal(this)"
                           oninput="updateSummaryRowRealtime(this)"
                           onblur="checkNewRowCompletion(this)"
                           onclick="ensureMinimumEmptyRows()"
                           onfocus="ensureMinimumEmptyRows()">
                    <input type="text" class="cost-display-editable"
                           value="${item.cost ? formatNumberWithDots(item.cost) : ''}"
                           oninput="handleCostDisplayInput(this, ${index})"
                           onblur="checkNewRowCompletion(this)"
                           onclick="ensureMinimumEmptyRows()"
                           onfocus="ensureMinimumEmptyRows()"
                           placeholder="0">
                </td>
                <td class="col-quantity">
                    <input type="number" class="cell-input" value="${item.quantity || ''}"
                           onchange="trackChange(this, ${index}, 'quantity'); calculateTotal(this)"
                           oninput="updateSummaryRowRealtime(this)"
                           onblur="checkNewRowCompletion(this)"
                           onclick="ensureMinimumEmptyRows()"
                           onfocus="ensureMinimumEmptyRows()">
                </td>
                <td class="col-total">
                    <div class="total-display calculated-cell total-cell">
                        ${formatNumberWithDots((parseFloat(item.cost) || 0) * (parseFloat(item.quantity) || 0))}
                    </div>
                </td>`;

            // Add depreciation columns only for cost tab
            if (config.hasDepreciation) {
                rowHTML += `
                <td class="col-depreciation">
                    <input type="number" class="cell-input" value="${item.depreciation || ''}"
                           onchange="trackChange(this, ${index}, 'depreciation'); calculateTotal(this); reapplyCurrentTabFiltering()"
                           oninput="updateSummaryRowRealtime(this); reapplyCurrentTabFiltering()"
                           onblur="checkNewRowCompletion(this)"
                           onclick="ensureMinimumEmptyRows()"
                           onfocus="ensureMinimumEmptyRows()">
                </td>
                <td class="col-unit-depreciation">
                    <div class="unit-depreciation-display calculated-cell monthly-allocation-cell">
                        ${formatNumberWithDots(
                            (item.category === 'fixed' || item.category === 'fixed_direct') ?
                                (parseFloat(item.cost) || 0) :
                                (((parseFloat(item.depreciation) || 0) > 0) ? Math.round((parseFloat(item.cost) || 0) / (parseFloat(item.depreciation) || 1)) : 0)
                        )}
                    </div>
                </td>
                <td class="col-monthly-allocation">
                    <div class="monthly-allocation-display calculated-cell monthly-allocation-cell">
                        ${formatNumberWithDots(
                            (item.category === 'fixed' || item.category === 'fixed_direct') ?
                                ((parseFloat(item.cost) || 0) * (parseFloat(item.quantity) || 0)) :
                                (((parseFloat(item.depreciation) || 0) > 0) ? Math.round(((parseFloat(item.cost) || 0) * (parseFloat(item.quantity) || 0)) / (parseFloat(item.depreciation) || 1)) : 0)
                        )}
                    </div>
                </td>
                <td class="col-yearly-allocation">
                    <div class="yearly-allocation-display calculated-cell yearly-allocation-cell">
                        ${formatNumberWithDots(
                            (item.category === 'fixed' || item.category === 'fixed_direct') ?
                                ((parseFloat(item.cost) || 0) * (parseFloat(item.quantity) || 0) * 12) :
                                (((parseFloat(item.depreciation) || 0) > 0) ? Math.round(((parseFloat(item.cost) || 0) * (parseFloat(item.quantity) || 0)) / (parseFloat(item.depreciation) || 1)) * 12 : 0)
                        )}
                    </div>
                </td>`;
            }

            // Note column
            rowHTML += `
                <td class="col-note">
                    <input type="text" class="cell-input" value="${item.note || ''}"
                           onchange="trackChange(this, ${index}, 'note')"
                           onblur="checkNewRowCompletion(this)"
                           onclick="ensureMinimumEmptyRows()"
                           onfocus="ensureMinimumEmptyRows()"
                           placeholder="Ghi chú...">
                </td>`;

            // Actions column
            rowHTML += `
                <td class="col-actions">
                    <div class="row-actions">
                        ${isNew ?
                '<button class="action-btn save-btn" onclick="saveNewRow(this)">💾</button>' :
                '<button class="action-btn save-btn" onclick="saveRow(this)">💾</button>'
            }
                        <button class="action-btn delete-btn" onclick="deleteRow(this)">🗑️</button>
                    </div>
                </td>`;

            row.innerHTML = rowHTML;

            // Apply initial cost input styling based on category
            setTimeout(() => {
                updateCostInputStyling(row);
            }, 100);

            return row;
        }

        // Create summary row
        function createSummaryRow(tabId) {
            const config = TAB_CONFIG[tabId];
            const row = document.createElement('tr');
            row.className = 'summary-row';
            row.dataset.tabId = tabId;
            row.id = `summary-row-${tabId}`;

            // Base columns for summary row
            let rowHTML = `
                <td class="col-id" style="background-color: #f8f9fa; font-weight: bold; border-top: 2px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;">.....</div>
                </td>

                <td class="col-tmp" style="background-color: #f8f9fa; font-weight: bold; border-top: 2px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;">.</div>
                </td>

                <td class="col-stt" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;">---</div>
                </td>
                <td class="col-name" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">
                    <div style="text-align: center; color: #495057; font-weight: bold;">TỔNG CỘNG</div>
                </td>
                <td class="col-category" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;"></div>
                </td>
                <td class="col-option1" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;"></div>
                </td>
                <td class="col-cost x1" id="sum_val_01" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">

                </td>
                <td class="col-quantity" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">

                </td>
                <td class="col-total" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">
                    <div id="summary-total-${tabId}" class="summary-value" style="text-align: right; font-weight: bold; padding: 8px;">0</div>
                </td>`;

            // Add depreciation columns only for cost tab
            if (config.hasDepreciation) {
                rowHTML += `
                <td class="col-depreciation" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">
                    <div id="summary-depreciation-${tabId}" class="summary-value" style="text-align: right; font-weight: bold; color: #6c757d; padding: 8px;">0</div>
                </td>
                <td class="col-unit-depreciation" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">
                    <div id="summary-unit-depreciation-${tabId}" class="summary-value" style="text-align: right; font-weight: bold; color: #6c757d; padding: 8px;">0</div>
                </td>
                <td class="col-monthly-allocation" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">
                    <div id="summary-monthly-${tabId}" class="summary-value" style="text-align: right; font-weight: bold; padding: 8px;">0</div>
                </td>
                <td class="col-yearly-allocation" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">
                    <div id="summary-yearly-${tabId}" class="summary-value" style="text-align: right; font-weight: bold; padding: 8px;">0</div>
                </td>`;
            }

            // Note column for summary
            rowHTML += `
                <td class="col-note" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;"></div>
                </td>`;

            // Actions column
            rowHTML += `
                <td class="col-actions" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;"></div>
                </td>`;

            row.innerHTML = rowHTML;
            return row;
        }

        // Create text summary row (số tiền bằng chữ)
        function createTextSummaryRow(tabId) {
            const config = TAB_CONFIG[tabId];
            const textRow = document.createElement('tr');
            textRow.className = 'summary-text-row';
            textRow.dataset.tabId = tabId;
            textRow.id = `summary-text-row-${tabId}`;

            let textRowHTML = `
                <td class="col-id" style="background-color: #e9ecef; border-top: 1px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;">.....</div>
                </td>
                <td class="col-tmp" style="background-color: #e9ecef; border-top: 1px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;">.</div>
                </td>
                <td class="col-stt" style="background-color: #e9ecef; border-top: 1px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;">---</div>
                </td>
                <td class="col-name" style="background-color: #e9ecef; border-top: 1px solid #dee2e6;">
                    <div style="text-align: center; color: #495057; font-weight: bold; font-style: italic;">Bằng chữ</div>
                </td>
                <td class="col-category" style="background-color: #e9ecef; border-top: 1px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;"></div>
                </td>
                <td class="col-option1" style="background-color: #e9ecef; border-top: 1px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;"></div>
                </td>
                <td class="col-cost x2" style="background-color: #e9ecef; border-top: 1px solid #dee2e6;">

                </td>
                <td class="col-quantity" style="background-color: #e9ecef; border-top: 1px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;"></div>
                </td>
                <td class="col-total" style="background-color: #e9ecef; border-top: 1px solid #dee2e6;">
                    <div id="summary-total-text-${tabId}" class="summary-text-value" style="text-align: left; font-weight: bold;  padding: 8px; font-size: 11px; font-style: italic;">Không đồng</div>
                </td>`;

            // Add depreciation columns only for cost tab
            if (config.hasDepreciation) {
                textRowHTML += `
                <td class="col-depreciation" style="background-color: #e9ecef; border-top: 1px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;"></div>
                </td>
                <td class="col-unit-depreciation" style="background-color: #e9ecef; border-top: 1px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;"></div>
                </td>
                <td class="col-monthly-allocation" style="background-color: #e9ecef; border-top: 1px solid #dee2e6;">
                    <div id="summary-monthly-text-${tabId}" class="summary-text-value" style="text-align: left; font-weight: bold; color: #dc3545; padding: 8px; font-size: 11px; font-style: italic;">Không đồng</div>
                </td>
                <td class="col-yearly-allocation" style="background-color: #e9ecef; border-top: 1px solid #dee2e6;">
                    <div id="summary-yearly-text-${tabId}" class="summary-text-value" style="text-align: left; font-weight: bold; color: #dc3545; padding: 8px; font-size: 11px; font-style: italic;">Không đồng</div>
                </td>`;
            }

            // Note column for text summary
            textRowHTML += `
                <td class="col-note" style="background-color: #e9ecef; border-top: 1px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;"></div>
                </td>`;

            // Actions column
            textRowHTML += `
                <td class="col-actions" style="background-color: #e9ecef; border-top: 1px solid #dee2e6;">
                    <div style="text-align: center; color: #495057;"></div>
                </td>`;

            textRow.innerHTML = textRowHTML;
            return textRow;
        }

        // Update summary row with calculated totals
        function updateSummaryRow(tabId) {
            // Use DataManager to calculate totals for all items (no filtering)
            const totals = dataManager.calculateTotalsByTab('cost'); // Always use 'cost' for all items

            console.log(`🧮 updateSummaryRow for ${tabId}:`, totals);

            // Update summary elements
            const costElement = document.getElementById(`summary-cost-${tabId}`);
            const quantityElement = document.getElementById(`summary-quantity-${tabId}`);
            const totalElement = document.getElementById(`summary-total-${tabId}`);
            const monthlyElement = document.getElementById(`summary-monthly-${tabId}`);

            if (costElement) costElement.textContent = formatNumberWithDots(totals.totalCost);
            if (quantityElement) quantityElement.textContent = formatNumberWithDots(totals.totalQuantity);
            if (totalElement) {
                totalElement.textContent = formatNumberWithDots(totals.totalAmount);
                console.log(`📊 Updated total element: ${formatNumberWithDots(totals.totalAmount)}`);
            }

            const config = TAB_CONFIG[tabId];
            if (monthlyElement && config.hasDepreciation) {
                monthlyElement.textContent = formatNumberWithDots(totals.totalMonthlyAllocation);
            }

            // Update text summary (số tiền bằng chữ) - chỉ cho cột cost, total và monthly depreciation
            const costTextElement = document.getElementById(`summary-cost-text-${tabId}`);
            const totalTextElement = document.getElementById(`summary-total-text-${tabId}`);
            const monthlyTextElement = document.getElementById(`summary-monthly-text-${tabId}`);

            if (costTextElement) {
                costTextElement.textContent = numberToVietnameseWords(totals.totalCost);
            }
            if (totalTextElement) {
                totalTextElement.textContent = numberToVietnameseWords(totals.totalAmount);
            }

            // Chỉ hiển thị số tiền bằng chữ cho monthly depreciation (tab cost)
            if (monthlyTextElement && config.hasDepreciation) {
                monthlyTextElement.textContent = numberToVietnameseWords(totals.totalMonthlyAllocation);
            }
        }

        // Update summary row in real-time as user types
        function updateSummaryRowRealtime(input) {
            const row = input.closest('tr');
            const tabId = row.dataset.tabId || currentTab;

            // Update the total for current row first
            calculateTotal(input);

            // Get current active tab from URL
            const params = getURLParams();
            const currentActiveTab = params.tabId || 'cost';

            // Only update summary if the input's tab matches current active tab
            if (tabId === currentActiveTab) {
                // Always use the current active tab - no hardcoded 'cost'
                if (currentActiveTab === 'depreciation' || currentActiveTab === 'fixed-cost' || currentActiveTab === 'variable-cost') {
                    calculateFilteredTotals('cost'); // Only for legacy compatibility
                }
                updateSummaryRowByTab(currentActiveTab); // Always use current tab
            }
        }

        // Update STT (row number) for a specific row
        function updateRowSTT(row, tabId) {
            const config = TAB_CONFIG[tabId];
            const rowId = row.dataset.id;

            // Find the position of this row in the data array
            const dataIndex = config.data.findIndex(item => item.id == rowId);

            if (dataIndex !== -1) {
                const sttElement = row.querySelector('.col-stt .calculated-cell');
                if (sttElement) {
                    sttElement.textContent = dataIndex + 1;
                    console.log(`Updated STT for row ${rowId} to ${dataIndex + 1}`);
                }
            }
        }

        // Update all STT numbers for a tab
        function updateAllSTT(tabId) {
            const config = TAB_CONFIG[tabId];
            const tbody = document.getElementById(config.tableBodyId);
            const dataRows = tbody.querySelectorAll('tr:not([data-is-new="true"]):not(.summary-row):not(.summary-text-row)');

            dataRows.forEach((row, index) => {
                const sttElement = row.querySelector('.col-stt .calculated-cell');
                if (sttElement) {
                    sttElement.textContent = index + 1;
                }
            });
        }

        // Track changes - Using DataManager
        function trackChange(input, index, field, tabId = currentTab) {
            const config = TAB_CONFIG[tabId];
            const rowId = input.closest('tr').dataset.id;
            const row = input.closest('tr');
            const actualTabId = row.dataset.tabId || tabId;

            // Only track fields that are valid for this tab
            const validFields = ['item_name', 'category', 'option1', 'cost', 'quantity', 'note'];
            if (actualTabId === 'cost') {
                validFields.push('depreciation');
                // monthly_allocation is never tracked - it's calculated by JS only
            }

            // Skip tracking if field is not valid for this tab
            if (!validFields.includes(field)) {
                console.log(`Skipping field "${field}" for tab "${actualTabId}" - not in valid fields:`, validFields);
                return;
            }

            console.log(`Tracking field "${field}" for tab "${actualTabId}"`);

            // Update legacy change tracking
            if (!config.changes.has(rowId)) {
                config.changes.set(rowId, {});
            }
            config.changes.get(rowId)[field] = input.value;

            // Sync row data to DataManager
            syncRowToDataManager(row);

            // Update summary row immediately for numeric fields and option1 changes
            if (['cost', 'quantity', 'depreciation', 'option1'].includes(field)) {
                setTimeout(() => {
                    updateAllCalculationsFromDataManager();
                }, 50);
            }

            // Update Save All button state
            updateSaveAllButton();
        }



        // Helper function to get field name from input element
        function getFieldNameFromInput(input) {
            const td = input.closest('td');
            if (td.classList.contains('col-name')) return 'item_name';
            if (td.classList.contains('col-category')) return 'category';
            if (td.classList.contains('col-option1')) return 'option1';
            if (td.classList.contains('col-cost')) return 'cost';
            if (td.classList.contains('col-quantity')) return 'quantity';
            if (td.classList.contains('col-depreciation')) return 'depreciation';
            if (td.classList.contains('col-note')) return 'note';
            return '';
        }





        // Auto-set category for new row when user actually starts typing
        function autoSetCategoryForNewRow(input) {
            const row = input.closest('tr');
            if (row && row.dataset.isNew === 'true') {
                const categorySelect = row.querySelector('.col-category select');

                // Only auto-set if category is empty and user has actually typed something
                if (categorySelect && categorySelect.value === '' && input.value.trim() !== '') {
                    const defaultCategory = getDefaultCategoryForCurrentTab();
                    if (defaultCategory) {
                        categorySelect.value = defaultCategory;
                        console.log(`🎯 Auto-set category to: ${defaultCategory} for new row after user typed: "${input.value}"`);
                        console.log(`🔍 Category select value after setting: ${categorySelect.value}`);

                        // Track the change for auto-save
                        const rowIndex = parseInt(row.dataset.index) || 0;
                        trackChange(categorySelect, rowIndex, 'category');

                        // Trigger change event to ensure proper handling
                        categorySelect.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            }
        }

        // Check if new row is completed and add another empty row
        function checkNewRowCompletion(input, tabId = currentTab) {
            const row = input.closest('tr');
            if (row.dataset.isNew === 'true') {
                const inputs = row.querySelectorAll('.cell-input:not(.readonly)');
                const hasData = Array.from(inputs).some(inp => inp.value.trim() !== '');

                if (hasData) {
                    // Ensure we always have minimum 2 empty rows
                    setTimeout(() => {
                        ensureMinimumEmptyRows(tabId);
                    }, 100);
                }
            }
        }

        // Ensure minimum 2 empty rows when clicking any cell
        function ensureMinimumEmptyRows(tabId = currentTab) {
            const config = TAB_CONFIG[tabId];
            // Get all empty new rows (rows with data-is-new="true" and no data)
            const newRows = document.querySelectorAll(`#${config.tableBodyId} tr[data-is-new="true"]`);
            const emptyNewRows = Array.from(newRows).filter(row => {
                const inputs = row.querySelectorAll('.cell-input:not(.readonly)');
                return Array.from(inputs).every(inp => inp.value.trim() === '');
            });

            const currentEmptyRows = emptyNewRows.length;
            console.log('Current empty rows:', currentEmptyRows, 'for tab:', tabId);

            if (currentEmptyRows < 2) {
                const tbody = document.getElementById(config.tableBodyId);
                const summaryRow = tbody.querySelector('.summary-row');
                const textSummaryRow = tbody.querySelector('.summary-text-row');
                const rowsToAdd = 2 - currentEmptyRows;
                console.log('Adding rows:', rowsToAdd);

                for (let i = 0; i < rowsToAdd; i++) {
                    const newRow = createRow({
                        id: config.nextTempId--,
                        item_name: '',
                        category: '',
                        cost: '',
                        quantity: '',
                        depreciation: '',
                        monthly_allocation: '',
                        note: ''
                    }, config.data.length + i, true, tabId);

                                        // Insert before summary rows instead of appending to end
                    if (summaryRow) {
                        tbody.insertBefore(newRow, summaryRow);
                    } else {
                        tbody.appendChild(newRow);
                    }
                    increaseContainerHeight();

                    // Scroll to make sure new row is visible
                    setTimeout(() => {
                        newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }, 100);

                    // Calculate total for the new row
                    setTimeout(() => {
                        const costInput = newRow.querySelector('.col-cost input');
                        const quantityInput = newRow.querySelector('.col-quantity input');
                        if (costInput && quantityInput) {
                            calculateTotal(costInput);
                        }
                    }, 50);
                }

                console.log('Added', rowsToAdd, 'empty rows for tab:', tabId);

                // Adjust container margin after adding rows
                adjustContainerAfterTableChange();
            }
        }

        // Excel-like navigation variables
        let currentSelectedCell = null;
        let isEditingMode = false;

        // Excel-like navigation functions
        function selectCell(cell) {
            console.log(`📍 Selecting cell:`, cell);

            // Remove selection from previous cell
            if (currentSelectedCell) {
                currentSelectedCell.classList.remove('cell-selected', 'cell-editing');
            }

            // Select new cell
            currentSelectedCell = cell;
            if (cell) {
                cell.classList.add('cell-selected');
                cell.focus();
                isEditingMode = false;
                console.log(`✅ Cell selected and focused`);
            } else {
                console.log(`❌ No cell to select`);
            }
        }

        function enterEditMode(cell) {
            if (cell) {
                cell.classList.remove('cell-selected');
                cell.classList.add('cell-editing');
                isEditingMode = true;

                // For text inputs, select all text
                if (cell.type === 'text' || cell.tagName === 'INPUT') {
                    setTimeout(() => {
                        cell.select();
                    }, 0);
                }
            }
        }

        function exitEditMode(cell) {
            if (cell) {
                // Format cost display if it's a cost input
                if (cell.classList.contains('cost-display-editable')) {
                    formatCostDisplayOnBlur(cell);
                }

                cell.classList.remove('cell-editing');
                cell.classList.add('cell-selected');
                isEditingMode = false;
                cell.blur();
                cell.focus(); // Refocus to maintain selection without editing
            }
        }

        function getNavigableCell(currentCell, direction) {
            const row = currentCell.closest('tr');
            const table = currentCell.closest('table');
            const currentCellIndex = Array.from(row.cells).findIndex(cell =>
                cell.contains(currentCell)
            );
            const currentRowIndex = Array.from(table.rows).findIndex(r => r === row);

            console.log(`🧭 Navigation: ${direction} from row ${currentRowIndex}, cell ${currentCellIndex}`);

            let targetCell = null;

            switch (direction) {
                case 'up':
                    // Find the first visible row above current row
                    for (let i = currentRowIndex - 1; i > 0; i--) { // Skip header row (index 0)
                        const targetRow = table.rows[i];
                        if (targetRow &&
                            !targetRow.classList.contains('summary-row') &&
                            !targetRow.classList.contains('summary-text-row') &&
                            window.getComputedStyle(targetRow).display !== 'none') {
                            targetCell = getEditableInputInCell(targetRow.cells[currentCellIndex]);
                            if (targetCell) break; // Found a navigable cell, stop searching
                        }
                    }
                    break;
                case 'down':
                    // Find the first visible row below current row
                    for (let i = currentRowIndex + 1; i < table.rows.length; i++) {
                        const targetRow = table.rows[i];
                        if (targetRow &&
                            !targetRow.classList.contains('summary-row') &&
                            !targetRow.classList.contains('summary-text-row') &&
                            window.getComputedStyle(targetRow).display !== 'none') {
                            targetCell = getEditableInputInCell(targetRow.cells[currentCellIndex]);
                            if (targetCell) break; // Found a navigable cell, stop searching
                        }
                    }
                    break;
                case 'left':
                    // Find previous editable cell in same row
                    for (let i = currentCellIndex - 1; i >= 0; i--) {
                        const cell = getEditableInputInCell(row.cells[i]);
                        if (cell) {
                            targetCell = cell;
                            break;
                        }
                    }
                    break;
                case 'right':
                    // Find next editable cell in same row
                    for (let i = currentCellIndex + 1; i < row.cells.length; i++) {
                        const cell = getEditableInputInCell(row.cells[i]);
                        if (cell) {
                            targetCell = cell;
                            break;
                        }
                    }
                    break;
            }

            console.log(`🎯 Navigation result: ${targetCell ? 'Found target cell' : 'No target cell found'}`);
            return targetCell;
        }

        function getEditableInputInCell(cell) {
            if (!cell) return null;

            // Skip hidden cells (like category column when hidden)
            const cellStyle = window.getComputedStyle(cell);
            if (cellStyle.display === 'none') return null;

            // Look for editable inputs in order of preference
            const costDisplay = cell.querySelector('.cost-display-editable');
            if (costDisplay) return costDisplay;

            const cellInput = cell.querySelector('.cell-input:not([readonly]):not([disabled])');
            if (cellInput && cellInput.style.display !== 'none') return cellInput;

            const select = cell.querySelector('select.cell-input');
            if (select) return select;

            return null;
        }

        // Add click and focus event listeners to all cell inputs
        function addCellInputListeners() {


            // Legacy focus event for ensuring minimum empty rows
            document.addEventListener('focus', function(event) {
                if (event.target.classList.contains('cell-input') || event.target.classList.contains('cost-display-editable')) {
                    const row = event.target.closest('tr');
                    const tabId = row ? row.dataset.tabId || currentTab : currentTab;
                    console.log('Focused on cell-input or cost-display, checking empty rows for tab:', tabId);

                    setTimeout(() => {
                        ensureMinimumEmptyRows(tabId);
                    }, 50);
                }
            }, true); // Use capture phase

            // Input event for real-time checking
            document.addEventListener('input', function(event) {
                if (event.target.classList.contains('cell-input') || event.target.classList.contains('cost-display-editable')) {
                    console.log('🎯 Input event triggered on cell-input or cost-display:', event.target.value);
                    const row = event.target.closest('tr');
                    const tabId = row ? row.dataset.tabId || currentTab : currentTab;

                    // Auto-set category for new rows when user actually types something
                    autoSetCategoryForNewRow(event.target);

                    // Calculate total if cost, quantity, or depreciation changed
                    if (event.target.closest('.col-cost') || event.target.closest('.col-quantity') || event.target.closest('.col-depreciation')) {
                        // For cost-display-editable, the calculation is already handled in handleCostDisplayInput
                        if (!event.target.classList.contains('cost-display-editable')) {
                            calculateTotal(event.target);
                            updateSummaryRowRealtime(event.target);
                        }
                    }

                    // Setup auto-save for this row
                    console.log('🚀 Calling setupAutoSave for row:', row.dataset.id, 'tab:', tabId);
                    setupAutoSave(row, tabId);

                    setTimeout(() => {
                        ensureMinimumEmptyRows(tabId);
                    }, 100);
                }
            });

            // Change event for select elements (category dropdown)
            document.addEventListener('change', function(event) {
                if (event.target.classList.contains('cell-input') && event.target.tagName === 'SELECT') {
                    console.log('🎯 Change event triggered on select:', event.target.value);
                    const row = event.target.closest('tr');
                    const tabId = row ? row.dataset.tabId || currentTab : currentTab;

                    // Setup auto-save for this row
                    console.log('🚀 Calling setupAutoSave for row:', row.dataset.id, 'tab:', tabId);
                    setupAutoSave(row, tabId);

                    setTimeout(() => {
                        ensureMinimumEmptyRows(tabId);
                    }, 100);
                }
            });

            // Keyboard navigation for Excel-like behavior
            document.addEventListener('keydown', function(event) {
                const target = event.target;

                // Only handle if target is a cell input or cost display
                if (!target.classList.contains('cell-input') && !target.classList.contains('cost-display-editable')) {
                    return;
                }

                // Skip if target is readonly or disabled
                if (target.readOnly || target.disabled) {
                    return;
                }

                switch (event.key) {
                    case 'Enter':
                        event.preventDefault();
                        if (isEditingMode) {
                            // Exit edit mode
                            exitEditMode(target);
                        } else {
                            // Enter edit mode
                            enterEditMode(target);
                        }
                        break;

                    case 'Escape':
                        if (isEditingMode) {
                            event.preventDefault();
                            exitEditMode(target);
                        }
                        break;

                    case 'ArrowUp':
                        if (!isEditingMode) {
                            event.preventDefault();
                            const upCell = getNavigableCell(target, 'up');
                            if (upCell) {
                                selectCell(upCell);
                            }
                        }
                        break;

                    case 'ArrowDown':
                        if (!isEditingMode) {
                            event.preventDefault();
                            const downCell = getNavigableCell(target, 'down');
                            if (downCell) {
                                selectCell(downCell);
                            }
                        }
                        break;

                    case 'ArrowLeft':
                        if (!isEditingMode) {
                            event.preventDefault();
                            const leftCell = getNavigableCell(target, 'left');
                            if (leftCell) {
                                selectCell(leftCell);
                            }
                        }
                        break;

                    case 'ArrowRight':
                        if (!isEditingMode) {
                            event.preventDefault();
                            const rightCell = getNavigableCell(target, 'right');
                            if (rightCell) {
                                selectCell(rightCell);
                            }
                        }
                        break;

                    case 'Tab':
                        if (!isEditingMode) {
                            event.preventDefault();
                            const nextCell = getNavigableCell(target, event.shiftKey ? 'left' : 'right');
                            if (nextCell) {
                                selectCell(nextCell);
                            }
                        }
                        break;
                }
            });

            // Handle click to select cell (merged with legacy functionality)
            document.addEventListener('click', function(event) {
                const target = event.target;
                if (target.classList.contains('cell-input') || target.classList.contains('cost-display-editable')) {
                    // Don't auto-enter edit mode on click, just select
                    if (!target.readOnly && !target.disabled) {
                        selectCell(target);
                    }

                    // Legacy functionality - ensure minimum empty rows
                    const row = target.closest('tr');
                    const tabId = row ? row.dataset.tabId || currentTab : currentTab;
                    console.log('Clicked on cell-input or cost-display, checking empty rows for tab:', tabId);
                    setTimeout(() => {
                        ensureMinimumEmptyRows(tabId);
                    }, 50);
                }
            });

            // Handle double-click to enter edit mode
            document.addEventListener('dblclick', function(event) {
                const target = event.target;
                if (target.classList.contains('cell-input') || target.classList.contains('cost-display-editable')) {
                    if (!target.readOnly && !target.disabled) {
                        enterEditMode(target);
                    }
                }
            });
        }

                // Auto-save functionality
        function setupAutoSave(row, tabId) {
            console.log('📝 setupAutoSave called for row:', row.dataset.id, 'tabId:', tabId);

            if (!currentPlanId) {
                console.log('❌ No currentPlanId, skipping auto-save');
                return; // Don't auto-save if no project selected
            }

            const rowId = row.dataset.id;
            const isNew = row.dataset.isNew === 'true';
            console.log('📊 Row info - ID:', rowId, 'isNew:', isNew);

            // Skip auto-save for completely empty rows
            if (isRowEmpty(row)) {
                console.log('🚫 Row is empty, skipping auto-save');
                return;
            }

            // Clear existing timeout for this row
            if (autoSaveTimeouts.has(rowId)) {
                console.log('🔄 Clearing existing timeout for row:', rowId);
                clearTimeout(autoSaveTimeouts.get(rowId));
            }

                        // Set new timeout for auto-save
            console.log('⏰ Setting auto-save timeout (3 seconds) for row:', rowId);
            const timeoutId = setTimeout(() => {
                console.log('🎯 Auto-save timeout triggered for row:', rowId);
                autoSaveRow(row, tabId);
                autoSaveTimeouts.delete(rowId);
            }, AUTO_SAVE_DELAY);

            autoSaveTimeouts.set(rowId, timeoutId);

            // Show auto-save indicator
            showAutoSaveIndicator(row, 'pending');

            // Show global auto-save indicator
            showGlobalAutoSaveIndicator('pending');

            console.log('✅ Auto-save setup completed for row:', rowId);
        }

        function isRowEmpty(row) {
            const inputs = row.querySelectorAll('.cell-input:not(.readonly)');
            return Array.from(inputs).every(input => input.value.trim() === '');
        }

        async function autoSaveRow(row, tabId) {
            console.log('💾 autoSaveRow started for row:', row.dataset.id, 'tabId:', tabId);
            const config = TAB_CONFIG[tabId];
            const rowId = row.dataset.id;
            const isNew = row.dataset.isNew === 'true';

            try {
                showAutoSaveIndicator(row, 'saving');
                console.log('📤 Preparing to save row - isNew:', isNew);

                // Update global counter and indicator
                globalAutoSaveCount++;
                updateGlobalAutoSaveStatus();

                const formData = getRowFormData(row);
                console.log('📋 FormData prepared for row:', rowId);

                let response;
                if (isNew) {
                    // Save new row - similar to saveNewRow function
                    console.log('🆕 Auto-saving new row...');

                    // Remove id from formData for new rows
                    formData.delete('id');

                    // Add plan_id from selected plan
                    formData.set('plan_id', currentPlanId);

                    // Don't override category - keep the value from getRowFormData
                    // The category should already be set correctly by auto-set functionality
                    console.log('🔄 FormData category (keeping original):', formData.get('category'));

                    // Validate required fields
                    const itemName = formData.get('item_name');
                    if (!currentPlanId) {
                        throw new Error('Vui lòng chọn Dự án trước khi thêm dữ liệu!');
                    }
                    if (!itemName || !itemName.trim()) {
                        throw new Error('Tên mục không được để trống!');
                    }

                    const url = config.apiEndpoint + config.apiEndpointAdd;
                    console.log('🆕 Calling ADD API:', url);

                    // Convert FormData to JSON for new row (like saveNewRow)
                    const jsonData = {};
                    for (let [key, value] of formData.entries()) {
                        jsonData[key] = value;
                    }
                    console.log('📋 Sending JSON data:', jsonData);

                    response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(jsonData)
                    });
                } else {
                    // Update existing row
                    const url = config.apiEndpoint + config.apiEndpointUpdate + '/' + rowId;
                    console.log('🔄 Calling UPDATE API:', url);
                    response = await fetch(url, {
                        method: 'POST',
                        body: formData
                    });
                }

                console.log('📡 API Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                console.log('📊 API Response result:', result);

                if (result.code === 1) {
                    // Success
                    console.log('✅ Auto-save successful for row:', rowId);

                    let newId = null; // Declare newId outside the if block

                    if (isNew) {
                        // Handle new row success (like saveNewRow)
                        newId = result.payload || Date.now();
                        console.log('🆔 Updating new row ID from', rowId, 'to', newId);

                        // Update row with new ID from server
                        row.dataset.id = newId;
                        row.dataset.isNew = 'false';

                        // Update hidden ID input
                        const idInput = row.querySelector('input[name="id"]');
                        if (idInput) {
                            idInput.value = newId;
                        }

                        // Update readonly ID display
                        const readonlyInput = row.querySelector('.readonly');
                        if (readonlyInput) {
                            readonlyInput.value = newId;
                        }

                        // Add to local data (like saveNewRow)
                        const newItem = {
                            id: newId,
                            item_name: formData.get('item_name'),
                            category: formData.get('category'),
                            cost: formData.get('cost'),
                            quantity: formData.get('quantity'),
                            note: formData.get('note')
                        };

                        // Add depreciation for cost tab
                        if (tabId === 'cost') {
                            newItem.depreciation = formData.get('depreciation');
                        }

                        config.data.push(newItem);
                        console.log('📊 Added new item to local data:', newItem);

                        // Convert row to regular row (remove new row styling)
                        row.className = '';

                        // Update STT for this row immediately
                        updateRowSTT(row, tabId);

                        // Enable checkbox for this row
                        const checkbox = row.querySelector('.row-checkbox');
                        if (checkbox) {
                            checkbox.disabled = false;
                        }
                    }

                    showAutoSaveIndicator(row, 'success');

                    // Update global counter and indicator
                    globalAutoSaveCount--;
                    updateGlobalAutoSaveStatus();

                    // Update summary row only if it's the current active tab
                    const params = getURLParams();
                    const currentActiveTab = params.tabId || 'cost';
                    if (tabId === currentActiveTab) {
                        updateSummaryRowByTab(tabId);
                    }



                } else {
                    console.log('❌ API returned error:', result.message);
                    globalAutoSaveCount--;
                    throw new Error(result.message || 'Save failed');
                }

            } catch (error) {
                console.error('💥 Auto-save error for row:', rowId, 'Error:', error);
                showAutoSaveIndicator(row, 'error');

                // Update global counter and show error
                globalAutoSaveCount--;
                showGlobalAutoSaveIndicator('error');
                showAutoSaveErrorDialog(error);
            }
        }

        function showAutoSaveIndicator(row, status) {
            // Remove existing indicators
            const existingIndicator = row.querySelector('.auto-save-indicator');
            if (existingIndicator) {
                existingIndicator.remove();
            }

            // Create new indicator
            const indicator = document.createElement('div');
            indicator.className = 'auto-save-indicator';

            switch (status) {
                case 'pending':
                    indicator.innerHTML = '⏳';
                    indicator.title = 'Sẽ tự động lưu sau 3 giây...';
                    indicator.style.color = '#ffa500';
                    break;
                case 'saving':
                    indicator.innerHTML = '💾';
                    indicator.title = 'Đang lưu...';
                    indicator.style.color = '#007bff';
                    break;
                case 'success':
                    indicator.innerHTML = '✅';
                    indicator.title = 'Đã lưu tự động';
                    indicator.style.color = '#28a745';
                    // Auto-hide success indicator after 2 seconds
                    setTimeout(() => {
                        if (indicator.parentNode) {
                            indicator.remove();
                        }
                    }, 2000);
                    break;
                case 'error':
                    indicator.innerHTML = '❌';
                    indicator.title = 'Lỗi khi lưu tự động';
                    indicator.style.color = '#dc3545';
                    break;
            }

            indicator.style.position = 'absolute';
            indicator.style.right = '5px';
            indicator.style.top = '50%';
            indicator.style.transform = 'translateY(-50%)';
            indicator.style.fontSize = '12px';
            indicator.style.zIndex = '10';

            // Add to first cell of the row
            const firstCell = row.querySelector('td');
            if (firstCell) {
                firstCell.style.position = 'relative';
                firstCell.appendChild(indicator);
            }
        }

        // Global Auto-save indicator functions
        function showGlobalAutoSaveIndicator(status) {
            const indicator = document.getElementById('globalAutoSaveIndicator');
            const icon = document.getElementById('globalAutoSaveIcon');

            // Remove all status classes
            indicator.className = 'global-auto-save-indicator';

            switch (status) {
                case 'pending':
                    indicator.classList.add('pending');
                    icon.innerHTML = '⏳';
                    icon.title = 'Đang chờ auto-save...';
                    break;
                case 'saving':
                    indicator.classList.add('saving');
                    icon.innerHTML = '💾';
                    icon.title = 'Đang auto-save...';
                    break;
                case 'success':
                    indicator.classList.add('success');
                    icon.innerHTML = '✅';
                    icon.title = 'Auto-save thành công';
                    // Auto-hide after 2 seconds
                    setTimeout(() => {
                        if (globalAutoSaveCount === 0) {
                            indicator.style.display = 'none';
                        }
                    }, 2000);
                    break;
                case 'error':
                    indicator.classList.add('error');
                    icon.innerHTML = '❌';
                    icon.title = 'Lỗi auto-save';
                    break;
            }

            indicator.style.display = 'flex';
        }

        function updateGlobalAutoSaveStatus() {
            if (globalAutoSaveCount > 0) {
                showGlobalAutoSaveIndicator('saving');
            } else {
                const indicator = document.getElementById('globalAutoSaveIndicator');
                if (indicator.classList.contains('saving')) {
                    showGlobalAutoSaveIndicator('success');
                }
            }
        }

        function showAutoSaveErrorDialog(error) {
            const errorMessage = error.message || 'Có lỗi xảy ra khi auto-save';

            // Create error dialog
            const dialog = document.createElement('div');
            dialog.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                z-index: 10000;
                max-width: 400px;
                border: 2px solid #dc3545;
            `;

            dialog.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                    <span style="font-size: 24px;">❌</span>
                    <h3 style="margin: 0; color: #dc3545;">Lỗi Auto-save</h3>
                </div>
                <p style="margin: 10px 0; color: #666;">${errorMessage}</p>
                <div style="text-align: right; margin-top: 15px;">
                    <button onclick="this.parentElement.parentElement.remove()"
                            style="padding: 8px 16px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Đóng
                    </button>
                </div>
            `;

            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 9999;
            `;
            backdrop.onclick = () => {
                backdrop.remove();
                dialog.remove();
            };

            document.body.appendChild(backdrop);
            document.body.appendChild(dialog);
        }

        // Get default category based on current active tab
        function getDefaultCategoryForCurrentTab() {
            const params = getURLParams();
            const currentActiveTab = params.tabId || 'cost';

            switch (currentActiveTab) {
                case 'depreciation':
                    return 'depreciated';
                case 'fixed-cost':
                    return 'fixed'; // Default to fixed for fixed-cost tab (user can change to depreciated if needed)
                case 'variable-cost':
                    return 'variable';
                default:
                    return ''; // For cost tab, let user choose
            }
        }

        // Get number of new rows for specific tab
        function getNewRowsCount(tabId = currentTab) {
            const config = TAB_CONFIG[tabId];
            return document.querySelectorAll(`#${config.tableBodyId} tr[data-is-new="true"]`).length;
        }



        // Calculate and update total for a specific row
        function calculateTotal(input) {
            const row = input.closest('tr');
            const costInput = row.querySelector('.col-cost .cell-input'); // Get hidden input
            const quantityInput = row.querySelector('.col-quantity input');
            const depreciationInput = row.querySelector('.col-depreciation input');
            const totalDisplay = row.querySelector('.col-total .total-display');
            const unitDepreciationDisplay = row.querySelector('.col-unit-depreciation .unit-depreciation-display');
            const monthlyAllocationDisplay = row.querySelector('.col-monthly-allocation .monthly-allocation-display');

            if (costInput && quantityInput && totalDisplay) {
                const cost = parseFloat(costInput.value) || 0;
                const quantity = parseFloat(quantityInput.value) || 0;
                const total = cost * quantity;

                console.log(`🧮 calculateTotal: cost=${cost}, quantity=${quantity}, total=${total}`);

                totalDisplay.textContent = formatNumberWithDots(total);

                // Calculate unit depreciation and monthly allocation if depreciation exists
                if (depreciationInput) {
                    const depreciation = parseFloat(depreciationInput.value) || 0;
                    const categorySelect = row.querySelector('.col-category select');
                    const category = categorySelect ? categorySelect.value : '';

                    // Calculate unit depreciation based on category
                    if (unitDepreciationDisplay) {
                        let unitDepreciation = 0;
                        if (category === 'fixed' || category === 'fixed_direct') {
                            // For fixed and fixed_direct categories, use cost directly
                            unitDepreciation = cost;
                        } else {
                            // For depreciated category, use original formula
                            unitDepreciation = depreciation > 0 ? Math.round(cost / depreciation) : 0;
                        }
                        unitDepreciationDisplay.textContent = formatNumberWithDots(unitDepreciation);
                    }

                                            // Calculate monthly allocation based on category
                        let monthlyAllocation = 0;
                        if (category === 'fixed' || category === 'fixed_direct') {
                            // For fixed and fixed_direct categories, use cost * quantity directly
                            monthlyAllocation = total; // total is already cost * quantity
                        } else {
                            // For depreciated category, use original formula
                            monthlyAllocation = depreciation > 0 ? Math.round(total / depreciation) : 0;
                        }

                        if (monthlyAllocationDisplay) {
                            monthlyAllocationDisplay.textContent = formatNumberWithDots(monthlyAllocation);
                        }

                        // Calculate yearly allocation
                        const yearlyAllocationDisplay = row.querySelector('.col-yearly-allocation .yearly-allocation-display');
                        if (yearlyAllocationDisplay) {
                            let yearlyAllocation = 0;
                            if (category === 'variable') {
                                // For variable cost: yearly = monthly total * 12
                                yearlyAllocation = total * 12;
                            } else {
                                // For fixed and depreciation: yearly = monthly allocation * 12
                                yearlyAllocation = monthlyAllocation * 12;
                            }
                            yearlyAllocationDisplay.textContent = formatNumberWithDots(yearlyAllocation);
                        }
                }
            }
        }

        // Calculate all totals when loading data
        function calculateAllTotals(tabId = currentTab) {
            const config = TAB_CONFIG[tabId];
            const tbody = document.getElementById(config.tableBodyId);
            const rows = tbody.querySelectorAll('tr');

            rows.forEach(row => {
                const costInput = row.querySelector('.col-cost .cell-input'); // Get hidden input
                const quantityInput = row.querySelector('.col-quantity input');
                const depreciationInput = row.querySelector('.col-depreciation input');
                const totalDisplay = row.querySelector('.col-total .total-display');
                const unitDepreciationDisplay = row.querySelector('.col-unit-depreciation .unit-depreciation-display');
                const monthlyAllocationDisplay = row.querySelector('.col-monthly-allocation .monthly-allocation-display');

                if (costInput && quantityInput && totalDisplay) {
                    const cost = parseFloat(costInput.value) || 0;
                    const quantity = parseFloat(quantityInput.value) || 0;
                    const total = cost * quantity;
                    totalDisplay.textContent = formatNumberWithDots(total);

                    // Calculate unit depreciation and monthly allocation if depreciation exists
                    if (depreciationInput) {
                        const depreciation = parseFloat(depreciationInput.value) || 0;
                        const categorySelect = row.querySelector('.col-category select');
                        const category = categorySelect ? categorySelect.value : '';

                        // Calculate unit depreciation based on category
                        if (unitDepreciationDisplay) {
                            let unitDepreciation = 0;
                            if (category === 'fixed' || category === 'fixed_direct') {
                                // For fixed and fixed_direct categories, use cost directly
                                unitDepreciation = cost;
                            } else {
                                // For depreciated category, use original formula
                                unitDepreciation = depreciation > 0 ? Math.round(cost / depreciation) : 0;
                            }
                            unitDepreciationDisplay.textContent = formatNumberWithDots(unitDepreciation);
                        }

                        // Calculate monthly allocation based on category
                        let monthlyAllocation = 0;
                        if (category === 'fixed' || category === 'fixed_direct') {
                            // For fixed and fixed_direct categories, use cost * quantity directly
                            monthlyAllocation = total; // total is already cost * quantity
                        } else {
                            // For depreciated category, use original formula
                            monthlyAllocation = depreciation > 0 ? Math.round(total / depreciation) : 0;
                        }

                        if (monthlyAllocationDisplay) {
                            monthlyAllocationDisplay.textContent = formatNumberWithDots(monthlyAllocation);
                        }

                        // Calculate yearly allocation
                        const yearlyAllocationDisplay = row.querySelector('.col-yearly-allocation .yearly-allocation-display');
                        if (yearlyAllocationDisplay) {
                            let yearlyAllocation = 0;
                            if (category === 'variable') {
                                // For variable cost: yearly = monthly total * 12
                                yearlyAllocation = total * 12;
                            } else {
                                // For fixed and depreciation: yearly = monthly allocation * 12
                                yearlyAllocation = monthlyAllocation * 12;
                            }
                            yearlyAllocationDisplay.textContent = formatNumberWithDots(yearlyAllocation);
                        }
                    }
                }
            });
        }

        // Save single row
        async function saveRow(button, tabId = currentTab) {
            const config = TAB_CONFIG[tabId];
            const row = button.closest('tr');
            const rowId = row.dataset.id;
            const isNew = row.dataset.isNew === 'true';

            try {
                const formData = getRowData(row);

                //Bổ xung biến category = updateVariableOption
                // formData.category = config.updateVariableOption;

                if (isNew) {
                    await saveNewRow(button, tabId);
                } else {
                    const response = await fetch(`${config.apiEndpoint}/update/${rowId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(formData)
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    // Check if API returned success response
                    if (result.code !== 1) {
                        throw new Error(result.message || 'Failed to update item');
                    }

                    // Update local data
                    const index = parseInt(row.dataset.index);
                    Object.assign(config.data[index], formData);
                    config.changes.delete(rowId);



                    // Update summary row only if it's the current active tab
                    const params = getURLParams();
                    const currentActiveTab = params.tabId || 'cost';
                    if (tabId === currentActiveTab) {
                        updateSummaryRowByTab(tabId);
                    }

                    showStatus('Row updated successfully!', 'success');
                }
            } catch (error) {
                console.error('Error saving row:', error);
                showStatus('Error saving row: ' + error.message, 'error');
            }
        }

        // Save new row
        async function saveNewRow(button, tabId = currentTab) {

            console.log("Call saveNewRow.. ");
            const config = TAB_CONFIG[tabId];
            const row = button.closest('tr');

            try {
                const formData = getRowData(row);

                //Bổ xung biến category = updateVariableOption (chỉ khi category trống)
                if (!formData.category) {
                    formData.category = config.updateVariableOption;
                    console.log('🔄 Using fallback category:', config.updateVariableOption);
                } else {
                    console.log('🔄 Keeping existing category:', formData.category);
                }

                // Add plan_id from selected plan
                formData.plan_id = currentPlanId;

                // Validate required fields
                if (!currentPlanId) {
                    showStatus('Vui lòng chọn Dự án trước khi thêm dữ liệu!', 'error');
                    return;
                }

                if (!formData.item_name.trim()) {
                    showStatus('Item name is required!', 'error');
                    return;
                }

                //Bỏ id khỏi formData
                delete formData.id;

                // Debug: Log formData being sent
                console.log('Sending formData to API:', formData);

                const response = await fetch(`${config.apiEndpoint}/add`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                // Check if API returned success response
                if (result.code !== 1) {
                    throw new Error(result.message || 'Failed to add item');
                }

                // Add to local data
                const newItem = { id: result.payload || Date.now(), ...formData };
                config.data.push(newItem);

                // Convert row to regular row
                row.className = '';
                row.dataset.isNew = 'false';
                row.dataset.id = newItem.id;
                row.querySelector('.readonly').value = newItem.id;

                // Update STT for this row immediately
                updateRowSTT(row, tabId);

                // Enable checkbox for this row
                const checkbox = row.querySelector('.row-checkbox');
                if (checkbox) {
                    checkbox.disabled = false;
                }



                // Update summary row only if it's the current active tab
                const params = getURLParams();
                const currentActiveTab = params.tabId || 'cost';
                if (tabId === currentActiveTab) {
                    updateSummaryRowByTab(tabId);
                }

                showStatus('New item added successfully!', 'success');
                updateItemCount();
                // increaseContainerHeight();

            } catch (error) {
                console.error('Error adding new row:', error);
                showStatus('Error adding new item: ' + error.message, 'error');
            }
        }

        // Get row data as FormData for API calls
        function getRowFormData(row) {
            // Get specific inputs by their parent column class to avoid confusion with total column
            const itemNameInput = row.querySelector('.col-name input');
            const categorySelect = row.querySelector('.col-category select');
            const option1Select = row.querySelector('.col-option1 select');
            const costInput = row.querySelector('.col-cost .cell-input'); // Get the hidden input
            const quantityInput = row.querySelector('.col-quantity input');
            const depreciationInput = row.querySelector('.col-depreciation input');
            const noteInput = row.querySelector('.col-note input');
            const idInput = row.querySelector('input[name="id"]');

            // Get tabId from row data
            const tabId = row.dataset.tabId || currentTab;
            const config = TAB_CONFIG[tabId];

            const formData = new FormData();

            // Add basic fields
            formData.append('id', idInput ? idInput.value : (row.dataset.id || ''));
            formData.append('plan_id', currentPlanId);
            formData.append('item_name', itemNameInput ? itemNameInput.value.trim() : '');

            // Handle category - debug the issue
            const categoryValue = categorySelect ? categorySelect.value : '';
            console.log(`🔍 getRowFormData - categorySelect.value: "${categoryValue}"`);
            console.log(`🔍 getRowFormData - config.updateVariableOption: "${config.updateVariableOption}"`);

            // Only use fallback if category is truly empty
            const finalCategory = categoryValue || config.updateVariableOption;
            console.log(`🔍 getRowFormData - final category: "${finalCategory}"`);
            formData.append('category', finalCategory);
            formData.append('option1', option1Select ? option1Select.value : '');
            formData.append('cost', costInput ? (parseFloat(costInput.value) || 0) : 0);
            formData.append('quantity', quantityInput ? (parseFloat(quantityInput.value) || 0) : 0);
            formData.append('note', noteInput ? noteInput.value.trim() : '');

            // Add depreciation only for cost tab
            if (tabId === 'cost' && depreciationInput) {
                formData.append('depreciation', parseFloat(depreciationInput.value) || 0);
            } else {
                formData.append('depreciation', '');
            }

            formData.append('status', '');

            return formData;
        }

        // Delete row
        async function deleteRow(button, tabId = currentTab) {
            const config = TAB_CONFIG[tabId];
            const row = button.closest('tr');
            const rowId = row.dataset.id;
            const isNew = row.dataset.isNew === 'true';

            if (isNew) {
                // Just remove the row if it's new
                row.remove();
                // Update summary row after removing new row only if it's the current active tab
                const params = getURLParams();
                const currentActiveTab = params.tabId || 'cost';
                if (tabId === currentActiveTab) {
                    updateSummaryRowByTab(tabId);
                }
                return;
            }

            if (!confirm('Are you sure you want to delete this item?')) {
                return;
            }

            try {
                const response = await fetch(`${config.apiEndpoint}/delete?id=${rowId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    // body: JSON.stringify({ id: rowId })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                // Check if API returned success response
                if (result.code !== 1) {
                    throw new Error(result.message || 'Failed to delete item');
                }

                // Remove from local data
                const index = parseInt(row.dataset.index);
                config.data.splice(index, 1);
                config.changes.delete(rowId);



                // Re-render table
                renderTable(tabId);
                decreaseContainerHeight();

                // Update summary row only if it's the current active tab
                const params = getURLParams();
                const currentActiveTab = params.tabId || 'cost';
                if (tabId === currentActiveTab) {
                    updateSummaryRowByTab(tabId);
                }

                // Adjust container margin after deleting row
                adjustContainerAfterTableChange();

                showStatus('Item deleted successfully!', 'success');

            } catch (error) {
                console.error('Error deleting row:', error);
                showStatus('Error deleting item: ' + error.message, 'error');
            }
        }

        // Save all changes
        async function saveAll(tabId = currentTab) {
            const config = TAB_CONFIG[tabId];
            if (config.changes.size === 0) {
                showStatus('No changes to save!', 'info');
                return;
            }

            try {
                const updateData = Array.from(config.changes.entries()).map(([id, changes]) => {
                    const filteredChanges = { ...changes };

                    // Remove invalid fields for this tab
                    if (tabId === 'variable') {
                        delete filteredChanges.depreciation;
                        delete filteredChanges.monthly_allocation;
                    }

                    return {
                        id: id,
                        ...filteredChanges
                    };
                });

                const response = await fetch(`${config.apiEndpoint}/update-multi`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ items: updateData })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                // Check if API returned success response
                if (result.code !== 1) {
                    throw new Error(result.message || 'Failed to save changes');
                }

                config.changes.clear();
                showStatus('All changes saved successfully!', 'success');

            } catch (error) {
                console.error('Error saving all changes:', error);
                showStatus('Error saving changes: ' + error.message, 'error');
            }
        }



        // Show toast notification
        function showStatus(message, type = 'info') {
            const toastContainer = document.getElementById('toastContainer');

            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;

            toast.innerHTML = `
                <div class="toast-icon"></div>
                <div class="toast-content">${message}</div>
                <button class="toast-close" onclick="removeToast(this)">×</button>
            `;

            // Add to container
            toastContainer.appendChild(toast);

            // Trigger animation
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);

            // Auto remove after 5 seconds
            setTimeout(() => {
                removeToast(toast.querySelector('.toast-close'));
            }, 5000);
        }

        // Remove toast
        function removeToast(closeBtn) {
            const toast = closeBtn.closest('.toast');
            if (toast) {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }
        }

        // Legacy function - now using updateTotalItemCount
        function updateItemCount() {
            updateTotalItemCount();
        }

        // Initialize container height based on current data
        function initializeContainerHeight(tabId = currentTab) {
            const config = TAB_CONFIG[tabId];
            const container = document.querySelector('.cls_contain');

            // Get actual empty rows count after rendering
            const actualEmptyRows = getNewRowsCount(tabId);
            const totalDataRows = config.data.length;
            const totalRows = totalDataRows + actualEmptyRows + 1; // +1 for header row

            // Calculate heights
            const planSelectorHeight = 80;  // Plan selector
            const toolbarHeight = 60;      // Toolbar
            const tabNavHeight = 50;       // Tab navigation
            const tableHeaderHeight = 40;  // Table header
            const marginsPadding = 60;     // Various margins and paddings

            const baseHeight = planSelectorHeight + toolbarHeight + tabNavHeight + tableHeaderHeight + marginsPadding;
            const tableBodyHeight = totalRows * ROW_HEIGHT;
            const calculatedHeight = baseHeight + tableBodyHeight;

            // Ensure minimum height and add some buffer
            const finalHeight = Math.max(600, calculatedHeight + 100);

            container.style.height = finalHeight + 'px';
            container.style.overflow = 'visible'; // Ensure content isn't hidden

            console.log(`Container height set to: ${finalHeight}px for ${totalDataRows} data rows + ${actualEmptyRows} empty rows`);
        }

        // Debug function to check row visibility
        function debugRowVisibility(tabId = currentTab) {
            const config = TAB_CONFIG[tabId];
            const tbody = document.getElementById(config.tableBodyId);
            const allRows = tbody.querySelectorAll('tr');
            const newRows = tbody.querySelectorAll('tr[data-is-new="true"]');

            console.log(`=== Debug ${config.name} ===`);
            console.log(`Total rows: ${allRows.length}`);
            console.log(`New rows: ${newRows.length}`);
            console.log(`Data rows: ${config.data.length}`);
            console.log(`Container height: ${document.querySelector('.cls_contain').style.height}`);

            // Check if new rows are visible
            newRows.forEach((row, index) => {
                const rect = row.getBoundingClientRect();
                console.log(`New row ${index + 1}: ${rect.height > 0 ? 'Visible' : 'Hidden'} (height: ${rect.height}px)`);
            });
        }

        // Add new plan
        async function addNewPlan() {
            const newPlanNameInput = document.getElementById('newPlanName');
            const planName = newPlanNameInput.value.trim();

            if (!planName) {
                showStatus('Vui lòng nhập tên Dự án!', 'error');
                newPlanNameInput.focus();
                return;
            }

            try {
                document.getElementById('planStatus').textContent = 'Đang tạo Dự án mới...';

                const response = await fetch('/api/member-plan-name/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ name: planName })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                // Check if API returned success response
                if (result.code === 1 && result.payload) {
                    const newPlanId = result.payload;

                    // Clear input
                    newPlanNameInput.value = '';

                    // Reload plans and select the new one
                    await reloadAndSelectPlan(newPlanId);

                    showStatus(`Dự án "${planName}" đã được tạo thành công!`, 'success');
                } else {
                    throw new Error(result.message || 'Failed to create plan');
                }
            } catch (error) {
                console.error('Error creating new plan:', error);
                document.getElementById('planStatus').textContent = 'Lỗi tạo Dự án';
                showStatus('Error creating plan: ' + error.message, 'error');
            }
        }

        // Reload plans and select specific plan
        async function reloadAndSelectPlan(planIdToSelect) {
            try {
                // Reload plans list
                await loadPlans();

                // Select the specified plan
                const planSelect = document.getElementById('planSelect');
                planSelect.value = planIdToSelect;

                // Trigger change event to load data
                handlePlanChange();

            } catch (error) {
                console.error('Error reloading and selecting plan:', error);
                showStatus('Error reloading plans: ' + error.message, 'error');
            }
        }

        // Increase container height when adding a new row
        function increaseContainerHeight() {
            const container = document.querySelector('.cls_contain');
            const currentHeight = parseInt(container.style.height) || container.offsetHeight;
            const newHeight = currentHeight + ROW_HEIGHT;

            container.style.height = newHeight + 'px';
        }

        // Decrease container height when deleting a row
        function decreaseContainerHeight() {
            const container = document.querySelector('.cls_contain');
            const currentHeight = parseInt(container.style.height) || container.offsetHeight;
            const newHeight = Math.max(250, currentHeight - ROW_HEIGHT); // Min height 250px

            container.style.height = newHeight + 'px';
        }

        // Update total item count across all tabs
        function updateTotalItemCount() {
            const totalItems = Object.values(TAB_CONFIG).reduce((sum, config) => sum + config.data.length, 0);
            document.getElementById('totalItemCount').textContent = totalItems;
        }



        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Clear tables and add listeners
            clearAllTables();
            addCellInputListeners();

            // Add global debug functions to window for testing
            window.debugCostInputStyling = debugCostInputStyling;
            window.updateAllCostInputsStyling = updateAllCostInputsStyling;
            window.debugRowSorting = debugRowSorting;
            window.sortRowsAfterCategoryChange = sortRowsAfterCategoryChange;
            window.sortRowsAfterOption1Change = sortRowsAfterOption1Change;
            window.calculateFilteredTotals = calculateFilteredTotals;

            window.forceRecalculate = function() {
                const params = getURLParams();
                const currentActiveTab = params.tabId || 'cost';
                console.log(`🔧 Force recalculating for tab: ${currentActiveTab}`);
                if (currentActiveTab === 'depreciation' || currentActiveTab === 'fixed-cost' || currentActiveTab === 'variable-cost') {
                    calculateFilteredTotals('cost'); // Only for legacy compatibility
                }
                updateSummaryRowByTab(currentActiveTab); // Always use current active tab
            };

            // Debug function to test DataManager calculations
            window.debugDataManagerCalculations = function() {
                console.log('🐛 DEBUG: Testing DataManager calculations...');
                console.log('📊 All items in DataManager:');
                const allItems = dataManager.getAllItems();
                allItems.forEach((item, index) => {
                    console.log(`Item ${index + 1}:`, {
                        id: item.id,
                        name: item.item_name,
                        category: item.category,
                        option1: item.option1,
                        cost: item.cost,
                        quantity: item.quantity,
                        total: item.getTotal(),
                        isEmpty: item.isEmpty
                    });
                });

                console.log('🧮 Totals by tab:');
                const costTotals = dataManager.calculateTotalsByTab('cost');
                const fixedTotals = dataManager.calculateTotalsByTab('fixed-cost');
                const variableTotals = dataManager.calculateTotalsByTab('variable-cost');

                console.log('Cost tab totals:', costTotals);
                console.log('Fixed-cost tab totals:', fixedTotals);
                console.log('Variable-cost tab totals:', variableTotals);

                // Test special calculations
                console.log('🎯 Special calculations:');
                const giaVonItems = allItems.filter(item => item.option1 === 'gia_von');
                const fixedItems = allItems.filter(item => item.category === 'fixed');

                console.log('Items with option1 = gia_von:');
                giaVonItems.forEach(item => {
                    console.log(`  - ${item.item_name}: ${item.cost} (${formatNumberWithDots(item.cost)})`);
                });

                console.log('Items with category = fixed:');
                fixedItems.forEach(item => {
                    console.log(`  - ${item.item_name}: ${item.cost} (${formatNumberWithDots(item.cost)})`);
                });

                // Calculate manual totals for verification
                const manualGiaVonSum = giaVonItems.reduce((sum, item) => sum + item.cost, 0);
                const manualFixedSum = fixedItems.reduce((sum, item) => sum + item.cost, 0);

                console.log('🔍 Manual calculations:');
                console.log(`Manual giaVonSum: ${manualGiaVonSum} (${formatNumberWithDots(manualGiaVonSum)})`);
                console.log(`Manual fixedSum: ${manualFixedSum} (${formatNumberWithDots(manualFixedSum)})`);

                console.log('🆚 Comparison:');
                console.log(`DataManager giaVonSum: ${costTotals.giaVonSum} vs Manual: ${manualGiaVonSum} - ${costTotals.giaVonSum === manualGiaVonSum ? '✅ MATCH' : '❌ MISMATCH'}`);
                console.log(`DataManager fixedSum: ${costTotals.fixedSum} vs Manual: ${manualFixedSum} - ${costTotals.fixedSum === manualFixedSum ? '✅ MATCH' : '❌ MISMATCH'}`);

                return {
                    allItems,
                    costTotals,
                    fixedTotals,
                    variableTotals,
                    manualGiaVonSum,
                    manualFixedSum
                };
            };

            // Add DataManager debug functions
            window.dataManager = dataManager;

            // Add summary calculation debug functions
            window.calculateTotalForColTotal = calculateTotalForColTotal;
            window.calculateTotalForColDepreciation = calculateTotalForColDepreciation;
            window.calculateTotalForColUnitDepreciation = calculateTotalForColUnitDepreciation;
            window.calculateTotalForColMonthlyAllocation = calculateTotalForColMonthlyAllocation;
            window.updateSummaryRowByTab = updateSummaryRowByTab;
            window.updateSummaryRowByTabImmediate = updateSummaryRowByTabImmediate;
            window.getFilteredItemsByTab = getFilteredItemsByTab;

            // Debug function to check summary row elements
            window.debugSummaryElements = function(tabId = 'cost') {
                console.log(`🔍 Checking summary elements for tab: ${tabId}`);

                const elements = [
                    `summary-total-${tabId}`,
                    `summary-depreciation-${tabId}`,
                    `summary-unit-depreciation-${tabId}`,
                    `summary-monthly-${tabId}`,
                    `summary-total-text-${tabId}`,
                    `summary-monthly-text-${tabId}`
                ];

                elements.forEach(elementId => {
                    const element = document.getElementById(elementId);
                    if (element) {
                        console.log(`✅ Found: ${elementId}`, element);
                    } else {
                        console.log(`❌ Missing: ${elementId}`);
                    }
                });

                // Check summary row exists
                const summaryRow = document.getElementById(`summary-row-${tabId}`);
                if (summaryRow) {
                    console.log(`✅ Found summary row: summary-row-${tabId}`, summaryRow);
                } else {
                    console.log(`❌ Missing summary row: summary-row-${tabId}`);
                }

                return elements.map(id => ({
                    id,
                    exists: !!document.getElementById(id)
                }));
            };
            window.testDataManager = function() {
                console.log('🧪 Testing DataManager...');
                console.log('📊 Current items:', dataManager.getAllItems());
                console.log('🔢 Total items:', dataManager.getAllItems().length);

                // Test calculations for each tab
                ['cost', 'depreciation', 'fixed-cost', 'variable-cost'].forEach(tabId => {
                    const totals = dataManager.calculateTotalsByTab(tabId);
                    console.log(`📈 Totals for ${tabId}:`, totals);
                });

                // Test filtering
                console.log('🔍 Variable items:', dataManager.getItemsByTab('variable-cost'));
                console.log('🔍 Fixed items:', dataManager.getItemsByTab('fixed-cost'));
                console.log('🔍 Depreciation items:', dataManager.getItemsByTab('depreciation'));

                return 'DataManager test completed. Check console for details.';
            };

            // Debug function to check what's causing 500.070.000
            window.debugSummaryCalculation = function() {
                console.log('🐛 DEBUG: Checking summary calculation...');

                // Check current tab
                const params = getURLParams();
                const currentActiveTab = params.tabId || 'cost';
                console.log(`📋 Current active tab: ${currentActiveTab}`);

                // Check all items in DataManager
                const allItems = dataManager.getAllItems();
                console.log(`📊 Total items in DataManager: ${allItems.length}`);

                allItems.forEach((item, index) => {
                    console.log(`Item ${index + 1}:`, {
                        id: item.id,
                        name: item.item_name,
                        category: item.category,
                        option1: item.option1,
                        cost: item.cost,
                        quantity: item.quantity,
                        total: item.getTotal(),
                        isEmpty: item.isEmpty,
                        isNew: item.isNew
                    });
                });

                // Check filtered items for current tab
                const filteredItems = getFilteredItemsByTab(currentActiveTab);
                console.log(`🔍 Filtered items for ${currentActiveTab}: ${filteredItems.length}`);

                filteredItems.forEach((item, index) => {
                    console.log(`Filtered Item ${index + 1}:`, {
                        id: item.id,
                        name: item.item_name,
                        total: item.getTotal()
                    });
                });

                // Calculate totals manually
                const manualTotal = filteredItems.reduce((sum, item) => sum + item.getTotal(), 0);
                console.log(`🧮 Manual calculation for ${currentActiveTab}: ${manualTotal} (${formatNumberWithDots(manualTotal)})`);

                // Check DataManager calculation
                const dataManagerTotals = dataManager.calculateTotalsByTab(currentActiveTab);
                console.log(`🤖 DataManager calculation for ${currentActiveTab}:`, dataManagerTotals);

                // Check DOM elements
                const summaryTotalElement = document.getElementById(`summary-total-${currentActiveTab}`);
                const summaryTotalCostElement = document.getElementById(`summary-total-cost`);

                console.log(`🎯 DOM Elements:`, {
                    [`summary-total-${currentActiveTab}`]: summaryTotalElement?.textContent,
                    'summary-total-cost': summaryTotalCostElement?.textContent
                });

                // Check if there are any legacy calculations
                const legacyConfig = TAB_CONFIG['cost'];
                console.log(`📂 Legacy config.data length: ${legacyConfig.data.length}`);

                legacyConfig.data.forEach((item, index) => {
                    const cost = parseFloat(item.cost) || 0;
                    const quantity = parseFloat(item.quantity) || 0;
                    const total = cost * quantity;
                    console.log(`Legacy Item ${index + 1}: ${item.item_name} - cost: ${cost}, quantity: ${quantity}, total: ${total}`);
                });

                return {
                    currentTab: currentActiveTab,
                    allItems: allItems.length,
                    filteredItems: filteredItems.length,
                    manualTotal,
                    dataManagerTotals,
                    legacyDataLength: legacyConfig.data.length
                };
            };

            // Debug function to check both calculation methods
            window.debugBothCalculations = function() {
                console.log('🔍 DEBUG: Comparing both calculation methods...');

                const currentActiveTab = getURLParams().tabId || 'cost';

                // Method 1: calculateFilteredTotals
                console.log('📊 Method 1: calculateFilteredTotals');
                const totals1 = dataManager.calculateTotalsByTab(currentActiveTab);
                console.log(`Result 1: ${totals1.totalAmount} (${formatNumberWithDots(totals1.totalAmount)})`);

                // Method 2: calculateTotalForColTotal
                console.log('📊 Method 2: calculateTotalForColTotal');
                const totals2 = calculateTotalForColTotal(currentActiveTab);
                console.log(`Result 2: ${totals2} (${formatNumberWithDots(totals2)})`);

                // Check if they match
                console.log(`🆚 Match: ${totals1.totalAmount === totals2 ? '✅ YES' : '❌ NO'}`);

                if (totals1.totalAmount !== totals2) {
                    console.log('❌ MISMATCH DETECTED!');
                    console.log(`Method 1 (DataManager): ${totals1.totalAmount}`);
                    console.log(`Method 2 (calculateTotalForColTotal): ${totals2}`);
                }

                return {
                    method1: totals1.totalAmount,
                    method2: totals2,
                    match: totals1.totalAmount === totals2
                };
            };

            // Debug function for Variable Cost calculations
            window.debugVariableCostCalculations = function() {
                console.log('🐛 DEBUG: Checking Variable Cost calculations...');

                // Get all variable cost items
                const variableItems = dataManager.getItemsByTab('variable-cost').filter(item => !item.isEmpty);
                console.log(`📊 Variable cost items found: ${variableItems.length}`);

                // Group by option1
                const banHangItems = variableItems.filter(item => item.option1 === 'ban_hang');
                const giaVonItems = variableItems.filter(item => item.option1 === 'gia_von');

                console.log(`🛒 Ban hang items: ${banHangItems.length}`);
                banHangItems.forEach((item, index) => {
                    console.log(`  ${index + 1}. ${item.item_name} - cost: ${item.cost}, quantity: ${item.quantity}, total: ${item.getTotal()}`);
                });

                console.log(`💰 Gia von items: ${giaVonItems.length}`);
                giaVonItems.forEach((item, index) => {
                    console.log(`  ${index + 1}. ${item.item_name} - cost: ${item.cost}, quantity: ${item.quantity}, total: ${item.getTotal()}`);
                });

                // Calculate totals
                const banHangMonthlyTotal = banHangItems.reduce((sum, item) => sum + item.getTotal(), 0);
                const giaVonMonthlyTotal = giaVonItems.reduce((sum, item) => sum + item.getTotal(), 0);
                const banHangYearlyTotal = banHangMonthlyTotal * 12;
                const giaVonYearlyTotal = giaVonMonthlyTotal * 12;

                console.log(`🧮 Calculations:`);
                console.log(`  Ban hang monthly total: ${banHangMonthlyTotal} (${formatNumberWithDots(banHangMonthlyTotal)})`);
                console.log(`  Ban hang yearly total: ${banHangYearlyTotal} (${formatNumberWithDots(banHangYearlyTotal)})`);
                console.log(`  Gia von monthly total: ${giaVonMonthlyTotal} (${formatNumberWithDots(giaVonMonthlyTotal)})`);
                console.log(`  Gia von yearly total: ${giaVonYearlyTotal} (${formatNumberWithDots(giaVonYearlyTotal)})`);

                // Check DOM elements
                const domBanHangThang = document.getElementById('tong_cp_ban_hang_hang_thang')?.textContent || 'NOT FOUND';
                const domBanHangNam = document.getElementById('tong_cp_ban_hang_hang_nam')?.textContent || 'NOT FOUND';
                const domGiaVonThang = document.getElementById('tong_cp_gia_von_hang_thang')?.textContent || 'NOT FOUND';
                const domGiaVonNam = document.getElementById('tong_cp_gia_von_hang_nam')?.textContent || 'NOT FOUND';

                console.log(`🎯 DOM values:`);
                console.log(`  tong_cp_ban_hang_hang_thang: ${domBanHangThang}`);
                console.log(`  tong_cp_ban_hang_hang_nam: ${domBanHangNam}`);
                console.log(`  tong_cp_gia_von_hang_thang: ${domGiaVonThang}`);
                console.log(`  tong_cp_gia_von_hang_nam: ${domGiaVonNam}`);

                return {
                    banHangItems: banHangItems.length,
                    giaVonItems: giaVonItems.length,
                    banHangMonthlyTotal,
                    banHangYearlyTotal,
                    giaVonMonthlyTotal,
                    giaVonYearlyTotal,
                    domValues: {
                        banHangThang: domBanHangThang,
                        banHangNam: domBanHangNam,
                        giaVonThang: domGiaVonThang,
                        giaVonNam: domGiaVonNam
                    }
                };
            };

            // Debug function specifically for depreciation tab issue
            window.debugDepreciationTab = function() {
                console.log('🐛 DEBUG: Checking depreciation tab calculation...');

                // Get all items from DataManager
                const allItems = dataManager.getAllItems();
                console.log(`📊 Total items in DataManager: ${allItems.length}`);

                // Filter items by category
                const depreciatedItems = allItems.filter(item => item.category === 'depreciated');
                const nonEmptyDepreciatedItems = depreciatedItems.filter(item => !item.isEmpty);

                console.log(`🏷️ Items with category = 'depreciated': ${depreciatedItems.length}`);
                console.log(`🏷️ Non-empty depreciated items: ${nonEmptyDepreciatedItems.length}`);

                // Show each depreciated item
                console.log('📋 Depreciated items:');
                depreciatedItems.forEach((item, index) => {
                    console.log(`  ${index + 1}. ${item.item_name || '(empty name)'} - cost: ${item.cost}, quantity: ${item.quantity}, total: ${item.getTotal()}, isEmpty: ${item.isEmpty}`);
                });

                // Calculate manual total
                const manualTotal = nonEmptyDepreciatedItems.reduce((sum, item) => sum + item.getTotal(), 0);
                console.log(`🧮 Manual total for depreciated items: ${manualTotal} (${formatNumberWithDots(manualTotal)})`);

                // Test getFilteredItemsByTab
                const filteredItems = getFilteredItemsByTab('depreciation');
                console.log(`🔍 getFilteredItemsByTab('depreciation') returned: ${filteredItems.length} items`);

                filteredItems.forEach((item, index) => {
                    console.log(`  Filtered ${index + 1}. ${item.item_name} - total: ${item.getTotal()}`);
                });

                const filteredTotal = filteredItems.reduce((sum, item) => sum + item.getTotal(), 0);
                console.log(`🔍 Filtered total: ${filteredTotal} (${formatNumberWithDots(filteredTotal)})`);

                // Test calculateTotalForColTotal
                const calculatedTotal = calculateTotalForColTotal('depreciation');
                console.log(`🧮 calculateTotalForColTotal('depreciation'): ${calculatedTotal} (${formatNumberWithDots(calculatedTotal)})`);

                // Check DOM element
                const summaryElement = document.getElementById('summary-total-depreciation');
                console.log(`🎯 DOM summary-total-depreciation: ${summaryElement?.textContent || 'NOT FOUND'}`);

                // Show all items with their categories for comparison
                console.log('📊 All items with categories:');
                allItems.forEach((item, index) => {
                    if (!item.isEmpty) {
                        console.log(`  ${index + 1}. ${item.item_name} - category: '${item.category}', cost: ${item.cost}, quantity: ${item.quantity}, total: ${item.getTotal()}`);
                    }
                });

                return {
                    totalItems: allItems.length,
                    depreciatedItems: depreciatedItems.length,
                    nonEmptyDepreciatedItems: nonEmptyDepreciatedItems.length,
                    manualTotal,
                    filteredTotal,
                    calculatedTotal,
                    domValue: summaryElement?.textContent,
                    expectedTotal: 36000 // KH1 (12000) + KH2 (24000)
                };
            };

            // Quick test function to debug the 500.070.000 issue
            window.quickTest = function() {
                console.log('🚀 QUICK TEST: Debugging 500.070.000 issue...');

                const currentActiveTab = getURLParams().tabId || 'cost';
                console.log(`Current tab: ${currentActiveTab}`);

                // Test both calculation methods
                console.log('=== Testing calculateTotalForColTotal ===');
                const method1Result = calculateTotalForColTotal(currentActiveTab);
                console.log(`Method 1 result: ${method1Result} (${formatNumberWithDots(method1Result)})`);

                console.log('=== Testing dataManager.calculateTotalsByTab ===');
                const method2Result = dataManager.calculateTotalsByTab(currentActiveTab);
                console.log(`Method 2 result: ${method2Result.totalAmount} (${formatNumberWithDots(method2Result.totalAmount)})`);

                // Check DOM element
                const summaryElement = document.getElementById(`summary-total-${currentActiveTab}`);
                console.log(`DOM element current value: ${summaryElement?.textContent || 'NOT FOUND'}`);

                // Force update with method 1
                console.log('=== Forcing update with method 1 ===');
                updateSummaryRowByTab(currentActiveTab);

                setTimeout(() => {
                    console.log(`DOM element after updateSummaryRowByTab: ${summaryElement?.textContent || 'NOT FOUND'}`);
                }, 100);

                return {
                    currentTab: currentActiveTab,
                    method1: method1Result,
                    method2: method2Result.totalAmount,
                    domValue: summaryElement?.textContent,
                    match: method1Result === method2Result.totalAmount
                };
            };

            // Debug function to check data sync between DOM and DataManager
            window.debugDataSync = function() {
                console.log('🔄 DEBUG: Checking data sync between DOM and DataManager...');

                const tbody = document.getElementById('tableBody-cost');
                const rows = tbody.querySelectorAll('tr:not(.summary-row):not(.summary-text-row)');

                console.log(`🏗️ DOM rows found: ${rows.length}`);
                console.log(`📊 DataManager items: ${dataManager.getAllItems().length}`);

                // Check each DOM row
                rows.forEach((row, index) => {
                    const rowId = row.dataset.id;
                    const isNew = row.dataset.isNew === 'true';

                    if (!isNew && rowId) {
                        const nameInput = row.querySelector('.col-name input');
                        const categorySelect = row.querySelector('.col-category select');
                        const costInput = row.querySelector('.col-cost .cell-input');
                        const quantityInput = row.querySelector('.col-quantity input');

                        const domData = {
                            id: rowId,
                            name: nameInput?.value || '',
                            category: categorySelect?.value || '',
                            cost: parseFloat(costInput?.value) || 0,
                            quantity: parseFloat(quantityInput?.value) || 0
                        };

                        const dataManagerItem = dataManager.getItem(rowId);

                        console.log(`Row ${index + 1} (ID: ${rowId}):`);
                        console.log(`  DOM:`, domData);
                        console.log(`  DataManager:`, dataManagerItem ? {
                            id: dataManagerItem.id,
                            name: dataManagerItem.item_name,
                            category: dataManagerItem.category,
                            cost: dataManagerItem.cost,
                            quantity: dataManagerItem.quantity,
                            total: dataManagerItem.getTotal()
                        } : 'NOT FOUND');

                        if (dataManagerItem) {
                            const matches = {
                                name: domData.name === dataManagerItem.item_name,
                                category: domData.category === dataManagerItem.category,
                                cost: domData.cost === dataManagerItem.cost,
                                quantity: domData.quantity === dataManagerItem.quantity
                            };
                            console.log(`  Matches:`, matches);
                        }
                    }
                });

                // Force sync from DOM to DataManager
                console.log('🔄 Force syncing DOM to DataManager...');
                rows.forEach(row => {
                    if (row.dataset.isNew !== 'true') {
                        syncRowToDataManager(row);
                    }
                });

                console.log(`📊 DataManager items after sync: ${dataManager.getAllItems().length}`);

                return {
                    domRows: rows.length,
                    dataManagerItems: dataManager.getAllItems().length
                };
            };

            // Emergency debug function to check the 80.000 vs 36.000 issue
            window.emergencyDebug = function() {
                console.log('🚨 EMERGENCY DEBUG: Checking 80.000 vs 36.000 issue...');

                // Expected data from API
                const expectedDepreciatedItems = [
                    { name: 'KH2', cost: 12000, quantity: 2, total: 24000, category: 'depreciated' },
                    { name: 'KH1', cost: 6000, quantity: 2, total: 12000, category: 'depreciated' }
                ];
                const expectedTotal = 36000;

                console.log('🎯 Expected depreciated items:', expectedDepreciatedItems);
                console.log(`🎯 Expected total: ${expectedTotal} (${formatNumberWithDots(expectedTotal)})`);

                // Check DataManager items
                const allItems = dataManager.getAllItems();
                console.log(`📊 DataManager total items: ${allItems.length}`);

                allItems.forEach((item, index) => {
                    console.log(`DataManager Item ${index + 1}:`, {
                        id: item.id,
                        name: item.item_name,
                        category: item.category,
                        cost: item.cost,
                        quantity: item.quantity,
                        total: item.getTotal(),
                        isEmpty: item.isEmpty
                    });
                });

                // Filter depreciated items
                const depreciatedItems = allItems.filter(item =>
                    item.category === 'depreciated' && !item.isEmpty
                );
                console.log(`🏷️ Depreciated items found: ${depreciatedItems.length}`);

                depreciatedItems.forEach((item, index) => {
                    console.log(`Depreciated ${index + 1}:`, {
                        name: item.item_name,
                        cost: item.cost,
                        quantity: item.quantity,
                        total: item.getTotal()
                    });
                });

                // Calculate manual total
                const manualTotal = depreciatedItems.reduce((sum, item) => sum + item.getTotal(), 0);
                console.log(`🧮 Manual depreciated total: ${manualTotal} (${formatNumberWithDots(manualTotal)})`);

                // Test DataManager method
                const dataManagerFiltered = dataManager.getItemsByTab('depreciation').filter(item => !item.isEmpty);
                const dataManagerTotal = dataManagerFiltered.reduce((sum, item) => sum + item.getTotal(), 0);
                console.log(`🤖 DataManager getItemsByTab('depreciation') total: ${dataManagerTotal} (${formatNumberWithDots(dataManagerTotal)})`);

                // Test calculateTotalForColTotal
                const calculateTotal = calculateTotalForColTotal('depreciation');
                console.log(`🧮 calculateTotalForColTotal('depreciation'): ${calculateTotal} (${formatNumberWithDots(calculateTotal)})`);

                // Check DOM element
                const summaryElement = document.getElementById('summary-total-depreciation');
                console.log(`🎯 DOM summary-total-depreciation: ${summaryElement?.textContent || 'NOT FOUND'}`);

                // Force update and check again
                console.log('🔄 Force updating summary row...');
                updateSummaryRowByTab('depreciation');

                setTimeout(() => {
                    console.log(`🎯 DOM after force update: ${summaryElement?.textContent || 'NOT FOUND'}`);
                }, 100);

                return {
                    expectedTotal,
                    manualTotal,
                    dataManagerTotal,
                    calculateTotal,
                    domValue: summaryElement?.textContent,
                    isCorrect: manualTotal === expectedTotal
                };
            };

            // Debug function for plan define values
            window.debugPlanDefineValues = function() {
                console.log('🐛 DEBUG: Testing plan define values...');

                const giaBanInput = document.getElementById('input_gia_ban_du_kien');
                const luongBanInput = document.getElementById('input_luong_ban_du_kien_thang');
                const doanhThuElement = document.getElementById('doanh-thu-du-kien-thang');

                console.log('📊 Input values:');
                console.log(`  Giá bán: ${giaBanInput?.value || 'NOT FOUND'}`);
                console.log(`  Lượng bán: ${luongBanInput?.value || 'NOT FOUND'}`);
                console.log(`  Doanh thu: ${doanhThuElement?.textContent || 'NOT FOUND'}`);
                console.log(`  Current plan ID: ${currentPlanId}`);

                console.log('📊 Project analysis values:');
                const analysisElements = [
                    'chi_phi_co_dinh_thang', 'chi_phi_co_dinh_nam',
                    'chi_phi_bien_doi_thang', 'chi_phi_bien_doi_nam',
                    'bien_phi_don_vi_thang', 'bien_phi_don_vi_nam',
                    'san_luong_hoa_von_thang', 'san_luong_hoa_von_nam'
                ];

                analysisElements.forEach(id => {
                    const element = document.getElementById(id);
                    console.log(`  ${id}: ${element?.textContent || 'NOT FOUND'}`);
                });

                return {
                    giaBan: giaBanInput?.value,
                    luongBan: luongBanInput?.value,
                    doanhThu: doanhThuElement?.textContent,
                    currentPlanId: currentPlanId,
                    analysisValues: analysisElements.reduce((acc, id) => {
                        acc[id] = document.getElementById(id)?.textContent;
                        return acc;
                    }, {})
                };
            };

                        // Debug function for testing break-even calculation
            window.testSanLuongHoaVon = function(chiPhiCoDinh, giaBan, bienPhiDonVi) {
                console.log('🐛 DEBUG: Testing Sản lượng hòa vốn calculation...');

                // Use default values if not provided
                const testChiPhiCoDinh = chiPhiCoDinh || 1000000;
                const testGiaBan = giaBan || 50000;
                const testBienPhiDonVi = bienPhiDonVi || 30000;

                console.log('📊 Test inputs:');
                console.log(`  Chi phí cố định: ${formatNumberWithDots(testChiPhiCoDinh)}`);
                console.log(`  Giá bán: ${formatNumberWithDots(testGiaBan)}`);
                console.log(`  Biến phí đơn vị: ${formatNumberWithDots(testBienPhiDonVi)}`);

                const result = calculateSanLuongHoaVonThang(testChiPhiCoDinh, testGiaBan, testBienPhiDonVi);

                console.log('📊 Result:');
                console.log(`  Sản lượng hòa vốn: ${formatNumberWithDots(result)} sản phẩm`);
                console.log(`  Verification: ${formatNumberWithDots(result * (testGiaBan - testBienPhiDonVi))} = ${formatNumberWithDots(testChiPhiCoDinh)}`);

                return {
                    chiPhiCoDinh: testChiPhiCoDinh,
                    giaBan: testGiaBan,
                    bienPhiDonVi: testBienPhiDonVi,
                    sanLuongHoaVon: result,
                    verification: result * (testGiaBan - testBienPhiDonVi)
                };
            };

            // Debug function for multi-scenario analysis
            window.debugMultiScenario = function() {
                console.log('🐛 DEBUG: Testing multi-scenario analysis...');

                const giaBan = document.getElementById('input_gia_ban_du_kien')?.value;
                console.log(`📊 Giá bán dự kiến: ${giaBan}`);

                for (let i = 1; i <= 4; i++) {
                    const scenarioInput = document.getElementById(`input_san_luong_ban_du_kien_thang_${i}`);
                    const sanLuong = scenarioInput?.value || 0;

                    console.log(`📊 Scenario ${i}:`);
                    console.log(`  Sản lượng: ${sanLuong}`);

                    // Check calculated values
                    const elements = [
                        'chi_phi_co_dinh_thang', 'chi_phi_bien_doi_thang', 'tong_phi_thang',
                        'doanh_thu_thuan', 'loi_nhuan_thuan', 'thue_phai_dong_cho_nn',
                        'loi_nhuan_net', 'thu_hoi_khau_hao_dan'
                    ];

                    elements.forEach(baseId => {
                        const element = document.getElementById(`${baseId}_${i}`);
                        console.log(`  ${baseId}: ${element?.textContent || 'NOT FOUND'}`);
                    });
                }

                return {
                    giaBan: giaBan,
                    scenarios: Array.from({length: 4}, (_, i) => ({
                        index: i + 1,
                        sanLuong: document.getElementById(`input_san_luong_ban_du_kien_thang_${i + 1}`)?.value,
                        values: ['chi_phi_co_dinh_thang', 'chi_phi_bien_doi_thang', 'tong_phi_thang',
                                'doanh_thu_thuan', 'loi_nhuan_thuan', 'thue_phai_dong_cho_nn',
                                'loi_nhuan_net', 'thu_hoi_khau_hao_dan'].reduce((acc, baseId) => {
                            acc[baseId] = document.getElementById(`${baseId}_${i + 1}`)?.textContent;
                            return acc;
                        }, {})
                    }))
                };
            };

            console.log('🔧 Debug functions added to window: debugCostInputStyling(), updateAllCostInputsStyling(), debugRowSorting(), sortRowsAfterCategoryChange(), sortRowsAfterOption1Change(), calculateFilteredTotals(), forceRecalculate(), debugSummaryCalculation(), debugBothCalculations(), quickTest(), debugDepreciationTab(), debugDataSync(), emergencyDebug(), debugVariableCostCalculations(), debugPlanDefineValues(), testSanLuongHoaVon(), debugMultiScenario()');
        });

        // Helper function to get all field values from a row
        function getRowData(row) {
            const data = {};

            // Get specific inputs by their parent column class
            const itemNameInput = row.querySelector('.col-name input');
            const costInput = row.querySelector('.col-cost .cell-input'); // Get hidden input
            const quantityInput = row.querySelector('.col-quantity input');
            const depreciationInput = row.querySelector('.col-depreciation input');
            const noteInput = row.querySelector('.col-note input');
            const option1Select = row.querySelector('.col-option1 select');

            // Set data values
            data.item_name = itemNameInput ? itemNameInput.value.trim() : '';
            data.cost = costInput ? (costInput.value.trim() || '0') : '0';
            data.quantity = quantityInput ? (quantityInput.value.trim() || '0') : '0';
            data.depreciation = depreciationInput ? depreciationInput.value.trim() : '';
            data.note = noteInput ? noteInput.value.trim() : '';

            // Get category from select
            const categorySelect = row.querySelector('.col-category select');
            if (categorySelect) {
                data.category = categorySelect.value;
                console.log(`🔍 getRowData - categorySelect.value: "${categorySelect.value}"`);
            } else {
                console.log(`🔍 getRowData - categorySelect not found`);
            }

            // Get option1 from select
            if (option1Select) {
                data.option1 = option1Select.value;
            }

            return data;
        }

        // Save all changes in current tab using update-multi
        async function saveAllChanges() {
            if (!currentPlanId) {
                showStatus('Vui lòng chọn Dự án trước!', 'error');
                return;
            }

            try {
                showStatus('Đang lưu tất cả thay đổi...', 'info');

                const config = TAB_CONFIG[currentTab];
                const tbody = document.getElementById(config.tableBodyId);

                // Collect all data from current table (both existing and new rows)
                const allRows = tbody.querySelectorAll('tr:not(.summary-row)');
                const formData = new FormData();

                let rowIndex = 0;
                allRows.forEach(row => {
                    const rowId = row.dataset.id;
                    const isNew = row.dataset.isNew === 'true';

                    // Get all field data using helper function
                    const rowData = getRowData(row);

                    // Only include rows that have some data
                    if (rowData.item_name || rowData.category || rowData.cost !== '0' || rowData.quantity !== '0' || rowData.depreciation || rowData.note) {
                        // Add data in array format
                        formData.append('id[]', isNew ? '' : rowId);
                        formData.append('plan_id[]', currentPlanId);
                        formData.append('item_name[]', rowData.item_name || '');
                        formData.append('category[]', rowData.category || '');
                        formData.append('option1[]', rowData.option1 || '');
                        formData.append('cost[]', rowData.cost || '0');
                        formData.append('quantity[]', rowData.quantity || '0');
                        formData.append('depreciation[]', rowData.depreciation || '');
                        formData.append('note[]', rowData.note || '');
                        formData.append('status[]', '');

                        console.log(`Row ${rowIndex}:`, rowData);
                        rowIndex++;
                    }
                });

                if (rowIndex === 0) {
                    showStatus('Không có dữ liệu để lưu!', 'warning');
                    return;
                }

                console.log(`Saving ${rowIndex} rows to update-multi endpoint`);

                // Debug: Log FormData contents
                console.log('FormData contents:');
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }

                // Call update-multi API
                const response = await fetch(`${config.apiEndpoint}/update-multi`, {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.code !== 1) {
                    throw new Error(result.message || 'Save failed');
                }

                // Clear changes map since everything is saved
                config.changes.clear();

                // Reload data to get updated IDs for new rows
                await loadData(currentTab);



                // Update button states
                updateSaveAllButton();

                showStatus(`Đã lưu thành công ${rowIndex} hàng!`, 'success');

            } catch (error) {
                console.error('Error saving all changes:', error);
                showStatus('Lỗi khi lưu: ' + error.message, 'error');
            }
        }

        // Update Save All button state
        function updateSaveAllButton() {
            const saveBtn = document.getElementById('saveAllBtn');
            const config = TAB_CONFIG[currentTab];

            if (!currentPlanId) {
                saveBtn.disabled = true;
                return;
            }

            // Enable if there are changes in current tab
            const hasChanges = config.changes.size > 0;
            saveBtn.disabled = !hasChanges;
        }

        // Toggle select all checkboxes for a tab
        function toggleSelectAll(tabId) {
            const selectAllCheckbox = document.getElementById(`selectAllCost`);
            const config = TAB_CONFIG[tabId];
            const tbody = document.getElementById(config.tableBodyId);
            const rowCheckboxes = tbody.querySelectorAll('.row-checkbox:not(:disabled)');

            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });

            updateDeleteButton();
        }

        // Update delete button visibility and state
        function updateDeleteButton() {
            const config = TAB_CONFIG[currentTab];
            const tbody = document.getElementById(config.tableBodyId);
            const checkedBoxes = tbody.querySelectorAll('.row-checkbox:checked');
            const deleteBtn = document.getElementById('deleteSelectedBtn');

            // Show/hide and enable/disable delete button based on selection
            if (checkedBoxes.length > 0) {
                deleteBtn.style.display = 'inline-block';
                deleteBtn.disabled = false;
            } else {
                deleteBtn.style.display = 'none';
                deleteBtn.disabled = true;
            }

            // Update select all checkbox state
            const selectAllCheckbox = document.getElementById(`selectAllCost`);
            const allCheckboxes = tbody.querySelectorAll('.row-checkbox:not(:disabled)');
            const checkedCount = checkedBoxes.length;

            if (checkedCount === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedCount === allCheckboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
            }
        }

        // Delete selected rows
        async function deleteSelectedRows() {
            const config = TAB_CONFIG[currentTab];
            const tbody = document.getElementById(config.tableBodyId);
            const checkedBoxes = tbody.querySelectorAll('.row-checkbox:checked');

            if (checkedBoxes.length === 0) {
                showStatus('Vui lòng chọn ít nhất một hàng để xóa!', 'error');
                return;
            }

            // Confirm deletion
            const confirmMessage = `Bạn có chắc chắn muốn xóa ${checkedBoxes.length} hàng đã chọn?`;
            if (!confirm(confirmMessage)) {
                return;
            }

            try {
                showStatus(`Đang xóa ${checkedBoxes.length} hàng...`, 'info');

                // Collect row IDs to delete
                const rowIdsToDelete = Array.from(checkedBoxes).map(checkbox => {
                    return checkbox.getAttribute('data-row-id');
                }).filter(id => id && id !== 'undefined' && id !== '');

                console.log('Deleting rows with IDs:', rowIdsToDelete);

                // Create comma-separated ID list for API
                const idsParam = rowIdsToDelete.join(',');

                // Call delete API with multiple IDs
                const response = await fetch(`${config.apiEndpoint}/delete?id=${idsParam}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                if (result.code !== 1) {
                    throw new Error(result.message || 'Delete failed');
                }

                // Remove deleted rows from data and UI
                rowIdsToDelete.forEach(rowId => {
                    // Remove from data array
                    const index = config.data.findIndex(item => item.id == rowId);
                    if (index !== -1) {
                        config.data.splice(index, 1);
                    }

                    // Remove from changes map
                    config.changes.delete(rowId);
                });



                // Re-render current table
                renderTable(currentTab); // Always use 'cost' for table operations

                // Update summary for current tab
                const params = getURLParams();
                const currentActiveTab = params.tabId || 'cost';
                updateSummaryRowByTab(currentActiveTab);



                // Clear select all checkbox
                const selectAllCheckbox = document.getElementById(`selectAll${currentTab.charAt(0).toUpperCase() + currentTab.slice(1)}`);
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;

                // Hide delete button
                updateDeleteButton();

                // Update save button state
                updateSaveAllButton();

                showStatus(`Đã xóa thành công ${rowIdsToDelete.length} hàng!`, 'success');

            } catch (error) {
                console.error('Error deleting selected rows:', error);
                showStatus('Lỗi khi xóa: ' + error.message, 'error');
            }
        }



        // Auto-save every 30 seconds
        setInterval(() => {

            const hasChanges = Object.values(TAB_CONFIG).some(config => config.changes.size > 0);
            if (hasChanges) {
                console.log('Auto-saving changes...');

            }
        }, 30000);

        // Format number with dots (1200000 -> 1.200.000)
        //Nhưng 1200,3 = 1.200,3

        function formatNumberWithDots(num) {
            // Làm tròn num
            num = Math.round(num);

            if (!num || num == 0) return '0';

            // Kiểm tra số âm
            const isNegative = num < 0;

            // Lấy giá trị tuyệt đối và chuyển thành string
            const cleanNum = Math.abs(num).toString().replace(/\D/g, '');

            if (!cleanNum) return '0';

            // Thêm dấu chấm mỗi 3 chữ số từ phải sang trái
            const formatted = cleanNum.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            // Thêm dấu âm nếu cần
            return isNegative ? '-' + formatted : formatted;
        }

        /**
         * Tính sản lượng hòa vốn hàng tháng
         * Công thức: Sản lượng hòa vốn = Chi phí cố định hàng tháng / (Giá bán dự kiến - Biến phí đơn vị hàng tháng)
         * @param {number} chiPhiCoDinhThang - Chi phí cố định hàng tháng
         * @param {number} giaBanDuKien - Giá bán dự kiến
         * @param {number} bienPhiDonVi - Biến phí đơn vị hàng tháng
         * @returns {number} Sản lượng hòa vốn hàng tháng
         */
        function calculateSanLuongHoaVonThang(chiPhiCoDinhThang, giaBanDuKien, bienPhiDonVi) {

            console.log('🔧xxx calculateSanLuongHoaVonThang called');
            console.log('   chiPhiCoDinhThang:', chiPhiCoDinhThang);
            console.log('   giaBanDuKien:', giaBanDuKien);
            console.log('   bienPhiDonVi:', bienPhiDonVi);

            // Validate inputs
            if (!chiPhiCoDinhThang || !giaBanDuKien || giaBanDuKien <= 0) {
                console.log('⚠️ Không thể tính sản lượng hòa vốn: thiếu dữ liệu đầu vào');
                return 0;
            }

            // Tính biến lãi đơn vị (giá bán - biến phí đơn vị)
            const bienLaiDonVi = giaBanDuKien - (bienPhiDonVi || 0);
            console.log('  xxx bienLaiDonVi:', bienLaiDonVi);

            // Kiểm tra điều kiện có thể tính hòa vốn
            if (bienLaiDonVi <= 0) {
                console.log('⚠️ Không thể tính sản lượng hòa vốn: biến lãi đơn vị <= 0');
                console.log(`   Giá bán dự kiến: ${giaBanDuKien}`);
                console.log(`   Biến phí đơn vị: ${bienPhiDonVi || 0}`);
                console.log(`   Biến lãi đơn vị: ${bienLaiDonVi}`);
                return 0;
            }

            // Tính sản lượng hòa vốn
            const sanLuongHoaVon = chiPhiCoDinhThang / bienLaiDonVi;

            console.log(`📊 Tính sản lượng hòa vốn:`);
            console.log(`   Chi phí cố định hàng tháng: ${formatNumberWithDots(chiPhiCoDinhThang)}`);
            console.log(`   Giá bán dự kiến: ${formatNumberWithDots(giaBanDuKien)}`);
            console.log(`   Biến phí đơn vị: ${formatNumberWithDots(bienPhiDonVi || 0)}`);
            console.log(`   Biến lãi đơn vị: ${formatNumberWithDots(bienLaiDonVi)}`);
            console.log(`   Sản lượng hòa vốn: ${formatNumberWithDots(sanLuongHoaVon)}`);

            return sanLuongHoaVon;
        }

                // Handle input in cost display (editable)
        function handleCostDisplayInput(costDisplayInput, index) {
            const row = costDisplayInput.closest('tr');
            const hiddenCostInput = row.querySelector('.col-cost .cell-input');

            // Get the raw input value
            let inputValue = costDisplayInput.value;

            // Remove all dots and non-numeric characters except digits
            let numericValue = inputValue.replace(/\./g, '').replace(/[^\d]/g, '');

            // Convert to number
            let numberValue = parseInt(numericValue) || 0;

            // Update the hidden input with raw number
            if (hiddenCostInput) {
                hiddenCostInput.value = numberValue;

                // Trigger change events on hidden input
                trackChange(hiddenCostInput, index, 'cost');
                calculateTotal(hiddenCostInput);
                updateSummaryRowRealtime(hiddenCostInput);
            }

            // Format display in real-time only if we're in editing mode
            if (isEditingMode) {
                // Only format when not actively typing (to avoid cursor jumping)
                // We'll format on blur instead for better UX
            } else {
                // If not in editing mode, format immediately
                const formattedValue = numberValue > 0 ? formatNumberWithDots(numberValue) : '';
                if (costDisplayInput.value !== formattedValue) {
                    costDisplayInput.value = formattedValue;
                }
            }
        }

        // Format cost display when exiting edit mode
        function formatCostDisplayOnBlur(costDisplayInput) {
            const row = costDisplayInput.closest('tr');
            const hiddenCostInput = row.querySelector('.col-cost .cell-input');

            if (hiddenCostInput) {
                const numberValue = parseInt(hiddenCostInput.value) || 0;
                const formattedValue = numberValue > 0 ? formatNumberWithDots(numberValue) : '';
                costDisplayInput.value = formattedValue;
            }
        }

        // Update cost display with formatted number (legacy function - now unused)
        function updateCostDisplay(input) {
            // This function is no longer needed as we use editable cost display
            return;
        }

        // Convert number to Vietnamese words
        function numberToVietnameseWords(num) {
            if (num === 0) return "Không đồng";

            const ones = ["", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín"];
            const tens = ["", "", "hai mươi", "ba mươi", "bốn mươi", "năm mươi", "sáu mươi", "bảy mươi", "tám mươi", "chín mươi"];
            const scales = ["", "nghìn", "triệu", "tỷ"];

            function convertHundreds(n) {
                let result = "";
                const hundred = Math.floor(n / 100);
                const remainder = n % 100;
                const ten = Math.floor(remainder / 10);
                const one = remainder % 10;

                if (hundred > 0) {
                    result += ones[hundred] + " trăm";
                    if (remainder > 0) result += " ";
                }

                if (ten >= 2) {
                    result += tens[ten];
                    if (one > 0) {
                        if (one === 1) {
                            result += " một";
                        } else if (one === 5 && ten > 1) {
                            result += " lăm";
                        } else {
                            result += " " + ones[one];
                        }
                    }
                } else if (ten === 1) {
                    if (one === 0) {
                        result += "mười";
                    } else if (one === 5) {
                        result += "mười lăm";
                    } else {
                        result += "mười " + ones[one];
                    }
                } else if (one > 0) {
                    if (hundred > 0 && one === 1) {
                        result += "lẻ một";
                    } else {
                        result += ones[one];
                    }
                }

                return result.trim();
            }

            // Convert thousands to millions, billions
            num = Math.round(num); // Round to integer
            if (num < 0) return "Âm " + numberToVietnameseWords(-num);

            let result = "";
            let scaleIndex = 0;

            while (num > 0) {
                const chunk = num % 1000;
                if (chunk > 0) {
                    const chunkWords = convertHundreds(chunk);
                    if (scaleIndex > 0) {
                        result = chunkWords + " " + scales[scaleIndex] + (result ? " " + result : "");
                    } else {
                        result = chunkWords;
                    }
                }
                num = Math.floor(num / 1000);
                scaleIndex++;
            }

            return result.charAt(0).toUpperCase() + result.slice(1) + " đồng";
        }



        // Auto-adjust container margin to prevent footer overlap
        function adjustContainerHeight() {
            // return;

            console.log('🔧 adjustContainerHeight called');
            const container = document.querySelector('.cls_contain');
            const footer = document.querySelector('footer');
            const tabContainer = document.querySelector('.tab-container');

            if (container && footer && tabContainer) {
                // Calculate current table height
                const activeTabContent = document.querySelector('.tab-content.active');
                if (activeTabContent) {
                    const tableHeight = activeTabContent.scrollHeight;
                    const containerRect = container.getBoundingClientRect();
                    const footerRect = footer.getBoundingClientRect();

                    console.log('📏 Table height:', tableHeight, 'Container bottom:', containerRect.bottom, 'Footer top:', footerRect.top);

                    // Calculate how much space we need
                    const containerBottom = containerRect.top + window.scrollY + tableHeight + 100; // 100px for other elements
                    const footerTop = footerRect.top + window.scrollY;

                    // If container would overlap footer, add more margin-bottom
                    if (containerBottom > footerTop) {
                        const extraMargin = containerBottom - footerTop + 50; // 50px buffer
                        const currentMargin = parseInt(getComputedStyle(container).marginBottom) || 100;
                        const newMargin = Math.max(currentMargin, extraMargin);

                        container.style.marginBottom = newMargin + 'px';
                        console.log(`✅ Adjusted container margin-bottom to: ${newMargin}px`);
                    } else {
                        // Reset to default margin if no overlap
                        container.style.marginBottom = '100px';
                        console.log('✅ Reset margin-bottom to default: 100px');
                    }
                } else {
                    console.log('❌ No active tab content found');
                }
            } else {
                console.log('❌ Missing elements - container:', !!container, 'footer:', !!footer, 'tabContainer:', !!tabContainer);
            }
        }

        // Call on window resize and load
        window.addEventListener('resize', adjustContainerHeight);
        window.addEventListener('load', () => {
            console.log('🚀 Window load event - calling adjustContainerHeight');
            // Delay to ensure all content is rendered
            setTimeout(adjustContainerHeight, 500);

            // Attach event listeners cho input sản lượng theo tháng
            setTimeout(attachMonthlyInputListeners, 600);
        });

        // Call when switching tabs
        const originalSwitchTab = switchTab;
        switchTab = function(tabId) {
            originalSwitchTab.call(this, tabId);
            console.log('🔄 Tab switched to:', tabId);
            setTimeout(adjustContainerHeight, 300);
        };

        // Call when table content changes (add/remove rows)
        function adjustContainerAfterTableChange() {
            console.log('📊 Table content changed - adjusting container');
            setTimeout(adjustContainerHeight, 200); // Small delay to ensure DOM is updated
        }

        // Update STT numbers for visible rows only
        function updateSTTForVisibleRows() {
            const tbody = document.getElementById('tableBody-cost');
            if (!tbody) return;

            const visibleRows = tbody.querySelectorAll('tr:not(.loading):not(.summary-row):not([style*="display: none"])');
            let sttCounter = 1;

            visibleRows.forEach(row => {
                // Skip new empty rows
                if (row.dataset.isNew === 'true') {
                    const nameInput = row.querySelector('td.col-name input');
                    if (!nameInput || !nameInput.value.trim()) {
                        // Keep STT empty for new empty rows
                        const sttDiv = row.querySelector('td.col-stt div');
                        if (sttDiv) {
                            sttDiv.textContent = '';
                        }
                        return;
                    }
                }

                // Update STT for data rows and filled new rows
                const sttDiv = row.querySelector('td.col-stt div');
                if (sttDiv) {
                    sttDiv.textContent = sttCounter;
                    sttCounter++;
                }
            });
        }

        // Calculate totals based on visible rows only (for filtering)
        //x2
    </script>

@endsection

