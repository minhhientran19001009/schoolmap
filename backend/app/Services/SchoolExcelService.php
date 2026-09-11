<?php

namespace App\Services;

use App\Models\EducationLevel;
use App\Models\School;
use App\Models\Ward;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SchoolExcelService
{
    /**
     * Danh sách cột dữ liệu đồng bộ với toàn bộ Form Admin (SchoolForm)
     */
    public const COLUMNS = [
        // TAB 1: THÔNG TIN CHUNG & VỊ TRÍ ĐỊA LÝ
        'A'  => ['header' => 'Tên cơ sở giáo dục (*)', 'width' => 38, 'desc' => 'Bắt buộc. Tên đầy đủ của cơ sở giáo dục'],
        'B'  => ['header' => 'Mã nhóm / cơ sở chính', 'width' => 24, 'desc' => 'Mã chung cho cơ sở chính và tất cả cơ sở/phân hiệu trực thuộc, ví dụ: NB-CAEE. Để trống nếu địa điểm hoạt động độc lập.'],
        'C'  => ['header' => 'Tên cơ sở / phân hiệu', 'width' => 26, 'desc' => 'Để trống nếu đây là cơ sở chính. Nhập Cơ sở 1, Cơ sở 2 hoặc Phân hiệu Ninh Bình cho địa điểm còn lại.'],
        'D'  => ['header' => 'Cấp học (*)', 'width' => 18, 'desc' => 'Bắt buộc. Chọn từ danh sách xổ xuống (Đại học, Cao đẳng, Trung cấp, GDTX...)'],
        'E'  => ['header' => 'Xã / Phường', 'width' => 26, 'desc' => 'Chọn từ danh sách xổ xuống (129 xã/phường có sẵn trong sheet Danh_Muc)'],
        'F'  => ['header' => 'Địa chỉ chi tiết', 'width' => 38, 'desc' => 'Số nhà, tên đường, khu đô thị'],
        'G'  => ['header' => 'Vĩ độ (Lat)', 'width' => 16, 'desc' => 'Tọa độ GPS thập phân WGS84 (VD: 20.2605)'],
        'H'  => ['header' => 'Kinh độ (Lng)', 'width' => 16, 'desc' => 'Tọa độ GPS thập phân WGS84 (VD: 105.9750)'],
        'I'  => ['header' => 'Số điện thoại', 'width' => 18, 'desc' => 'Số hotline hoặc điện thoại bàn liên hệ'],
        'J'  => ['header' => 'Website trường', 'width' => 30, 'desc' => 'Địa chỉ website hoặc cổng thông tin (VD: https://cdyteninhbinh.edu.vn)'],

        // TAB 1 (tiếp): CƠ SỞ VẬT CHẤT
        'K'  => ['header' => 'Diện tích khuôn viên (m²)', 'width' => 24, 'desc' => 'Tổng diện tích mặt bằng khuôn viên trường (m²)'],
        'L'  => ['header' => 'Số phòng học / Giảng đường', 'width' => 24, 'desc' => 'Tổng số phòng học lý thuyết và giảng đường'],
        'M'  => ['header' => 'Số xưởng thực hành / Lab', 'width' => 24, 'desc' => 'Tổng số xưởng kỹ thuật, phòng thực hành, phòng thí nghiệm'],

        // TAB 2: BAN GIÁM HIỆU & LÃNH ĐẠO
        'N'  => ['header' => 'Ban Giám hiệu & Lãnh đạo', 'width' => 45, 'desc' => 'Cú pháp: Chức vụ: Họ tên; Chức vụ 2: Họ tên 2 (VD: Hiệu trưởng: PGS.TS. Vũ Văn Hùng; Phó Hiệu trưởng: TS. Nguyễn Văn A)'],

        // TAB 3: ĐÀO TẠO & QUY MÔ SINH VIÊN
        'O'  => ['header' => 'Tuyển sinh hàng năm', 'width' => 20, 'desc' => 'Chỉ tiêu / số lượng học sinh, sinh viên tuyển sinh mới mỗi năm'],
        'P'  => ['header' => 'Tổng sinh viên đang học', 'width' => 22, 'desc' => 'Tổng quy mô học sinh/sinh viên đang theo học hiện tại'],
        'Q'  => ['header' => 'Tốt nghiệp hàng năm', 'width' => 20, 'desc' => 'Số lượng học sinh/sinh viên ra trường mỗi năm'],
        'R'  => ['header' => 'Tỷ lệ có việc làm (%)', 'width' => 20, 'desc' => 'Tỷ lệ sinh viên có việc làm sau tốt nghiệp (VD: 94.5)'],
        'S'  => ['header' => 'Ngành đào tạo & Chỉ tiêu', 'width' => 50, 'desc' => 'Cú pháp: Tên ngành:Chỉ tiêu, cách nhau bằng dấu chấm phẩy ;'],

        // TAB 4: GIẢNG VIÊN & ĐỊNH BIÊN
        'T'  => ['header' => 'Tổng số GV / Giảng viên', 'width' => 22, 'desc' => 'Tổng số cán bộ, giáo viên, giảng viên hiện có'],
        'U'  => ['header' => 'Định biên được giao', 'width' => 20, 'desc' => 'Tổng chỉ tiêu biên chế Sở / Bộ giao'],
        'V'  => ['header' => 'Số GV còn thiếu', 'width' => 18, 'desc' => 'Số lượng cán bộ giảng dạy còn thiếu so với định biên'],
        'W'  => ['header' => 'Số GV dôi dư (thừa)', 'width' => 20, 'desc' => 'Số lượng cán bộ giảng dạy dôi dư (nếu có)'],
        'X'  => ['header' => 'GV Hạng I (Cao cấp)', 'width' => 22, 'desc' => 'Số giảng viên đạt tiêu chuẩn Hạng I / Loại 1'],
        'Y'  => ['header' => 'GV Hạng II (Chính)', 'width' => 22, 'desc' => 'Số giảng viên đạt tiêu chuẩn Hạng II / Loại 2'],
        'Z'  => ['header' => 'GV Hạng III / Tiêu chuẩn', 'width' => 22, 'desc' => 'Số giảng viên tiêu chuẩn Hạng III'],
        'AA' => ['header' => 'Số Tiến sĩ (TS)', 'width' => 18, 'desc' => 'Số lượng cán bộ có học vị Tiến sĩ'],
        'AB' => ['header' => 'Số Thạc sĩ (ThS)', 'width' => 18, 'desc' => 'Số lượng cán bộ có học vị Thạc sĩ'],
        'AC' => ['header' => 'Số GS / PGS', 'width' => 18, 'desc' => 'Số lượng cán bộ có học hàm Giáo sư, Phó Giáo sư'],

        // TAB 5: DOANH NGHIỆP LIÊN KẾT
        'AD' => ['header' => 'Doanh nghiệp liên kết', 'width' => 45, 'desc' => 'Cú pháp: Tên DN:Nội dung hợp tác, cách nhau bằng dấu chấm phẩy ;'],
    ];

    /**
     * Dữ liệu mẫu hoàn chỉnh cho 3 cấp học
     */
    public const SAMPLE_ROWS = [
        [
            'Trường Đại học Hoa Lư',
            'NB-HLU',
            '',
            'Đại học',
            'Phường Hoa Lư',
            'Khu đô thị Xuân Thành, TP. Ninh Bình',
            20.267500,
            105.952000,
            '0229 389 2244',
            'http://hluv.edu.vn',
            150000,
            85,
            12,
            'Hiệu trưởng: PGS.TS. Vũ Văn Hùng; Phó Hiệu trưởng: TS. Nguyễn Văn A; Phó Hiệu trưởng: ThS. Lê Thị B',
            1200,
            3500,
            1050,
            94.5,
            'Sư phạm Tiểu học:300; Sư phạm Mầm non:250; Quản trị Dịch vụ Du lịch:250; Công nghệ Thông tin:200',
            210,
            220,
            10,
            0,
            25,
            85,
            100,
            45,
            140,
            6,
            'Tập đoàn Hyundai Thành Công:Tuyển dụng kỹ sư & Thực tập; Công ty TNHH Mcnex Vina:Hợp tác R&D vi mạch',
        ],
        [
            'Trường Cao đẳng Cơ điện Xây dựng Việt Xô',
            'NB-VIETXO',
            '',
            'Cao đẳng',
            'Phường Trung Sơn',
            'Số 10 Đường Quang Trung, Phường Trung Sơn, TP. Tam Điệp',
            20.158200,
            105.908500,
            '0229 386 4224',
            'https://cdvietxo.edu.vn',
            95000,
            60,
            18,
            'Hiệu trưởng: ThS. Phạm Ngọc Minh; Phó Hiệu trưởng: ThS. Trần Văn C',
            1100,
            2800,
            980,
            96.8,
            'Công nghệ Kỹ thuật Ô tô:350; Điện công nghiệp:280; Hàn công nghệ cao:200; Lắp đặt Điện & Điều hòa:150',
            145,
            150,
            5,
            0,
            12,
            58,
            75,
            8,
            92,
            0,
            'Tổng công ty LILAMA:Đào tạo thợ hàn quốc tế; Doanh nghiệp Xây dựng Xuân Trường:Cung ứng kỹ sư thi công',
        ],
        [
            'Trung tâm GDNN - GDTX Huyện Kim Bảng',
            'NB-KIMBANG',
            '',
            'GDTX',
            'Thị trấn Quế',
            'Thị trấn Quế, Huyện Kim Bảng',
            20.575000,
            105.902000,
            '0226 385 1234',
            'http://gdnnkimbang.hanam.edu.vn',
            18000,
            20,
            4,
            'Giám đốc: ThS. Trần Thị Mai; Phó Giám đốc: CN. Hoàng Văn D',
            280,
            650,
            240,
            89.0,
            'GDTX cấp THPT:250; Kỹ thuật gò hàn cơ bản:100; Sửa chữa xe gắn máy:80',
            35,
            35,
            0,
            0,
            2,
            15,
            18,
            1,
            18,
            0,
            'Công ty May Xuất khẩu Kim Bảng:Thực tập nghề may; HTX Cơ khí Quế:Bảo dưỡng cơ khí',
        ]
    ];

    /**
     * Tạo và tải về file Excel mẫu chuẩn 27 cột với Data Validation Dropdown
     */
    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();

        // 1. Lấy danh sách danh mục từ Database (hoặc danh sách chuẩn)
        try {
            $dbLevels = EducationLevel::pluck('name')->toArray();
        } catch (\Throwable $e) {
            $dbLevels = [];
        }
        $levels = array_values(array_unique(array_merge(['Đại học', 'Cao đẳng', 'GDTX'], $dbLevels)));

        try {
            $wards = Ward::orderBy('full_name')->pluck('full_name')->toArray();
        } catch (\Throwable $e) {
            $wards = [];
        }
        if (empty($wards)) {
            $wards = ['Phường Hoa Lư', 'Phường Trung Sơn', 'Phường Đông Thành', 'Phường Bích Đào', 'Xã Ninh Tiến', 'Thị trấn Quế'];
        }

        // =====================================================================
        // SHEET 3 TRƯỚC HẾT: DANH MỤC TRA CỨU (Danh_Muc) ĐỂ VALIDATION THAM CHIẾU
        // =====================================================================
        $catSheet = $spreadsheet->createSheet();
        $catSheet->setTitle('Danh_Muc');

        // Header sheet Danh mục
        $catSheet->setCellValue('A1', 'Danh sách Cấp học');
        $catSheet->setCellValue('B1', 'Danh sách Xã / Phường (' . count($wards) . ' đơn vị)');
        $catSheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $catSheet->getColumnDimension('A')->setWidth(26);
        $catSheet->getColumnDimension('B')->setWidth(35);
        $catSheet->getRowDimension(1)->setRowHeight(28);

        // Nạp dữ liệu cấp học vào Cột A
        foreach ($levels as $idx => $lvl) {
            $rowNum = $idx + 2;
            $catSheet->setCellValue("A{$rowNum}", $lvl);
            $catSheet->getStyle("A{$rowNum}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $catSheet->getRowDimension($rowNum)->setRowHeight(22);
        }

        // Nạp dữ liệu xã/phường vào Cột B
        foreach ($wards as $idx => $wName) {
            $rowNum = $idx + 2;
            $catSheet->setCellValue("B{$rowNum}", $wName);
            $catSheet->getStyle("B{$rowNum}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $catSheet->getRowDimension($rowNum)->setRowHeight(22);
        }

        $levelCount = count($levels);
        $wardCount = count($wards);

        // =====================================================================
        // SHEET 1: MẪU NHẬP LIỆU (Mau_Nhap_Lieu)
        // =====================================================================
        $sheet = $spreadsheet->getSheet(0);
        $sheet->setTitle('Mau_Nhap_Lieu');

        // 1. Tiêu đề chính trang tính
        $sheet->mergeCells('A1:AD1');
        $sheet->setCellValue('A1', 'KHUNG DỮ LIỆU NHẬP DANH SÁCH CƠ SỞ GIÁO DỤC - ĐỒNG BỘ THEO FORM QUẢN TRỊ ADMIN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E3A8A'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(36);

        // 2. Dòng hướng dẫn nhanh
        $sheet->mergeCells('A2:AD2');
        $sheet->setCellValue('A2', 'Lưu ý: Các cột có dấu (*) là bắt buộc. Cột "Cấp học" và "Xã/Phường" chọn trực tiếp từ danh sách xổ xuống. Có thể xóa các dòng mẫu 4, 5, 6 trước khi nhập.');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(22);

        // 3. Header cột (Dòng 3: A đến AB)
        foreach (self::COLUMNS as $colLetter => $colData) {
            $cellCoord = "{$colLetter}3";
            $sheet->setCellValue($cellCoord, $colData['header']);
            $sheet->getColumnDimension($colLetter)->setWidth($colData['width']);

            // Màu header: Bắt buộc (Xanh đậm 1E40AF), Tùy chọn (Xám than 334155)
            $isReq = str_contains($colData['header'], '(*)');
            $headerColor = $isReq ? '1E40AF' : '334155';

            $sheet->getStyle($cellCoord)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $headerColor],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);
        }
        $sheet->getRowDimension(3)->setRowHeight(34);

        // 4. Thêm các dòng mẫu thực tế (Dòng 4, 5, 6)
        $rowIdx = 4;
        $centerColumns = ['D', 'G', 'H', 'I', 'R']; // Cấp học, Lat, Lng, Phone, Employment %
        foreach (self::SAMPLE_ROWS as $row) {
            $colLetters = array_keys(self::COLUMNS);
            foreach ($row as $i => $val) {
                $colIdx = $colLetters[$i];
                $coord = "{$colIdx}{$rowIdx}";
                $sheet->setCellValue($coord, $val);

                // Căn chỉnh kiểu dữ liệu thông minh
                $align = Alignment::HORIZONTAL_LEFT;
                if (is_numeric($val) && !is_float($val)) {
                    $align = Alignment::HORIZONTAL_RIGHT;
                } elseif (in_array($colIdx, $centerColumns)) {
                    $align = Alignment::HORIZONTAL_CENTER;
                }

                $sheet->getStyle($coord)->applyFromArray([
                    'alignment' => [
                        'horizontal' => $align,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E2E8F0'],
                        ],
                    ],
                ]);
            }
            $sheet->getRowDimension($rowIdx)->setRowHeight(26);
            $rowIdx++;
        }

        // =====================================================================
        // DATA VALIDATION: CỘT B (CẤP HỌC) & CỘT C (XÃ/PHƯỜNG) DẠNG SELECT DROPDOWN
        // =====================================================================
        // Validation cho Cột D: Cấp học (D4:D500)
        $validationLevel = new DataValidation();
        $validationLevel->setType(DataValidation::TYPE_LIST);
        $validationLevel->setErrorStyle(DataValidation::STYLE_STOP);
        $validationLevel->setAllowBlank(false);
        $validationLevel->setShowDropDown(true);
        $validationLevel->setShowInputMessage(true);
        $validationLevel->setShowErrorMessage(true);
        $validationLevel->setErrorTitle('Cấp học không hợp lệ');
        $validationLevel->setError('Vui lòng chọn Cấp học từ danh sách xổ xuống (Đại học, Cao đẳng, GDTX...). Không được tự ý nhập giá trị ngoài danh mục.');
        $validationLevel->setPromptTitle('Chọn Cấp học');
        $validationLevel->setPrompt('Bấm vào biểu tượng mũi tên để chọn Cấp học tương ứng.');
        $validationLevel->setFormula1("Danh_Muc!\$A\$2:\$A\$" . ($levelCount + 1));
        $sheet->setDataValidation('D4:D500', $validationLevel);

        // Validation cho Cột E: Xã / Phường (E4:E500)
        $validationWard = new DataValidation();
        $validationWard->setType(DataValidation::TYPE_LIST);
        $validationWard->setErrorStyle(DataValidation::STYLE_STOP);
        $validationWard->setAllowBlank(true);
        $validationWard->setShowDropDown(true);
        $validationWard->setShowInputMessage(true);
        $validationWard->setShowErrorMessage(true);
        $validationWard->setErrorTitle('Xã/Phường không hợp lệ');
        $validationWard->setError('Vui lòng chọn Xã/Phường từ danh sách 129 xã/phường có sẵn. Không được tự ý nhập tên khác.');
        $validationWard->setPromptTitle('Chọn Xã / Phường');
        $validationWard->setPrompt('Bấm vào biểu tượng mũi tên để chọn Xã/Phường trụ sở.');
        $validationWard->setFormula1("Danh_Muc!\$B\$2:\$B\$" . ($wardCount + 1));
        $sheet->setDataValidation('E4:E500', $validationWard);

        // =====================================================================
        // SHEET 2: HƯỚNG DẪN QUY ƯỚC (Huong_Dan)
        // =====================================================================
        $guideSheet = $spreadsheet->createSheet(1);
        $guideSheet->setTitle('Huong_Dan_Quy_Uoc');

        $guideSheet->setCellValue('A1', 'BẢNG GIẢI THÍCH QUY ƯỚC CỘT VÀ ĐỊNH DẠNG DỮ LIỆU (ĐỒNG BỘ 5 TAB ADMIN)');
        $guideSheet->getStyle('A1')->getFont()->setBold(true)->setSize(13)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E40AF'));
        $guideSheet->mergeCells('A1:C1');
        $guideSheet->getRowDimension(1)->setRowHeight(30);

        $guideHeaders = ['Cột', 'Tên trường thông tin', 'Quy ước nhập liệu & Giá trị hợp lệ'];
        $guideSheet->setCellValue('A3', $guideHeaders[0]);
        $guideSheet->setCellValue('B3', $guideHeaders[1]);
        $guideSheet->setCellValue('C3', $guideHeaders[2]);

        $guideSheet->getStyle('A3:C3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $guideSheet->getColumnDimension('A')->setWidth(10);
        $guideSheet->getColumnDimension('B')->setWidth(30);
        $guideSheet->getColumnDimension('C')->setWidth(75);
        $guideSheet->getRowDimension(3)->setRowHeight(28);

        $guideRow = 4;
        foreach (self::COLUMNS as $letter => $info) {
            $guideSheet->setCellValue("A{$guideRow}", $letter);
            $guideSheet->setCellValue("B{$guideRow}", $info['header']);
            $guideSheet->setCellValue("C{$guideRow}", $info['desc']);

            $guideSheet->getStyle("A{$guideRow}:C{$guideRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            $guideSheet->getStyle("A{$guideRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $guideSheet->getRowDimension($guideRow)->setRowHeight(24);
            $guideRow++;
        }

        // Chọn lại Sheet 1 làm active sheet khi mở file
        $spreadsheet->setActiveSheetIndex(0);

        // Xuất file dưới dạng StreamedResponse
        $filename = 'Mau_Danh_Sach_Truong_Hoc_Ninh_Binh.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Xuất toàn bộ danh sách các trường học hiện có trong CSDL ra file Excel theo đúng khung mẫu chuẩn 28 cột
     */
    public function exportCurrentSchools(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();

        // 1. Lấy danh sách danh mục từ Database cho Sheet Danh_Muc
        try {
            $dbLevels = EducationLevel::pluck('name')->toArray();
        } catch (\Throwable $e) {
            $dbLevels = [];
        }
        $levels = array_values(array_unique(array_merge(['Đại học', 'Cao đẳng', 'Trung cấp', 'GDTX'], $dbLevels)));

        try {
            $wards = Ward::orderBy('full_name')->pluck('full_name')->toArray();
        } catch (\Throwable $e) {
            $wards = [];
        }
        if (empty($wards)) {
            $wards = ['Phường Hoa Lư', 'Phường Trung Sơn', 'Phường Đông Thành', 'Phường Bích Đào', 'Xã Ninh Tiến', 'Thị trấn Quế'];
        }

        // =====================================================================
        // SHEET 3: DANH MỤC TRA CỨU (Danh_Muc) ĐỂ VALIDATION THAM CHIẾU
        // =====================================================================
        $catSheet = $spreadsheet->createSheet();
        $catSheet->setTitle('Danh_Muc');

        $catSheet->setCellValue('A1', 'Danh sách Cấp học');
        $catSheet->setCellValue('B1', 'Danh sách Xã / Phường (' . count($wards) . ' đơn vị)');
        $catSheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $catSheet->getColumnDimension('A')->setWidth(26);
        $catSheet->getColumnDimension('B')->setWidth(35);
        $catSheet->getRowDimension(1)->setRowHeight(28);

        foreach ($levels as $idx => $lvl) {
            $rowNum = $idx + 2;
            $catSheet->setCellValue("A{$rowNum}", $lvl);
            $catSheet->getStyle("A{$rowNum}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $catSheet->getRowDimension($rowNum)->setRowHeight(22);
        }

        foreach ($wards as $idx => $wName) {
            $rowNum = $idx + 2;
            $catSheet->setCellValue("B{$rowNum}", $wName);
            $catSheet->getStyle("B{$rowNum}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $catSheet->getRowDimension($rowNum)->setRowHeight(22);
        }

        $levelCount = count($levels);
        $wardCount = count($wards);

        // =====================================================================
        // SHEET 1: MẪU NHẬP LIỆU (Mau_Nhap_Lieu) VỚI DỮ LIỆU THỰC TẾ
        // =====================================================================
        $sheet = $spreadsheet->getSheet(0);
        $sheet->setTitle('Mau_Nhap_Lieu');

        // Tiêu đề chính trang tính
        $sheet->mergeCells('A1:AD1');
        $sheet->setCellValue('A1', 'DANH SÁCH CƠ SỞ GIÁO DỤC HIỆN CÓ TRÊN HỆ THỐNG (KHUNG CHUẨN 28 CỘT)');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E3A8A'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(36);

        // Dòng hướng dẫn nhanh
        $sheet->mergeCells('A2:AD2');
        $sheet->setCellValue('A2', 'Dữ liệu được xuất tự động từ hệ thống. Bạn có thể bổ sung, sửa đổi thông tin các trường hoặc thêm dòng mới, sau đó dùng chức năng "Nhập từ Excel" để cập nhật lên hệ thống.');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(22);

        // Header cột (Dòng 3: A đến AB)
        foreach (self::COLUMNS as $colLetter => $colData) {
            $cellCoord = "{$colLetter}3";
            $sheet->setCellValue($cellCoord, $colData['header']);
            $sheet->getColumnDimension($colLetter)->setWidth($colData['width']);

            $isReq = str_contains($colData['header'], '(*)');
            $headerColor = $isReq ? '1E40AF' : '334155';

            $sheet->getStyle($cellCoord)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $headerColor],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);
        }
        $sheet->getRowDimension(3)->setRowHeight(34);

        // Lấy danh sách toàn bộ trường học từ CSDL
        $schools = School::with(['educationLevels', 'parentCampus:id,code'])->orderBy('name', 'asc')->get();

        $rowIdx = 4;
        $centerColumns = ['D', 'G', 'H', 'I', 'R'];
        $numColumns = ['K', 'L', 'M', 'O', 'P', 'Q', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC'];

        foreach ($schools as $s) {
            // Tên cấp học
            $firstLvl = $s->educationLevels->first();
            $levelName = $firstLvl?->name;
            if (empty($levelName)) {
                $levelName = match ($s->education_level_id) {
                    'trung_cap' => 'Trung cấp',
                    'cao_dang'  => 'Cao đẳng',
                    'dai_hoc'   => 'Đại học',
                    'gdtx'      => 'GDTX',
                    default     => 'Cao đẳng',
                };
            }

            // Ban giám hiệu dạng chuỗi
            $leadersStr = '';
            if (!empty($s->leaders) && is_array($s->leaders)) {
                $parts = [];
                foreach ($s->leaders as $l) {
                    $pos = $l['position'] ?? 'Lãnh đạo';
                    $name = $l['name'] ?? '';
                    if ($name) {
                        $parts[] = "{$pos}: {$name}";
                    }
                }
                $leadersStr = implode('; ', $parts);
            }
            if (empty($leadersStr) && !empty($s->principal)) {
                $leadersStr = 'Hiệu trưởng: ' . $s->principal;
            }

            // Ngành đào tạo dạng chuỗi: Tên ngành:Chỉ tiêu
            $majorsStr = '';
            if (!empty($s->training_majors) && is_array($s->training_majors)) {
                $parts = [];
                foreach ($s->training_majors as $m) {
                    $mName = $m['name'] ?? '';
                    $quota = $m['annual_quota'] ?? 0;
                    if ($mName) {
                        $parts[] = $quota > 0 ? "{$mName}:{$quota}" : $mName;
                    }
                }
                $majorsStr = implode('; ', $parts);
            }

            // Doanh nghiệp liên kết dạng chuỗi: Tên:Nội dung
            $partnersStr = '';
            if (!empty($s->partner_enterprises) && is_array($s->partner_enterprises)) {
                $parts = [];
                foreach ($s->partner_enterprises as $p) {
                    $pName = $p['name'] ?? '';
                    $coop = $p['cooperation'] ?? '';
                    if ($pName) {
                        $parts[] = $coop ? "{$pName}:{$coop}" : $pName;
                    }
                }
                $partnersStr = implode('; ', $parts);
            }

            $rowMap = [
                'A' => $s->name,
                'B' => $s->parentCampus?->code ?? $s->code,
                'C' => $s->campus_type === 'MAIN' ? null : $s->campus_name,
                'D' => $levelName,
                'E' => $s->ward,
                'F' => $s->address,
                'G' => $s->lat,
                'H' => $s->lng,
                'I' => $s->phone,
                'J' => $s->website,
                'K' => $s->campus_area_m2,
                'L' => $s->classroom_count,
                'M' => $s->workshops_count,
                'N' => $leadersStr,
                'O' => $s->annual_enrollment,
                'P' => $s->student_count,
                'Q' => $s->annual_graduates,
                'R' => $s->employment_rate,
                'S' => $majorsStr,
                'T' => $s->teacher_count,
                'U' => $s->teacher_quota,
                'V' => $s->teachers_shortage,
                'W' => $s->teachers_surplus,
                'X' => $s->faculty_rank_1,
                'Y' => $s->faculty_rank_2,
                'Z' => $s->faculty_rank_3,
                'AA' => $s->faculty_doctors,
                'AB' => $s->faculty_masters,
                'AC' => $s->faculty_professors,
                'AD' => $partnersStr,
            ];

            foreach ($rowMap as $col => $val) {
                $coord = "{$col}{$rowIdx}";
                $sheet->setCellValue($coord, $val);

                $align = Alignment::HORIZONTAL_LEFT;
                if (is_numeric($val) && !is_float($val)) {
                    $align = Alignment::HORIZONTAL_RIGHT;
                } elseif (in_array($col, $centerColumns)) {
                    $align = Alignment::HORIZONTAL_CENTER;
                }

                $sheet->getStyle($coord)->applyFromArray([
                    'alignment' => [
                        'horizontal' => $align,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E2E8F0'],
                        ],
                    ],
                ]);
            }
            $sheet->getRowDimension($rowIdx)->setRowHeight(26);
            $rowIdx++;
        }

        // Validation cho Cột D: Cấp học (D4:D500)
        $validationLevel = new DataValidation();
        $validationLevel->setType(DataValidation::TYPE_LIST);
        $validationLevel->setErrorStyle(DataValidation::STYLE_STOP);
        $validationLevel->setAllowBlank(false);
        $validationLevel->setShowDropDown(true);
        $validationLevel->setShowInputMessage(true);
        $validationLevel->setShowErrorMessage(true);
        $validationLevel->setErrorTitle('Cấp học không hợp lệ');
        $validationLevel->setError('Vui lòng chọn Cấp học từ danh sách xổ xuống.');
        $validationLevel->setFormula1("Danh_Muc!\$A\$2:\$A\$" . ($levelCount + 1));
        $sheet->setDataValidation('D4:D500', $validationLevel);

        // Validation cho Cột E: Xã / Phường (E4:E500)
        $validationWard = new DataValidation();
        $validationWard->setType(DataValidation::TYPE_LIST);
        $validationWard->setErrorStyle(DataValidation::STYLE_STOP);
        $validationWard->setAllowBlank(true);
        $validationWard->setShowDropDown(true);
        $validationWard->setShowInputMessage(true);
        $validationWard->setShowErrorMessage(true);
        $validationWard->setErrorTitle('Xã/Phường không hợp lệ');
        $validationWard->setError('Vui lòng chọn Xã/Phường từ danh sách 129 xã/phường có sẵn.');
        $validationWard->setFormula1("Danh_Muc!\$B\$2:\$B\$" . ($wardCount + 1));
        $sheet->setDataValidation('E4:E500', $validationWard);

        // =====================================================================
        // SHEET 2: HƯỚNG DẪN QUY ƯỚC (Huong_Dan)
        // =====================================================================
        $guideSheet = $spreadsheet->createSheet(1);
        $guideSheet->setTitle('Huong_Dan_Quy_Uoc');

        $guideSheet->setCellValue('A1', 'BẢNG GIẢI THÍCH QUY ƯỚC CỘT VÀ ĐỊNH DẠNG DỮ LIỆU (ĐỒNG BỘ 5 TAB ADMIN)');
        $guideSheet->getStyle('A1')->getFont()->setBold(true)->setSize(13)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E40AF'));
        $guideSheet->mergeCells('A1:C1');
        $guideSheet->getRowDimension(1)->setRowHeight(30);

        $guideHeaders = ['Cột', 'Tên trường thông tin', 'Quy ước nhập liệu & Giá trị hợp lệ'];
        $guideSheet->setCellValue('A3', $guideHeaders[0]);
        $guideSheet->setCellValue('B3', $guideHeaders[1]);
        $guideSheet->setCellValue('C3', $guideHeaders[2]);

        $guideSheet->getStyle('A3:C3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $guideSheet->getColumnDimension('A')->setWidth(10);
        $guideSheet->getColumnDimension('B')->setWidth(30);
        $guideSheet->getColumnDimension('C')->setWidth(75);
        $guideSheet->getRowDimension(3)->setRowHeight(28);

        $guideRow = 4;
        foreach (self::COLUMNS as $letter => $info) {
            $guideSheet->setCellValue("A{$guideRow}", $letter);
            $guideSheet->setCellValue("B{$guideRow}", $info['header']);
            $guideSheet->setCellValue("C{$guideRow}", $info['desc']);

            $guideSheet->getStyle("A{$guideRow}:C{$guideRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            $guideSheet->getStyle("A{$guideRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $guideSheet->getRowDimension($guideRow)->setRowHeight(24);
            $guideRow++;
        }

        // Chọn lại Sheet 1 làm active sheet khi mở file
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'Danh_Sach_Truong_Hoc_Ninh_Binh_' . date('Y_m_d') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Tra cứu tọa độ trọng tâm (Centroid) của Xã/Phường từ GeoJSON nếu người dùng để trống tọa độ
     */
    public function getWardCentroid(?string $wardName): ?array
    {
        if (empty($wardName)) {
            return null;
        }

        static $cachedCentroids = null;
        if ($cachedCentroids === null) {
            $cachedCentroids = [];
            $paths = [
                base_path('../src/data/gis/ninh_binh_wards.geojson'),
                base_path('public/data/gis/ninh_binh_wards.geojson'),
                'd:/Code/SchoolMap/src/data/gis/ninh_binh_wards.geojson',
            ];

            $geojsonPath = null;
            foreach ($paths as $p) {
                if (file_exists($p)) {
                    $geojsonPath = $p;
                    break;
                }
            }

            if ($geojsonPath) {
                $data = json_decode(file_get_contents($geojsonPath), true);
                if (!empty($data['features'])) {
                    foreach ($data['features'] as $feature) {
                        $fName = $feature['properties']['fullName'] ?? $feature['properties']['name'] ?? '';
                        $geom = $feature['geometry'] ?? null;
                        if (!$geom) continue;

                        $coords = [];
                        if ($geom['type'] === 'Polygon') {
                            $coords = $geom['coordinates'][0] ?? [];
                        } elseif ($geom['type'] === 'MultiPolygon') {
                            foreach ($geom['coordinates'] as $poly) {
                                foreach ($poly[0] as $pt) {
                                    $coords[] = $pt;
                                }
                            }
                        }

                        if (!empty($coords)) {
                            $sumLng = 0; $sumLat = 0;
                            foreach ($coords as $pt) {
                                $sumLng += $pt[0];
                                $sumLat += $pt[1];
                            }
                            $count = count($coords);
                            $centroid = [
                                'lat' => round($sumLat / $count, 6),
                                'lng' => round($sumLng / $count, 6),
                            ];
                            $cachedCentroids[mb_strtolower(trim($fName))] = $centroid;
                            $cachedCentroids[mb_strtolower(trim(str_ireplace(['Phường ', 'Xã ', 'Thị trấn '], '', $fName)))] = $centroid;
                        }
                    }
                }
            }
        }

        $cleanKey = mb_strtolower(trim($wardName));
        if (isset($cachedCentroids[$cleanKey])) {
            return $cachedCentroids[$cleanKey];
        }

        $strippedKey = mb_strtolower(trim(str_ireplace(['Phường ', 'Xã ', 'Thị trấn '], '', $wardName)));
        return $cachedCentroids[$strippedKey] ?? null;
    }

    /**
     * Nhập danh sách trường học từ file Excel với cơ chế chống lỗi 100% (Bulletproof Fallbacks)
     */
    public function importFromFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'message' => 'Tệp tin Excel không tồn tại trên máy chủ.',
                'total' => 0,
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => ['File không tồn tại: ' . $filePath],
            ];
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getSheet(0); // Lấy sheet đầu tiên
            $rows = $sheet->toArray(null, true, true, false);
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Không thể đọc tệp Excel: ' . $e->getMessage(),
                'total' => 0,
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => [$e->getMessage()],
            ];
        }

        // Bỏ qua 3 dòng đầu (Dòng 1: Banner, Dòng 2: Hướng dẫn, Dòng 3: Header)
        $dataRows = array_slice($rows, 3);
        $total = count($dataRows);
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        // Avoid one database lookup per Excel row. Imports commonly contain
        // hundreds/thousands of rows, so these small in-memory indexes remove
        // the repeated EducationLevel and School queries from the hot loop.
        $levelCatalog = EducationLevel::query()
            ->get(['id', 'name'])
            ->map(fn ($level) => [
                'id' => $level->id,
                'name' => mb_strtolower((string) $level->name),
            ])
            ->all();
        $schools = School::query()
            ->select(['id', 'name', 'code'])
            ->get()
            ->values();
        $schoolsByName = $schools->keyBy('name');
        $schoolsByCode = $schools->filter(fn (School $school) => filled($school->code))->keyBy('code');

        // Kiểm tra xem file Excel có cột Website hay không (dựa vào header dòng 3)
        $headerRow = $rows[2] ?? [];
        $hasWebsiteCol = false;
        foreach ($headerRow as $hdrText) {
            if (str_contains(mb_strtolower((string)$hdrText), 'website') || str_contains(mb_strtolower((string)$hdrText), 'trang web')) {
                $hasWebsiteCol = true;
                break;
            }
        }
        $offset = $hasWebsiteCol ? 1 : 0;
        $hasCampusColumns = collect($headerRow)
            ->contains(fn ($header) => str_contains(mb_strtolower((string) $header), 'mã nhóm'));
        $primaryCodesInFile = $hasCampusColumns
            ? collect($dataRows)
                ->filter(function ($row) use ($offset): bool {
                    $label = trim((string) ($row[2] ?? ''));

                    return $label === '' || mb_strtolower($label) === 'cơ sở chính';
                })
                ->map(fn ($row) => trim((string) ($row[1] ?? '')))
                ->filter()
                ->flip()
            : collect();
        $deferredCampusLinks = [];

        foreach ($dataRows as $index => $row) {
            $rowNum = $index + 4; // Số dòng thực tế trong Excel

            // The simplified layout keeps the group fields in B-C. Remove
            // them from this working row so the established field parsing
            // below retains its stable A-H / I-AB positions.
            $primaryCode = $hasCampusColumns ? trim((string) ($row[1] ?? '')) : '';
            $campusName = $hasCampusColumns ? (trim((string) ($row[2] ?? '')) ?: null) : null;
            if ($hasCampusColumns) {
                $row = array_merge([$row[0] ?? null], array_slice($row, 3));
            }

            // 1. Tên trường học (Cột A - Bắt buộc)
            $name = trim((string)($row[0] ?? ''));
            if (empty($name)) {
                $skipped++;
                continue; // Bỏ qua an toàn các dòng trống
            }

            try {
                // 2. Cấp học (Cột B) - Fallback an toàn nếu thiếu hoặc gõ khác
                $rawLevel = trim((string)($row[1] ?? ''));
                $levelId = 'cao_dang';
                if (!empty($rawLevel)) {
                    $rawLevelLower = mb_strtolower($rawLevel);
                    $slugLevel = Str::slug($rawLevel);
                    $foundLevel = null;
                    foreach ($levelCatalog as $catalogLevel) {
                        if ($catalogLevel['id'] === $slugLevel || str_contains($catalogLevel['name'], $rawLevelLower)) {
                            $foundLevel = $catalogLevel;
                            break;
                        }
                    }
                    if ($foundLevel) {
                        $levelId = $foundLevel['id'];
                    } elseif (str_contains($rawLevelLower, 'đại học') || str_contains($rawLevelLower, 'dai hoc')) {
                        $levelId = 'dai_hoc';
                    } elseif (str_contains($rawLevelLower, 'trung cấp') || str_contains($rawLevelLower, 'trung cap')) {
                        $levelId = 'trung_cap';
                    } elseif (str_contains($rawLevelLower, 'gdtx') || str_contains($rawLevelLower, 'thường xuyên') || str_contains($rawLevelLower, 'nghề')) {
                        $levelId = 'gdtx';
                    }
                }

                // 3. Xã / Phường (Cột C)
                $ward = trim((string)($row[2] ?? '')) ?: null;

                // 4. Địa chỉ chi tiết (Cột D)
                $address = trim((string)($row[3] ?? '')) ?: null;

                // 5. Tọa độ Vĩ độ & Kinh độ (Cột E, F) - CƠ CHẾ CHỐNG THIẾU TỌA ĐỘ
                $latRaw = $row[4] ?? null;
                $lngRaw = $row[5] ?? null;
                $lat = is_numeric($latRaw) ? (float)$latRaw : null;
                $lng = is_numeric($lngRaw) ? (float)$lngRaw : null;

                // Nếu thiếu hoặc không hợp lệ -> Tự động truy vấn trọng tâm xã/phường hoặc trung tâm tỉnh Ninh Bình
                if (empty($lat) || empty($lng) || $lat < 15 || $lat > 25 || $lng < 100 || $lng > 115) {
                    $centroid = $this->getWardCentroid($ward);
                    if ($centroid) {
                        $lat = $centroid['lat'];
                        $lng = $centroid['lng'];
                    } else {
                        $lat = 20.250600;
                        $lng = 105.974500;
                    }
                }

                // 6. Số điện thoại (Cột G)
                $phone = trim((string)($row[6] ?? '')) ?: null;

                // 7. Website trường (Cột H nếu có)
                $website = $hasWebsiteCol ? (trim((string)($row[7] ?? '')) ?: null) : null;

                // 8. Cơ sở vật chất (Cột H/I, I/J, J/K)
                $campusArea = is_numeric($row[7 + $offset] ?? null) ? (float)$row[7 + $offset] : null;
                $classrooms = is_numeric($row[8 + $offset] ?? null) ? (int)$row[8 + $offset] : 0;
                $workshops = is_numeric($row[9 + $offset] ?? null) ? (int)$row[9 + $offset] : 0;

                // 9. Ban Giám hiệu & Lãnh đạo - Phân tích thông minh chống lỗi dấu cách/dấu phân cách
                [$leadersList, $principal] = $this->parseLeadersClean($row[10 + $offset] ?? '');

                // 10. Đào tạo & Sinh viên
                $annualEnrollment = is_numeric($row[11 + $offset] ?? null) ? (int)$row[11 + $offset] : 0;
                $studentCount = is_numeric($row[12 + $offset] ?? null) ? (int)$row[12 + $offset] : 0;
                $annualGraduates = is_numeric($row[13 + $offset] ?? null) ? (int)$row[13 + $offset] : 0;
                $employmentRate = is_numeric($row[14 + $offset] ?? null) ? (float)$row[14 + $offset] : null;

                // Phân tích ngành đào tạo thông minh chống lỗi nhập thừa dấu cách, quên dấu chấm phẩy, quên số chỉ tiêu
                $majorsList = $this->parseTrainingMajorsClean($row[15 + $offset] ?? '', $levelId);

                // 11. Giảng viên & Định biên
                $teacherCount = is_numeric($row[16 + $offset] ?? null) ? (int)$row[16 + $offset] : 0;
                $teacherQuota = is_numeric($row[17 + $offset] ?? null) ? (int)$row[17 + $offset] : 0;
                $teachersShortage = is_numeric($row[18 + $offset] ?? null) ? (int)$row[18 + $offset] : 0;
                $teachersSurplus = is_numeric($row[19 + $offset] ?? null) ? (int)$row[19 + $offset] : 0;
                $facultyRank1 = is_numeric($row[20 + $offset] ?? null) ? (int)$row[20 + $offset] : 0;
                $facultyRank2 = is_numeric($row[21 + $offset] ?? null) ? (int)$row[21 + $offset] : 0;
                $facultyRank3 = is_numeric($row[22 + $offset] ?? null) ? (int)$row[22 + $offset] : 0;
                $facultyDoctors = is_numeric($row[23 + $offset] ?? null) ? (int)$row[23 + $offset] : 0;
                $facultyMasters = is_numeric($row[24 + $offset] ?? null) ? (int)$row[24 + $offset] : 0;
                $facultyProfessors = is_numeric($row[25 + $offset] ?? null) ? (int)$row[25 + $offset] : 0;

                // 12. Doanh nghiệp liên kết - Phân tích thông minh
                $partnersList = $this->parsePartnerEnterprisesClean($row[26 + $offset] ?? '');

                // 13. Quan hệ cơ sở / phân hiệu: users enter only one shared
                // group code and an optional label. The type is inferred.
                $isPrimaryCampus = $campusName === null || mb_strtolower($campusName) === 'cơ sở chính';
                $campusType = $isPrimaryCampus
                    ? 'MAIN'
                    : (str_starts_with(mb_strtolower($campusName), 'phân hiệu') ? 'BRANCH' : 'CAMPUS');

                if ($hasCampusColumns && ! $isPrimaryCampus && $primaryCode === '') {
                    throw new \InvalidArgumentException('Cơ sở trực thuộc/phân hiệu phải có Mã nhóm / cơ sở chính.');
                }
                if ($hasCampusColumns && ! $isPrimaryCampus
                    && ! $schoolsByCode->has($primaryCode) && ! $primaryCodesInFile->has($primaryCode)) {
                    throw new \InvalidArgumentException("Không tìm thấy Mã nhóm / cơ sở chính '{$primaryCode}'.");
                }

                // A primary row is found by its stable group code. Child rows
                // retain their individual record code and are matched by name.
                $school = ($hasCampusColumns && $isPrimaryCampus && $primaryCode !== '')
                    ? $schoolsByCode->get($primaryCode)
                    : $schoolsByName->get($name);
                if ($hasCampusColumns && $isPrimaryCampus && $primaryCode !== ''
                    && $school && $school->name !== $name && $schoolsByName->has($name)) {
                    throw new \InvalidArgumentException('Mã nhóm đang thuộc về một cơ sở chính khác.');
                }
                $isNew = false;
                if (!$school) {
                    $school = new School();
                    $school->name = $name;
                    $isNew = true;
                }

                // Gán toàn bộ giá trị an toàn
                $school->education_level_id = $levelId;
                $school->ward = $ward;
                $school->address = $address;
                $school->lat = $lat;
                $school->lng = $lng;
                $school->geom = DB::raw("Point({$lng}, {$lat})");
                $school->phone = $phone;
                if ($hasWebsiteCol || !empty($website)) {
                    $school->website = $website;
                }
                $school->campus_area_m2 = $campusArea;
                $school->classroom_count = $classrooms;
                $school->workshops_count = $workshops;
                $school->principal = $principal;
                $school->leaders = !empty($leadersList) ? $leadersList : null;
                $school->annual_enrollment = $annualEnrollment;
                $school->student_count = $studentCount;
                $school->annual_graduates = $annualGraduates;
                $school->employment_rate = $employmentRate;
                $school->training_majors = !empty($majorsList) ? $majorsList : null;
                $school->teacher_count = $teacherCount;
                $school->teacher_quota = $teacherQuota;
                $school->teachers_shortage = $teachersShortage;
                $school->teachers_surplus = $teachersSurplus;
                $school->faculty_rank_1 = $facultyRank1;
                $school->faculty_rank_2 = $facultyRank2;
                $school->faculty_rank_3 = $facultyRank3;
                $school->faculty_doctors = $facultyDoctors;
                $school->faculty_masters = $facultyMasters;
                $school->faculty_professors = $facultyProfessors;
                $school->partner_enterprises = !empty($partnersList) ? $partnersList : null;

                if ($hasCampusColumns && $isPrimaryCampus && $primaryCode !== '') {
                    $school->code = $primaryCode;
                }
                if ($hasCampusColumns) {
                    // Child rows may precede the primary campus. Persist all
                    // locations first, then resolve their parent in one pass.
                    $school->campus_type = 'MAIN';
                    $school->parent_school_id = null;
                    $school->campus_name = null;
                }

                $school->save();

                // Make newly created rows available to later duplicate rows in
                // the same file without another database round trip.
                $schoolsByName->put($name, $school);
                $schoolsByCode->put($school->code, $school);

                if ($hasCampusColumns && ! $isPrimaryCampus) {
                    $deferredCampusLinks[] = [
                        'id' => $school->id,
                        'type' => $campusType,
                        'parent_code' => $primaryCode,
                        'name' => $campusName,
                        'row' => $rowNum,
                    ];
                }

                // Đồng bộ quan hệ nhiều-nhiều educationLevels
                if ($levelId) {
                    $school->educationLevels()->sync([$levelId]);
                }

                if ($isNew) {
                    $created++;
                } else {
                    $updated++;
                }
            } catch (\Throwable $e) {
                $errors[] = "Dòng {$rowNum} ('{$name}'): " . $e->getMessage();
            }
        }

        foreach ($deferredCampusLinks as $link) {
            try {
                /** @var School|null $campus */
                $campus = School::find($link['id']);
                /** @var School|null $parent */
                $parent = $schoolsByCode->get($link['parent_code']);
                if (! $campus || ! $parent) {
                    throw new \RuntimeException("Không tìm thấy cơ sở chính '{$link['parent_code']}' sau khi nhập.");
                }

                $campus->campus_type = $link['type'];
                $campus->parent_school_id = $parent->id;
                $campus->campus_name = $link['name'];
                $campus->save();
            } catch (\Throwable $e) {
                $errors[] = "Dòng {$link['row']} (quan hệ cơ sở): " . $e->getMessage();
            }
        }

        return [
            'success' => true,
            'total' => $total,
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Phân tích chuỗi Ngành đào tạo cực kỳ thông minh:
     * - Tự động dọn dẹp khoảng trắng thừa ("   Công nghệ   Ô tô   :  200  ")
     * - Chấp nhận nhiều loại dấu phân cách: chấm phẩy (;), xuống dòng (\n, \r), dấu gạch đứng (|), dấu phẩy (,)
     * - Tự động xử lý trường hợp quên điền số chỉ tiêu (mặc định 0) hoặc chỉ ghi chữ ("Công nghệ Thông tin")
     * - Tự động trích xuất số nếu người dùng gõ kèm chữ ("200 chỉ tiêu", "150 sinh viên")
     * - Chấp nhận dấu gạch ngang thay cho dấu hai chấm ("Công nghệ Ô tô - 150")
     */
    public function parseTrainingMajorsClean(?string $raw, string $levelId = 'cao_dang'): array
    {
        if (empty($raw)) {
            return [];
        }

        // Chuẩn hóa dấu xuống dòng và dấu gạch đứng thành dấu chấm phẩy
        $clean = str_replace(["\r\n", "\r", "\n", "|"], ';', $raw);

        // Nếu người dùng không dùng chấm phẩy mà dùng dấu phẩy ngăn cách các ngành
        if (!str_contains($clean, ';') && substr_count($clean, ':') > 1) {
            $clean = str_replace(',', ';', $clean);
        }

        $parts = explode(';', $clean);
        $results = [];

        foreach ($parts as $part) {
            // Xóa dấu cách thừa liên tiếp bên trong và 2 đầu
            $part = trim(preg_replace('/\s+/', ' ', $part));
            if (empty($part)) continue;

            $majorName = $part;
            $quota = 0;

            if (str_contains($part, ':')) {
                [$nameChunk, $quotaChunk] = explode(':', $part, 2);
                $majorName = trim($nameChunk);
                $quotaChunk = trim($quotaChunk);

                // Trích xuất số nếu có (bỏ qua chữ như "chỉ tiêu", "SV", "học sinh")
                if (preg_match('/\d+/', $quotaChunk, $m)) {
                    $quota = (int)$m[0];
                }
            } elseif (preg_match('/^(.*?)[-–—]\s*(\d+)/u', $part, $dashMatch)) {
                // Nhận diện cú pháp gạch ngang "Tên ngành - 150"
                $majorName = trim($dashMatch[1]);
                $quota = (int)$dashMatch[2];
            }

            if (!empty($majorName)) {
                $results[] = [
                    'name' => $majorName,
                    'annual_quota' => $quota,
                    'degree_level' => $levelId,
                    'major_code' => '',
                ];
            }
        }

        return $results;
    }

    /**
     * Phân tích danh sách Ban Giám hiệu & Lãnh đạo thông minh:
     * - Tự động dọn dẹp khoảng trắng thừa
     * - Nhận diện nhiều dấu phân cách (; , \n |)
     * - Nhận diện chức danh bằng dấu hai chấm hoặc dấu gạch ngang
     * - Tự động suy luận Hiệu trưởng/Giám đốc làm lãnh đạo chính
     */
    public function parseLeadersClean(?string $raw): array
    {
        if (empty($raw)) {
            return [[], null];
        }

        $clean = str_replace(["\r\n", "\r", "\n", "|"], ';', $raw);
        if (!str_contains($clean, ';') && substr_count($clean, ':') > 1) {
            $clean = str_replace(',', ';', $clean);
        }

        $parts = explode(';', $clean);
        $leadersList = [];
        $principal = null;

        foreach ($parts as $p) {
            $p = trim(preg_replace('/\s+/', ' ', $p));
            if (empty($p)) continue;

            $pos = 'Lãnh đạo';
            $ldrName = $p;

            if (str_contains($p, ':')) {
                [$posChunk, $nameChunk] = explode(':', $p, 2);
                $pos = trim($posChunk) ?: 'Lãnh đạo';
                $ldrName = trim($nameChunk);
            } elseif (preg_match('/^(Hiệu trưởng|Phó Hiệu trưởng|Giám đốc|Phó Giám đốc|Trưởng khoa|Phó Trưởng khoa)\s*[-–—]\s*(.*)$/ui', $p, $m)) {
                $pos = trim($m[1]);
                $ldrName = trim($m[2]);
            }

            if (!empty($ldrName)) {
                $leadersList[] = ['position' => $pos, 'name' => $ldrName];
                if (empty($principal) && (str_contains(mb_strtolower($pos), 'hiệu trưởng') || str_contains(mb_strtolower($pos), 'giám đốc'))) {
                    $principal = $ldrName;
                }
            }
        }

        if (empty($principal) && !empty($leadersList)) {
            $principal = $leadersList[0]['name'];
        }

        return [$leadersList, $principal];
    }

    /**
     * Phân tích Doanh nghiệp liên kết thông minh:
     * - Tự động dọn dẹp khoảng trắng thừa
     * - Xử lý trường hợp chỉ ghi tên doanh nghiệp (tự gán nội dung hợp tác tiêu chuẩn)
     * - Nhận diện phân cách qua dấu hai chấm hoặc gạch ngang
     */
    public function parsePartnerEnterprisesClean(?string $raw): array
    {
        if (empty($raw)) {
            return [];
        }

        $clean = str_replace(["\r\n", "\r", "\n", "|"], ';', $raw);
        if (!str_contains($clean, ';') && substr_count($clean, ':') > 1) {
            $clean = str_replace(',', ';', $clean);
        }

        $parts = explode(';', $clean);
        $partnersList = [];

        foreach ($parts as $p) {
            $p = trim(preg_replace('/\s+/', ' ', $p));
            if (empty($p)) continue;

            $eName = $p;
            $coop = 'Liên kết đào tạo & tuyển dụng';

            if (str_contains($p, ':')) {
                [$nameChunk, $coopChunk] = explode(':', $p, 2);
                $eName = trim($nameChunk);
                $coopClean = trim($coopChunk);
                if (!empty($coopClean)) {
                    $coop = $coopClean;
                }
            } elseif (preg_match('/^(.*?)[-–—]\s*(.*)$/u', $p, $m)) {
                $eName = trim($m[1]);
                $coop = trim($m[2]) ?: 'Liên kết đào tạo & tuyển dụng';
            }

            if (!empty($eName)) {
                $partnersList[] = [
                    'name' => $eName,
                    'cooperation' => $coop,
                    'is_featured' => true,
                ];
            }
        }

        return $partnersList;
    }
}
